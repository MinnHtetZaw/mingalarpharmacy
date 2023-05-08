@extends('master')

@section('title','Sale History Page')

@section('place')

<div class="col-md-5 col-8 align-self-center">
    <h4 class="text-themecolor m-b-0 m-t-0">@lang('lang.sale_history')</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('index')}}">@lang('lang.back_to_dashboard')</a></li>
        <li class="breadcrumb-item active">@lang('lang.sale_history')</li>
    </ol>
</div>

@endsection

@section('content')
<section id="plan-features">
    <div class="row ml-2 mr-2">
            <div class="col-xl-3 col-lg-6">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                            <span class="h3 font-weight-normal mb-0 text-info" style="font-size: 20px;">{{$total_sales}}  @lang('lang.ks')</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                                <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-success font-weight-normal text-sm">
                        <span>@lang('lang.all_time_sale')</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h3 font-weight-normal mb-0 text-info" style="font-size: 20px;">{{$daily_sales}} @lang('lang.ks')</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-success font-weight-normal text-sm">
                        <span>@lang('lang.today') @lang('lang.sales')</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h2 font-weight-normal mb-0 text-info" style="font-size: 25px;">{{$weekly_sales}} @lang('lang.ks')</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-success font-weight-normal text-sm">
                        <span>@lang('lang.this') @lang('lang.week')</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                            <span class="h3 font-weight-normal mb-0 text-info" style="font-size: 20px;">{{$monthly_sales}} @lang('lang.ks')</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                                <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-success font-weight-normal text-sm">
                        <span>@lang('lang.this') @lang('lang.month')</span>
                        </p>
                    </div>
                </div>
            </div>
    </div>

    <div class="row ml-4 mt-3">
        <div class="col-8">
        <form action="{{route('search_sale_history')}}" method="POST" class="form">
            @csrf
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label font-weight-bold">@lang('lang.from')</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{$fromdate}}" onChange="setFrom(this.value)" required>
                </div>

                <div class="col-md-2">
                    <label class="font-weight-bold">@lang('lang.to')</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{$todate}}" onChange="setTo(this.value)" required>
                </div>

                <div class="col-md-2">
                        <label class="">Customer</label>
                        <select name="customer" id="customer" class="form-control form-control-sm select2" onChange="setCustomer(this.value)">
                            <option>Select Customers</option>
                                <option value=0 selected>All</option>
                            @foreach(\App\SalesCustomer::all() as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="">Sales Person</label>
                        <select name="sales_person" id="sales_person" class="form-control form-control-sm select2" onChange="setSales(this.value)">
                            <option>Select Sales Person</option>
                                <option value=0 selected>All</option>
                            @foreach(\App\User::where('role','Sale')->orWhere('role','Owner')->get() as $employee)
                                <option value="{{$employee->id}}">{{$employee->name}}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="col-md-1 m-t-20 m-l-40">
                    <input type="submit" name="btnsubmit" class="btnsubmit float-right btn btn-primary" value="@lang('lang.search')">
                </div>
            </div>
        </form>
        </div>


        <div class="col-md-4 mt-4">

             <form id="exportForm" onsubmit="return exportForm()" method="get">
                 <div class="row">
                <input type="hidden" name="export_from" id="export_from" class="form-control form-control-sm hidden" required>
                <input type="hidden" name="export_to" id="export_to" class="form-control form-control-sm hidden" required>
                <input type="hidden" name="export_customer" id="export_customer" class="form-control form-control-sm hidden" required>
                <input type="hidden" name="export_sales" id="export_sales" class="form-control form-control-sm hidden" required>
                <div class="col-3">
                     <select name="export_data_type" id="export_data_type" class="form-control form-control-sm select2" style="font-size: 12px;">
                                <option value=1 selected>Vouchers</option>
                                <option value=2 >Items</option>
                        </select>

                </div>

                <div class="col-3">
                     <select name="export_type" id="export_type" class="form-control form-control-sm select2" style="font-size: 12px;">
                                <option value=1 selected>Excel</option>
                                <option value=2 >PDF</option>
                        </select>

                </div>


                <div class="col-6">
                <input type="submit" class="btn btn-sm rounded btn-outline-info col-4" value=" Export ">
                </div>
                </div>

            </form>


        </div>

    </div>
    <br/>



    <div class="container">
        <div class="card">
            <div class="card-body shadow">

                <div class="row ml-4 mt-3">
        @if ($search_sales !=0)
        <p class="text-right font-weight-normal text-danger ml-5 mt-4 pt-2">Search Sales = <span> {{$search_sales}} ကျပ်</span></p>
        @endif
    </div>

                <div class="row">



                    <div class="col-md-12">

                        <div class="row p-2 offset-10">
                        <input  type="text" id="table_search" placeholder="Quick Search" onkeyup="search_table()" >
                    </div>

                        <div class="table-responsive text-black" id="slimtest2">
                            <table class="table" id="item_table">
                                <thead class="head">
                                    <tr>
                                        <th class="text-black">#</th>
                                        <th class="text-black">@lang('lang.voucher') @lang('lang.number')</th>
                                        <th class="text-black">@lang('lang.voucher') @lang('lang.date')</th>
                                        <th class="text-black">@lang('lang.name')</th>
                                        <th class="text-black">@lang('lang.total') @lang('lang.quantity')</th>
                                        <th class="text-black">@lang('lang.total') @lang('lang.price')</th>
                                        <th class="text-black">Discount</th>
                                        <th class="text-black">@lang('lang.details')</th>
                                    </tr>
                                </thead>
                                <tbody id="item_list" class="body">
                                    <?php
                                        $i = 1;
                                        $name = "Customer"
                                    ?>
                                   @foreach($voucher_lists as $voucher)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$voucher->voucher_code}}</td>
                                        <td>{{$voucher->voucher_date}}</td>
                                        <td>{{($voucher->sales_customer_name != "") ? $voucher->sales_customer_name : $name }}</td>
                                        <td>{{$voucher->total_quantity}}</td>
                                        @php
                                            if($voucher->discount > 0){
                                                $total_wif_discount = ($voucher->total_price) - ((int) $voucher->discount);
                                            }
                                            else if ($voucher->discount == "foc"){
                                                $total_wif_discount = 0;
                                            }
                                            else if ($voucher->discount == 0){
                                                $total_wif_discount = $voucher->total_price;
                                            }
                                        @endphp
                                        <td>{{$total_wif_discount}}</td>
                                        <td>{{$voucher->discount}}</td>
                                        <td style="text-align: center;"><a href="{{ route('getVoucherDetails',$voucher->id)}}" class="btn btn-primary" style="color: white;">Details</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')

<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('assets/js/jquery.slimscroll.js')}}"></script>

<script type="text/javascript">

// 	$('#item_table').DataTable( {

//             "paging":   false,
//             "ordering": true,
//             "info":     false

//     });

$(document).ready(function(){
	     const today = new Date();
         var dd = today.getDate();
         var mm = today.getMonth()+1;
         var yyyy= today.getFullYear();
	    $('#export_from').val(yyyy+'-'+mm+'-'+dd);
	    $('#export_to').val(yyyy+'-'+mm+'-'+dd);
	    $('#export_customer').val(0);
	    $('#export_sales').val(0);
	    $('#export_data_type').val(1);
	    $("#export_type").val(1);
	});

    function search_table(){
            var input, filter, table,tr,td,i;
            input = document.getElementById("table_search");
            filter = input.value.toUpperCase();
            table = document.getElementById("item_table");
            tr = table.getElementsByTagName("tr");

            var searchColumn = [1,2,3,4,5,6];

            for(i = 0; i < tr.length; i++){
                if($(tr[i]).parent().attr('class') == 'head'){
                    continue;
                }

                var found = false;

                for(var k=0; k < searchColumn.length; k++){
                    td = tr[i].getElementsByTagName("td")[searchColumn[k]];
                    if(td){
                        if(td.innerHTML.toUpperCase().indexOf(filter) > -1){
                            found=true;
                        }
                    }
                }
                if(found == true){
                    tr[i].style.display = "";
                }else{
                    tr[i].style.display = "none";
                }
            }
        }

    function setFrom(value){
        $("#exportForm :input[name=export_from]").val(value);
    }

     function setTo(value){
        $("#exportForm :input[name=export_to]").val(value);
    }

     function setCustomer(value){
        $("#exportForm :input[name=export_customer]").val(value);
        // console.log($("#exportForm :input[name=export_customer]").val());
    }

    function setSales(value){
        $("#exportForm :input[name=export_sales]").val(value);
        // console.log($("#exportForm :input[name=export_sales]").val());
    }
    function exportForm(){

        //var form = document.getElementById("exportForm");
        //var data = new URLSearchParams(form).toString();
        var from = $("#exportForm :input[name=export_from]").val();
        var to = $("#exportForm :input[name=export_to]").val();
        var id =  $("#exportForm :input[name=export_customer]").val();
        var sales = $("#exportForm :input[name=export_sales]").val();
        var data_type = $("#exportForm :input[name=export_data_type]").find(":selected").val();
        var type = $("#exportForm :input[name=export_type]").find(":selected").val();
        console.log(from,to,id,data_type,type,sales);

        // fetch("http://medicalworldinvpos.kwintechnologykw09.com/Sale/Voucher/HistoryExport/${from}/${to}/${id}",{
        //     method: "get"
        // }).then(()=>{console.log('Export Success');})
        // .catch((err)=>{console.log(err);});
         let url = `/export-salehistory/${from}/${to}/${id}/${sales}/${data_type}/${type}`;
         window.location.href= url;
         const today = new Date();
         var dd = today.getDate();
         var mm = today.getMonth()+1;
         var yyyy= today.getFullYear();
         if(dd <10){
             dd = '0' + dd;
         }
         if(mm < 10){
             mm = '0' + mm;
         }
          $('#export_from').val(yyyy+'-'+mm+'-'+dd);
	    $('#export_to').val(yyyy+'-'+mm+'-'+dd);
	    $('#export_customer').val(0);
	    $('#export_sales').val('All');
	    $('#export_data_type').val(1);
	    $("#export_type").val(1);

        return false;
    };

    $('#slimtest2').slimScroll({
        color: '#00f',
        height: '600px'
    });

</script>

@endsection
