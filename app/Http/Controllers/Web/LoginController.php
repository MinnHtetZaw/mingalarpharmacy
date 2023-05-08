<?php

namespace App\Http\Controllers\Web;


use App\From;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Voucher;
use Session;
use Auth;
use DateTime;

class LoginController extends Controller
{
    public function index(Request $request) {

        if (Session::has('user')) {

            if($request->session()->get('user')->role == "Owner"){
                $froms = From::all();
                return view('Admin.shoplists',compact('froms'));

            }elseif ($request->session()->get('user')->role == "Sale") {
            $from_id= $request->session()->get('user')->from_id;
            $request->session()->put('from',$from_id);
            return redirect()->route('sale_page');

            }elseif ($request->session()->get('user')->role == "Doctor" || $request->session()->get('user')->role == "Nurse") {
                $from_id= $request->session()->get('user')->from_id;
                $request->session()->put('from',$from_id);
                return redirect()->route('today.appointments');
            }elseif ($request->session()->get('user')->role == "Employee") {
                $from_id= $request->session()->get('user')->from_id;
                $request->session()->put('from',$from_id);
                return redirect()->route('patientregister');
            }elseif ($request->session()->get('user')->role == "Inventory_Stock") {
            $from_id= $request->session()->get('user')->from_id;
            $request->session()->put('from',$from_id);
            return redirect()->route('inven_dashboard');
            }elseif ($request->session()->get('user')->role == "Stock") {
            $from_id= $request->session()->get('user')->from_id;
            $request->session()->put('from',$from_id);
            return redirect()->route('stock_dashboard');
            }elseif ($request->session()->get('user')->role == "Finance") {
            $from_id= $request->session()->get('user')->from_id;
            $request->session()->put('from',$from_id);
            return redirect()->route('financial');
            }
        }
        else{

            return view('login');

        }

	}

    public function loginProcess(Request $request){

        $validator = Validator::make($request->all(), [
            'user_code' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong! Validation Error!');

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->where('user_code', $request->user_code)->first();

        if (!isset($user)) {

            alert()->error('Wrong User Code Or Email!');

            return redirect()->back();
        }
        elseif (!\Hash::check($request->password, $user->password)) {

            alert()->error('Wrong Password!');

            return redirect()->back();
        }

        session()->put('user', $user);

        $today_date = (new DateTime)->format('Y-m-d');

        $last_date = date('Y-m-d', strtotime('-1day', strtotime($today_date)));

        $today_sale = 0;

        $last_day_sale = 0;

        $today_vouchers = Voucher::where('voucher_date', $today_date)->get();

        $last_date_vouchers = Voucher::where('voucher_date', $last_date)->get();

        foreach ($today_vouchers as $tdy) {

            $today_sale += $tdy->total_price;
        }

        foreach ($last_date_vouchers as $last) {

            $last_day_sale += $last->total_price;
        }

        session()->put('today_sale', $today_sale);

        session()->put('last_day_sale', $last_day_sale);

        return redirect()->route('index');

    }

    public function logoutProcess(Request $request){

        Session::flush();

        alert()->success("Successfully Logout");

        return redirect()->route('index');

    }

    public function getChangePasswordPage(){

        return view('change_pw');
    }

    protected function updatePassword(Request $request){

        $validator = Validator::make($request->all(), [
             'current_pw' => 'required',
             'new_pw' => 'required|confirmed|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/'

        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong!');
            return redirect()->back()->withErrors($validator);

        }

        $user = $request->session()->get('user');

        $current_pw = $request->current_pw;

        if(!\Hash::check($current_pw, $user->password)){

            alert()->info("Wrong Current Password!");

            return redirect()->back();
        }

        $has_new_pw = \Hash::make($request->new_pw);

        $user->password = $has_new_pw;

        $user->save();

        alert()->success('Successfully Changed!');

        return redirect()->route('Admin.shoplists');
    }
}
