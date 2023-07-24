<?php

namespace App\Http\Controllers\Web;

use App\From;
use App\Item;
use App\User;
use DateTime;
use App\Voucher;
use App\Category;
use App\Customer;
use App\Discount;
use App\Employee;
use App\Stockcount;
use App\Appointment;
use App\Packagetype;
use App\SubCategory;
use App\Wayplanning;
use App\CountingUnit;
use App\DiscountMain;
use App\SalesCustomer;
use App\Deliveryreceive;
use Dotenv\Regex\Success;
use Illuminate\Http\Request;
use App\SaleCustomerCreditlist;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
    {

    public function wayPlaningForm()
    {
        $deliverorder = Deliveryreceive::all();
        return view('Delivery.wayplaning',compact('deliverorder'));
    }

    public function wayPlaningLists()
    {
        $wayplanningLists = Wayplanning::all();
        return view('Delivery.wayplaningList',compact('wayplanningLists'));
    }
    public function deliveryOrderReceiveStore(Request $request)
    {
        $request->validate([
            "customerphone" => "required",
            "pickup" =>  "required",
            "pickupaddress" =>  "required",
            "pick_charges" =>  "required",
            "contactn_at_pickup" =>  "required",
            "contactp_at_pickup" =>  "required",
            "destination_add" =>  "required",
            "destination_township" =>  "required",
            "deliverycharges" =>  "required",
            "nameDestination" =>  "required",
            "contactph" =>  "required",
            "package_type"=>"required",
            "dimension" => "required",
            "pick_delivery"=> "required",
            "qty"=>"required"
		]);

        $qty =explode(',',$request->qty,-1);
        $dimension =explode(',',$request->dimension,-1);
        $pick_delivery =explode(',',$request->pick_delivery,-1);
        $qty =explode(',',$request->qty,-1);
        $price =explode(',',$request->price,-1);

         $deliverorder = Deliveryreceive::create([
            "customer_name"=>$request->customername,
            "customer_phone"=>$request->customerphone,
            "pick_delivery"=>$request->pickup ,
            "pickup_address"=>$request->pickupaddress,
            "pickup_township_id"=>$request->pickuptownship,
            "pickup_charges"=>$request->pick_charges ,
            "contactname_at_pickup"=>$request->contactn_at_pickup,
            "contactphone_at_pickup"=>$request->contactp_at_pickup ,
            "destination_address"=>$request->destination_add,
            "township_id"=>$request->destination_township,
            "delivery_charges"=>$request->deliverycharges,
            "contactname_at_destination"=>$request->nameDestination,
            "contactphone_at_destination"=>$request->contactph,
        ]);

        $count = count($pick_delivery);
        for ($i=0; $i < $count; $i++) {
            $deliverorder->packagelists()->attach($pick_delivery[$i], ['dimension' => $dimension[$i],'pickup_delivery'=> $pick_delivery[$i],'qty'=> $qty[$i],'price'=>$price[$i]]);
        }
        alert()->success('Success!');
        return back();

    }
    public function wayplanningstore(Request $request)
    {
        $request->validate([
            "wayno" => "required",
            "delivery_id" => "required",
            "date" => "required",
            "pickup" => "required",
            "township_id" => "required",
            "start_time" => "required",
            "end_time" => "required",
		]);
        $wayplanning = Wayplanning::create([
            "wayno" => $request->wayno,
            "delivery_id" => $request->delivery_id,
            "date" => $request->date,
            "pick_delivery" => $request->pickup,
            "township_id" => $request->township_id,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
        ]);
        alert()->success("Successfully created");
        return back();
    }
    public function getshopList(Request $request)
    {
        $request->session()->put('ShopOrWh','shop');
    	return view('Admin.shoplists');
    }
    public function SalePage(Request $request,$id)
    {
        $request->session()->put('from',$id);
        $request->session()->put('ShopOrWh','shop');

        $adminpass = User::find(1)->password;
        // dd($adminpass);
        $role= $request->session()->get('user')->role;
        if($role=='Sale_Person'){
            $item_from= $request->session()->get('user')->from_id;
      }
      else {
        $item_from= $request->session()->get('from');
      }
      $warehouses=From::where('id',$item_from)->orWhere('id',3)->orWhere('id',4)->orWhere('id',5)->get();



        $name= $request->session()->get('from');

        $froms=From::find($id);
        $categories=[];
        $items = $froms->items()->with('category')->with('sub_category')->get();

        // foreach ($items as $item) {

        //     if (!isset($result[$item->category->id])){
        //         $result[$item->category->id] = $item->category;
        //     }
        // }
        // $categories = array_values($result);
        $categories = Category::all();


        $employees = Employee::all();

        $date = new DateTime('Asia/Yangon');
        $customers = Customer::all();

        $today_date = strtotime($date->format('d-m-Y H:i'));
        $fItems =Item::with('category')->with('sub_category')->get();
        $salescustomers = SalesCustomer::all();
        $last_voucher = Voucher::get()->last();

        if($last_voucher != null){
        $voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", ($last_voucher->id + 1));
        }else{
         $voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", 1);
        }

        if($id == 2){
            $date = new DateTime('Asia/Yangon');
            $today_date = $date->format('y-m-d');
            $appointments = Appointment::where('date',$today_date)->with('clinic_patient')->get();

            return view('Clinic.appointments',compact('appointments'));
        }else{
            return view('Sale.sale_page',compact('voucher_code','salescustomers','adminpass','fItems','warehouses','items','categories','employees','today_date','customers'));
        }
        // $today_date = $date->format('d-m-Y H:i');
        // dd($today_date);
    }
    public function storetestVoucher(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'item' => 'required',
            'grand' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 0,
            ]);
        }

        $user = session()->get('user');

        $shop_id = session()->get('from');

        if($request->editvoucher != 0 ){
            $units = Voucher::findOrfail($request->editvoucher)->counting_unit;
            foreach($units as $unit){
                $stock = Stockcount::where('counting_unit_id',$unit->id)->where('from_id',$shop_id)->first();
                $balanceQty = $stock->stock_qty + $unit->pivot->quantity;
                $stock->stock_qty = $balanceQty ;
                $stock->save();
            }
            $deleted = DB::table('vouchers')->where('id', $request->editvoucher)->delete();
        }

        try {


        // dd(json_decode(json_encode($request->grand)));


        $date = new DateTime('Asia/Yangon');

        $today_date = $date->format('d-m-Y h:i:s');

        $voucher_date = $date->format('Y-m-d');

        $today_time = $date->format('g:i A');

        $itemss = json_decode($request->item);

        $grand = json_decode($request->grand);

        $total_quantity = $grand->total_qty;

        $total_amount = $grand->sub_total;

        if($grand->vou_discount == 'foc'){
            $discount = 'foc';
            $total_wif_discount = 0;
        }
        else if($grand->vou_discount > 0) {
            $discount = $grand->vou_discount;
            $total_wif_discount = $grand->sub_total - $grand->vou_discount;
        }
        else {
            $discount= 0;
            $total_wif_discount = $grand->sub_total;

        }

        $agent = new \Jenssegers\Agent\Agent;

        // $is_mobile = $agent->isDesktop();

        $is_mobile = $agent->isMobile();
        //    dd('hello');
        $voucher = Voucher::create([
            'sale_by' => $user->id,
            'voucher_code' => $request->voucher_code,
            'total_price' =>  $total_amount,
            'discount' => $discount,
            'total_quantity' => $total_quantity,
            'voucher_date' => $voucher_date,
            'type' => 1,
            'status' => 0,
            'sales_customer_id' => $request->sales_customer_id,
            'sales_customer_name' => $request->sales_customer_name,
            'from_id'=> $shop_id,
            'is_print'=> $is_mobile ? 1 : 0,
            'is_mobile'=> $is_mobile ? 1 : 0,
            'pay' => $request->cus_pay,
            'change' => $request->credit_amount ? 0 : (int)($request->cus_pay)- (int)($total_wif_discount),
        ]);
        // dd($voucher);

         if(isset($request->credit_amount) && $request->credit_amount > 0){
             $sales_customer = SalesCustomer::find($request->sales_customer_id);
             $sales_customer->credit_amount += $request->credit_amount;
             $sales_customer->deleted_at = null;
             $sales_customer->save();

                $salescustomer_credit = SaleCustomerCreditlist::create([
                    'sales_customer_id' => $request->sales_customer_id,
                    "voucher_id" => $voucher->id,
                    "voucher_code" => $voucher->voucher_code,
                    "repaymentdate"=> $request->repaymentDate,
                    "credit_amount"=>$request->credit_amount,

                 ]);
         }
        //  return response()->json("here");
        foreach ($itemss as $item) {

            if($item->discount == 'foc'){
                $item_discount = 'foc';
            }
            else if($item->discount > 0){
                $item_discount = $item->selling_price -  ( (int) $item->discount );
            }
            else {
                $item_discount = 0;
            }
            $voucher->counting_unit()->attach($item->id, ['quantity' => $item->order_qty,'price' => $item->selling_price,'discount'=> $item_discount]);

            $counting_unit = CountingUnit::find($item->id);
            $stock=$counting_unit->stockcount->where('from_id', $shop_id)->first();
            $balance_qty = ($stock->stock_qty - $item->order_qty);

            $stock->stock_qty = $balance_qty;

            $stock->save();

        }
        //   dd($stock);
        $role= $request->session()->get('user')->role;
        if($role=='Sale_Person'){
            $item_from= $request->session()->get('user')->from_id;
        }
        else {
            $item_from= $request->session()->get('from');
        }
        $froms=From::find($item_from);
        $items = $froms->items()->with('category')->with('counting_units')->with("counting_units.stockcount")->with('sub_category')->get();
        // dd($items);
        $last_voucher = Voucher::get()->last();

        $voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", ($last_voucher->id + 1));
        // dd($voucher_code);
        return response()->json([
            'status' => 1,
            'voucher_code' => $voucher_code,
            'items' =>$items,
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 0,
            ]);

        }
    }
    public function getItemA5(Request $request)
    {
        // dd($request->items);
        $items = json_decode(json_encode($request->items));
        // dd($items);
        return response()->json($items);
    }
}
