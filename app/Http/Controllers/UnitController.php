<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Model\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UnitController extends Controller {
	public $unit;
	public $columns;

	public function __construct() {
		$this->unit = new ProductUnit;
		$this->columns = [
			"sno", "unit", "status", "activate", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('units.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function unitsAjax(Request $request) {
		if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column']; 
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->unit->fetchUnits($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$units = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$units = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		foreach ($units as $unit) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $unit->id . '"><i class="input-helper"></i></label></div>';
			$data['unit'] = $unit->fullname . "(" . $unit->unit . ")";
			$data['status'] = ucfirst(config('constants.STATUS.' . $unit->status));
			$action = '';

			if (Helper::checkAccess(route('changeStatusUnit'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($unit->status == 'AC' ? ' checked' : '') . ' data-id="' . $unit->id . '" data-code="' . $unit->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusUnit"></div></div>';

			}else{
				$data['activate'] = ucfirst(config('constants.STATUS.' . $unit->status));
			}

			if (Helper::checkAccess(route('editUnit'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editUnit', ['id' => $unit->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			if (Helper::checkAccess(route('deleteUnit'))) {
				$action .= ' &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteUnit" data-code="' . $unit->unit . '" data-id="' . $unit->id . '" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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
		$url = route('addUnit');
		$unit = new ProductUnit;

		return view('units.create', compact('type', 'url', 'unit'));
	}

	/**
	 * check for unique name during adding new unit
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkUnit(Request $request, $id = null) {
		if (isset($request->unit_name)) {
			$unit = $request->unit_name;
			$names = ($unit[strlen($unit) - 1] == 's') ? [$unit, rtrim($unit, 's')] : [$unit, $unit . 's'];
			$check = ProductUnit::whereIN('unit', $names);
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
			'unit_name' => 'required',
			'unit_fullname' => 'required',

		]);

		$attr = [
			'unit_name' => 'Unit Name',
			'unit_fullname' => 'Unit FullName',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createUnit')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$unit = new ProductUnit;

				$unit->unit = $request->post('unit_name');
				$unit->fullname = $request->post('unit_fullname');
				$unit->status = trim($request->post('status'));
				$unit->created_at = date('Y-m-d H:i:s');

				if ($unit->save()) {
					$request->session()->flash('success', 'Unit added successfully');
					return redirect()->route('units');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('units');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('units');
			}

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
			$unit = ProductUnit::where('id', $id)->first();
			if (isset($unit->id)) {
				if ($this->checkUnitProd($unit->id) <= 0) {
					$type = 'edit';
					$url = route('updateUnit', ['id' => $unit->id]);
					return view('units.create', compact('unit', 'type', 'url'));
				} else {
					$request->session()->flash('error', 'This unit cannot be edited. Products are added using this unit.');
					return redirect()->route('units');
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('units');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('units');
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
			$unit = ProductUnit::where('id', $id)->first();
			if (isset($unit->id)) {
				$validate = Validator($request->all(), [
					'unit_name' => 'required',
					'unit_fullname' => 'required',

				]);

				$attr = [
					'unit_name' => 'Unit',
					'unit_fullname' => 'Unit Fullname',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editUnit', ['id' => $unit->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {

						$unit->unit = $request->post('unit_name');
						$unit->fullname = $request->post('unit_fullname');
						$unit->status = trim($request->post('status'));
						if ($unit->save()) {
							$request->session()->flash('success', 'Unit updated successfully');
							return redirect()->route('units');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('units');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('units');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('units');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('units');
		}

	}

	// activate/deactivate unit
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$unit = ProductUnit::find($request->statusid);

			if (isset($unit->id)) {
				$unit->status = $request->status;
				if ($unit->save()) {
					$request->session()->flash('success', 'Unit updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update unit. Please try again later.');
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

	// activate/deactivate unit
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$unit = ProductUnit::find($request->statusid);

			if (isset($unit->id)) {
				$unit->status = $request->status;
				if ($unit->save()) {
					echo json_encode(['status' => 1, 'message' => 'Unit updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update unit. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Unit']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Unit']);
		}

	}

	//function to check if any product is added using this unit
	public function checkUnitProd($id) {
		$count = ProductUnit::whereHas('product_variations', function ($q) {
			$q->where('status', 'AC');
		})->where('id', $id)->count();

		return $count;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$unit = ProductUnit::find($request->deleteid);

			if (isset($unit->id)) {
				if ($this->checkUnitProd($unit->id) <= 0) {
					$unit->status = 'DL';
					if ($unit->save()) {
						$request->session()->flash('success', 'Unit deleted successfully.');
						return redirect()->back();
					} else {
						$request->session()->flash('error', 'Unable to delete unit. Please try again later.');
						return redirect()->back();
					}
				} else {
					$request->session()->flash('error', 'This unit cannot be edited. Products are added using this unit.');
					return redirect()->route('units');
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
				$unit = ProductUnit::find($id);

				if (isset($unit->id)) {
					if ($this->checkUnitProd($unit->id) <= 0) {
						$unit->status = 'DL';
						if ($unit->save()) {
							$count++;
						}
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Units deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all units were deleted. Products are added using those units.']);
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
				$unit = ProductUnit::find($id);

				if (isset($unit->id)) {
					if ($unit->status == 'AC') {
						$unit->status = 'IN';
					} elseif ($unit->status == 'IN') {
						$unit->status = 'AC';
					}

					if ($unit->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Units updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all units were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
