<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Library\Notify;
use App\Model\BusRuleRef;
use App\Model\Category;
use App\Model\CartItem;
use App\Model\Offer;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use URL;
use File;

class OfferController extends Controller {
	public $offer;
	public $columns;
	public $restrict;
	public $category;
	public $subcategories;
	public $existing_categories;

	public function __construct() {
		$this->offer = new Offer;
		$this->subcategories = [];
		$this->restrict = BusRuleRef::where('rule_name', 'category_restrict')->first()->rule_value;
		$this->category = new Category;
		$this->columns = [
			"select","sno", "image", "offer_code", "categories", "type", "value", "start_date", "end_date", "top_offer", "status", "activate", "action", "min_amount", "max_amount",
		];
		$this->existing_categories = [];

		$data = Offer::where('status', 'AC')->pluck('category_id');
		foreach ($data as $value) {
			$cat = explode(',', $value);
			foreach ($cat as $val) {
				if (!in_array($val, $this->existing_categories)) {
					$this->existing_categories[] = $val;
				}

			}
		}

	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {

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
		return view('offers.index', compact('categories'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function offersAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->offer->fetchOffers($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$offers = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$offers = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = $request->start + 1;
		foreach ($offers as $offer) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $offer->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $i++;
			$data['image'] = ($offer->image != null) ? '<img src="' . URL::asset('/uploads/offers/' . $offer->image) . '" width="50%">' : '-';
			$data['offer_code'] = $offer->offer_code;
			$data['categories'] = $this->formatCategory($offer->category_id);
			$data['type'] = ucfirst(config('constants.PLAN_TYPE.' . $offer->type));
			$data['value'] = $offer->value . '%';
			$data['start_date'] = date('Y M, d', strtotime($offer->start_date));
			$data['end_date'] = date('Y M, d', strtotime($offer->end_date));
			$data['top_offer'] = ucfirst(config('constants.CONFIRM.' . $offer->is_top_offer));
			$data['status'] = ucfirst(config('constants.STATUS.' . $offer->status));

			// check active coupon_codes of similar categories
			$cat = explode(',', $offer->category_id);
			$count = 0;
			foreach ($cat as $key => $value) {
				if (Offer::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->count() > 0) {
					$count++;
				}

			}

			if (Helper::checkAccess(route('changeStatusOffer'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($offer->status == 'AC' ? ' checked' : '') . ' data-id="' . $offer->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusOffer" data-count="' . $count . '"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $offer->status));
			}
			$action = '';

			if (Helper::checkAccess(route('editOffer'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editOffer', ['id' => $offer->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			if (Helper::checkAccess(route('viewOffer'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewOffer', ['id' => $offer->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			}
			$data['action'] = $action;
			$data['image_link'] = ($offer->image != null) ? URL::asset('/uploads/offers/' . $offer->image) : '-';
			$data['min_amount'] = $offer->min_amount;
			$data['max_amount'] = $offer->max_amount;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($total),
			'recordsFiltered' => count($total),
		]);
		// &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteUser" data-code="{{$user->name}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
		echo $data;

	}

	// function to create comma separated string of categories from id.
	public function formatCategory($ids) {
		$ids = explode(',', $ids);
		$categories = implode(',', Category::whereIN('id', $ids)->pluck('name')->toArray());
		return $categories;
	}

	//recursive function to create array of categories and subcategories together using nested loop along with indented serial number.
	public function setList($root, $categories, $i, $level) {
		$child = $root->childCat;
		$j = 1;
		if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
			if (!isset($this->subcategories[$i]) && count($child) > 0) {
				$this->subcategories[$i] = array();
			}

			foreach ($child as $ch) {
				if ($ch->status != 'DL') {
					//ordering for each subcategory along with nesting.
					$k = "&nbsp;&nbsp;" . $i . "." . $j;
					$categories[$ch->id] = $k . '. ' . $ch->name;
					array_push($this->subcategories[$i], $ch->id);
					//calling function recursively to loop through subcategories of each category.
					$categories = $this->setList($ch, $categories, $k, ++$level);
					$level = 2;
					$j++;
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
		$url = route('addOffer');
		$offer = new Offer;
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
		$subcategories = $this->subcategories;
		$existing = $this->existing_categories;
		return view('offers.create', compact('type', 'url', 'offer', 'categories', 'subcategories', 'existing'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'offer_image' => 'required|mimes:jpeg,png,jpg,gif,svg|min:100',
			'offer_type' => 'required',
			'discount_value' => 'required',
			'max_amount' => 'nullable',
			'min_amount' => 'required',
			'start_date' => 'required',
			'end_date' => 'required',
			'categories.*' => 'required',

		]);

		$attr = [
			'offer_image' => 'Offer Image',
			'offer_type' => 'Type',
			'discount_value' => 'Discount value',
			'max_amount' => 'Maximum Amount',
			'min_amount' => 'Minimum Amount',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'categories.*' => 'Categories',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createOffer')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$offer = new Offer;
				$offer->offer_code = Helper::generateNumber('c_offers', 'offer_code');

				$imageName = '';
				if ($request->file('offer_image') != null) {
					$image = $request->file('offer_image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/offers'), $imageName);
					Helper::compress_image(public_path('uploads/offers/' . $imageName), Notify::getBusRuleRef('image_quality'));
				}
				// remove subcategories from the list if parent category is selected.
				$offer_cat = $request->post('categories');
				foreach ($offer_cat as $value) {
					if (isset($this->subcategories[$value])) {
						foreach ($this->subcategories[$value] as $key) {
							unset($offer_cat[array_search($key, $offer_cat)]);
						}
					}
				}
				$offer->category_id = implode(',', $offer_cat);
				$offer->image = str_replace('.jpeg', '.jpg', $imageName);
				$offer->type = $request->post('offer_type');
				$offer->value = $request->post('discount_value');
				$offer->min_amount = $request->post('min_amount');
				$offer->max_amount = ($request->post('max_amount') != null) ? $request->post('max_amount') : null;
				$offer->start_date = date('Y-m-d', strtotime($request->post('start_date')));
				$offer->end_date = date('Y-m-d', strtotime($request->post('end_date')));
				$offer->is_top_offer = $request->post('top_offer');
				$offer->status = trim($request->post('status'));
				$offer->created_at = date('Y-m-d H:i:s');

				if ($offer->save()) {
					$request->session()->flash('success', 'Offer added successfully');
					return redirect()->route('offers');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('offers');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('offers');
			}

		}
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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$offer = Offer::where('id', $id)->first();
			if (isset($offer->id)) {
				$list = Category::with(['childCat' => function ($q) {
					$q->where('c_categories.status', '!=', 'DL');
					// exclude those subcategories where separate offer is applied
					$q->whereRaw('not exists(select category_id from c_offers where find_in_set(c_categories.id,c_offers.category_id))');
				}])->where('status', '!=', 'DL')->whereIN('id', explode(',', $offer->category_id))->orderBy('created_at', 'asc')->get();
				$categories = [];
				$i = 1;
				$level = 1;
				foreach ($list as $key => $value) {
					$categories[$value->id] = $i . ". " . $value->name;
					$root = $value;
					$categories = $this->setList($root, $categories, $i, $level);
					$i++;
				}

				$subcategories = $this->subcategories;
				return view('offers.view', compact('offer', 'categories'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('offers');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('offers');
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
			$offer = Offer::where('id', $id)->first();
			if (isset($offer->id)) {
				// if (count($offer->offer_cartitems) <= 0) {
					// if (count($offer->offer_orderitems) <= 0) {
						$type = 'edit';
						$url = route('updateOffer', ['id' => $offer->id]);

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
						$subcategories = $this->subcategories;
						$existing = $this->existing_categories;

						return view('offers.create', compact('offer', 'type', 'url', 'categories', 'subcategories', 'existing'));
					// } else {
					// 	$request->session()->flash('error', 'This offer cannot be edited. Orders have been placed using this offer.');
					// 	return redirect()->route('offers');
					// }
				// } else {
				// 	$request->session()->flash('error', 'This offer cannot be edited. Cart has been created using this offer.');
				// 	return redirect()->route('offers');
				// }

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('offers');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('offers');
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
			$offer = Offer::where('id', $id)->first();
			if (isset($offer->id)) {
				$validate = Validator($request->all(), [
					'offer_type' => 'required',
					'discount_value' => 'required',
					'max_amount' => 'nullable',
					'min_amount' => 'required',
					'start_date' => 'required',
					'end_date' => 'required',
					'categories.*' => 'required',
					'offer_image => mimes:jpeg,png,jpg,gif,svg|min:100',

				]);

				$attr = [
					'offer_type' => 'Type',
					'discount_value' => 'Discount value',
					'max_amount' => 'Maximum Amount',
					'min_amount' => 'Minimum Amount',
					'start_date' => 'Start Date',
					'end_date' => 'End Date',
					'categories.*' => 'Categories',
					'offer_image' => 'Offer Image',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editOffer', ['id' => $offer->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {

						$imageName = '';
						if ($request->file('offer_image') != null) {
							$image = $request->file('offer_image');
							$imageName = time() . $image->getClientOriginalName();
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/offers'), $imageName);
							if ($offer->image != null) {
								if(File::exists(public_path('uploads/offers/' . $offer->image))){
									unlink(public_path('uploads/offers/' . $offer->image));
								}
							}

							Helper::compress_image(public_path('uploads/offers/' . $imageName), Notify::getBusRuleRef('image_quality'));
							$offer->image = str_replace('.jpeg', '.jpg', $imageName);
						}

						// remove subcategories from the list if parent category is selected.
						$offer_cat = $request->post('categories');
						foreach ($offer_cat as $value) {
							if (isset($this->subcategories[$value])) {
								foreach ($this->subcategories[$value] as $key) {
									unset($offer_cat[array_search($key, $offer_cat)]);
								}
							}
						}
						$offer->category_id = implode(',', $offer_cat);
						$offer->type = $request->post('offer_type');
						$offer->value = $request->post('discount_value');
						$offer->min_amount = $request->post('min_amount');
						$offer->max_amount = ($request->post('max_amount') != null) ? $request->post('max_amount') : null;
						$offer->start_date = date('Y-m-d', strtotime($request->post('start_date')));
						$offer->end_date = date('Y-m-d', strtotime($request->post('end_date')));
						$offer->is_top_offer = $request->post('top_offer');
						$offer->status = trim($request->post('status'));

						if ($offer->save()) {

							//update offer price for product stored in cart
							$cartItems  	   = CartItem::with('product_variation')->get();
							
							foreach($cartItems as $cartItem){
								$product_variation 		= app('App\Http\Controllers\MyController')->checkProductOffer($cartItem->product_variation_id);
								$cartItem->offer_id 		= isset($cartItem->product_variation->offer_id) ? $product_variation->offer_id : null;
								$cartItem->special_price 	= $product_variation->discount_price;
								$cartItem->price 			= $product_variation->price;
								$cartItem->coupon_code_id 	= null;
								$cartItem->coupon_discount  = null;
								$cartItem->save();
							}

							$request->session()->flash('success', 'Offer updated successfully');
							return redirect()->route('offers');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('offers');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('offers');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('offers');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('offers');
		}

	}

	// activate/deactivate offer
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$offer = Offer::find($request->statusid);

			if (isset($offer->id)) {
				$offer->status = $request->status;
				if (isset($request->top_offer)) {
					$offer->is_top_offer = $request->top_offer;
				}
				if ($offer->save()) {

					// check active coupon_codes of similar categories
					$cat = explode(',', $offer->category_id);
					foreach ($cat as $key => $value) {
						$others = Offer::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->where('id', '!=', $offer->id)->get();
						foreach ($others as $k => $v) {
							$cats = str_replace(',' . $value, '', $v->category_id);
							$cats = str_replace($value . ',', '', $cats);
							$v->category_id = $cats;
							$v->save();
						}

					}
					$request->session()->flash('success', 'Offer updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update offer. Please try again later.');
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

	// activate/deactivate offer
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$offer = Offer::find($request->statusid);

			if (isset($offer->id)) {
				$offer->status = $request->status;
				if ($offer->save()) {

					// check active coupon_codes of similar categories
					$cat = explode(',', $offer->category_id);
					foreach ($cat as $key => $value) {
						$others = Offer::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->where('id', '!=', $offer->id)->get();
						foreach ($others as $k => $v) {
							$cats = str_replace(',' . $value, '', $v->category_id);
							$cats = str_replace($value . ',', '', $cats);
							$v->category_id = $cats;
							$v->save();
						}

					}
					echo json_encode(['status' => 1, 'message' => 'Offer updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update offer. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Offer']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Offer']);
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
				$offer = Offer::find($id);

				if (isset($offer->id)) {
					if ($offer->status == 'AC') {
						$offer->status = 'IN';
					} elseif ($offer->status == 'IN') {
						$offer->status = 'AC';

						// check active coupon_codes of similar categories
						$cat = explode(',', $offer->category_id);
						foreach ($cat as $key => $value) {
							$others = Offer::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->where('id', '!=', $offer->id)->get();
							foreach ($others as $k => $v) {
								$cats = str_replace(',' . $value, '', $v->category_id);
								$cats = str_replace($value . ',', '', $cats);
								$v->category_id = $cats;
								$v->save();
							}

						}
					}

					if ($offer->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Offers updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all offers were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
}
