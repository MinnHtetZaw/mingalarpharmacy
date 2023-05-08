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

class PayableHistoryExport implements FromArray,ShouldAutoSize,WithHeadings
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

        $outstanding_credits=[];

        if($this->id==0){
            $supplier_id= Supplier::get()->pluck('id')->toArray();
        }else{
            $supplier_id= array($this->id);
        }

        $purchases = Purchase::whereIn('supplier_id',$supplier_id)->whereBetween('purchase_date', [$this->from_date,$this->to_date])->get();
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
                    if($remaincredit_amount !=0){
                        $outstanding_credit = array (

                            'purchase_vou' => $purchase->purchase_vou,
                            'purchase_date' => $purchase->purchase_date,
                            'supplier_name' => $purchase->supplier_name,
                            'total_amount' => $purchase->total_price,
                            'credit_amount' => $purchase->credit_amount,
                            'paycredit_amount' => $paycredit_amount,
                            'remaincredit_amount' => $remaincredit_amount
                             );
                            array_push($outstanding_credits,$outstanding_credit);
                    }
                }
            }
        }
        return $outstanding_credits;
    }


    public function headings():array{

            return [
                'purchase_voucher',
                'purchase_date',
                'supplier_name',
                'total_amount',
                'credit_amount',
                'paycredit_amount',
                'remaincredit_amount'
        ];

    }


}
