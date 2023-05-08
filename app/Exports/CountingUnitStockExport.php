<?php

namespace App\Exports;

use App\CountingUnit;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CountingUnitStockExport implements FromCollection,ShouldAutoSize,WithMapping,WithHeadings
{
    use Exportable;
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CountingUnit::with('item')->get();
        //return [
          // ['William Win','winkoslb2015@gmail.com']    
      // ];
      //return Item::with('category')->with('sub_category')->with('counting_units')->with('counting_units.stockcount')->get()
    }
    
    // public function query(){
    //     return CountingUnit::query()->with('items');
    // }
    
    public function map($counting_unit):array{
        return [
            $counting_unit->item->item_name,
            $counting_unit->unit_code,
            $counting_unit->unit_name,
            $counting_unit->current_quantity,
             $counting_unit->reorder_quantity,
              $counting_unit->normal_sale_price,
               $counting_unit->purchase_price
        ];        
    }
    
    public function headings():array{
        return [
            'Item Name',
            'Unit Code',
            'Unit Name',
            'Instock Quantity',
            'Reorder Quantity',
            'Sale Price',
            'Purchase Price'
        ];
    }
    
    public function registerEvents():array{
        return[
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true    
                    ]
                ]);
            }    
        ];
    }
}