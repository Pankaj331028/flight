<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\CouponRange;
use Illuminate\Http\Request;

class CouponRangeController extends Controller {
	public $range;
	public $columns;

	public function __construct() {
		$this->range = new CouponRange;
		$this->columns = [
			"select", "sno", "min_price", "max_price", "value", "status", "activate",
		];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function rangesAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->range->fetchRanges($request, $this->columns);
		$total = $records->count();
		$ranges = $records->offset($request->start)->limit($request->length)->get();
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($ranges as $range) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $range->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $i++;
			$data['min_price'] = $range->min_price;
			$data['max_price'] = $range->max_price;
			$data['value'] = $range->value . '%';
			$data['status'] = ucfirst(config('constants.STATUS.' . $range->status));

			if (Helper::checkAccess(route('changeStatusAjaxRange'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($range->status == 'AC' ? ' checked' : '') . ' data-id="' . $range->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusRange"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $range->status));
			}

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
		]);
		echo $data;

	}

	// activate/deactivate product range
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$var = CouponRange::find($request->statusid);

			if (isset($var->id)) {
				$var->status = $request->status;
				if ($var->save()) {
					echo json_encode(['status' => 1, 'message' => 'Range updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update range. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Range']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Range']);
		}

	}

	// activate/deactivate range
	public function destroy(Request $request) {

		if (isset($request->id) && $request->id != null) {
			$var = CouponRange::find($request->id);

			if (isset($var->id)) {
				$var->status = 'DL';
				if ($var->save()) {
					echo json_encode(['status' => 1, 'message' => 'Range deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete range. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Range']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Range']);
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
				$var = CouponRange::find($id);

				if (isset($var->id)) {
					if ($var->status == 'AC') {
						$var->status = 'IN';
					} elseif ($var->status == 'IN') {
						$var->status = 'AC';
					}

					if ($var->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Ranges updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all ranges were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
}
