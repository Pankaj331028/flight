<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\ProductVariation;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public $variation;
    public $columns;

    public function __construct()
    {
        $this->variation = new ProductVariation;
        $this->columns = [
            "select", "sno", "weight", "unit", "qty", "max_qty", "price", "special", "status", "activate",
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function variationsAjax(Request $request)
    {
        if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->variation->fetchVariations($request, $this->columns);
        $total = $records->count();
        $variants = $records->offset($request->start)->limit($request->length)->get();
        // echo $total;
        $result = [];
        $i = 1;
        foreach ($variants as $var) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $var->id . '"><i class="input-helper"></i></label></div>';
            $data['sno'] = $i++;
            $data['weight'] = '';
            $data['unit'] = $var->product_units->unit ?? 0;
            $data['qty'] = $var->qty;
            $data['max_qty'] = $var->max_qty;
            $data['price'] = $var->price;
            $data['special'] = ($var->special_price) ? $var->special_price : '-';
            $data['status'] = ucfirst(config('constants.STATUS.' . $var->status));
            if (Helper::checkAccess(route('changeStatusAjaxVariation'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($var->status == 'AC' ? ' checked' : '') . ' data-id="' . $var->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusVariation"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $var->status));
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

    // activate/deactivate product variation
    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $var = ProductVariation::find($request->statusid);

            if (isset($var->id)) {
                $var->status = $request->status;
                if ($var->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Variation updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update variation. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Variation']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Variation']);
        }

    }

    // activate/deactivate variation
    public function destroy(Request $request)
    {

        if (isset($request->id) && $request->id != null) {
            $var = ProductVariation::find($request->id);

            if (isset($var->id)) {
                $var->status = 'DL';
                if ($var->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Variation deleted successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to delete variation. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Variation']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Variation']);
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
                $var = ProductVariation::find($id);

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
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Variations updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all variations were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
}
