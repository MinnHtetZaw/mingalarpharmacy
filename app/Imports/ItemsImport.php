<?php

namespace App\Imports;

use App\Item;
use App\User;
use Carbon\Carbon;
use App\Stockcount;
use App\CountingUnit;
use Illuminate\Support\Collection;
use function PHPSTORM_META\elementType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ItemsImport implements ToCollection,WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // $date=[];

        // foreach($rows as $row)
        // {
        //     $row['expdate']= Date::excelToDateTimeObject($row['expdate'])->format('Y-m-d');
        //     $date[]=$row['expdate'];
        // }

        foreach($rows as $row){
            if($row->filter()->isNotEmpty()){
                $last_item = Item::get()->last();
                if($last_item){
                    $last_id = $last_item->id;
                }
                else{
                    $last_id = 1;
                }
                $item_code = sprintf("%04s", ($last_id + 1));
                $itemId= new Item([
                    'item_code'=> $row['item_code'] ?? $item_code,
                    'item_name'=> $row['item_name']?? "default item name",
                    'created_by'=> "MGL-001" ?? "null",
                    'photo_path'=> "default.png" ?? "default photo path",
                    'category_id'=> $row['category_id'] ?? "1",
                    'customer_console'=> 0,
                    'unit_name'=>$row['item_unit_name'] ?? "null",
                ]);

                $itemId->save();

                if($itemId != null){
                    $itemId->froms()->attach(1);
                }

                if(!empty($row['expdate']))
                {
                    $row['expdate']= Date::excelToDateTimeObject($row['expdate'])->format('Y-m-d');

                    $exp_date=Carbon::parse($row['expdate']);

                    $diff_day = Carbon::now()->diffInDays($exp_date, false);

                    if($diff_day > 30){
                        $expired_status = 1;
                    }else if($diff_day > 7 && $diff_day <= 30  )
                    {
                        $expired_status=2;
                    }else if($diff_day >0  && $diff_day <=7 ){

                        $expired_status=3;
                    }else if($diff_day <=0){

                        $expired_status=4;
                    }
                }

                if(!empty($row["unit_total"]) )
                {

                    for ($i=1; $i <= intval($row["unit_total"]); $i++) {

                        $countingunit_id  = new CountingUnit([
                            'item_id'=> $itemId->id,
                            'unit_code'=>$row["code_$i"] ?? "default code",
                            'unit_name'=>$row["name_$i"] ?? "Default Name",
                            'expired_date'=>$row['expdate'] ?? null,
                            'expired_status'=> $expired_status ?? 0,
                            'current_quantity'=> $row["instock_qty_$i"] ?? 1,
                            'reorder_quantity'=>$row["reorder_qty_$i"] ?? 1,
                            'normal_sale_price'=>$row["normal_price_$i"] ?? 1,
                            'purchase_price'=>$row["purchase_price_$i"] ?? 1,

                        ]);
                        $countingunit_id->save();

                        if($countingunit_id != null){
                            $stock = Stockcount::updateOrCreate([
                                    'counting_unit_id'=> $countingunit_id->id,
                                    'from_id'=> 1,
                                ],
                                [
                                    'stock_qty' => $row["instock_qty_$i"] ?? 1,
                                ]
                            );
                        }
                    }
                }

            }
        }

        return $itemId;


    }
}

