<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Library\Notify;
use App\Model\BusRuleRef;
use App\Model\Category;
use App\Model\CouponCode;
use App\Model\Offer;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Image;
use URL;

class CategoryController extends Controller {
	public $category;
	public $sub_category;
	public $columns;
	public $restrict;

	public function __construct() {
		$this->category = new Category;
		$this->restrict = BusRuleRef::where('rule_name', 'category_restrict')->first()->rule_value;
		$this->columns = [
			"select", "s_no", "category_no", "parent_id", "category_code", "name", "image", "banner", "description", "status", "activate", "action", "offer",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = Category::where('status', '!=', 'DL')->count();
		return view('categories.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function categoryAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->category->fetchCategories();
		$count = $records->get();
		if (isset($request->start)) {
			$list = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$list = $records->offset($request->start)->limit(count($count))->get();
		}
		// echo $total;
		$result = [];
		$categories = [];
		$i = 1;

		foreach ($list as $key => &$value) {
			$level = 1;
			$value->sno = $i . ". ";
			if ($this->checkFilter($value, $request)) {
				$categories[$value->id] = $value;
			}
			$root = $value;
			$categories = $this->setListView($root, $categories, $i, $request, $level);
			$i++;

		}
		if (isset($request->order_column)) {
			$categories = $this->sortArray($categories, $request->order_column, $request->order_dir);
		}
		$total = count($categories);
		// die();
		$i = 1;
		foreach ($categories as $cat) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $i++;
			$data['category_no'] = $cat->sno;
			$data['parent_id'] = ($cat->parent != null) ? $cat->parent : '-';
			$data['category_code'] = $cat->category_code;
			$data['name'] = $cat->name;
			$data['image'] = ($cat->image != null) ? URL::asset('/uploads/categories/' . $cat->image) : '-';
			$data['banner'] = ($cat->banner != null) ? URL::asset('/uploads/categories/' . $cat->banner) : '-';
			$data['description'] = ($cat->description != null) ? $cat->description : '-';
			// $data['featured'] = ucfirst(config('constants.CONFIRM.' . $cat->featured));
			$data['status'] = ucfirst(config('constants.STATUS.' . $cat->status));

			if (Helper::checkAccess(route('changeStatusCategory'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($cat->status == 'AC' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusCategory"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $cat->status));
			}

			$action = '';

			$sub = $cat;
			$cat_offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$sub->id])->first();
			$sub->offer = ($cat_offer) ? $cat_offer->id : '';
			while ($sub->offer == '') {

				$temp = Category::where('id', $sub->parent_id)->first();
				if (isset($temp->id)) {

					$cat_offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$temp->id])->first();
					$sub->offer = ($cat_offer) ? $cat_offer->id : '';
					if ($sub->offer != '') {
						break;
					}
				} else {
					break;
				}
				$sub = $temp;
			}
			$data['offer'] = ($sub->offer != '') ? Offer::find($sub->offer)->offer_code : '-';
			/*if (Helper::checkAccess(route('changeStatusCategory'))) {

				if ($cat->status == 'AC') {
					$action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $cat->status . '" data-code="' . $cat->name . '" data-id="' . $cat->id . '" data-toggle="tooltip" data-placement="bottom" title="Deactivate Category"><i class="fa fa-lock" aria-hidden="true"></i></a>';
				} else {
					$action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $cat->status . '" data-id="' . $cat->id . '" data-code="' . $cat->name . '" data-toggle="tooltip" data-placement="bottom" title="Activate Category"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
				}
			}*/
			if (Helper::checkAccess(route('editCategory'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editCategory', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			if (Helper::checkAccess(route('viewCategory'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewCategory', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			}
			if (Helper::checkAccess(route('deleteCategory'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteCategory" data-toggle="tooltip" data-placement="bottom" data-id="' . $cat->id . '" title="Delete"><i class="fa fa-times"></i></a>';
			}
			$data['action'] = $action;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
		]);
		echo $data;

	}

	//function to sort final array for listing page
	//need to create function to sort parent as well as child categories
	public function sortArray($data, $column, $dir) {
		$col = $this->columns[$column];
		if ($this->columns[$column] == 'parent_id') {
			$col = 'parent';
		}
		usort($data, function ($a, $b) use ($col, $dir) {
			if ($a->$col == $b->$col) {
				return 0;
			}
			if ($dir == 'desc') {
				return ($a->$col < $b->$col) ? -1 : 1;
			} else {
				return ($a->$col > $b->$col) ? -1 : 1;
			}

		});
		return $data;
	}

	//function to filter data before adding to final array for listing page
	//need to create function to filter parent as well as child categories
	public function checkFilter($data, $request) {
		$valid = true;
		if (isset($request->from_date)) {
			if (date('Y-m-d', strtotime($data->created_at)) < date("Y-m-d", strtotime($request->from_date))) {
				$valid = false;
			}
		}
		if (isset($request->end_date)) {
			if (date('Y-m-d', strtotime($data->created_at)) > date("Y-m-d", strtotime($request->end_date))) {
				if (!$valid) {
					$valid = false;
				}

			}
		}
		if (isset($request->search)) {
			if (stripos($data->name, $request->search) === false && stripos($data->category_code, $request->search) === false && stripos($data->description, $request->search) === false) {
				$valid = false;
			}

		}
		if (isset($request->status)) {
			if ($data->status != $request->status) {
				$valid = false;
			}

		}
		/*if (isset($request->featured)) {
			if ($data->featured != $request->featured) {
				$valid = false;
			}

		}*/
		return $valid;
	}

	//recursive function to create array of categories and subcategories together using nested loop along with indented serial number for listing page.
	public function setListView($root, $categories, $i, $request, $level) {
		$child = $root->childCat;
		$j = 1;
		// print_r($root);
		// echo "level-" . $level;
		// echo "restrict-" . $this->restrict;
		if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
			foreach ($child as &$ch) {
				if ($ch->status != 'DL') {
					//ordering for each subcategory along with nesting.
					$k = $i . "." . $j;
					$ch->sno = $k . '. ';
					$ch->parent = $root->name;
					//check filter before adding to the array
					if ($this->checkFilter($ch, $request)) {
						$categories[$ch->id] = $ch;
					}

					//calling function recursively to loop through subcategories of each category.
					$categories = $this->setListView($ch, $categories, $k, $request, ++$level);
					$level = 2;
					$j++;
				}

			}
			//once all subcategories are done looping, move to next main subcategory.
			$root = $child;
		}
		return $categories;
	}

	//recursive function to create array of categories and subcategories together using nested loop along with indented serial number.
	public function setList($root, $categories, $i, $level, $id = null)
    {
        $child = $root->childCat;
        $j = 1;
        if (($this->restrict == null) || ($this->restrict != null && $level < $this->restrict)) {
            foreach ($child as $ch) {
                if ($ch->status != 'DL' && ($id == null || ($id != null && $id != $ch->id))) {
                    //ordering for each subcategory along with nesting.
                    $k = "&nbsp;&nbsp;" . $i . "." . $j;
                    $categories[$ch->id] = $k . '. ' . $ch->name;

                    //calling function recursively to loop through subcategories of each category.
                    $categories = $this->setList($ch, $categories, $k, ++$level, $id);
                    $j++;
                }
            }
            //once all subcategories are done looping, move to next main subcategory.
            $root = $child;
        }
        return $categories;
    }

	//recursive function to create array of categories and subcategories together using nested loop along with indented serial number for view page
	public function setListOrder($root, $categories, $level) {
		$child = $root->childCat;
		if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
			foreach ($child as $ch) {
				if ($ch->status != 'DL') {
					//ordering for each subcategory along with nesting.
					if (!array_key_exists($root->id, $categories)) {
						$categories[$root->id] = array();
					}
					$categories[$root->id][$ch->id] = $ch->name;

					//calling function recursively to loop through subcategories of each category.
					$categories = $this->setListOrder($ch, $categories, ++$level);
					$level = 2;
				}
			}
			//once all subcategories are done looping, move to next main subcategory.
			$root = $child;
		}
		return $categories;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() { 
		$type = 'add';
		$url = route('addCategory');
		$category = new Category;
		$list = $this->category->fetchCategories()->get(); 

		$categories = [];
		$i = 1;
		$level = 1;
		foreach ($list as $key => $value) {
			$categories[$value->id] = $i . ". " . $value->name;
			$root = $value;
			$categories = $this->setList($root, $categories, $i, $level);
			$i++;
		}

		$offers = Offer::where('status', 'AC')->whereRaw('? between start_date and end_date', [date('Y-m-d')])->get();

		return view('categories.create', compact('type', 'url', 'category', 'categories', 'offers'));
	}

	/**
	 * check for unique category
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkCategory(Request $request, $id = null) {
		if (isset($request->cat_name)) {
			$check = Category::where('name', $request->cat_name);
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
			}
			if (isset($request->parent) && $request->parent != null) {
				$check = $check->where('parent_id', $request->parent);
			}
			$check = $check->where('status', '!=', 'DL')->count();
			if ($check > 0) {
				return "false";
			} else {
				return "true";
			}

		} else {
			return "true";
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		$validate = Validator($request->all(), [
			'cat_name' => 'required',
			'description' => 'required',
			'cat_image' => 'mimes:jpeg,png,jpg,gif,svg|min:1',
			'banner' => 'mimes:jpeg,png,jpg,gif,svg|min:1', 

		]);

		$attr = [
			'cat_name' => 'Category Name',
			'description' => 'Description',
			'cat_image' => 'Image',
			'banner' => 'Banner',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createCategory')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$category = new Category;

				$imageName = '';
				if ($request->file('cat_image') != null) {
					$image = $request->file('cat_image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/categories'), $imageName);
					Helper::compress_image(public_path('uploads/categories/' . $imageName), Notify::getBusRuleRef('image_quality'));
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}

				$bannerName = '';
				if ($request->file('cat_banner') != null) {
					$banner = $request->file('cat_banner');
					$bannerName = time() . $banner->getClientOriginalName();
					$bannerName = str_replace(' ', '', $bannerName);
					$bannerName = str_replace('.jpeg', '.jpg', $bannerName);
					$banner->move(public_path('uploads/categories'), $bannerName);
					Helper::compress_image(public_path('uploads/categories/' . $bannerName), Notify::getBusRuleRef('image_quality'));
				}

				$category->category_code = Helper::generateNumber('c_categories', 'category_code');
				$category->name = $request->post('cat_name');
				$category->image = str_replace('.jpeg', '.jpg', $imageName);
				$category->banner = $bannerName;
				$category->description = $request->post('description');

				if (isset($request->parent_id)) {
					$category->parent_id = $request->post('parent_id');
				}
				// $category->featured = trim($request->post('featured'));
				$category->status = trim($request->post('status'));
				$category->created_at = date('Y-m-d H:i:s');

				if ($category->save()) {
					if (isset($request->offer) && $request->offer != null) {
						$offer = Offer::find($request->offer);
						$offer->category_id .= ',' . $category->id;
						$offer->save();
					}
					$request->session()->flash('success', 'Category added successfully');
					return redirect()->route('categories');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('categories');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('categories');
			}

		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$category = Category::where('id', $id)->first();
			if (isset($category->id)) {

				$categories = [];
				$i = 1;

				$categories = $this->setListOrder($category, $categories, 1);
				$offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->id])->where('status', 'AC')->first();
				$code = CouponCode::whereRaw('FIND_IN_SET(?,category_id)', [$category->id])->where('status', 'AC')->first();
				// dd($categories);
				return view('categories.view', compact('category', 'categories', 'offer', 'code'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('categories');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('categories');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		if (isset($id) && $id != null) {
            $category = Category::where('id', $id)->first();
            if (isset($category->id)) {
                $type = 'edit';
                $url = route('updateCategory', ['id' => $category->id]);

                $list = $this->category->fetchCategories($id)->get();

                $categories = [];
                $i = 1;
                foreach ($list as $key => $value) {
                    if ($value->id != $category->id) {

                        $categories[$value->id] = $i . ". " . $value->name;
                        $root = $value;
                        $categories = $this->setList($root, $categories, $i, 1, $id);
                        $i++;
                    }
                }
                $offers = Offer::where('status', '!=', 'DL')->get();

                $cat_offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->id])->first();
                $category->offer = ($cat_offer) ? $cat_offer->id : '';
                $sub = $category;

                // if this is a subcategory and no offer is applied on this category explicitly, then parent category offer will be shown in the dropdown
                while ($sub->offer == '') {

                    $temp = Category::where('id', $sub->parent_id)->first();
                    if (isset($temp->id)) {

                        $cat_offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$temp->id])->first();
                        $sub->offer = ($cat_offer) ? $cat_offer->id : '';
                        if ($sub->offer != '') {
                            break;
                        }
                    } else {
                        break;
                    }
                    $sub = $temp;
                }
                $category->offer = $sub->offer;
                return view('categories.create', compact('category', 'type', 'url', 'categories', 'offers', 'cat_offer'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('categories');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('categories');
        }
	}

	// fetch offer from the parent category if subcategory parent is changed
	public function fetchCategoryOffer(Request $request) {

		if (isset($request->parent) && $request->parent != null) {
			$offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$request->parent])->first();

			if (isset($offer->id)) {
				echo $offer->id;
			} else {
				echo '';
			}
		} else {
			echo '';
		}

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$category = Category::where('id', $id)->first();
			if (isset($category->id)) {

				$validate = Validator($request->all(), [
					'cat_name' => 'required',
					'description' => 'required',
					// 'cat_image' => 'mimes:jpeg,png,jpg,gif,svg|min:1000',
					// 'cat_banner' => 'mimes:jpeg,png,jpg,gif,svg|min:1000',
				]);

				$attr = [
					'cat_name' => 'Category Name',
					'description' => 'Description',
					'cat_image' => 'Image',
					'cat_banner' => 'Banner',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('createCategory')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						$imageName = '';
						if ($request->file('cat_image') != null) {
							$image = $request->file('cat_image');
							$imageName = time() . $image->getClientOriginalName();
							if ($category->image != null && file_exists(public_path('uploads/categories/' . $category->image))) {
								unlink(public_path('uploads/categories/' . $category->image));
							}

							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/categories'), $imageName);
							Helper::compress_image(public_path('uploads/categories/' . $imageName), Notify::getBusRuleRef('image_quality'));
							$category->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$bannerName = '';
						if ($request->file('cat_banner') != null) {
							$banner = $request->file('cat_banner');
							$bannerName = time() . $banner->getClientOriginalName();
							if ($category->banner != null && file_exists(public_path('uploads/categories/' . $category->banner))) {
								unlink(public_path('uploads/categories/' . $category->banner));
							}

							$bannerName = str_replace(' ', '', $bannerName);
							$bannerName = str_replace('.jpeg', '.jpg', $bannerName);
							$banner->move(public_path('uploads/categories'), $bannerName);
							Helper::compress_image(public_path('uploads/categories/' . $bannerName), Notify::getBusRuleRef('image_quality'));
							$category->banner = str_replace('.jpeg', '.jpg', $bannerName);
						}
						$category->name = $request->post('cat_name');
						$category->description = $request->post('description');
						if (isset($request->parent_id)) {
							$category->parent_id = $request->post('parent_id');
						}
						$category->status = trim($request->post('status'));

						if ($category->save()) {
							// remove this category from the offer
							if (isset($request->category_offer)) {
								$offer = Offer::find($request->category_offer);
								$cat = explode(',', $offer->category_id);

								// check if parent category exists in the offer category list or not. If yes, then do not unset the category id from the list
								if (isset($category->parentCat->id)) {
									if (!in_array($category->parentCat->id, $cat)) {
										// in case of subcategories when subcategory offer is different from parent category
										unset($cat[array_search($category->id, $cat)]);
									} else {
										// in case of subcategories when subcategory offer is same as parent category
										// unset($cat[array_search($category->parentCat->id, $cat)]);
									}
								} else if (in_array($category->id, $cat)) {
									// in all cases except above one
									unset($cat[array_search($category->id, $cat)]);
								}

								$offer->category_id = implode(',', $cat);
								$offer->save();
							}

							// add this category to the selected offer
							if (isset($request->offer) && $request->offer != null) {
								$offer = Offer::find($request->offer);
								$cat = explode(',', $offer->category_id);
								// check if category id or it's parent category id exists the offer category list or not
								if (isset($category->parentCat->id) && !in_array($category->parentCat->id, $cat)) {
									if (!in_array($category->id, $cat)) {
										$offer->category_id .= ',' . $category->id;
									}

								} else if (!isset($category->parentCat->id)) {
									if (!in_array($category->id, $cat)) {
										$offer->category_id .= ',' . $category->id;
									}
								}

								$offer->save();
							}

							// if subcategory overwrite is selected, then remove all the subcategories from the offers
							if (isset($request->subcat_overwrite) && $request->subcat_overwrite == 'on') {
								if ($category->parent_id == null) {
									$childs = array_column($category->childCat->toArray(), 'id');
									foreach ($childs as $id) {
										$offers = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$id])->get();
										foreach ($offers as $offer) {
											$cat = explode(',', $offer->category_id);
											unset($cat[array_search($id, $cat)]);
											$offer->category_id = implode(',', $cat);
											$offer->save();
										}
									}
								}
							}

							$request->session()->flash('success', 'Category updated successfully');
							return redirect()->route('categories');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('categories');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('categories');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('categories');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('categories');
		}

	}

	// activate/deactivate category
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$category = Category::find($request->statusid);

			if (isset($category->id)) {
				$category->status = $request->status;
				/*if (isset($request->featured)) {
					$category->featured = $request->featured;
				}*/
				if ($category->save()) {
					$request->session()->flash('success', 'Category updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update category. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}

	}

	// activate/deactivate category
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$category = Category::find($request->statusid);

			if (isset($category->id)) {
				$category->status = $request->status;
				if ($category->save()) {
					echo json_encode(['status' => 1, 'message' => 'Category updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update category. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Category']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Category']);
		}

	}

	public function deleteItems($root, $level) {
		$child = $root->childCat;
		foreach ($child as $ch) {
			$ch->status = 'DL';
			$ch->save();
			//calling function recursively to loop through subcategories of each category.
			$this->deleteItems($ch, ++$level);
		}
		//once all subcategories are done looping, move to next main subcategory.
		$root = $child;
		return true;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$category = Category::find($request->deleteid);

			if (isset($category->id)) {
				$category->status = 'DL';
				if ($category->save()) {

					$this->deleteItems($category, 1);

					echo json_encode(['status' => 1, 'message' => 'Category deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete category. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Category']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Category']);
		}
	}
	/**
	 * Remove multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkdelete(Request $request) {

		if (isset($request->deleteid) && $request->deleteid != null) {
			$deleteid = explode(',', $request->deleteid);
			$ids = count($deleteid);
			$count = 0;
			foreach ($deleteid as $id) {
				$category = Category::find($id);

				if (isset($category->id)) {
					$category->status = 'DL';
					if ($category->save()) {
						$this->deleteItems($category, 1);
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Category deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all category were deleted. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
	/**
	 * activate/deactivate multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkchangeStatus(Request $request) {

		if (isset($request->ids) && $request->ids != null) {
			$ids = count($request->ids);
			$count = 0;
			foreach ($request->ids as $id) {
				$category = Category::find($id);

				if (isset($category->id)) {
					if ($category->status == 'AC') {
						$category->status = 'IN';
					} elseif ($category->status == 'IN') {
						$category->status = 'AC';
					}

					if ($category->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Category updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all categories were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
