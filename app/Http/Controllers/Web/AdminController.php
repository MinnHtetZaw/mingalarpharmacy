<?php

namespace App\Http\Controllers\Web;

use App\From;
use App\Item;
use App\User;
use DateTime;
use App\Order;
use App\Expense;
use App\Income;
use App\Voucher;
use App\Category;
use App\Customer;
use App\Employee;
use App\Purchase;
use App\Supplier;
use App\PayCredit;
use Carbon\Carbon;
use App\FixedAsset;
use App\Stockcount;
use App\Itemrequest;
use App\CountingUnit;
use App\SalesCustomer;
use App\ShareholderList;
use App\SupplierPayCredit;
use App\Capitaltransaction;
use App\GeneralInformation;
use App\SupplierCreditList;
use App\Doctor;
use App\ClinicPatient;
use App\Appointment;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use App\SaleCustomerCreditlist;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\SaleCustomerList;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
// use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel;
use App\Exports\ExpenseHistoryExport;
use App\Exports\IncomeHistoryExport;
use App\Exports\PayableHistoryExport;
use App\Exports\WelldoneHistoryExport;

class AdminController extends Controller {

    private $excel;


    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    protected function getAdminDashboard(){

	   return view('Admin.admin_panel');
	}

	protected function expenseList(request $request){

	    $expenses = Expense::all();

	    return view('Admin.expense_update', compact('expenses'));
	}

	protected function storeExpense(request $request){

	       $validator = Validator::make($request->all(), [
            "type" => "required",
            "title" => "required",
            "description" => "required",
            "amount" => "required",
            "profit_loss_flag" => "required",

        ]);

        if($validator->fails()){

            alert()->error('အချက်အလက် များ မှားယွင်း နေပါသည်။');

            return redirect()->back();
        }

        $item = Expense::create([
                'type' => $request->type,
                'period' => $request->period,
                'date' => $request->date,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'profit_loss_flag' => $request->profit_loss_flag,
        ]);

        return redirect()->back();
	}

	protected function updateExpense($id, Request $request)
	{
		try {

        	$expense = Expense::findOrFail($id);

   		} catch (\Exception $e) {

        	alert()->error("Expense Not Found!")->persistent("Close!");

            return redirect()->back();

    	}

        $expense->type = $request->type;

        $expense->period = $request->period;

        $expense->date = $request->date;

        $expense->title = $request->title;

        $expense->description = $request->description;

        $expense->amount = $request->amount;

        $expense->profit_loss_flag = $request->profit_loss_flag;

        $expense->save();

        alert()->success('Successfully Updated!');

        return redirect()->route('expenses');
	}

	protected function deleteExpense(Request $request)
	{


        $expense = Expense::find($request->expense_id);

        $expense->delete();

        alert()->success('Successfully Deleted!');

        return redirect()->route('expenses');
	}

	protected function expenseHistoryExport(Request $request,$from,$to){
        return $this->excel->download(new ExpenseHistoryExport($from,$to),'expense_history.xlsx');
    }

    protected function incomeHistoryExport(Request $request){
        return $this->excel->download(new IncomeHistoryExport($request),'income_history.xlsx');
    }

    protected function searchExpenseHistory(Request $request){
        // $validator = $this->validateData($request);
        // if ($this->validateData($request)->fails()) {
        //     alert()->error('Something Wrong!');
        //     return redirect()->back();
        // }


        $expenses = Expense::whereBetween('date',[$request->from, $request->to])->get();


        return response()->json($expenses);
    }

    protected function searchIncomeHistory(Request $request){
        // $validator = $this->validateData($request);
        // if ($this->validateData($request)->fails()) {
        //     alert()->error('Something Wrong!');
        //     return redirect()->back();
        // }


        $incomes = Income::whereBetween('date',[$request->from, $request->to])->get();
        $from =$request->from;
        $to =$request->to;


        // return response()->json($incomes);
        return view('Admin.income', compact('incomes','from','to'));
    }

	protected function incomeList(request $request){

	    $incomes = Income::all();

	    return view('Admin.income', compact('incomes'));
	}

	protected function storeIncome(request $request){

        $validator = Validator::make($request->all(), [
         "type" => "required",
         "title" => "required",
         "description" => "required",
         "amount" => "required",
         "profit_loss_flag" => "required",

     ]);

     if($validator->fails()){

         alert()->error('အချက်အလက် များ မှားယွင်း နေပါသည်။');

         return redirect()->back();
     }

     $item = Income::create([
             'type' => $request->type,
             'period' => $request->period,
             'date' => $request->date,
             'title' => $request->title,
             'description' => $request->description,
             'amount' => $request->amount,
             'profit_loss_flag' => $request->profit_loss_flag,
     ]);

     return redirect()->back();
 }

 protected function updateIncome($id, Request $request)
	{
		try {

        	$income = Income::findOrFail($id);

   		} catch (\Exception $e) {

        	alert()->error("Income Not Found!")->persistent("Close!");

            return redirect()->back();

    	}

        $income->type = $request->type;

        $income->period = $request->period;

        $income->date = $request->date;

        $income->title = $request->title;

        $income->description = $request->description;

        $income->amount = $request->amount;

        $income->profit_loss_flag = $request->profit_loss_flag;

        $income->save();

        alert()->success('Successfully Updated!');

        return redirect()->route('incomes');
	}

	protected function deleteIncome(Request $request)
	{


        $income = Income::find($request->income_id);

        $income->delete();

        alert()->success('Successfully Deleted!');

        return redirect()->route('incomes');
	}

	protected function getReceivablePayable(Request $request){
     $date = new DateTime('Asia/Yangon');
        $current_Date = $date->format('Y-m-d');

        $name = $request->session()->get('user')->name;
    //  $orders = Order::whereBetween('order_date',[$current_Date,$current_Date])->get();

    //  $unpaid_orders = [];
    //  if($orders != null){
    //      foreach($orders as $order){
    //          if($order->advance_pay < $order->est_price){
    //              $unpaid_order = [
    //                 'order_id' =>$order->id,
    //                 'order_code' => $order->order_number,
    //                 'order_date' => $order->order_date,
    //                 'order_status' => $order->status,
    //                 'customer_id' => $order->customer_id,
    //                 'customer_name' => $order->name,
    //                 'customer_phone' => $order->phone,
    //                 'total_amount' => $order->est_price,
    //                 'paid_amount' => $order->advance_pay,
    //             ];
    //             array_push($unpaid_orders,$unpaid_order);
    //          }
    //      }
    //  }

     $purchases = Purchase::whereBetween('purchase_date',[$current_Date,$current_Date])->get();
    $allcreditlist= Purchase::all();
   $allcreditpurchase=[];
     $credit_purchases = [];
     if($purchases != null){
         foreach($purchases as $purchase){

             if($purchase->credit_amount > 0){
                 $remaincredit_amount = 0;
             $paycredit_amount = 0;
             $remain_credits = SupplierCreditList::where('purchase_id',$purchase->id)->get();
             foreach($remain_credits as $remain_credit){
                 $remaincredit_amount += $remain_credit->credit_amount;
             }
             $pay_credits = SupplierPayCredit::where('purchase_id',$purchase->id)->get();
             foreach($pay_credits as $pay_credit){
                 $paycredit_amount += $pay_credit->pay_amount;
             }

            //  $paydate = SupplierPayCredit::where('purchase_id',$purchase->id)->where('paid_status',1)->first();

                 $credit_purchase = [
                    'purchase_id' =>$purchase->id,
                    'purchase_vou' => $purchase->purchase_vou,
                    'purchase_date' => $purchase->purchase_date,
                    'purchase_type' => $purchase->purchase_type,
                    'supplier_id' => $purchase->supplier_id,
                    'supplier_name' => $purchase->supplier_name,
                    'total_amount' => $purchase->total_price,
                    'credit_amount' => $purchase->credit_amount,
                    'paycredit_amount' => $paycredit_amount,
                    'remaincredit_amount' => $remaincredit_amount,

                ];
                array_push($credit_purchases,$credit_purchase);
             }
         }
     }
     if($allcreditlist != null){
        foreach($allcreditlist as $allpurchase){

            if($allpurchase->credit_amount > 0){
                $remaincredit_amount = 0;
            $paycredit_amount = 0;
            $remain_credits = SupplierCreditList::where('purchase_id',$allpurchase->id)->get();
            foreach($remain_credits as $remain_credit){
                $remaincredit_amount += $remain_credit->credit_amount;
            }
            $pay_credits = SupplierPayCredit::where('purchase_id',$allpurchase->id)->get();
            foreach($pay_credits as $pay_credit){
                $paycredit_amount += $pay_credit->pay_amount;
            }

           //  $paydate = SupplierPayCredit::where('purchase_id',$purchase->id)->where('paid_status',1)->first();

                $allcreditpurchases = [
                   'purchase_id' =>$allpurchase->id,
                   'purchase_vou' => $allpurchase->purchase_vou,
                   'purchase_date' => $allpurchase->purchase_date,
                   'purchase_type' => $allpurchase->purchase_type,
                   'supplier_id' => $allpurchase->supplier_id,
                   'supplier_name' => $allpurchase->supplier_name,
                   'total_amount' => $allpurchase->total_price,
                   'credit_amount' => $allpurchase->credit_amount,
                   'paycredit_amount' => $paycredit_amount,
                   'remaincredit_amount' => $remaincredit_amount,

               ];
               array_push($allcreditpurchase,$allcreditpurchases);
            }
        }
    }
     $paycredit= SupplierPayCredit::whereBetween('pay_date',[$current_Date,$current_Date])->where('left_amount',0)->get();


     $pays =[];
     foreach ($paycredit as $paycredits){
        $purchase = Purchase::where('id',$paycredits->purchase_id)->first();
        $paydate = SupplierPayCredit::where('purchase_id',$paycredits->purchase_id)->where('left_amount',0)->first();

        $pay=[
            'purchase_vou'=> $purchase->purchase_vou,
            'purchase_date'=>$purchase->purchase_date,
            'supplier_name'=>$purchase->supplier_name,
            'total_amount'=>$purchase->total_price,
            'credit_amount'=>$purchase->credit_amount,
            'remain_credit'=>$paycredits->left_amount,
            'pay_date'=>$paydate->pay_date,
        ];
        array_push($pays,$pay);
     }

     //$customers = OrderCustomer::all();
     $suppliers = Supplier::all();
     $paypay = SupplierPayCredit::all();

     return view('Admin.receivable_payable',compact('credit_purchases','current_Date','suppliers','name','pays','purchases','paypay','allcreditpurchase'));
 }

 protected function search_welldone(Request $request){

    if($request->value == 0){
        $supplier_id= Supplier::get()->pluck('id')->toArray();
    }else{
        $supplier_id= array($request->value);
    }
    $pays =[];

    $paycredit= SupplierPayCredit::whereIn('supplier_id',$supplier_id)->whereBetween('pay_date',[$request->from,$request->to])->where('left_amount',0)->get();
    foreach ($paycredit as $paycredits){
        $purchase = Purchase::where('id',$paycredits->purchase_id)->first();
        $paydate = SupplierPayCredit::where('purchase_id',$paycredits->purchase_id)->where('left_amount',0)->first();

        $pay=[
            'purchase_vou'=> $purchase->purchase_vou,
            'purchase_date'=>$purchase->purchase_date,
            'supplier_name'=>$purchase->supplier_name,
            'total_amount'=>$purchase->total_price,
            'credit_amount'=>$purchase->credit_amount,
            'remain_credit'=>$paycredits->left_amount,
            'pay_date'=>$paydate->pay_date,
        ];
        array_push($pays,$pay);
     }
     return response()->json($pays);
 }

 protected function welldoneHistoryExport(Request $request,$from,$to,$id){
    return $this->excel->download(new WelldoneHistoryExport($from,$to,$id),'Welldone_history.xlsx');
}

 protected function payableHistoryExport(Request $request,$from,$to,$id){
    return $this->excel->download(new PayableHistoryExport($from,$to,$id),'Outstanding_history.xlsx');
}

    protected function search_payable_bydate(Request $request){


        $credit_purchases=[];
        $pay=[];

        if($request->value==0){
            $supplier_id= Supplier::get()->pluck('id')->toArray();
        }else{
            $supplier_id= array($request->value);
        }



        $purchases = Purchase::whereIn('supplier_id',$supplier_id)->whereBetween('purchase_date', [$request->from,$request->to])->get();
        $paypay = SupplierPayCredit::all();

        if($purchases != null){
            foreach($purchases as $purchase){

                // foreach($paypay as $paypays){


                // if($purchase->id == $paypays->purchase_id){


                if($purchase->credit_amount > 0){
                    $remaincredit_amount = 0;
             $paycredit_amount = 0;
             $remain_credits = SupplierCreditList::where('purchase_id',$purchase->id)->get();
             foreach($remain_credits as $remain_credit){
                 $remaincredit_amount += $remain_credit->credit_amount;
             }
             $pay_credits = SupplierPayCredit::where('purchase_id',$purchase->id)->get();
             foreach($pay_credits as $pay_credit){
                 $paycredit_amount += $pay_credit->pay_amount;
             }
                    if($remaincredit_amount !=0){
                        $credit_purchase = [
                            'purchase_id' =>$purchase->id,
                            'purchase_code' => $purchase->purchase_vou  ,
                            'purchase_date' => $purchase->purchase_date,
                            'purchase_type' => $purchase->purchase_type,
                            'supplier_id' => $purchase->supplier_id,
                            'supplier_name' => $purchase->supplier_name,
                            'total_amount' => $purchase->total_price,
                            'credit_amount' => $purchase->credit_amount,
                            'paycredit_amount' => $paycredit_amount,
                            'remaincredit_amount' => $remaincredit_amount,
                            ];
                            array_push($credit_purchases,$credit_purchase);
                    }
                }
            }
        }
    //     }
    // }



        $paypay = SupplierPayCredit::all();
        foreach ($paypay as $paypays){
            $pays = [
                'pay_id' =>$paypays->id,
                's_id' => $paypays->supplier_id,
                'p_id' => $paypays->purchase_id,
                'pay_amount' => $paypays->pay_amount,
                'left_amount' => $paypays->left_amount,
                'description' => $paypays->description,
                'pay_date' => $paypays->pay_date,
                ];
                array_push($pay,$pays);
        }

     ;

        return response()->json([
            'cpurchase' => $credit_purchases,
            'purchase' => $pay]);
    }

	protected function getEmployeeList(){

        $employee = Employee::all();

        $froms = From::all();
		return view('Admin.employee_list', compact('employee','froms'));
	}


    protected function storeSalesCustomer(Request $request){
            $sales_customer = SalesCustomer::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'credit_amount' => $request->credit_amount,

                ]);
                $last_row= DB::table('sales_customers')->orderBy('id', 'DESC')->first();
              // $last_row=SalesCustomer::last();
              //dd($last_row);
                Session::flash('data',$last_row);




        return response()->json([
                "success" => 1,
                "message" => "Customer is successfully added",
                "last_row"=>$last_row,

            ]);


    }

    public function shopnameEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shopID' => 'required',
            'shopname' =>'required'
        ]);

        if ($validator->fails()) {

            alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        }
        try {
            $from = From::findOrfail($request->shopID);
        } catch (\Exception $e) {
            alert()->error("Cannot Find shop");

            return redirect()->back();
        }

            $from->name = $request->shopname;
            $from->save();

            alert()->success("Successfully Update");

            return redirect()->back();
        dd($request->all());
    }
    protected function getSalesCustomerList(){
        $salescustomer = SalesCustomer::all();
        return response()->json($salescustomer);
    }

    protected function getSalesCustomerWithID(Request $request){

        $salescustomerwID = SalesCustomer::findOrFail($request->customer_id);

        $cust_credit = SaleCustomerCreditlist::where('sales_customer_id',$request->customer_id)->first();

        return response()->json([
            'sale_credit' => $cust_credit,

            'sale_cust' => $salescustomerwID]);
        }
    public function employeeupdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'employee_code'=> 'required'
        ]);

        if ($validator->fails()) {

            alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        }

        try {
            $employee = Employee::findOrfail($request->employee_id);
            $employee->phone = $request->phone;
            $employee->save();

            $employee->user->user_code = $request->employee_code;
            $employee->user->email = $request->email;
            $employee->user->name = $request->name;
            if($request->enable_access){
            $give_access = 1;
            $employee->user->enable_access = $give_access;
            }
            if($request->password){

                $employee->user->password = bcrypt($request->password);
            }
            $employee->user->save();

        } catch (\Exception $e) {

            alert()->error("Fail when updating the employee");

            return redirect()->back();
        }

        alert()->success("Successfully Update");

        return redirect()->back();
    }

    public function purchaseDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        }


        // try {

        $purchase =Purchase::findOrfail($request->purchase_id);

        $purchase_units= $purchase->counting_unit;

        if($purchase_units != null){
        foreach($purchase_units as $unit){

            $current_stock= Stockcount::where("counting_unit_id",$unit->id)->where('from_id',1)->first();

            $balance_qty = $current_stock->stock_qty - $unit->pivot->quantity;
            if($balance_qty <0) {

            alert()->error("Stock ပြန်နုတ်ရန် မလုံလောက်ပါ..");

            return redirect()->back();
        }
            $current_stock->stock_qty = $balance_qty;

            $current_stock->save();

            $counting_units_delete= DB::table('counting_unit_purchase')->where('counting_unit_id', $unit->id)->where('purchase_id',$purchase->id)->delete();




        }
        }
            // $purchase->counting_unit()->delete();

        if($purchase->credit_amount > 0){
            $delete_credit = SupplierCreditList::where('purchase_id',$purchase->id)->first();
            $delete_credit->delete();
        }

        $purchase->delete();
        // } catch (Exception $e) {

            // alert()->error("ဖျက်မရပါ..");

            // return redirect()->back();
        // }

        alert()->success("Successfully Deleted");

        return redirect()->route('purchase_list');

    }
  protected function storeEmployee(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:App\User,email',
            'password' => 'required',
            'phone' => 'required',
            'role' => 'required',
            'from_id' =>'required'
        ]);

        if ($validator->fails()) {

            alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        }

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'role' => $request->role,
                'prohibition_flag' => 1,
                'photo_path' => "user.jpg",
                'from_id'=> $request->from_id
            ]);

            $user->user_code = "SHW-".sprintf('%03s', $user->id);

            $user->save();

            $employee = Employee::create([
                'phone' => $request->phone,
                'user_id' => $user->id,
            ]);

        } catch (\Exception $e) {

            alert()->error('Something Wrong! When Creating Emplyee.');

            return redirect()->back();
        }

        alert()->success('Successfully Added');

        return redirect()->route('employee_list');
    }

    protected function getEmployeeDetails($id){

        try {

            $employee = Employee::findOrFail($id);

        } catch (\Exception $e) {

            alert()->error("Employee Not Found!")->persistent("Close!");

            return redirect()->back();

        }

        return view('Admin.employee_details', compact('employee'));
    }

	protected function getCustomerList(){

        $customer_lists = Customer::all();

        // dd($customer_lists);
		return view('Admin.customer_list', compact('customer_lists'));
    }

    // public function getSalesCustomerCreditList($id){
    //     $sale_customer_lists = SalesCustomer::all();
    //     return view('Sale.sale_customer_lists',compact('sale_customer_lists'));
    // }


    public function show_sale_customer_credit_list(){
        $sale_customer = SalesCustomer::where('credit_amount','<>',0)->get();
        $credit_list = SaleCustomerCreditList::all();
        // dd($credit_list);
        return view('Sale.sale_customer_lists',compact('sale_customer','credit_list'));

    }

    public function show_supplier_credit_lists()
    {

        $supplier_credit_list = Supplier::all();
        return view('Admin.supplier_credit_list',compact('supplier_credit_list'));
    }

    public function add_supplier(Request $request){
        $suppliers = Supplier::all();
        return view('Admin.add_supplier',compact('suppliers'));
    }

    public function store_supplier(Request $request){
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone_number' => 'required',
            ]);

            $suppliers = Supplier::create([
                 'name' => $request->name,
                 'phone_number' => $request->phone_number,
            ]);

        alert()->success('successfully stored Supplier Data');
        return back();
    }

    public function supplier_credit($id)
    {

        $supplier = Supplier::find($id);
        $creditlist = SupplierCreditList::all();
        $credit = SupplierCreditList::where('supplier_id',$id)->get();
       $paypay = SupplierPayCredit::where('supplier_id',$id)->get();
    //    dd($credit);
       return view('Admin.supplier_credit_detail',compact('credit','supplier','paypay','creditlist'));
    }

    public function store_eachPaidSupplier(Request $request)
    {


        $sale_customer = Supplier::find($request->supid);

        $credit_list = SupplierCreditlist::where('purchase_id',$request->purchase_id)->first();


        $pay = $credit_list->credit_amount - $request->payamt;
        $credit_list->credit_amount = $pay;

        $sale_customer->credit_amount = $sale_customer->credit_amount - $request->payamt;
        $sale_customer->save();

        $credit_list->paid_status = 0;
        $credit_list->save();

        if($pay == 0)
        {
            $credit_list->paid_status =1;
            $credit_list->save();
            $pay_credit = SupplierPayCredit::create([
                'supplier_id' => $request->supid,
                'purchase_id' => $request->purchase_id,
                'left_amount' => $pay,
                'description' => $request->dest,
                'voucher_id'=>$request->vou_id,
                'pay_amount' => $request->payamt,
                'pay_date' => $request->paydate,
                'paid_status' => 1,
            ]);
        }
        elseif($pay != 0)
        {
            $credit_list->paid_status =0;
            $credit_list->save();
            $pay_credit = SupplierPayCredit::create([
                'supplier_id' => $request->supid,
                'purchase_id' => $request->purchase_id,
                'left_amount' => $pay,
                'description' => $request->dest,
                'voucher_id'=>$request->vou_id,
                'pay_amount' => $request->payamt,
                'pay_date' => $request->paydate,
                'paid_status' => 0,
            ]);
        }
        // dd($pay);
        if($pay == 0){
            $paycre = SupplierPayCredit::where('purchase_id',$request->purchase_id)->get();

            foreach($paycre as $paycree)
            {
            $paycree->paid_status = 1;

            $paycree->save();
            }
        }

        $supplier = SalesCustomer::find($request->supid);
        $creditlist = SupplierCreditlist::all();
        $credit = SupplierCreditlist::where('supplier_id',$request->supid)->get();
        $paypay = SupplierPayCredit::where('supplier_id',$request->supid)->get();

        return back()->with(compact('paypay','supplier','creditlist','credit'));
        // return back()->with(compact(''))

    }

    public function store_allSupplierPaid(Request $request,$id)
    {
        $SID = Supplier::find($id);

        if($SID->credit_amount == 0){
            $SID->status = 1;
            $SID->save();
        }
        $purchase = SupplierCreditlist::where('supplier_id',$id)->where('paid_status',0)->get();
        $pay_amount = $request->repay;
        $supplier = Supplier::find($id);
        if($supplier->credit_amount == 0)
        {
            $supplier->status = 1;
            $supplier->save();
        }
        $saletotal = $supplier->credit_amount - $pay_amount;
        $supplier->credit_amount = $saletotal;
        $supplier->save();
        $variable = 0;
        foreach($purchase as $purchases)
        {
         $repaycreditvoucher = SupplierPayCredit::where('purchase_id',$purchases->purchase_id)->first();
        $paypay = PayCredit::where('sale_customer_id',$id)->first();
        $last = $purchases->credit_amount - $pay_amount;

        if($last > 0)
        {
            $last = $last;
        }
        else{
            $last = $last * -1;
        }
        if($purchases->credit_amount <= $pay_amount)
        {


            if($repaycreditvoucher == null)
            {
                // dd("hello");
                if($purchases->credit_amount <= $request->repay)
                {
                    $begin_amt = $purchases->credit_amount;
                }
                else{
                    $begin_amt = $pay_amount;
                }
                $purchases->credit_amount = 0;
                $purchases->paid_status = 1;
                $purchases->save();

                    $paycredit = SupplierPayCredit::create([
                        'supplier_id' => $id,
                        'left_amount' => 0,
                        'description' => $request->remark,
                        'purchase_id'=>$purchases->purchase_id,

                        'pay_amount' => $begin_amt,
                        'pay_date' => $request->repaydate,
                        'paid_status' => 1,

                         ]);



            }
            else{
                // dd("hello2");
                if($purchases->credit_amount <= $request->repay)
                {
                    $begin_amout = $purchases->credit_amount;
                }
                else{
                    $begin_amtout = $pay_amount;
                }
                $purchases->credit_amount = 0;
                $purchases->paid_status = 1;
                $purchases->save();
                $paycredit = SupplierPayCredit::create([
                    'supplier_id' => $id,
                    'left_amount' => 0,
                    'description' => $request->remark,
                    'purchase_id'=>$purchases->purchase_id,

                    'pay_amount' => $begin_amout,
                    'pay_date' => $request->repaydate,
                    'paid_status' => 1,

                     ]);
        $change_status = SupplierCreditlist::where('purchase_id',$purchases->purchase_id)->first();
        if($change_status->credit_amount == 0)
        {
            // dd("hello0000");
        $paycredd = SupplierPayCredit::where('purchase_id',$change_status->purchase_id)->get();

        foreach($paycredd as $paycreedd)
        {
        $insertone = 1;
        $paycreedd->paid_status = 1;
            // dd($paycreedd->voucher_status);
        $paycreedd->save();
        // dd($paycreedd->voucher_status);

        }
        }

            }



        $pay_amount = $last;

        }


        else
        {
            // dd($purchases->purchase_id);

                $purchases->credit_amount = $last;
            $purchases->paid_status = 0;
            $purchases->save();


            $paycredit = SupplierPayCredit::create([
                'supplier_id' => $id,
                'left_amount' => $last,
                'description' => $request->remark,
                'purchase_id'=>$purchases->purchase_id,
                'pay_amount' => $pay_amount,
                'pay_date' => $request->repaydate,
                'paid_status' => 0,

        ]);

        // dd("end");


        $pay_amount = 0;
        }


        }
        // end foreach
        // $change_status = SaleCustomerCreditlist::where('voucher_id',$voucher->voucher_id)->first();
        // if($change_status->credit_amount == 0)
        // {

        // $paycredd = PayCredit::where('voucher_id',$change_status->voucher_id)->get();

        // foreach($paycredd as $paycreedd)

        // $paycreedd->voucher_status = 1;

        // $paycreedd->save();


        // }


        return back();
    }

    public function getPurchase_Info(Request $request)
    {

        $credit_list = SupplierCreditList::find($request->credit_list_id);
        // dd($request->credit_list_id);
        $purchase = Purchase::find($credit_list->purchase_id);

        return response()->json(['data'=>$purchase]);
    }

    public function getsell_end_info(Request $request)
    {
        $asset = FixedAsset::find($request->asset_id);
        if($asset != null)
        {
            $has_asset = 1;
        }
        else
        {
            $has_asset = 2;
        }
        // dd($has_asset);
        return response()->json([
            'flag' => $asset->sell_or_end_flag,
            'has_asset' => $has_asset,
            'asset' => $asset,
        ]);
    }

    public function showFixasset(){
        $fixed_asset = FixedAsset::all();
        $nowdate = new DateTime('Asia/Yangon');
        $realdate = $nowdate->format('Y-m-d');
        $fillyear = [];
        $filldate =[];

        foreach($fixed_asset as $fday)
        {
            // dd($realdate."-------".$fday->future_day);
            if($realdate == $fday->future_day)
            {
                array_push($filldate,$fday->id);
            }
        }
        // dd($filldate);
        foreach($fixed_asset as $fyear)
        {
            if($realdate == $fyear->future_date)
            {
                array_push($fillyear,$fyear->id);
            }
        }


// dd($fillyear);
foreach($filldate as $fdate)
{
    $change_date = FixedAsset::find($fdate);
    $change_date->depriciation_total = $change_date->daily_depriciation +  $change_date->depriciation_total;
    $change_date->current_value = $change_date->current_value - $change_date->daily_depriciation;
    $futureDay=date('Y-m-d', strtotime('+1 day', strtotime($change_date->start_date)));
    $change_date->future_day = $futureDay;
    $change_date->save();
}
foreach($fillyear as $fyear)
{
    $change_all = FixedAsset::find($fyear);
    $change_all->depriciation_total = $change_all->yearly_depriciation +  $change_all->depriciation_total;
    $change_all->current_value = $change_all->current_value - $change_all->yearly_depriciation;
    $change_all->used_years +=1;
    $futureDate=date('Y-m-d', strtotime('+1 year', strtotime($change_all->start_date)));
    $change_all->future_date = $futureDate;
    $change_all->save();
}
$done = FixedAsset::all();
// dd($done);
// dd($change_all);
return view('Admin.fixasset',compact('fixed_asset','done'));
    }

    public function show_capitalPanel()
    {
        $fix_arr =[];
        $sale_credit_arr = [];
        $supp_credit_arr = [];
        $fixed = FixedAsset::all();
        $sale_credit = SalesCustomer::all();
        $supplier_credit = Supplier::all();
        foreach($supplier_credit as $sup)
        {
            array_push($supp_credit_arr,$sup->credit_amount);
        }
        $total_sup_credit = array_sum($supp_credit_arr);
        foreach($sale_credit as $sale_credits)
        {
            array_push($sale_credit_arr,$sale_credits->credit_amount);
        }
        $total_sale_credit = array_sum($sale_credit_arr);
        foreach($fixed as $asset)
        {
            array_push($fix_arr,$asset->current_value);
        }
        $current_asset = array_sum($fix_arr);
        // dd($current_asset);
        $general_info = GeneralInformation::all();
        // dd($general_info);
        $nowdate = new DateTime('Asia/Yangon');
        $realdate = $nowdate->format('Y-m-d');

        foreach($general_info as $general)
        {
            // dd($realdate."-----".$general->future_year);
            if($realdate == $general->future_year)
            {
                // dd($general->start_capital);
                $total_capital = ($general->current_capital) + (($general->reinvest)/100);
                $general->current_capital = $total_capital;
                // dd($general->current_capital);
                $fut_year=date('Y-m-d', strtotime('+1 year', strtotime($general->future_year)));
                $general->future_year = $fut_year;
                $general->save();
            }
        }
// dd("s");
        $general_information = GeneralInformation::all();
        $share_holder = ShareholderList::all();
        $transition = Capitaltransaction::all();
        return view('Admin.capital_panel',compact('transition','share_holder','general_information','current_asset','total_sale_credit','total_sup_credit'));
    }

    public function store_capitalInfo(Request $request)
    {
        // dd($request->name[0]);
        // dd($request->all());

            $current_equity =  ($request->start_capital + $request->cashin + $request->currentasset +$request->sale_credit) - ($request->sup_credit);
            // dd($current_equity);
            // dd(count($request->amount));
        if($request->general_id == null)
        {
            $general_info = GeneralInformation::create([
                'bussiness_name' => $request->buss_name,
                'business_type' => $request->buss_type,
                'total_starting_capital' => $request->start_capital,
                'number_shareholder' => $request->sharer,
                'old_holder' =>  $request->sharer,
                'current_capital' => $request->start_capital,
                'current_fixedasset' => $request->currentasset ,
                'current_cash' => $request->cashin,
                'current_equity' => $current_equity,
                'reinvest_percent' => $request->reinvest,
            ]);

            $future_year=date('Y-m-d', strtotime('+1 year', strtotime($general_info->created_at)));
            $general_info->future_year = $future_year;
            $general_info->save();

            // dd($request->name[0]);
            $length = count($request->name);
            // $i = 0;
                for($i = 0;$i<$length;$i++)
                {

                    $shareholder_store = ShareholderList::create([
                        'general_information_id' => $general_info->id,
                        'name' =>  $request->name[$i],
                        'nrc_passport' =>  $request->nrc[$i],
                        'position' => $request->position[$i],
                        'share_percent' =>  $request->amount[$i],
                        'devident_percent' => $request->divid[$i],
                        'capital_amount' => $request->start_capital,
                    ]);
                }


            // dd("stop");
            alert()->success("Successfully Stored Capital's General Information!");
            return back();
        }
        else if($request->general_id != null)
        {

            $general = GeneralInformation::find($request->general_id);
            $future_year=date('Y-m-d', strtotime('+1 year', strtotime($general->created_at)));
            $general->bussiness_name =  $request->buss_name;
            $general->business_type = $request->buss_type;
            $general->total_starting_capital = $request->start_capital;
            $general->number_shareholder = $request->sharer;
            // $general->old_holder = 2;
            $general->current_capital = $request->start_capital;
            $general->current_fixedasset = $request->currentasset;
            $general->current_cash = $request->cashin;
            $general->current_equity = $current_equity;
            $general->reinvest_percent = $request->reinvest;
            $general->future_year = $future_year;
            $general->save();

            $length = count($request->name);

            if($request->number_shareholder == $general->old_holder)
            {
                // dd("equal");
                for($i = 0;$i<$length;$i++)
                {
                    $shareHolder = ShareholderList::where('id',$request->sharer_id[$i])->first();
                    $shareHolder->general_information_id = $request->general_id;
                    $shareHolder->name = $request->name[$i];
                    $shareHolder->nrc_passport =  $request->nrc[$i];
                    $shareHolder->position = $request->position[$i];
                    $shareHolder->share_percent = $request->amount[$i];
                    $shareHolder->devident_percent = $request->divid[$i];
                    $shareHolder->capital_amount = $request->start_capital;
                    $shareHolder->save();


                }
            }
            elseif($request->number_shareholder != $general->old_holder)
            {
                for($k = 0;$k<count($request->name);$k++)
                {

                    $holder = ShareholderList::find($request->sharer_id[$k]);
                    if($holder != null)
                    {
                        $holder->general_information = $request->general_id;
                        $holder->name = $request->name[$k];
                        $holder->nrc_passport = $request->nrc[$k];
                        $holder->position = $request->position[$k];
                        $holder->share_percent = $request->amount[$k];
                        $holder->devident_percent = $request->divid[$k];
                        $holder->capital_amount = $request->start_capital;
                    }
                    else
                    {
                        $shareholder_store = ShareholderList::create([
                            'general_information_id' => $request->general_id,
                            'name' =>  $request->name[$k],
                            'nrc_passport' =>  $request->nrc[$k],
                            'position' => $request->position[$k],
                            'share_percent' =>  $request->amount[$k],
                            'devident_percent' => $request->divid[$k],
                            'capital_amount' => $request->start_capital,
                        ]);
                    }
                }

                // for($k = 0;$k<count($request->name)-1;$k++)
                // {

                //     $holder = ShareholderList::find($request->sharer_id[$k]);
                //     $holder->delete();
                // }
                // for($k = 0;$k<count($request->name);$k++)
                // {

                //     $shareholder_store = ShareholderList::create([
                //         'general_information_id' => $request->general_id,
                //         'name' =>  $request->name[$k],
                //         'nrc_passport' =>  $request->nrc[$k],
                //         'position' => $request->position[$k],
                //         'share_percent' =>  $request->amount[$k],
                //         'devident_percent' => $request->divid[$k],
                //         'capital_amount' => $request->start_capital,
                //     ]);
                // }
            }

            alert()->success("Successfully Updated Capital's General Information!");
            return back();
        }

    }

    public function addasset(){
        return view('Admin.addasset');
    }

    public function storeAsset(Request $request){
        //  dd($request->all());
         if($request->exist_asset == 1){
             $used_year = $request->used_year;
             $total_dep = $request->depriciation_total;
         }
         elseif($request->exist_asset == 2){
            $used_year = 0;
            $total_dep = 0;
        }


            $futureDate=date('Y-m-d', strtotime('+1 year', strtotime($request->start_date)));
            $futureDay=date('Y-m-d', strtotime('+1 day', strtotime($request->start_date)));
            $daily_dep = $request->year_depriciation/365;


        // if($request->sell)

        // dd( $used_year );
        $asset = FixedAsset::create([
            'name' => $request->asset_name ,
            'type' => $request->type ,
            'description' => $request->asset_description  ,
            'initial_purchase_price' => $request->purchase_initial_price ,
            'salvage_value' => $request->salvage_value ,
            'use_life' => $request->use_life ,
            'yearly_depriciation' => $request->year_depriciation ,
            'existing_flag' => $request->exist_asset ,
            'used_years' => $used_year,
            'depriciation_total' => $total_dep ,
            'current_value' => $request->current_value ,
            'start_date' => $request->start_date ,
            'future_date'=> $futureDate,
            'daily_depriciation' => $daily_dep,
            'future_day' => $futureDay
        ]);
        alert()->success('successfully stored Asset Data');
        return redirect()->route('fixasset');
    }

     public function storeSellEnd(Request $request){
        //  dd($request->all());
        // $request->
        $fixed_asset = FixedAsset::find($request->id);
        // dd($fixed_asset);
        // dd($fixed_asset->profit_loss_status);
        if($request->sell_price > $request->current_value){
            $fixed_asset->profit_loss_status = 1;
        }
        elseif($request->sell_price < $request->current_value){
            $fixed_asset->profit_loss_status = 2;
        }
        if($request->exist_asset == 1){
            $fixed_asset->sell_or_end_flag = 1;
        }
        if($request->exist_asset == 2){
            $fixed_asset->sell_or_end_flag = 2;
        }
        if($request->sell_price != null)
        {
            $se_flag = 1;
        }
        else
        {
            $se_flag = 2;
        }
            $fixed_asset->sell_price = $request->sell_price;
            $fixed_asset->sell_date = $request->sell_date;
            $fixed_asset->profit_loss_value = $request->profit_loss;
            $fixed_asset->end_remark = $request->remark;
            $fixed_asset->end_date = $request->end_date;
            $fixed_asset->sell_end_flag = $se_flag;
            $fixed_asset->save();


            return redirect()->route('fixasset');


    }

    public function store_reinvest_info(Request $request){
        // dd($request->all());
        $general = GeneralInformation::find($request->general_id);
        if($request->reinvest_type == 1 && $request->proof == 3)
        {

            $general->current_capital +=$request->reinvest_amount;
            $general->current_cash -=$request->reinvest_amount;
            $general->save();
            $trans = Capitaltransaction::create([
                'type' => 1,
                'amount' => $request->reinvest_amount,
                'date' => $request->reinvest_date,
                'source' => $request->reinvest_type,

            ]);

            // dd($real_amt);
            alert()->success("Successfully Reinvest Cash Transition !!");
            return back();
        }
        elseif($request->reinvest_type == 2 && $request->proof == 3)
        {


            $general->current_capital +=$request->reinvest_amount;

            $general->save();
            $trans = Capitaltransaction::create([
                'type' => 1,
                'amount' => $request->reinvest_amount,
                'date' => $request->reinvest_date,
                'source' => $request->reinvest_type,
                'remark' => $request->reinvest_remark,
            ]);

            // dd($real_amt);
            alert()->success("Successfully Reinvest Other Transition !!");
            return back();
        }




    }

    public function store_withdraw_info(Request $request){
        // dd($request->all());
        $general = GeneralInformation::find($request->general_id);
        if($request->withdraw_type == 1 && $request->proof == 4)
        {

            $general->current_cash +=$request->withdraw_amount;
            $general->current_capital -=$request->withdraw_amount;
            $general->save();
            $trans = Capitaltransaction::create([
                'type' => 2,
                'amount' => $request->withdraw_amount,
                'date' => $request->withdraw_date,
                'source' => $request->withdraw_type,
                'remark' => $request->withdraw_remark,
            ]);
            alert()->success("Successfully Withdraw Cash Transition !!");
            return back();

        }
        elseif($request->withdraw_type == 2 && $request->proof == 4)
        {

            $general->current_capital -=$request->withdraw_amount;
            $general->save();
            $trans = Capitaltransaction::create([
                'type' => 2,
                'amount' => $request->withdraw_amount,
                'date' => $request->withdraw_date,
                'source' => $request->withdraw_type,
                'remark' => $request->withdraw_remark,
            ]);
            alert()->success("Successfully Withdraw Other Transition !!");
            return back();
        }
    }

    // public function history(){
    //     $voucher_id = SaleCustomerCreditlist::
    //     dd($voucher_id);
    //     return view('Sale.voucher_details',compact('voucher_id'));

    // }
           // public function history(){
               // $voucher_id = SaleCustomerLists::select('voucher_id')->get();
               // dd($voucher_id);
            //    $id=$request->all();
            //    dd($id);
               // return view('Sale.voucher_details',compact('voucher_id'));
               // }

    // function index(){
    //     $sale_customer_lists = SalesCustomer::all();
    //     // dd($sale_customer_lists);

    //     return view('Sale.sale_customer_lists',compact('sale_customer_lists'));
    // }
    public function credit($id){

       // $str = json_encode($sale_customer_id);

       //$credit_id = SalesCustomer::
       $salecustomer = SalesCustomer::find($id);
       $creditlist = SaleCustomerCreditlist::all();
       $credit = SaleCustomerCreditlist::where('sales_customer_id',$id)->get();

    //    dd($credit);
      $paypay = PayCredit::where('sale_customer_id',$id)->get();
        return view('Sale.credit_detail',compact('creditlist','credit','salecustomer','paypay'));



    }

    protected function store_eachPaid(Request $request)
    {
        // dd($request->all());
        $sale_customer = SalesCustomer::find($request->scid);

        $credit_list = SaleCustomerCreditlist::where('voucher_id',$request->vou_id)->where('sales_customer_id',$request->scid)->first();

        $pay = $credit_list->credit_amount - $request->payamt;
        $credit_list->credit_amount = $pay;
        $sale_customer->credit_amount = $pay;
        $sale_customer->save();
        $credit_list->paid_status = 0;
        $credit_list->save();
        if($pay == 0)
        {
            $credit_list->paid_status =1;
            $credit_list->save();
            $pay_credit = PayCredit::create([
                'sale_customer_id' => $request->scid,
                'left_amount' => $pay,
                'description' => $request->dest,
                'voucher_id'=>$request->vou_id,
                'pay_amount' => $request->payamt,
                'pay_date' => $request->paydate,
                'paid_status' => 1,
            ]);
        }
        elseif($pay != 0)
        {
            $credit_list->paid_status =0;
            $credit_list->save();
            $pay_credit = PayCredit::create([
                'sale_customer_id' => $request->scid,
                'left_amount' => $pay,
                'description' => $request->dest,
                'voucher_id'=>$request->vou_id,
                'pay_amount' => $request->payamt,
                'pay_date' => $request->paydate,
                'paid_status' => 0,
            ]);
        }
        // dd($pay);
        if($pay == 0){
            $paycre = PayCredit::where('voucher_id',$request->vou_id)->get();

            foreach($paycre as $paycree)
            {
            $paycree->paid_status = 1;

            $paycree->save();
            }
        }

        $salecustomer = SalesCustomer::find($request->scid);
        $creditlist = SaleCustomerCreditlist::all();
        $credit = SaleCustomerCreditlist::where('sales_customer_id',$request->scid)->get();
        $paypay = PayCredit::where('sale_customer_id',$request->scid)->get();

        return back()->with(compact('paypay','salecustomer','creditlist','credit'));
    }

    protected function store_allPaid(Request $request,$id)
    {
        // dd($request->all());
        $SID = SalesCustomer::find($id);

        if($SID->credit_amount == 0){
            $SID->status = 1;
            $SID->save();
        }
        $vouchers = SaleCustomerCreditlist::where('sales_customer_id',$id)->where('paid_status',0)->get();
        $pay_amount = $request->repay;
        $saleCustomer = SalesCustomer::find($id);
        if($saleCustomer->credit_amount == 0)
        {
            $saleCustomer->status = 1;
            $saleCustomer->save();
        }
        $saletotal = $saleCustomer->credit_amount - $pay_amount;
        $saleCustomer->credit_amount = $saletotal;
        $saleCustomer->save();
        $variable = 0;
        foreach($vouchers as $voucher)
        {
         $repaycreditvoucher = PayCredit::where('voucher_id',$voucher->voucher_id)->first();
        $paypay = PayCredit::where('sale_customer_id',$id)->first();
        $last = $voucher->credit_amount - $pay_amount;

        if($last > 0)
        {
            $last = $last;
        }
        else{
            $last = $last * -1;
        }
        if($voucher->credit_amount <= $pay_amount)
        {


            if($repaycreditvoucher == null)
            {
                // dd("hello");
                if($voucher->credit_amount <= $request->repay)
                {
                    $begin_amt = $voucher->credit_amount;
                }
                else{
                    $begin_amt = $pay_amount;
                }
                $voucher->credit_amount = 0;
                $voucher->paid_status = 1;
                $voucher->save();

                    $paycredit = PayCredit::create([
                        'sale_customer_id' => $id,
                        'left_amount' => 0,
                        'description' => $request->remark,
                        'voucher_id'=>$voucher->voucher_id,

                        'pay_amount' => $begin_amt,
                        'pay_date' => $request->repaydate,
                        'paid_status' => 1,

                         ]);



            }
            else{
                // dd("hello2");
                if($voucher->credit_amount <= $request->repay)
                {
                    $begin_amout = $voucher->credit_amount;
                }
                else{
                    $begin_amtout = $pay_amount;
                }
                $voucher->credit_amount = 0;
                $voucher->paid_status = 1;
                $voucher->save();
                $paycredit = PayCredit::create([
                    'sale_customer_id' => $id,
                    'left_amount' => 0,
                    'description' => $request->remark,
                    'voucher_id'=>$voucher->voucher_id,

                    'pay_amount' => $begin_amout,
                    'pay_date' => $request->repaydate,
                    'paid_status' => 1,

                     ]);
        $change_status = SaleCustomerCreditlist::where('voucher_id',$voucher->voucher_id)->first();
        if($change_status->credit_amount == 0)
        {
            // dd("hello0000");
        $paycredd = PayCredit::where('voucher_id',$change_status->voucher_id)->get();

        foreach($paycredd as $paycreedd)
        {
        $insertone = 1;
        $paycreedd->paid_status = 1;
            // dd($paycreedd->voucher_status);
        $paycreedd->save();
        // dd($paycreedd->voucher_status);

        }
        }

            }



        $pay_amount = $last;

        }


        else
        {
            // dd("djfd");

                $voucher->credit_amount = $last;
            $voucher->paid_status = 0;
            $voucher->save();


            $paycredit = PayCredit::create([
                'sale_customer_id' => $id,
                'left_amount' => $last,
                'description' => $request->remark,
                'voucher_id'=>$voucher->voucher_id,
                'pay_amount' => $pay_amount,
                'pay_date' => $request->repaydate,
                'paid_status' => 0,

        ]);




        $pay_amount = 0;
        }


        }
        // end foreach
        // $change_status = SaleCustomerCreditlist::where('voucher_id',$voucher->voucher_id)->first();
        // if($change_status->credit_amount == 0)
        // {

        // $paycredd = PayCredit::where('voucher_id',$change_status->voucher_id)->get();

        // foreach($paycredd as $paycreedd)

        // $paycreedd->voucher_status = 1;

        // $paycreedd->save();


        // }


        return back();
    }

	protected function storeCustomer(Request $request){

		$validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'level' => 'required',
        ]);

        if ($validator->fails()) {

        	alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        }

        $count_cus = count(Customer::all());



        if ($count_cus == 40) {

            alert()->error("Your Customer Count is full!");

            return redirect()->back();

        } else {


                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => \Hash::make($request->password),
                    'role' => "Customer",
                    'prohibition_flag' => 1,
                    'photo_path' => "user.jpg",
                ]);

                $user->user_code = "SHW-CUS-".sprintf('%03s', $user->id);

                $user->save();

                $customer = Customer::create([
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'allow_credit' => $request->allow_credit = "on"?1:0,
                    'customer_level' => $request->level,
                    'user_id' => $user->id,
                ]);



            alert()->success('Successfully Added');

            return redirect()->route('customer_list');
        }
	}

    protected function getCustomerDetails($id){

        try {

            $customer = Customer::findOrFail($id);

        } catch (\Exception $e) {

            alert()->error("Customer Not Found!")->persistent("Close!");

            return redirect()->back();

        }

        $order_lists = Order::where('customer_id', $customer->id)->get();

       return view('Admin.customer_details', compact('customer','order_lists'));
    }

    protected function updateCustomer(Request $request, $id){

        $validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required',
			'phone' => 'required',
			'address' => 'required',
		]);

		if ($validator->fails()) {

			return $this->sendFailResponse("Something Wrong! Validation Error.", "400");
		}

        try {

            $customer = Customer::findOrFail($id);

            $user = User::findOrFail($customer->user_id);

        } catch (\Exception $e) {

            alert()->error("Customer Not Found!")->persistent("Close!");

            return redirect()->back();

        }

        $user->name = $request->name;

        $user->email = $request->email;

        $user->save();

        $customer->phone = $request->phone;

		$customer->address = $request->address;

		$customer->save();

		alert()->success("Successfully Updated Customer!");

		return redirect()->route('customer_list');
    }

    protected function changeCustomerLevel(Request $request){

        try {

            $customer = Customer::findOrFail($request->customer_id);

            $customer->customer_level = $request->level;

            $customer->save();

            alert()->success('Successfully Updated');

            return redirect()->back();

        } catch (\Exception $e) {

            alert()->error("Customer Not Found!")->persistent("Close!");

            return redirect()->back();

        }
    }

    protected function getCustomerInfo(Request $request){

        $customer = Customer::where('id',$request->customer_id)->with('user')->first();

        return response()->json($customer);
    }

    protected function getPurchaseHistory(Request $request){

        $purchase_lists = Purchase::all();

        return view('Purchase.purchase_lists', compact('purchase_lists'));
    }

    protected function createPurchaseHistory(){

        $froms=From::find(1);
        // $items = $froms->items()->with('counting_units')->with("counting_units.stockcount")->get();
        $items = Item::with('counting_units')->with("counting_units.stockcount")->get();
        $supplier = Supplier::all();
        return view('Purchase.create_purchase', compact('items','supplier'));
    }

    protected function getPurchaseHistoryDetails($id){

        try {

            $purchase = Purchase::findOrFail($id);
            $ph = Supplier::select('phone_number')->where('id',$purchase->supplier_id)->first();


        } catch (\Exception $e) {

            alert()->error('Something Wrong! Purchase Cannot be Found.');

            return redirect()->back();
        }

        return view('Purchase.purchase_details', compact('purchase','ph'));

    }

    protected function storePurchaseHistory(Request $request){
        $validator = Validator::make($request->all(), [
            'purchase_vou' => 'required|unique:purchases,purchase_vou',
            'purchase_date' => 'required',
            'supp_name' => 'required',
            'unit' => 'required',
            'price' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        } // Minn Htet ZAw

        $user_code = $request->session()->get('user')->id;

        $unit = $request->unit;

        $price = $request->price;

        $qty = $request->qty;

        $total_qty = 0;

        $total_price = 0;

        $psub_total = 0;

        // foreach($price as $p){
        //     foreach($qty as $q){
        //         $psub_total = $p * $q;
        //         $total_price += $psub_total;
        //     }
        // }
        for($count = 0; $count < count($unit); $count++){
            $psub_total = $price[$count] * $qty[$count];
            $total_price += $psub_total;
        }

        foreach ($qty as $q) {

            $total_qty += $q;
        }
        $supplier = Supplier::find($request->supp_name);
        if($request->pay_method == 1)
        {

        $supplier->credit_amount +=  $request->credit_amount;
        $supplier->save();
        }
        try {

            $purchase = Purchase::create([
                'purchase_vou' => $request->purchase_vou,
                'supplier_name' => $supplier->name,
                'supplier_id' => $request->supp_name,
                'total_quantity' => $total_qty,
                'total_price' => $total_price,
                'purchase_date' => $request->purchase_date,
                'purchase_by' => $user_code,
                'credit_amount' => $request->credit_amount,
            ]);

            if($request->pay_method == 1)
            {

                $supplier_credit = SupplierCreditList::create([
                    'supplier_id' => $request->supp_name,
                    'purchase_id' => $purchase->id,
                    'credit_amount' => $request->credit_amount,
                    'repay_date' => $request->repay_date,
                ]);
            }


            for($count = 0; $count < count($unit); $count++){

                $purchase->counting_unit()->attach($unit[$count], ['quantity' => $qty[$count], 'price' => $price[$count]]);

                $stockcount = Stockcount::where('from_id',1)->where('counting_unit_id',$unit[$count])->first();

                $balance_qty = ($stockcount->stock_qty + $qty[$count]);

                $stockcount->stock_qty = $balance_qty;

                $stockcount->save();

            }

        } catch (\Exception $e) {

            alert()->error('Something Wrong! When Purchase Store.');

            return redirect()->back();
        }

        alert()->success("Success");

        return redirect()->route('purchase_list');
    }

    protected function getTotalSalenAndProfit(Request $request){

        return view('Admin.financial_panel_update');
    }

    // protected function getTotalSaleReport(Request $request){

    //     $type = $request->type;

    //     $total_sales = 0;

    //     $total_profit = 0;

    //     if($type == 1){

    //         $daily = date('Y-m-d', strtotime($request->value));

    //         $voucher_lists = Voucher::whereDate('voucher_date', $daily)->get();

    //     }
    //     elseif($type == 2){

    //         $week_count = $request->value;

    //         $start_month = date('Y-m-d',strtotime('first day of this month'));

    //         if ($week_count == 1) {

    //             $end_date = date('Y-m-d', strtotime("+1 week" , strtotime($start_month)));

    //             $voucher_lists = Voucher::whereBetween('voucher_date', [$start_month, $end_date])->get();

    //         } elseif ($week_count == 2) {

    //             $start_date = date('Y-m-d', strtotime("+1 week" , strtotime($start_month)));

    //             $end_date = date('Y-m-d', strtotime("+2 week" , strtotime($start_month)));

    //             $voucher_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])->get();

    //         } elseif ($week_count == 3) {

    //             $start_date = date('Y-m-d', strtotime("+2 week" , strtotime($start_month)));

    //             $end_date = date('Y-m-d', strtotime("+3 week" , strtotime($start_month)));

    //             $voucher_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])->get();

    //         } else {

    //             $start_date = date('Y-m-d', strtotime("+3 week" , strtotime($start_month)));

    //             $end_date = date('Y-m-d',strtotime('last day of this month'));

    //             $voucher_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])->get();
    //         }

    //     }
    //     else{

    //         $monthly = $request->value;

    //         $voucher_lists = Voucher::whereMonth('voucher_date', $monthly)->get();
    //     }

    //     foreach ($voucher_lists as $lists) {

    //         $total_sales += $lists->total_price;

    //         foreach ($lists->counting_unit as $unit) {

    //             $total_profit += ($unit->pivot->price * $unit->pivot->quantity) - ($unit->purchase_price * $unit->pivot->quantity);
    //         }

    //     }

    //     return response()->json([
    //         "total_sales" => $total_sales,
    //         "total_profit" => $total_profit,
    //         "voucher_lists" => $voucher_lists,
    //     ]);
    // }

    protected function getTotalSaleReport(Request $request){

        $type = $request->type;

        $from_date = $request->from_date;

        $to_date = $request->to_date;

        $total_sales = 0;

        //$total_order = 0;

        $total_profit = 0;

        $other_income = 0;

        $other_expense = 0;

        //$total_purchase = 0;

        //$total_transaction = 0;

        if($type == 1){

            $daily = date('Y-m-d', strtotime($request->value));

            $voucher_lists = Voucher::whereDate('voucher_date', $daily)->get();

            //$order_lists = Order::whereDate('order_date',$daily)->get();

            $other_incomes = Income::whereDate('date',$daily)->orWhere('type', 1)->get();

            foreach($other_incomes as $other){
                if($other->type == 1 && $other->period == 1){
                    $other_income += $other->amount;
                }
                else if($other->type == 1 && $other->period == 2){
                    $other_income += (int)($other->amount/7);
                }
                else if($other->type == 1 && $other->period == 3){
                    $other_income += (int)($other->amount/30);
                }
                else{
                    $other_income += $other->amount;
                }
            }

            $other_expenses = Expense::whereDate('date',$daily)
                                        ->orWhere('type', 1)->get();

            foreach($other_expenses as $other){
                if($other->type == 1 && $other->period == 1){
                    $other_expense += $other->amount;
                }
                else if($other->type == 1 && $other->period == 2){
                    $other_expense += (int)($other->amount/7);
                }
                else if($other->type == 1 && $other->period == 3){
                    $other_expense += (int)($other->amount/30);
                }
                else{
                    $other_expense += $other->amount;
                }
            }


            // $purchase_lists = Purchase::whereDate('purchase_date',$daily)->where('purchase_type',2)->get();

            // foreach($purchase_lists as $purchae){
            //     $total_purchase += $purchae->total_price;
            // }

            // $transaction_lists = Transaction::whereDate('tran_date',$daily)->get();

            // foreach($transaction_lists as $transaction){
            //     $total_transaction += $transaction->pay_amount;
            // }

            $date_fil_lists = Voucher::whereBetween('voucher_date',[$from_date,$to_date])->get();

        }
        elseif($type == 2){

            $week_count = $request->value;

            $start_month = date('Y-m-d',strtotime('first day of this month'));

            if ($week_count == 1) {

                $end_date = date('Y-m-d', strtotime("+1 week" , strtotime($start_month)));

                $voucher_lists = Voucher::whereBetween('voucher_date', [$start_month, $end_date])->get();

                //$order_lists = Order::whereBetween('order_date', [$start_month, $end_date])->get();

                $other_incomes = Income::whereBetween('date', [$start_month, $end_date])->orWhere('type', 1)->get();

                $other_expenses = Expense::whereBetween('date', [$start_month, $end_date])
                                            ->orWhere('type', 1)->get();

                //$purchase_lists = Purchase::whereBetween('purchase_date', [$start_month, $end_date])->where('purchase_type',2)->get();


           // $transaction_lists = Transaction::whereBetween('tran_date', [$start_month, $end_date])->get();



                $date_fil_lists = Voucher::whereBetween('voucher_date', [$start_month, $end_date])
                                        ->whereBetween('voucher_date',[$from_date,$to_date])->get();
                // $date_fil_lists = Voucher::whereBetween('voucher_date', [$from_date,$to_date])->get();

            } elseif ($week_count == 2) {

                $start_date = date('Y-m-d', strtotime("+1 week" , strtotime($start_month)));

                $end_date = date('Y-m-d', strtotime("+2 week" , strtotime($start_month)));

                $voucher_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])->get();

                //$order_lists = Order::whereBetween('order_date', [$start_date, $end_date])->get();

                $other_incomes = Income::whereBetween('date', [$start_date, $end_date])->orWhere('type', 1)->get();

                $other_expenses = Expense::whereBetween('date', [$start_date, $end_date])
                                            ->orWhere('type', 1)->get();

                //$purchase_lists = Purchase::whereBetween('purchase_date', [$start_date, $end_date])->where('purchase_type',2)->get();


            //$transaction_lists = Transaction::whereBetween('tran_date', [$start_date, $end_date])->get();


                $date_fil_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])
                                           ->whereBetween('voucher_date',[$from_date,$to_date])->get();

            } elseif ($week_count == 3) {

                $start_date = date('Y-m-d', strtotime("+2 week" , strtotime($start_month)));

                $end_date = date('Y-m-d', strtotime("+3 week" , strtotime($start_month)));

                $voucher_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])->get();

                 //$order_lists = Order::whereBetween('order_date', [$start_date, $end_date])->get();

                $other_incomes = Income::whereBetween('date', [$start_date, $end_date])->orWhere('type', 1)->get();

                $other_expenses = Expense::whereBetween('date', [$start_date, $end_date])
                                            ->orWhere('type', 1)->get();

                //$purchase_lists = Purchase::whereBetween('purchase_date', [$start_date, $end_date])->where('purchase_type',2)->get();


            //$transaction_lists = Transaction::whereBetween('tran_date', [$start_date, $end_date])->get();


                $date_fil_lists = Voucher::whereBetween('voucher_date', [$start_month, $end_date])
                                           ->whereBetween('voucher_date',[$from_date,$to_date])->get();

            } else {

                $start_date = date('Y-m-d', strtotime("+3 week" , strtotime($start_month)));

                $end_date = date('Y-m-d',strtotime('last day of this month'));

                $voucher_lists = Voucher::whereBetween('voucher_date', [$start_date, $end_date])->get();

                // $order_lists = Order::whereBetween('order_date', [$start_date, $end_date])->get();

                $other_incomes = Income::whereBetween('date', [$start_date, $end_date])->orWhere('type', 1)->get();

                $other_expenses = Expense::whereBetween('date', [$start_date, $end_date])
                                            ->orWhere('type', 1)->get();

                //$purchase_lists = Purchase::whereBetween('purchase_date', [$start_date, $end_date])->where('purchase_type',2)->get();


           // $transaction_lists = Transaction::whereBetween('tran_date', [$start_date, $end_date])->get();


                $date_fil_lists = Voucher::whereBetween('voucher_date', [$start_month, $end_date])
                                           ->whereBetween('voucher_date',[$from_date,$to_date])->get();
            }

            foreach($other_incomes as $other){
                if($other->type == 1 && $other->period == 1){
                    $other_income += $other->amount * 7;
                }
                else if($other->type == 1 && $other->period == 2){
                    $other_income += $other->amount;
                }
                else if($other->type == 1 && $other->period == 3){
                    $other_income += (int)($other->amount/4);
                }
                else{
                    $other_income += $other->amount;
                }
            }

            foreach($other_expenses as $other){
                if($other->type == 1 && $other->period == 1){
                    $other_expense += $other->amount * 7;
                }
                else if($other->type == 1 && $other->period == 2){
                    $other_expense += $other->amount;
                }
                else if($other->type == 1 && $other->period == 3){
                    $other_expense += (int)($other->amount/4);
                }
                else{
                    $other_expense += $other->amount;
                }
            }

            // foreach($purchase_lists as $purchae){
            //     $total_purchase += $purchae->total_price;
            // }


            // foreach($transaction_lists as $transaction){
            //     $total_transaction += $transaction->pay_amount;
            // }

        }
        else{

            $monthly = $request->value;

            $voucher_lists = Voucher::whereMonth('voucher_date', $monthly)->get();

            // $order_lists = Order::whereMonth('order_date', $monthly)->get();

            $other_incomes = Income::whereMonth('date', $monthly)->orWhere('type', 1)->get();

            foreach($other_incomes as $other){
                if($other->type == 1 && $other->period == 1){
                    $other_income += $other->amount * 30;
                }
                else if($other->type == 1 && $other->period == 2){
                    $other_income += $other->amount * 4;
                }
                else if($other->type == 1 && $other->period == 3){
                    $other_income += $other->amount;
                }
                else{
                    $other_income += $other->amount;
                }
            }

            $other_expenses = Expense::whereMonth('date', $monthly)
                                        ->orWhere('type', 1)->get();

            foreach($other_expenses as $other){
                if($other->type == 1 && $other->period == 1){
                    $other_expense += $other->amount * 30;
                }
                else if($other->type == 1 && $other->period == 2){
                    $other_expense += $other->amount * 4;
                }
                else if($other->type == 1 && $other->period == 3){
                    $other_expense += $other->amount;
                }
                else{
                    $other_expense += $other->amount;
                }
            }

            //  $purchase_lists = Purchase::whereMonth('purchase_date', $monthly)->where('purchase_type',2)->get();

            // foreach($purchase_lists as $purchae){
            //     $total_purchase += $purchae->total_price;
            // }

            // $transaction_lists = Transaction::whereMonth('tran_date', $monthly)->get();

            // foreach($transaction_lists as $transaction){
            //     $total_transaction += $transaction->pay_amount;
            // }

            $date_fil_lists = Voucher::whereBetween('voucher_date',[$from_date,$to_date])->get();
        }


        if($from_date == null){
            foreach ($voucher_lists as $lists) {

                $total_sales += $lists->total_price;

                foreach ($lists->counting_unit as $unit) {

                    $total_profit += ($unit->pivot->price * $unit->pivot->quantity) - ($unit->purchase_price * $unit->pivot->quantity);
                }

            }


        }else{
            foreach ($date_fil_lists as $lists) {

                $total_sales += $lists->total_price;

                foreach ($lists->counting_unit as $unit) {

                    $total_profit += ($unit->pivot->price * $unit->pivot->quantity) - ($unit->purchase_price * $unit->pivot->quantity);
                }

            }

        }

        // foreach($order_lists as $order){
        //     $total_order += $order->est_price;
        // }


        return response()->json([
            "total_sales" => $total_sales,
            //"total_order" => $total_order,
            "total_profit" => $total_profit,
           // "total_purchase" => $total_purchase,
           // "total_transaction" => $total_transaction,
            "voucher_lists" => $voucher_lists,
            //"order_lists" => $order_lists,
           // "purchase_lists" => $purchase_lists,
            //"transaction_lists" => $transaction_lists,
            "other_incomes" => $other_income,
            "other_expenses" => $other_expense,
            "date_fil_lists" => $date_fil_lists,
        ]);
    }

    protected function getTotalIncome(Request $request){
        $type = $request->type;
        if($type == 1){

            $daily = date('Y-m-d', strtotime($request->value));

            $income_lists = Income::whereDate('date',$daily)->orWhere('type', 1)->get();

            $expense_lists = Expense::whereDate('date',$daily)
                                    ->orWhere('type', 1)->get();
            $time = 1;
        }
        else if($type == 2){

            $week_count = $request->value;

            $start_month = date('Y-m-d',strtotime('first day of this month'));

            $time = 2;

            if ($week_count == 1) {
                $end_date = date('Y-m-d', strtotime("+1 week" , strtotime($start_month)));

                $income_lists = Income::whereBetween('date', [$start_month, $end_date])->orWhere('type', 1)->get();

                $expense_lists = Expense::whereBetween('date', [$start_month, $end_date])
                                            ->orWhere('type', 1)->get();
            }
            elseif ($week_count == 2) {

                $start_date = date('Y-m-d', strtotime("+1 week" , strtotime($start_month)));

                $end_date = date('Y-m-d', strtotime("+2 week" , strtotime($start_month)));

                $income_lists = Income::whereBetween('date', [$start_date, $end_date])->orWhere('type', 1)->get();

                $expense_lists = Expense::whereBetween('date', [$start_date, $end_date])
                                            ->orWhere('type', 1)->get();
            }
            elseif ($week_count == 3) {

                $start_date = date('Y-m-d', strtotime("+2 week" , strtotime($start_month)));

                $end_date = date('Y-m-d', strtotime("+3 week" , strtotime($start_month)));

                $income_lists = Income::whereBetween('date', [$start_date, $end_date])->orWhere('type', 1)->get();

                $expense_lists = Expense::whereBetween('date', [$start_date, $end_date])
                                            ->orWhere('type', 1)->get();
            } else {

                $start_date = date('Y-m-d', strtotime("+3 week" , strtotime($start_month)));

                $end_date = date('Y-m-d',strtotime('last day of this month'));

                $income_lists = Income::whereBetween('date', [$start_date, $end_date])->orWhere('type', 1)->get();

                $expense_lists = Expense::whereBetween('date', [$start_date, $end_date])
                                            ->orWhere('type', 1)->get();
            }

        }
        else{

            $monthly = $request->value;

            $time = 3;

            $income_lists = Income::whereMonth('date', $monthly)->orWhere('type', 1)->get();

            $expense_lists = Expense::whereMonth('date', $monthly)
                                        ->orWhere('type', 1)->get();
        }

        return response()->json([
            "income_lists" => $income_lists,
            "expense_lists" => $expense_lists,
            "time"  => $time,
        ]);
    }

    protected function changeCustomerPassword(Request $request){

    	$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {

        	return $this->sendFailResponse("Something Wrong! Validation Error.", "400");
        }

        $user = User::find($request->user_id);

        // $current_pw = $request->current_password;

        // if(!\Hash::check($current_pw, $user->password)){

        //     return $this->sendFailResponse("Something Wrong! Password doesn't match.", "400");
        // }

        $has_new_pw = \Hash::make($request->new_password);

        $user->password = $has_new_pw;

        $user->save();

        return response()->json([
                "user_code"=> $user->user_code,
            ]);
    }
    public function getFixedAssets(Request $request)
    {

        return view('Admin.fixedassetlists');
    }

    public function getnewAsset()
    {
        return view('Admin.newasset');
    }
    public function mobileprint(Request $request)
    {
            $mobile_print = Voucher::where('from_id',$request->from_id)->where('is_mobile',1)->where("is_print",1)->with('counting_unit')->with('counting_unit.item')->with('user')->orderBy('id','desc')->first();
            if($mobile_print){
                return response()->json($mobile_print);
            }else{
                return response()->json(null);
            }
    }
    protected function itemrequestlists(Request $request){
       $role= $request->session()->get('user')->role;
       if($role=="Owner"){
        $request_lists = Itemrequest::orderBy('id','desc')->get();
       }
       else{
        $from_id = $request->session()->get('from');
        $request_lists = Itemrequest::where("from_id",$from_id)->orderBy('id','desc')->get();
       }

        return view('Itemrequest.itemrequestlists', compact('request_lists'));
    }
    protected function getRequestHistoryDetails($id){

        try {

            $itemrequest = Itemrequest::findOrFail($id);

            $froms=From::find(1);
            $items = $froms->items()->with('counting_units')->with("counting_units.stockcount")->get();
        } catch (\Exception $e) {

            alert()->error('Something Wrong! Item Request Cannot be Found.');

            return redirect()->back();
        }

        return view('Itemrequest.requestdetail', compact('itemrequest','items'));

    }
    public function create_itemrequest(Request $request)
    {

        $from_id = $request->session()->get('from');
        $froms=From::find($from_id);
        $items = $froms->items()->with('counting_units')->with("counting_units.stockcount")->get();
        return view('Itemrequest.create_itemrequest', compact('items'));

    }
    protected function store_itemrequest(Request $request){

        $validator = Validator::make($request->all(), [
            'itemrequest_date' => 'required',
            'unit' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error("Something Wrong! Validation Error");

            return redirect()->back();
        }

        $user_code = $request->session()->get('user')->id;

        $unit = $request->unit;

        $qty = $request->qty;

        $total_qty = 0;

        foreach ($qty as $q) {

            $total_qty += $q;
        }

        try {
            $itemrequest = itemrequest::create([
                'request_by' => $request->session()->get('user')->id,
                'total_quantity' => $total_qty,
                'date' => $request->itemrequest_date,
                'from_id' => $request->session()->get('from'),
            ]);


            for($count = 0; $count < count($unit); $count++){

                $itemrequest->counting_units()->attach($unit[$count], ['quantity' => $qty[$count]]);

                // $counting_unit = CountingUnit::find($unit[$count]);

                // $balance_qty = ($counting_unit->current_quantity + $qty[$count]);

                // $counting_unit->current_quantity = $balance_qty;

                // $counting_unit->itemrequest_price = $price[$count];

                // $counting_unit->save();

            }

        } catch (\Exception $e) {

            alert()->error('Something Wrong! When itemrequest Store.');

            return redirect()->back();
        }

        alert()->success("Success");

        return redirect()->route('itemrequestlists');
    }
    public function requestitemssend(Request $request)
    {
        $sentqty= $request->sentqty;
        $counting_units= $request->counting_units;
        $itemrequest = Itemrequest::findOrfail($request->itemrequest_id);


        try {

        foreach($itemrequest->counting_units as $unit){
        $key = array_search ($unit->pivot->counting_unit_id, $counting_units);

        $shop_origin = Stockcount::where('counting_unit_id',$unit->pivot->counting_unit_id)->where('from_id',1)->whereNull('deleted_at')->first();

        if($sentqty[$key]<=$shop_origin->stock_qty){
            $itemrequest->counting_units()->updateExistingPivot($unit->pivot->counting_unit_id,['send_quantity'=>$sentqty[$key]]);
            //update origin stock
            // $shop_origin= DB::table('stockcounts')->where('counting_unit_id',$unit->pivot->counting_unit_id)->where('from_id',1)->whereNull('deleted_at')->first();
            // $balance_qty = $shop_origin->stock_qty-$sentqty[$key];


            $balance_qty = $shop_origin->stock_qty-$sentqty[$key];
            $shop_origin->stock_qty = $balance_qty;
            $shop_origin->save();

            //update request shop's stock

            $requestshop= Stockcount::where('counting_unit_id',$unit->pivot->counting_unit_id)->where('from_id',$request->shop_id)->whereNull('deleted_at')->first();
            $balance_qty = $requestshop->stock_qty+$sentqty[$key];
            $requestshop->stock_qty = $balance_qty;
            $requestshop->save();
        }

        }
        } catch (Exception $e) {
            alert()->error("No Instocks");
            return back();
        }


        $itemrequest->status=1;
        $itemrequest->save();
        alert()->success("Successfull send");
        return back();

    }
    public function purchasepriceUpdate(Request $request)
    {
        try{
            $counting_unit = CountingUnit::findOrfail($request->unit_id);
        } catch (\Exception $e) {
            return response()->json(0);
        }
        $counting_unit->update([
            'purchase_price' => $request->purchase_price,
            'normal_sale_price' => $request->normal_price,
            'whole_sale_price' => $request->whole_price,
         ]);

         return response()->json($counting_unit);

    }
    public function execelImport(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx'
         ]);
        //  try{

        $counts = Excel::import(new ItemsImport(),$request->file('select_file'));

        // } catch (\Exception $e) {
        // alert()->error("Something Went Wrong!");
        //  return back();

        // }
        alert()->success("Success");
        return back();
    }
    public function delete_units(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "unit_ids" => "required",
            "multi_delete" => "required",
        ]);

        if($validator->fails()){
            return response()->json(0);
        }

        if($request->multi_delete ==1){
            foreach($request->unit_ids as $unit_id){
            $unit = CountingUnit::find($unit_id);
            $unit->delete();
            }
        }
        else{
            $unit = CountingUnit::find($request->unit_ids);
            $unit->delete();
        }

        $unit->delete();

        return response()->json(1);

    }


    //Clinics
    protected function patientregister()
	{

		$doctors = Doctor::all();
        //    dd($doctors);
		return view('Clinic.patient_register', compact('doctors'));
	}

	public function appointmentStore(Request $request)
	{

		$validator = Validator::make($request->all(), [
            'name' => 'required',
            'fathername' => 'required',
            'age' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'appointmentdoc' => 'required',
            'appointmentclinic' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }

		$date = date("Y-m-d", strtotime($request->date));
		$time = date("h:i", strtotime($request->time));

		$check_booking = Appointment::where('doctor_id', $request->appointmentdoc)
		->whereDate('date', $date)->where('from_clinic',$request->appointmentclinic)
		->get();

		if(!empty($check_booking)){
			$count = count($check_booking) +1;
			$token_number = "TKN-" . sprintf("%03s", $count);

		}else{
			$token_number = "TKN-" . sprintf("%03s", 1);
		}



		$app= ClinicPatient::all();
		if(!empty($app)){
			$app_count = count($app) +1;
			$patient_code = "PTN-" . sprintf("%04s", $app_count);

		}else{
			$patient_code = "PTN-" . sprintf("%04s", 1);
		}

		$patient = ClinicPatient::create([
			'name'=>$request->name,
			'father_name'=>$request->fathername,
			'age'=>$request->age,
			'phone'=>$request->phone,
			'address'=>$request->address,
			'code'=> $patient_code,
			'age_month'=> $request->age_month,
		]);



		$appointment = Appointment::create([
			'doctor_id'=>$request->appointmentdoc,
			'clinic_patient_id' =>$patient->id,
			'from_clinic'=> $request->appointmentclinic,
			'date' => $date,
			'time' => $time,
			'token'=> $token_number
		]);
		alert()->success(' Success !');

		return redirect()->route("appointments",$patient->id);
	}

	public function oldpatientAppointment(Request $request)
	{

		$validator = Validator::make($request->all(), [
            'oldpatientid' => 'required',
            'oldappointmentdoc' => 'required',
            'oldappointmentclinic' => 'required',
            'olddate' => 'required',
            'oldtime' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }

		$date = date("Y-m-d", strtotime($request->olddate));
		$time = date("h:i", strtotime($request->oldtime));

		$check_booking = Appointment::where('doctor_id', $request->oldappointmentdoc)
		->whereDate('date', $date)->where('from_clinic',$request->oldappointmentclinic)
		->get();

		if(!empty($check_booking)){
			$count = count($check_booking) +1;
			$token_number = "TKN-" . sprintf("%03s", $count);

		}else{
			$token_number = "TKN-" . sprintf("%03s", 1);
		}

		$appointment = Appointment::create([
			'doctor_id'=>$request->oldappointmentdoc,
			'clinic_patient_id' =>$request->oldpatientid,
			'from_clinic'=> $request->oldappointmentclinic,
			'date' => $date,
			'time' => $time,
			'token'=> $token_number
		]);
		alert()->success(' Success !');

		return redirect()->route("appointments",$request->oldpatientid);

	}
}
