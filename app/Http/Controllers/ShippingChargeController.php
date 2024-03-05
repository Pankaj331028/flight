<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\ShippingCharge;
use Illuminate\Http\Request;
use Session;

class ShippingChargeController extends Controller
{
    public $shipping;
    public $columns;

    public function __construct()
    {
        $this->shipping = new ShippingCharge;
        $this->columns = [
            "select", "shipping_code", "min_price", "max_price", "shipping_charge", "status", "activate", "action",
        ];
    }

    public function index()
    {
        return view('shipping_charges.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shippingChargesAjax(Request $request)
    {
        if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->shipping->fetchShippingCharges($request, $this->columns);
        $total = $records->count();
        if (isset($request->start)) {
            $charges = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $charges = $records->offset($request->start)->limit($total)->get();
        }
        // echo $total;
        $result = [];
        // print_r($charges);die();
        foreach ($charges as $charge) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $charge->id . '"><i class="input-helper"></i></label></div>';
            $data['shipping_code'] = $charge->shipping_code;
            $data['min_price'] = session()->get('currency') . " " . $charge->min_price;
            $data['max_price'] = session()->get('currency') . " " . $charge->max_price;
            $data['shipping_charge'] = session()->get('currency') . " " . $charge->shipping_charge;
            $data['status'] = ucfirst(config('constants.STATUS.' . $charge->status));

            if (Helper::checkAccess(route('changeStatusShippingCharge'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($charge->status == 'AC' ? ' checked' : '') . ' data-id="' . $charge->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusShippingCharge"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $charge->status));
            }

            $action = '';

            if (Helper::checkAccess(route('editShippingCharge'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editShippingCharge', ['id' => $charge->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            if (Helper::checkAccess(route('deleteShippingCharge'))) {
                $action .= ' &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteShippingCharge" data-id="' . $charge->id . '" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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
    public function create()
    {
        $type = 'add';
        $url = route('addShippingCharge');
        $charge = new ShippingCharge;
        return view('shipping_charges.create', compact('type', 'url', 'charge'));
    }

    /**
     * check for unique name during adding new product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkShippingCharge(Request $request, $id = null)
    {

        if (isset($request->min_price)) {
            $check = ShippingCharge::where(function ($qu) use ($request) {
                $qu->where(function ($q) use ($request) {
                    $q->where('min_price', '<=', $request->min_price);
                    $q->where('max_price', '>=', $request->min_price);
                });

                if (isset($request->max_price) && $request->max_price != null) {
                    $qu->orWhere(function ($que) use ($request) {
                        $que->where(function ($q) use ($request) {
                            $q->where('min_price', '<=', $request->max_price);
                            $q->where('max_price', '>=', $request->max_price);
                        })->orWhere(function ($q) use ($request) {
                            $q->where('min_price', '<=', $request->max_price);
                            $q->where('min_price', '>=', $request->min_price);
                        })->orWhere(function ($q) use ($request) {
                            $q->where('max_price', '<=', $request->max_price);
                            $q->where('max_price', '>=', $request->min_price);
                        });
                    });
                }
            });
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
    public function store(Request $request)
    {
        // dd($request->all());
        $validate = Validator($request->all(), [
            'min_price' => 'required',
            'max_price' => 'required',
            'shipping_charge' => 'required',

        ]);

        $attr = [
            'min_price' => 'Minimum Price',
            'max_price' => 'Maximum Price',
            'shipping_charge' => 'Shipping Charge',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createShippingCharge')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $charge = new ShippingCharge;

                $charge->shipping_code = Helper::generateNumber('c_shipping_charges', 'shipping_code');
                $charge->min_price = $request->post('min_price');
                $charge->max_price = $request->post('max_price');
                $charge->shipping_charge = $request->post('shipping_charge');
                $charge->status = trim($request->post('status'));
                $charge->created_at = date('Y-m-d H:i:s');

                if ($charge->save()) {

                    $request->session()->flash('success', 'Shipping Charge added successfully');
                    return redirect()->route('shippingCharges');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('shippingCharges');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('shippingCharges');
            }

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $charge = ShippingCharge::where('id', $id)->first();
            if (isset($charge->id)) {
                $type = 'edit';
                $url = route('updateShippingCharge', ['id' => $charge->id]);
                return view('shipping_charges.create', compact('charge', 'type', 'url'));

            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('shippingCharges');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('shippingCharges');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $charge = ShippingCharge::where('id', $id)->first();
            if (isset($charge->id)) {
                $validate = Validator($request->all(), [
                    'min_price' => 'required',
                    'max_price' => 'required',
                    'shipping_charge' => 'required',

                ]);

                $attr = [
                    'min_price' => 'Minimum Price',
                    'max_price' => 'Maximum Price',
                    'shipping_charge' => 'Shipping Charge',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editShippingCharge', ['id' => $charge->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $charge->shipping_code = Helper::generateNumber('c_shipping_charges', 'shipping_code');
                        $charge->min_price = $request->post('min_price');
                        $charge->max_price = $request->post('max_price');
                        $charge->shipping_charge = $request->post('shipping_charge');
                        $charge->status = trim($request->post('status'));
                        $charge->created_at = date('Y-m-d H:i:s');

                        if ($charge->save()) {
                            $request->session()->flash('success', 'Shipping Charge updated successfully');
                            return redirect()->route('shippingCharges');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('shippingCharges');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('shippingCharges');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('products');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('products');
        }

    }

    // activate/deactivate shipping charge
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $charge = ShippingCharge::find($request->statusid);

            if (isset($charge->id)) {
                $charge->status = $request->status;

                if ($charge->save()) {
                    $request->session()->flash('success', 'Shipping Charge updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update shipping charge. Please try again later.');
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

    // activate/deactivate shipping charge
    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $charge = ShippingCharge::find($request->statusid);

            if (isset($charge->id)) {
                $charge->status = $request->status;
                if ($charge->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Shipping Charge updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update shipping charge. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid ShippingCharge']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid ShippingCharge']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $charge = ShippingCharge::find($request->deleteid);

            if (isset($charge->id)) {
                $charge->status = 'DL';
                if ($charge->save()) {
                    $request->session()->flash('success', 'Shipping Charge deleted successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to delete shipping charge. Please try again later.');
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
    public function bulkdelete(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $charge = ShippingCharge::find($id);

                if (isset($charge->id)) {
                    $charge->status = 'DL';
                    if ($charge->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Shipping Charges deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all charges were deleted. Please try again later.']);
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
    public function bulkchangeStatus(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $charge = ShippingCharge::find($id);

                if (isset($charge->id)) {
                    if ($charge->status == 'AC') {
                        $charge->status = 'IN';
                    } elseif ($charge->status == 'IN') {
                        $charge->status = 'AC';
                    }

                    if ($charge->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Shipping Charges updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all charges were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
}
