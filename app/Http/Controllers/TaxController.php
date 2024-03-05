<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Model\Tax;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TaxController extends Controller {
	public $tax;
	public $columns;

	public function __construct() {
		$this->tax = new Tax;
		$this->columns = [
			"select", "name", "value", "status", "activate", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('taxes.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function taxesAjax(Request $request) {
		$request->search = $request->search['value'];
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->tax->fetchTaxes($request, $this->columns);
		if (isset($request->start)) {
			$taxes = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$taxes = $records->offset($request->start)->limit($records->count())->get();
		} // echo $total;
		$result = [];

		$total = count($taxes);

		foreach ($taxes as $tax) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $tax->id . '"><i class="input-helper"></i></label></div>';
			$data['name'] = $tax->name;
			$data['value'] = $tax->value;
			$data['status'] = ucfirst(config('constants.STATUS.' . $tax->status));

			if (Helper::checkAccess(route('changeStatusTax'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($tax->status == 'AC' ? ' checked' : '') . ' data-id="' . $tax->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusTax"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $tax->status));
			}

			$action = '';
			if (Helper::checkAccess(route('editTax'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editTax', ['id' => $tax->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
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

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'add';
		$url = route('addTax');
		$tax = new Tax;

		return view('taxes.create', compact('type', 'url', 'tax'));
	}

	/**
	 * check for unique tax
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkTax(Request $request, $id = null) {
		if (isset($request->tax_name)) {
			$check = Tax::where('name', $request->tax_name);
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
			}
			if (isset($request->tax_value) && $request->tax_value != null) {
				$check = $check->where('value', $request->tax_value);
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
			'tax_name' => 'required',
			'tax_value' => 'required',

		]);

		$attr = [
			'tax_name' => 'Name',
			'tax_value' => 'Value',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createTax')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$tax = new Tax;
				$tax->name = $request->post('tax_name');
				$tax->value = $request->post('tax_value');
				$tax->status = trim($request->post('status'));
				$tax->created_at = date('Y-m-d H:i:s');

				if ($tax->save()) {

					$request->session()->flash('success', 'Tax added successfully');
					return redirect()->route('taxes');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('taxes');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('taxes');
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
			$tax = Tax::where('id', $id)->first();
			if (isset($tax->id)) {
				$type = 'edit';
				$url = route('updateTax', ['id' => $tax->id]);

				return view('taxes.create', compact('tax', 'type', 'url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('taxes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('taxes');
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
			$tax = Tax::where('id', $id)->first();
			if (isset($tax->id)) {

				$validate = Validator($request->all(), [
					'tax_name' => 'required',
					'tax_value' => 'required',

				]);

				$attr = [
					'tax_name' => 'Name',
					'tax_value' => 'Value',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('createTax')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						$tax->name = $request->post('tax_name');
						$tax->value = $request->post('tax_value');
						$tax->status = trim($request->post('status'));

						if ($tax->save()) {
							$request->session()->flash('success', 'Tax updated successfully');
							return redirect()->route('taxes');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('taxes');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('taxes');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('taxes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('taxes');
		}

	}

	// activate/deactivate tax
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$tax = Tax::find($request->statusid);

			if (isset($tax->id)) {
				$tax->status = $request->status;
				/*if (isset($request->featured)) {
					$tax->featured = $request->featured;
				}*/
				if ($tax->save()) {
					$request->session()->flash('success', 'Tax updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update tax. Please try again later.');
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

	// activate/deactivate tax
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$tax = Tax::find($request->statusid);

			if (isset($tax->id)) {
				$tax->status = $request->status;
				if ($tax->save()) {
					echo json_encode(['status' => 1, 'message' => 'Tax updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update tax. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Tax']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Tax']);
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
			$tax = Tax::find($request->deleteid);

			if (isset($tax->id)) {
				$tax->status = 'DL';
				if ($tax->save()) {
					$request->session()->flash('success', 'Tax deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to delete tax. Please try again later.');
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
				$tax = Tax::find($id);

				if (isset($tax->id)) {
					$tax->status = 'DL';
					if ($tax->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Tax deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all tax were deleted. Please try again later.']);
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
				$tax = Tax::find($id);

				if (isset($tax->id)) {
					if ($tax->status == 'AC') {
						$tax->status = 'IN';
					} elseif ($tax->status == 'IN') {
						$tax->status = 'AC';
					}

					if ($tax->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Tax updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all taxes were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
