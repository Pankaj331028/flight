<?php

namespace App\Imports;

use App\Model\ProductVariation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Request;
use Session;

class ProductUpdate implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        if ($rows->count()) {
            $count = 0;
            $error = 0;
            if (isset($rows[0])) {
                foreach ($rows as $key => $value) {
                    if (!empty($value['id']) && !empty($value['category']) && !empty($value['subcategory']) && !empty($value['name']) && !empty($value['description']) && !empty($value['status']) && !empty($value['quick_grab']) && !empty($value['is_exclusive']) && !empty($value['quantity']) && !empty($value['maximum_quantity_user_can_purchase_at_a_time'])) {
                        $variant = ProductVariation::find($value['id']);
                        $variant->qty = $value['quantity'];
                        $variant->max_qty = $value['maximum_quantity_user_can_purchase_at_a_time'];
                        if ($variant->qty <= 0 || $variant->max_qty <= 0) {
                            Request::session()->flash('error', 'Unable to import value should be greather than 0');
                            return redirect()->route('exportView');
                        } else {
                            $variant->save();
                        }

                    } else {
                        $error++;
                    }
                }
                if ($error <= 0) {
                    Request::session()->flash('success', 'Data imported successfully');
                    return redirect()->route('exportView');
                } else {
                    Request::session()->flash('error', 'Unable to import complete data. Please verify and import again.');
                    return redirect()->route('exportView');
                }

            } else {
                Request::session()->flash('error', 'Blank data or unable to read data from file.');
                return redirect()->route('exportView');
            }
        } else {
            Request::session()->flash('error', 'Blank data or unable to read data from file.');
            return redirect()->route('exportView');
        }
    }
}
