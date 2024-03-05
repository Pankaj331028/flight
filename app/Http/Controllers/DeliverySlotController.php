<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\DeliverySlot;
use Illuminate\Http\Request;
use Session;

class DeliverySlotController extends Controller {
	public $slot;
	public $columns;

	public function __construct() {
		$this->slot = new DeliverySlot;
		$this->columns = [
			"select", "slot_code", "type", "from_time", "to_time", "day", "max_order", "status", "activate", "action",
		];
	}

	public function index() {
		return view('delivery_slots.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function deliverySlotsAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->slot->fetchSlots($request, $this->columns);
		$total = $records->count();
		if (isset($request->start)) {
			$slots = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$slots = $records->offset($request->start)->limit($total)->get();
		}

		$result = [];

		foreach ($slots as $slot) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $slot->id . '"><i class="input-helper"></i></label></div>';
			$data['slot_code'] = $slot->slot_code;
			$data['slot_type'] = ucfirst(config('constants.SLOT_TYPE.' . $slot->type));
			$data['from_time'] = date('h:i A', strtotime($slot->from_time));
			$data['to_time'] = date('h:i A', strtotime($slot->to_time));
			$data['day'] = ($slot->day != null) ? ucfirst(config('constants.WEEK_DAY.' . $slot->day)) : '-';
			$data['order_limit'] = ($slot->max_order != null) ? $slot->max_order : '-';
			$data['status'] = ucfirst(config('constants.STATUS.' . $slot->status));

			if (Helper::checkAccess(route('changeStatusDeliverySlot'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($slot->status == 'AC' ? ' checked' : '') . ' data-id="' . $slot->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusDeliverySlot"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $slot->status));
			}

			$action = '';

			if (Helper::checkAccess(route('editDeliverySlot'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editDeliverySlot', ['id' => $slot->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
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
		$url = route('addDeliverySlot');
		$slot = new DeliverySlot;

		return view('delivery_slots.create', compact('type', 'url', 'slot'));
	}

	/**
	 * check for unique name during adding new product
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkDeliverySlot(Request $request, $id = null) {
		if (isset($request->from_time)) {
			$check = DeliverySlot::where(function ($qu) use ($request) {
				$qu->where('from_time', date('H:i:s', strtotime($request->from_time)))->orWhere(function ($q) use ($request) {
					$q->where('from_time', '<', date('H:i:s', strtotime($request->from_time)));
					$q->where('to_time', '>', date('H:i:s', strtotime($request->from_time)));
				});

				if (isset($request->to_time) && $request->to_time != null) {
					$qu->orWhere(function ($que) use ($request) {
						$que->where('to_time', date('H:i:s', strtotime($request->to_time)))->orWhere(function ($q) use ($request) {
							$q->where('from_time', '<', date('H:i:s', strtotime($request->to_time)));
							$q->where('to_time', '>', date('H:i:s', strtotime($request->to_time)));
						})->orWhere(function ($q) use ($request) {
							$q->where('from_time', '<', date('H:i:s', strtotime($request->to_time)));
							$q->where('from_time', '>', date('H:i:s', strtotime($request->from_time)));
						})->orWhere(function ($q) use ($request) {
							$q->where('to_time', '<', date('H:i:s', strtotime($request->to_time)));
							$q->where('to_time', '>', date('H:i:s', strtotime($request->from_time)));
						});
					});
				}
			});
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
			}
			if (isset($request->type)) {
				$check = $check->where('type', $request->type);
			}
			if (isset($request->day) && $request->type == 'single') {
				$check = $check->where('day', $request->day);
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
		// dd($request->all());
		$validate = Validator($request->all(), [
			'from_time' => 'required',
			'to_time' => 'required',
			'slot_typenew' => 'required',

		]);

		$attr = [
			'from_time' => 'Start Time',
			'to_time' => 'End Time',
			'slot_typenew' => 'Slot Type',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createDeliverySlot')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$slot = new DeliverySlot;

				$slot->slot_code = Helper::generateNumber('c_delivery_slots', 'slot_code');
				$slot->from_time = date('H:i:s', strtotime($request->from_time));
				$slot->to_time = date('H:i:s', strtotime($request->to_time));
				$slot->status = trim($request->post('status'));
				$slot->type = $request->post('slot_typenew');

				if ($slot->type == 'single') {
					$slot->day = $request->post('slot_day');
					$slot->max_order = $request->post('max_order');
				}

				$slot->created_at = date('Y-m-d H:i:s');

				if ($slot->save()) {

					$request->session()->flash('success', 'Delivery Slot added successfully');
					return redirect()->route('deliverySlots');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('deliverySlots');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('deliverySlots');
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
			$slot = DeliverySlot::where('id', $id)->first();
			if (isset($slot->id)) {
				return view('delivery_slots.view', compact('product'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('deliverySlots');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('deliverySlots');
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
			$slot = DeliverySlot::where('id', $id)->first();
			if (isset($slot->id)) {
				$type = 'edit';
				$url = route('updateDeliverySlot', ['id' => $slot->id]);
				return view('delivery_slots.create', compact('slot', 'type', 'url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('deliverySlots');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('deliverySlots');
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
			$slot = DeliverySlot::where('id', $id)->first();
			if (isset($slot->id)) {
				$validate = Validator($request->all(), [
					'from_time' => 'required',
					'to_time' => 'required',
					'slot_typenew' => 'required',

				]);

				$attr = [
					'from_time' => 'Start Time',
					'to_time' => 'End Time',
					'slot_typenew' => 'Slot Type',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editDeliverySlot', ['id' => $slot->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {
						$slot->from_time = date('H:i:s', strtotime($request->from_time));
						$slot->to_time = date('H:i:s', strtotime($request->to_time));
						$slot->status = trim($request->post('status'));
						$slot->type = $request->post('slot_typenew');

						if ($slot->type == 'single') {
							$slot->day = $request->post('slot_day');
							$slot->max_order = $request->post('max_order');
						} else {
							$slot->day = null;
							$slot->max_order = null;
						}

						if ($slot->save()) {

							$request->session()->flash('success', 'Delivery Slot updated successfully');
							return redirect()->route('deliverySlots');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('deliverySlots');
						}

					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('deliverySlots');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('deliverySlots');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('deliverySlots');
		}

	}

	// activate/deactivate slot
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$slot = DeliverySlot::find($request->statusid);

			if (isset($slot->id)) {
				$slot->status = $request->status;

				if ($slot->save()) {
					$request->session()->flash('success', 'Delivery Slot updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update slot. Please try again later.');
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

	// activate/deactivate slot
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$slot = DeliverySlot::find($request->statusid);

			if (isset($slot->id)) {
				$slot->status = $request->status;
				if ($slot->save()) {
					echo json_encode(['status' => 1, 'message' => 'Delivery Slot updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update slot. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid DeliverySlot']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid DeliverySlot']);
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
			$slot = DeliverySlot::find($request->deleteid);

			if (isset($slot->id)) {
				$slot->status = 'DL';
				if ($slot->save()) {
					$request->session()->flash('success', 'Delivery Slot deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to delete slot. Please try again later.');
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
				$slot = DeliverySlot::find($id);

				if (isset($slot->id)) {
					$slot->status = 'DL';
					if ($slot->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Delivery Slots deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all slots were deleted. Please try again later.']);
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
				$slot = DeliverySlot::find($id);

				if (isset($slot->id)) {
					if ($slot->status == 'AC') {
						$slot->status = 'IN';
					} elseif ($slot->status == 'IN') {
						$slot->status = 'AC';
					}

					if ($slot->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Delivery Slots updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all slots were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
}
