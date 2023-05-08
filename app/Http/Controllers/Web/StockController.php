<?php

namespace App\Http\Controllers\Web;

use App\From;
use App\Category;
use App\Purchase;
use App\Itemadjust;
use App\Stockcount;
use App\CountingUnit;
use App\Exports\CountingUnitStockExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;

class StockController extends Controller
{
    private $excel;


    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    protected function getStockPanel()
    {
    	return view('Stock.stock_panel');
    }

    protected function getStockCountPage(Request $request)
    {

        $role= $request->session()->get('user')->role;
        if($role=='Sale_Person'){

            $item_from= $request->session()->get('user')->from_id;

      }
      else {
        $item_from= $request->session()->get('from');
      }
       $items= From::find($item_from)->items()->with('category')->with('sub_category')->with('counting_units')->with('counting_units.stockcount')->get();

        $shops = From::all();
    	return view('Stock.stock_count_page', compact('items','shops'));
    }

    protected function stockCountExport(Request $request){
        // $role= $request->session()->get('user')->role;
        // if($role=='Sale_Person'){

        //     $item_from= $request->session()->get('user')->from_id;
        // }
        // else {
        //     $item_from= $request->session()->get('from');
        // }
        // $items= From::find($item_from)->items()->with('category')->with('sub_category')->with('counting_units')->with('counting_units.stockcount')->get();
        // $countingunit_stock[] = array( 'Item Name',
        //             'Unit Name' ,
        //             'Unit Code',
        //             'Instock Quantity' ,
        //             'Reorder Quantity');
        // foreach($items as $item){
        //     foreach($item->counting_units as $unit){
        //         $countingunit_stock[] = array(
        //             'Item Name' => $item->item_name,
        //             'Unit Name' => $unit->unit_name,
        //             'Unit Code' => $unit->unit_code,
        //             'Instock Quantity' =>  $unit->stockcount[0]->stock_qty,
        //             'Reorder Quantity' => $unit->reorder_quantity
        //         );
        //     }
        // }
        // Excel::create('Counting Unit Stock',function($excel)use($countingunit_stock){
        //     $excel->setTitle('Counting Unit Stock');
        //     $excel->sheet('Counting Unit Stock',function($sheet)use($countingunit_stock){
        //         $sheet->fromArray($countingunit_stock,null,'A1',false,false);
        //     });
        // })->download('xlsx');
        return $this->excel->download(new CountingUnitStockExport,'counting_unit_stock.xlsx');
    }

    protected function itemadjust(Request $request)
    {

        $role= $request->session()->get('user')->role;
        if($role=='Sale_Person'){

            $item_from= $request->session()->get('user')->from_id;

      }
      else {
        $item_from= $request->session()->get('from');
      }
       $items= From::find($item_from)->items()->with('category')->with('sub_category')->with('counting_units')->with('counting_units.stockcount')->get();

        $shops = From::all();
    	return view('Itemadjust.create_itemadjust', compact('items','shops'));
    }

    protected function getstocklists(Request $request)
    {
        $role= $request->session()->get('user')->role;
        if($role=='Sale_Person'){

            $item_from= $request->session()->get('user')->from_id;

      }
      else {
        $item_from= $request->session()->get('from');
      }
       $items= From::find($item_from)->items()->with('category')->with('sub_category')->with('counting_units')->with('counting_units.stockcount')->get();

        $shops = From::all();
    	return view('Stock.stock_lists', compact('items','shops'));
    }

    protected function getStockPricePage()
    {
        $units = CountingUnit::with('item')->whereNull('deleted_at')->orderBy('item_id', 'asc')->get();

    	return view('Stock.stock_price_page', compact('units'));
    }

    protected function getStockReorderPage(Request $request)
    {
        $role= $request->session()->get('user')->role;
        if($role=='Sale_Person'){

            $item_from= $request->session()->get('user')->from_id;

      }
      else {
        $item_from= $request->session()->get('from');
      }
       $items= From::find($item_from)->items()->with('category')->with('sub_category')->with('counting_units')->with('counting_units.stockcount')->get();

        $shops = From::all();
    	return view('Stock.reorder_page', compact('items','shops'));
    }

    protected function updateStockCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required',
            'unit_code' => 'required',
            'unit_name' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong! Validation Error!');

            return redirect()->back();
        }

        $id = $request->unit_id;

        try {

            $unit = CountingUnit::findOrFail($id);

        } catch (\Exception $e) {

            alert()->error("Counting Unit Not Found!")->persistent("Close!");

            return redirect()->back();

        }

        $unit->unit_code = $request->unit_code;

        $unit->unit_name = $request->unit_name;

        $unit->save();

        alert()->success('Successfully Updated!');

        return redirect()->back();
    }

    protected function updateStockPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required',
            'purchase_price' => 'required',
            'normal_sale_price' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong! Validation Error!');

            return redirect()->back();
        }

        $id = $request->unit_id;

        try {

            $unit = CountingUnit::findOrFail($id);

        } catch (\Exception $e) {

            alert()->error("Counting Unit Not Found!")->persistent("Close!");

            return redirect()->back();

        }

        $unit->purchase_price = $request->purchase_price;

        $unit->normal_sale_price = $request->normal_sale_price;

        $unit->save();

        alert()->success('Successfully Updated!');

        return redirect()->back();
    }
    public function stockUpdateAjax(Request $request)
    {
        $stock = Stockcount::updateOrCreate([
            'counting_unit_id'=> $request->unit_id,
            'from_id'=> $request->shop_id,
        ],
        [
            'stock_qty' => $request->stock_qty,
        ]
        );

        if($stock){
            return response()->json($stock);
        }
        else{
            return response()->json(0);
        }
    }


    public function pharmpriceUpdateAjax(Request $request){
        try {

            $unit = CountingUnit::findOrFail($request->unit_id);

        } catch (\Exception $e) {

            alert()->error("Counting Unit Not Found!")->persistent("Close!");

            return redirect()->back();

        }
        $unit->normal_sale_price = $request->pharm_price;
        $unit->save();
        if($unit){
            return response()->json($unit);
        }
        else{
            return response()->json(0);
        }

    }

    public function patientpriceUpdateAjax(Request $request){
         try {

            $unit = CountingUnit::findOrFail($request->unit_id);

        } catch (\Exception $e) {

            alert()->error("Counting Unit Not Found!")->persistent("Close!");

            return redirect()->back();

        }
        $unit->whole_sale_price = $request->patient_price;
        $unit->save();
        if($unit){
            return response()->json($unit);
        }
        else{
            return response()->json(0);
        }

    }

    public function purchaseUpdateAjax(Request $request)
    {
        $purchase = Purchase::findOrfail($request->purchase_id);

        $diff_qty = $request->new_qty - $request->olderqty;

        $unit = DB::table('counting_unit_purchase')->where('counting_unit_id', $request->unit_id)->where('purchase_id',$request->purchase_id)->update(['quantity' => $request->new_qty]);


        $unit_first = DB::table('counting_unit_purchase')->where('counting_unit_id', $request->unit_id)->where('purchase_id',$request->purchase_id)->first();

        // $unit->quantity = $diff_qty;

        $diff_total= ($diff_qty) * $unit_first->price;

        $purchase_new_total = $purchase->total_price + ($diff_total);

        try {

            $purchase->total_price = $purchase_new_total;
            $purchase->save();

        } catch (Exception $e) {
            return response()->json(0);
        }

        try {
            $update_stock = Stockcount::where('counting_unit_id',$request->unit_id)->where('from_id',1)->first();

        } catch (Exception $e) {
            return response()->json(0);

        }

        $balanced_qty = $update_stock->stock_qty + ($diff_qty);

        $update_stock->stock_qty= $balanced_qty;

        $update_stock->save();

        return response()->json($update_stock);

    }
    public function itemadjustLists(Request $request)
    {
        $item_from= $request->session()->get('from');

        $item_adjusts =  Itemadjust::where('from_id',$item_from)->get();
        // dd($item_adjusts);

        return view('Itemadjust.itemadjust',compact('item_adjusts'));
    }
    public function itemadjustAjax(Request $request)
    {
        $userid = session()->get('user')->id;
        if($request->plusminus == 'plus'){
            $balanced_qty = (int)$request->currentqty + (int) $request->adjust_qty;
            $adjust_qty = $request->adjust_qty;
        }
        elseif($request->plusminus == 'minus'){

            $balanced_qty = (int)$request->currentqty - (int) $request->adjust_qty;
            $adjust_qty = -$request->adjust_qty;


        }
        $stock = Stockcount::updateOrCreate([
            'counting_unit_id'=> $request->unit_id,
            'from_id'=> $request->shop_id,
        ],
        [
            'stock_qty' => $balanced_qty,
        ]
        );

        $item_adjust = Itemadjust::create([
            "counting_unit_id" => $request->unit_id,
            "oldstock_qty" => $request->currentqty,
            "adjust_qty" => $adjust_qty,
            "newstock_qty" => $balanced_qty,
            "from_id" => $request->shop_id,
            "user_id" => $userid
        ]);

        if($stock){
            return response()->json($stock);
        }
        else{
            return response()->json(0);
        }
    }
}
