<?php

namespace App\Exports;

use App\Model\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function __construct(array $id)
    {
        $this->id = $id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->id[0] != 'all') {
            return Product::leftjoin('product_variations', 'product_variations.product_id', '=', 'products.id')->whereIn('products.id', $this->id)->where('product_variations.status', '!=', 'DL')->select('*', 'product_variations.id as variant_id', 'product_variations.status as variant_status')->get();
        } else {
            return Product::with('category')->leftjoin('product_variations', 'product_variations.product_id', '=', 'products.id')
                ->where('product_variations.status', '!=', 'DL')
                ->select('*', 'product_variations.id as variant_id', 'product_variations.status as variant_status')
                ->get();
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'SubCategory',
            'Name',
            'Description',
            'Status',
            'Quick Grab',
            'Is exclusive',
            // 'Weight',
            'Quantity',
            'Maximum Quantity user can purchase at a time',
        ];
    }

    /**
     * @var Personne with risks
     * @return array
     */
    public function map($product): array
    {
        $data = [
            'id' => $product->id,
            'category' => ($product->category != null) ? $product->category->name : $product->subcategory->parentCat->name,
            'subcategory' => empty($product->subcategory) ? '-' : $product->subcategory->name,
            'name' => $product->name,
            'description' => $product->description,
            'status' => $product->variant_status == 'AC' ? 'Active' : 'Inactive',
            'quick_grab' => $product->quick_grab == '0' ? 'Yes' : 'No',
            'is_exclusive' => $product->exclusive == '1' ? 'Yes' : 'No',
            // 'weight' => $product->weight,
            'qty' => $product->qty,
            'max_qty' => $product->max_qty,
        ];

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = $event->getSheet()->getDelegate()->getHighestRow();
                $protection = $event->getSheet()->getDelegate()->getProtection();
                $protection->setPassword('lockUpdate');
                $event->sheet->getStyle('J2:K' . $rowCount)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $protection->setSheet(true);
            },
        ];
    }
}
