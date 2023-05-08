<?php

namespace App\Exports;

use App\Purchase;
use App\Supplier;
use App\SupplierCreditList;
use App\SupplierPayCredit;
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

class WelldoneHistoryExport implements FromArray,ShouldAutoSize,WithHeadings
{
    use Exportable;

    protected $from_date;
    protected $to_date;
    protected $id;

    public function __construct($from,$to,$id){

        $this->from_date =$from;
        $this->to_date =$to;
        $this->id=$id;

    }


   public function array() :array
    {

        $welldone_credits=[];

        if($this->id == 0){
            $supplier_id= Supplier::get()->pluck('id')->toArray();
        }else{
            $supplier_id= array($this->id);
        }


        $paycredit= SupplierPayCredit::whereIn('supplier_id',$supplier_id)->whereBetween('pay_date',[$this->from_date,$this->to_date])->where('left_amount',0)->get();
        foreach ($paycredit as $paycredits){
            $purchase = Purchase::where('id',$paycredits->purchase_id)->first();
            $paydate = SupplierPayCredit::where('purchase_id',$paycredits->purchase_id)->where('left_amount',0)->first();

            $pay=array(
                'purchase_vou'=> $purchase->purchase_vou,
                'purchase_date'=>$purchase->purchase_date,
                'supplier_name'=>$purchase->supplier_name,
                'total_amount'=>$purchase->total_price,
                // 'credit_amount'=>$purchase->credit_amount,
                'pay_date'=>$paydate->pay_date,
            );
            array_push($welldone_credits,$pay);
         }

        return $welldone_credits;
    }


    public function headings():array{

            return [
                'purchase_voucher',
                'purchase_date',
                'supplier_name',
                'total_amount',
                // 'repayment',
                'repay_date',

        ];

    }


}
