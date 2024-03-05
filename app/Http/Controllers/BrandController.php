<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Model\Brand;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BrandController extends Controller {
	public $brand;
	public $columns;

	public function __construct() {
		$this->brand = new Brand;
		$this->columns = [
			"select", "brand_code", "name", "status", "activate", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('brands.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function brandAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->brand->fetchBrands($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$brands = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$brands = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		foreach ($brands as $brand) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $brand->id . '"><i class="input-helper"></i></label></div>';
			$data['brand_code'] = $brand->brand_code;
			$data['name'] = $brand->name;
			$data['status'] = ucfirst(config('constants.STATUS.' . $brand->status));
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($brand->status == 'AC' ? ' checked' : '') . ' data-id="' . $brand->id . '" data-code="' . $brand->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusBrand"></div></div>';
			$action = '';
			/*
				if (Helper::checkAccess(route('changeStatusBrand'))) {
				if ($brand->status == 'AC') {
				$action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $brand->status . '" data-code="' . $brand->name . '" data-id="' . $brand->id . '" data-toggle="tooltip" data-placement="bottom" title="Deactivate Brand"><i class="fa fa-lock" aria-hidden="true"></i></a>';
				} else {
				$action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $brand->status . '" data-id="' . $brand->id . '" data-code="' . $brand->name . '" data-toggle="tooltip" data-placement="bottom" title="Activate Brand"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
				}
			*/
			if (Helper::checkAccess(route('editBrand'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editBrand', ['id' => $brand->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			if (Helper::checkAccess(route('viewBrand'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewBrand', ['id' => $brand->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			}
			if (Helper::checkAccess(route('deleteBrand'))) {
				$action .= ' &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteBrand" data-code="' . $brand->name . '" data-id="' . $brand->id . '" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
			}
			$data['action'] = $action;

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

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'add';
		$url = route('addBrand');
		$brand = new Brand;

		return view('brands.create', compact('type', 'url', 'brand'));
	}

	/**
	 * check for unique name during adding new brand
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkBrand(Request $request, $id = null) {
		if (isset($request->brand_name)) {
			$check = Brand::where('name', $request->brand_name);
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
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
			'brand_name' => 'required',

		]);

		$attr = [
			'brand_name' => 'Brand Name',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createBrand')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$brand = new Brand;

				$brand->brand_code = Helper::generateNumber('c_brands', 'brand_code');
				$brand->name = $request->post('brand_name');
				$brand->status = trim($request->post('status'));
				$brand->created_at = date('Y-m-d H:i:s');

				if ($brand->save()) {
					$request->session()->flash('success', 'Brand added successfully');
					return redirect()->route('brands');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('brands');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('brands');
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
			$brand = Brand::where('id', $id)->first();
			if (isset($brand->id)) {
				return view('brands.view', compact('brand'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('brands');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('brands');
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
			$brand = Brand::where('id', $id)->first();
			if (isset($brand->id)) {
				$type = 'edit';
				$url = route('updateBrand', ['id' => $brand->id]);
				return view('brands.create', compact('brand', 'type', 'url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('brands');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('brands');
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
			$brand = Brand::where('id', $id)->first();
			if (isset($brand->id)) {
				$validate = Validator($request->all(), [
					'brand_name' => 'required',

				]);

				$attr = [
					'brand_name' => 'Brand Name',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editBrand', ['id' => $brand->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {

						$brand->name = $request->post('brand_name');
						$brand->status = trim($request->post('status'));
						if ($brand->save()) {
							$request->session()->flash('success', 'Brand updated successfully');
							return redirect()->route('brands');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('brands');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('brands');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('brands');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('brands');
		}

	}

	// activate/deactivate brand
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$brand = Brand::find($request->statusid);

			if (isset($brand->id)) {
				$brand->status = $request->status;
				if ($brand->save()) {
					$request->session()->flash('success', 'Brand updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update brand. Please try again later.');
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

	// activate/deactivate brand
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$brand = Brand::find($request->statusid);

			if (isset($brand->id)) {
				$brand->status = $request->status;
				if ($brand->save()) {
					echo json_encode(['status' => 1, 'message' => 'Brand updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update brand. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Brand']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Brand']);
		}

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$brand = Brand::find($request->deleteid);

			if (isset($brand->id)) {
				$brand->status = 'DL';
				if ($brand->save()) {
					$request->session()->flash('success', 'Brand deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to delete brand. Please try again later.');
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
	/**
	 * Remove multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkdelete(Request $request) {

		if (isset($request->ids) && $request->ids != null) {
			$ids = count($request->ids);
			$count = 0;
			foreach ($request->ids as $id) {
				$brand = Brand::find($id);

				if (isset($brand->id)) {
					$brand->status = 'DL';
					if ($brand->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Brands deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all brands were deleted. Please try again later.']);
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
				$brand = Brand::find($id);

				if (isset($brand->id)) {
					if ($brand->status == 'AC') {
						$brand->status = 'IN';
					} elseif ($brand->status == 'IN') {
						$brand->status = 'AC';
					}

					if ($brand->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Brands updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all brands were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
}
