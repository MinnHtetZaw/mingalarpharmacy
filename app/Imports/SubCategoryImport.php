<?php

namespace App\Imports;

use App\Item;
use App\User;
use App\Stockcount;
use App\CountingUnit;
use App\Category;
use App\SubCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function PHPSTORM_META\elementType;

class ItemsImport implements ToCollection,WithHeadingRow
{
    public function collection(Collection $rows)
    {
       
        foreach($rows as $row){
            if($row->filter()->isNotEmpty()){
                $last_item = Item::get()->last();
                if($last_item){
                    $last_id = $last_item->id;
                }
                else{
                    $last_id = 1;
                }                            
                
			    $sub_category = SubCategory::create([
                    'name' => $row["name"] ?? "Default Name",
			        'subcategory_code'=> $row["code"] ?? "Default Code",
                    'category_id' => $row["cat_id"] ?? 1,
                ]);
            
                        
                $sub_category->save();
			
            }
        }

        return $itemId;
            

    }
}

