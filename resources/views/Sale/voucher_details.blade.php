@extends('master')

@section('title', 'Voucher Details')

@section('place')

    {{-- <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor m-b-0 m-t-0">Sale Page</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Back to Dashborad</a></li>
            <li class="breadcrumb-item active">Sale Page</li>
        </ol>
    </div> --}}

@endsection

@section('content')

    <style>
        td {

            text-align: left;
            font-size: 20px;
            font-weight: bold;
            overflow: hidden;
            white-space: nowrap;
        }

        th {
            text-align: left;
            font-size: 15px;
        }

        h6 {
            font-size: 15px;
            font-weight: 600;
        }

        .btn {
            width: 130px;
            overflow: hidden;
            white-space: nowrap;
        }

    </style>



@php
    $from_id = session()->get('from');
@endphp

<input type="hidden" id="from_id" value="{{$from_id}}">
{{-- <h1>hello</h1> --}}

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-7">
                    <ul class="nav nav-pills m-t-30 m-b-30">
                        <li class="nav-item">
                            <a href="#navpills-1" class="nav-link active" data-toggle="tab" aria-expanded="false">
                                SLIP
                            </a>
                        </li>
                        <li class=" nav-item">
                            <a href="#navpills-2" class="nav-link" data-toggle="tab" aria-expanded="false">
                                A5
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content br-n pn">
        <div id="navpills-1" class="tab-pane active">
            <div class="row justify-content-center">
                <div class="col-md-8 printableArea" style="width:45%;">
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <div>
                                        <h3 class="mt-1 text-center mb-2"> &nbsp;<b
                                                style="font-size: 21px;">မင်္ဂလာ ဆေးနှင့် ဆေးပစ္စည်း</b>

                                                {{-- <br/><span>Specialist Clinic</span> --}}

                                        </h3>

                                        <p class="mt-2" style="font-size: 12px;text-align:center;">ဆိုင်အမှတ် (၁) -> ဘောလုံးကွင်းတိုက်တန်း အရှေ့ဘက်၊ ဆိပ်ဖြူမြို့။
                                            <br/>
                                            <br/>ဆိုင်အမှတ် (၂) -> စည်ပင်လမ်း၊ ပတ္တမြားဈေးအရှေ့ဘက် ဆိပ်ဖြူမြို့။
                                            <br />
                                            <br /><i class="fas fa-mobile-alt"></i> ၀၉ ၄၀၁ ၅၅၆ ၇၇၅, ၀၉ ၉၇၂ ၄၂၈ ၀၈၄
                                        </p>
                                    </div>
                                </div>
                                <div class="pull-right text-left">

                                    <h6 class="text-black">Voucher Number : {{ $unit->voucher_code }} </h6>
                                    <h6 class="text-black">Voucher Date : <i class="fa fa-calendar"></i> {{date('d-m-Y H:i:s A', strtotime($unit->voucher_date))}} </h6>
                                    <h6 class="text-black">Customer Name : {{ $unit->sales_customer_name }} </h6>
                                    <h6 class="text-black">Cashier : {{ $unit->sale_by_name }} </h6>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive text-black" style="clear: both;">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="text-black">
                                                <th>No.</th>
                                                <th>Name</th>
                                                <th>Qty*Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-black" id="slip_live">
                                            @php
                                                $j=1;
                                            @endphp
                                            @foreach ($unit->counting_unit as  $key => $countingunit)
                                            @php
                                                if($countingunit->pivot->discount){
                                                    $price_wif_discount = $countingunit->pivot->price - (int)$countingunit->pivot->discount;
                                                }else{
                                                    $price_wif_discount = $countingunit->pivot->price;

                                                }
                                            @endphp
                                            <tr>
                                                <td style="font-size:15px;">{{$j++}}</td>
                                                <td style="font-size:15px;">{{ $countingunit->unit_name }}</td>
                                                <td style="font-size:15px;">{{ $countingunit->pivot->quantity }} * {{ $price_wif_discount }}</td>
                                                <td style="font-size:15px;" id="subtotal">{{ $countingunit->pivot->quantity *  $price_wif_discount}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="text-black">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-left" style="font-size:15px;">Total</td>
                                                <td id="total_charges" class="font-weight-bold" style="font-size:15px;">
                                                    <span id="slip_total"></span>{{$unit->total_price}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-left" style="font-size:15px;">Discount</td>
                                                <td id="total_charges" class="font-weight-bold" style="font-size:15px;">
                                                    <span id="slip_total"></span>{{$unit->discount}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-left" style="font-size:15px;">Net</td>
                                                <td id="total_charges" class="font-weight-bold" style="font-size:15px;">
                                                    <span id="slip_total"></span>{{$unit->total_price - $unit->discount}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-left" style="font-size:15px;">Pay</td>
                                                <td id="pay" style="font-size:15px;">{{$unit->pay}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-left" style="font-size:15px;">Change</td>
                                                <td id="changes" style="font-size:15px;">{{$unit->change}}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <h6 class="text-center font-weight-bold text-black">***Thank you. Please come again.***</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="navpills-2" class="tab-pane ">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <?php

                    $countitem = count($unit->counting_unit);

                    if ($countitem <= 12) {
                        $z = 1;
                    }
                    if ($countitem > 12 && $countitem < 31) {
                        $z = 2;
                    }
                    if ($countitem > 30 && $countitem < 49) {
                        $z = 3;
                    }
                    if ($countitem > 48) {
                        $z = 4;
                    }

                    $i = 1;
                    ?>
                    @for ($j = 1; $j <= $z; $j++)
                        <?php
                                if ($j == 1) {
                                    if ($countitem < 13) {
                                ?>
                        <div class="card card-body printableArea">
                            <div style="display:flex;justify-content:space-around">
                                 <div>
                                                            <img src="{{ asset('image/marlar_myaing_logo_resized.jpg') }}">
                                                        </div>

                                                        <div>
                                                            <h3 class="mt-1 text-center"> &nbsp;<b
                                                                    style="font-size: 30px;">Marlar Myaing</b><br/><span>Specialist Clinic</span>
                                                            </h3>

                                                            <p class="mt-2" style="font-size: 20px;text-align:center;"> No.6, Marlar Myaing 4th Street, 16th Ward, Hlaing Township
                                                                <br /><i class="fas fa-mobile-alt"></i> 09765111728, 09765111729,01-654764,01-654765
                                                            </p>
                                                        </div>

                                                        <div></div>
                            </div>
                            <div class="row">

                                <div class="col-md-12">

                                    <h3 class="text-info mt-3" style="font-size : 25px"><b>Voucher Number :</b> {{ $unit->voucher_code }} </h3>

                                    <h3 class="text-info mt-2" style="font-size : 25px"><b>Voucher Date :</b> {{ date('d-m-Y', strtotime($unit->voucher_date)) }}
                                    </h3>

                                    <h3 class="text-info mt-3" style="font-size : 25px"><b>Customer Name :</b> {{ $unit->sales_customer_name }} </h3>

                                    <h3 class="text-info mt-3" style="font-size : 25px"><b>Cashier :</b> {{ $unit->sale_by_name }} </h3>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-12">
                                    <table style="width: 100%; ">
                                        <thead class="text-center">
                                            <tr>
                                                <th
                                                    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">
                                                    @lang('lang.number')</th>
                                                <th
                                                    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">
                                                    @lang('lang.item')</th>
                                                <th
                                                    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">
                                                    Qty</th>
                                                <th
                                                    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">
                                                    @lang('lang.price')</th>

                                                <!--<th-->
                                                <!--    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">-->
                                                <!--    Discount</th>-->

                                                <!--<th-->
                                                <!--    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">-->
                                                <!--    FOC</th>-->

                                                <th
                                                    style="font-size:25px; font-weight:bold; height: 15px; border: 2px solid black; text-align: center;">
                                                    Sub Total</th>

                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($unit->counting_unit as $key => $countingunit)
                                                <tr>
                                                    <td
                                                        style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">
                                                        {{ $i++ }}</td>
                                                    <td
                                                        style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">
                                                        {{ $countingunit->unit_name }}
                                                    </td>
                                                    <td
                                                        style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">
                                                        {{ $countingunit->pivot->quantity }}</td>
                                                    <td
                                                        style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">
                                                        {{ $countingunit->pivot->price }}</td>

                                                    <!-- <td-->
                                                    <!--    style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">-->
                                                    <!--    0-->
                                                    <!--</td>-->

                                                    <!-- <td-->
                                                    <!--    style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">-->
                                                    <!--    No-->
                                                    <!--</td>-->

                                                    <td
                                                        style="font-size:25px; font-weight:bold; height: 8px; border: 2px solid black; text-align: center;">
                                                        {{ $countingunit->pivot->price * $countingunit->pivot->quantity }}
                                                    </td>

                                                </tr>
                                            @endforeach
                                            <tr>
                                            <td colspan="3"></td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">Total</td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">
                        <span id="total_charges_a5"></span>{{ $unit->total_price }}</td>
                        </tr>
                                            <tr>
                                            <td colspan="3"></td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">Discount</td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">
                        <span id="total_charges_a5"></span>{{ $unit->discount }}</td>
                        </tr>

                                            <tr>
                                            <td colspan="3"></td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">Net</td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">
                        <span id="total_charges_a5"></span>{{ $unit->total_price - $unit->discount }}</td>
                        </tr>

                                            <tr>
                                            <td colspan="3"></td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">Pay</td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">
                        <span id="total_charges_a5"></span>{{ $unit->pay }}</td>
                        </tr>

                                            <tr>
                                            <td colspan="3"></td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">Changes</td>
                    <td style="font-size:25px;height: 8px; border: 2px solid black; text-align: center;">
                        <span id="total_charges_a5"></span>{{ $unit->change }}</td>
                        </tr>

                                        </tbody>
                                    </table>
                                    <div><br></div>
                                    <h6 class="text-center font-weight-bold text-black" style="font-size:25px;">***Thank you. Please come again.***</h6>
                                </div>
                            </div>

                        </div>
                        <?php
                    }
                    else
                    {
                        $endpt = 14;
                    ?>
                        {{-- <div class="card card-body printableArea">
                            <div style="display:flex;justify-content:space-around">
                                <div>
                                    <img src="{{ asset('image/Shwe_Kyar_Phyu.png') }}">
                                </div>
                                <div>
                                    <h3 class="mt-1 text-center"> &nbsp;<b
                                            style="font-size: 40px;">ရွှေကြာဖြူ</b><span>(ပင်လယ်စာနှင့်အကင်အမျိုးမျိုး)</span>
                                    </h3>
                                    <p class="mt-2" style="font-size: 20px;"> အမှတ်-၆၆၃၊ သမိန်ဗရမ်းလမ်း၊
                                        ၃၅ရပ်ကွက်၊ ဒဂုံမြို့သစ်မြောက်ပိုင်းမြို့နယ်၊ ရန်ကုန်မြို့။
                                        <br /><i class="fas fa-mobile-alt"></i> 09-420022490 , 09-444345502 , 09-955132320
                                    </p>
                                </div>
                                <div></div>
                            </div>

                            <<div class="col-md-12">

                                <h3 class="text-info mt-3" style="font-size : 25px"><b>@lang('lang.invoice')
                                        @lang('lang.number') :</b> {{ $unit->voucher_code }} </h3>

                                <h3 class="text-info mt-2" style="font-size : 25px"><b>@lang('lang.invoice')
                                        @lang('lang.date') :</b> {{ date('d-m-Y', strtotime($unit->voucher_date)) }} </h3>
                        </div> --}}

                        <div class="row">
                            <div class="col-md-12">
                                <table style="width: 100%; ">
                                    <thead class="text-center">
                                        <tr>
                                            <th
                                                style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                                @lang('lang.number')</th>
                                            <th
                                                style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                                @lang('lang.item')</th>
                                            <th
                                                style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                                @lang('lang.order_voucher_qty')</th>
                                            <th
                                                style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                                @lang('lang.price')</th>
                                            <th
                                                style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                                @lang('lang.sub_total')</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <?php foreach ($unit->counting_unit as  $key => $countingunit) {

                                        if ($key == $endpt) {
                                            break;
                                        }
                                            ?>
                                        <tr>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $i++ }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->unit_name }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->quantity }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->price }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->price * $countingunit->pivot->quantity }}</td>
                                        </tr>
                                        <?php
                        }
                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <?php
                        }
                    }
                    // middle loop
                    else if ($j > 1 && $j < $z) {
                        $startpt = $endpt;
                        $endpt = $startpt + 18;
                        if ($j == 3) {
                            $startpt = 32;
                            $endpt = 50;
                        }
                    ?>
                <div class="card card-body printableArea">
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width: 100%; ">
                                <thead class="text-center">
                                    <tr>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.number')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.item')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.order_voucher_qty')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.price')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.sub_total')</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($unit->counting_unit as $key => $countingunit)
                                        <?php
                                        if ($key >= $startpt && $key < $endpt) {
                                        ?>
                                        <tr>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $i++ }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->unit_name }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->quantity }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->price }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->price * $countingunit->pivot->quantity }}</td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                            }
                                                        //last loop
                            else {
                            $startpt = $endpt;
                ?>
                <div class="card card-body printableArea">
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width: 100%; ">
                                <thead class="text-center">
                                    <tr>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.number')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.item')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.order_voucher_qty')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.price')</th>
                                        <th
                                            style="font-size:30px; font-weight:bold; height: 15px; border: 2px solid black;">
                                            @lang('lang.sub_total')</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($unit->counting_unit as $key => $countingunit)
                                        <?php
                                        if ($key >= $startpt)
                                        {
                                        ?>
                                        <tr>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $i++ }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->unit_name }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->quantity }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->price }}</td>
                                            <td
                                                style="font-size:35px; font-weight:bold; height: 8px; border: 2px solid black;">
                                                {{ $countingunit->pivot->price * $countingunit->pivot->quantity }}</td>
                                        </tr>
                                        <?php
                                            }
                                        ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-2 justify-content-around">

                            <div class="col-md-6">
                                <h3 class="text-info font-weight-bold" style="font-size:35px;">
                                    Name - <span id="cus_name"> {{ $unit->sales_customer_name }} </span>
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <h3 class="text-info font-weight-bold" style="font-size:35px;">
                                    @lang('lang.total') - <span id="total_charges"> {{ $unit->total_price }} </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>
                @endfor
            </div>
        </div>
        </div>
    </div>







    <div class="row">
        <div class="col-md-12 mb-3 text-center">
            <button id="print" class="btn btn-success" type="button">
                <span><i class="fa fa-print"></i> Print</span>
            </button>
            @php
            $button_enable = false;
            @endphp

            <button id="edit" class="btn btn-warning" type="button">
                <span><i class="fa fa-edit"></i> Edit</span>
            </button>


            <button id="delete" class="btn btn-danger" data-id="{{$unit->id}}" type="button">
                <span><i class="fa fa-trash"></i> Delete</span>
            </button>
        </div>
    </div>

@endsection

@section('js')

    <script src="{{ asset('js/jquery.PrintArea.js') }}" type="text/JavaScript"></script>
    {{-- <script>
        $("#print").click(function() {
                var mode = 'iframe'; //popup
                var close = mode == "popup";
                var options = {
                    mode: mode,
                    popClose: close
                };
                $(".tab-pane.active div.printableArea").printArea(options);

            });
<<<<<<< HEAD
    </script>
    {{--<script>

    </script> --}}
    <script>
        $(document).ready(function() {
            $("#print").click(function() {
                var mode = 'iframe'; //popup
                var close = mode == "popup";
                var options = {
                    mode: mode,
                    popClose: close
                };
                $(".tab-pane.active div.printableArea").printArea(options);

            });
            $('#edit').click(function(){
                var unit = @json($unit);
                clearLocalstorage(0);
                console.log(unit);
                var from_id = $('#from_id').val();
                var totalPrice = 0;
                var totalQty = 0;

                localStorage.removeItem('mycart');
                localStorage.removeItem('grandTotal');
                $.each(unit.counting_unit,function(i,countingUnit){
                    var current_stock = 0;
                    var currentStock = 0;

                    $.each(countingUnit.stockcount,function(j,stockCount){
                        if(stockCount.from_id == from_id){
                            console.log("stock", stockCount.stock_qty);
                            current_stock  += stockCount.stock_qty;
                        }
                        else{
                            current_stock += 0;
                        }
                        currentStock = current_stock;

                    })
                    if(countingUnit.pivot.discount > 1 &&  gettype($countingUnit.pivot.discount) != "string"){
                        var realPrice = countingUnit.pivot.discount;
                    }
                    else if(countingUnit.pivot.discount == 0 ){
                        var realPrice = countingUnit.pivot.price;
                    }
                    else {
                        var realPrice = 0;
                    }
                    var item = {
                        id: countingUnit.id,
                        item_name: countingUnit.unit_name,
                        unit_name: countingUnit.unit_name,
                        current_qty: currentStock,
                        order_qty: countingUnit.pivot.quantity,
                        selling_price: countingUnit.pivot.price,
                        each_sub: (parseInt(realPrice) * countingUnit.pivot.quantity),
                        discount: countingUnit.pivot.discount,
                    };

                    var mycart = localStorage.getItem('mycart');

                    if (mycart == null) {

                    mycart = '[]';

                    var mycartobj = JSON.parse(mycart);

                    mycartobj.push(item);

                    localStorage.setItem('mycart', JSON.stringify(mycartobj));

                    } else {

                    var mycartobj = JSON.parse(mycart);

                    mycartobj.push(item);

                    localStorage.setItem('mycart', JSON.stringify(mycartobj));
                    }

                    totalPrice += (parseInt(realPrice) * countingUnit.pivot.quantity);
                    totalQty +=  countingUnit.pivot.quantity;
                })
                    var total_amount = {
                        sub_total: totalPrice,
                        total_qty: totalQty,
                        vou_discount: unit.discount
                    };
                    console.log("grand",total_amount);

                    var grand_total = localStorage.getItem('grandTotal');

                    localStorage.setItem('grandTotal', JSON.stringify(total_amount));

                    localStorage.setItem('editvoucher', JSON.stringify(unit.id));

                    window.location.href = "{{ route('sale_page')}}";

            })
            $('#delete').click(function(){

                var voucher_id = $(this).data('id');

                $.ajax({

                type: 'POST',

                url: '/voucher-delete',

                data: {
                    "_token": "{{ csrf_token() }}",
                    "voucher_id": voucher_id,
                },

                success: function(data) {
                    if(data==1){
                        swal({
                            title: "Success",
                            text: "Voucher ဖျက်ပြီးပါပြီ!",
                            icon: "info",
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('sale_history')}}";
                            }, 600);
                    }else{
                        swal({
                            title: "Failed!",
                            text: "Voucher ဖျက်မရပါ !",
                            icon: "error",
                        });

                    }
                },


                });
            })
        });
    </script>



@endsection
