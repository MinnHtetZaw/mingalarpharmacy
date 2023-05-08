@extends('master')

@section('title','Financial Report')

@section('place')
{{-- 
<div class="col-md-5 col-8 align-self-center">
    <h3 class="text-themecolor m-b-0 m-t-0">@lang('lang.financial')</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('index')}}">@lang('lang.back_to_dashboard')</a></li>
        <li class="breadcrumb-item active">@lang('lang.financial')</li>
    </ol>
</div> --}}

@endsection

@section('content')

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
            	<h4 class="card-title text-success">@lang('lang.financial') @lang('lang.list')</h4>
                <ul class="nav nav-pills m-t-30 m-b-30">
                    <li class=" nav-item"> 
                        <a href="#navpills-1" class="nav-link active" data-toggle="tab" aria-expanded="false">@lang('lang.daily')</a> 
                    </li>
                    <li class="nav-item"> 
                        <a href="#navpills-2" class="nav-link" data-toggle="tab" aria-expanded="false">@lang('lang.weekly')</a> 
                    </li>
                    <li class="nav-item"> 
                        <a href="#navpills-3" class="nav-link" data-toggle="tab" aria-expanded="false">@lang('lang.monthly')</a> 
                    </li>
                </ul><br/>
                <div class="tab-content br-n pn">
                    <div id="navpills-1" class="tab-pane active">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                     <label class="control-label text-success font-weight-bold">@lang('lang.daily')</label>
                                    <input type="date" class="form-control" id="daily">
                                </div>
                            </div>

                            <div class="col-md-3 pull-right">
                                <button class="btn btn-success btn-submit" type="submit" onclick="showDailySale()">	
                                	@lang('lang.search')
                                </button>
                            </div>
                        </div> 
                    </div>

                    <div id="navpills-2" class="tab-pane">
                    	<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label text-success font-weight-bold">@lang('lang.weekly')</label>
                                    <select class="form-control custom-select" id="weekly">
                                        <option value="">@lang('lang.select_week')</option>
                                        <option value="1">@lang('lang.one_week')</option>
                                        <option value="2">@lang('lang.two_week')</option>
                                        <option value="3">@lang('lang.three_week')</option>
                                        <option value="4">@lang('lang.four_week')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 pull-right">
                                <button class="btn btn-success btn-submit" type="submit" onclick="showWeeklySale()">	
                                	@lang('lang.search')
                                </button>
                            </div>
                        </div>                        
                    </div>

                    <div id="navpills-3" class="tab-pane">
                    	<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label text-success font-weight-bold">@lang('lang.monthly')</label>
                                    <select class="form-control custom-select" id="monthly">
                                        <option value="">@lang('lang.select_month')</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 pull-right">
                                <button class="btn btn-success btn-submit" type="submit" onclick="showMonthlySale()">	
                                	@lang('lang.search')
                                </button>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3" id="report">
        	<div class="card-body">
        		<div class="row mt-2">
	                <div class="col-md-6">
	                    <h4 class="text-success font-weight-bold">
	                    	@lang('lang.total') @lang('lang.sales') -
	                    	<span class="badge badge-pill badge-success" id="total_sales"></span>
	                    </h4>
	                </div>

	                <div class="col-md-6">
	                    <h4 class="text-success font-weight-bold">
	                    	@lang('lang.total') @lang('lang.profit') -
	                    	<span class="badge badge-pill badge-success" id="profit"></span>
	                    </h4>
	                </div>

	                <div class="col-md-12 mt-3">
	                    <table class="table">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold text-success">
                                    	@lang('lang.voucher') @lang('lang.number')
                                    </th>
                                    <th class="font-weight-bold text-success">
                                    	@lang('lang.total') @lang('lang.amount')
                                    </th>
                                    <th class="font-weight-bold text-success">
                                    	@lang('lang.total') @lang('lang.quantity')
                                    </th>
                                    <th class="font-weight-bold text-success">
                                    	@lang('lang.date')
                                    </th>
                                    <th class="font-weight-bold text-success">
                                    	@lang('lang.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="sale_table">
        
                            </tbody>
                        </table>
	                </div>
	            </div>
        	</div>        	
        </div>       
    </div>
</div>

@endsection

@section('js')

<script>

	$(document).ready(function() {
	        
	    $('#report').hide();           

	});

	function showDailySale() {

		$('#total_sales').empty();

		$('#total_sales').empty();

		$('#sale_table').empty();

		var  daily = $('#daily').val();

		var  type  = 1;

		$.ajax({
           type:'POST',
           url:'/getTotalSaleReport',
           data:{   
            "value": daily,
            "type": type,
            "_token":"{{csrf_token()}}"
           },

           	success:function(data){

            	console.log(data);

            	$('#total_sales').text(data.total_sales);

            	$('#profit').text(data.total_profit);

		        $.each(data.voucher_lists,function(i,value){

		            let url = "{{url('/Order/Voucher-Details')}}/"+value.id;

		            let button = `<a href="${url}" class="btn btn-success">@lang('lang.details')</a>`

		            $('#sale_table').append($('<tr>')).append($('<td>').text(value.voucher_code)).append($('<td>').text(value.total_price)).append($('<td>').text(value.total_quantity)).append($('<td>').text(value.voucher_date)).append($('<td>').append($(button)));

		        });

		        $('#report').show();
            }
        });
	}

	function showWeeklySale() {

		$('#total_sales').empty();

		$('#total_sales').empty();
		
		$('#sale_table').empty();

		var  daily = $('#weekly').val();

		var  type  = 2;

		$.ajax({
           type:'POST',
           url:'/getTotalSaleReport',
           data:{   
            "value": daily,
            "type": type,
            "_token":"{{csrf_token()}}"
           },

           	success:function(data){

            	console.log(data);

            	$('#total_sales').text(data.total_sales);

            	$('#profit').text(data.total_profit);

		        $.each(data.voucher_lists,function(i,value){

		            let url = "{{url('/Order/Voucher-Details')}}/"+value.id;

		            let button = `<a href="${url}" class="btn btn-success">@lang('lang.details')</a>`

		            $('#sale_table').append($('<tr>')).append($('<td>').text(value.voucher_code)).append($('<td>').text(value.total_price)).append($('<td>').text(value.total_quantity)).append($('<td>').text(value.voucher_date)).append($('<td>').append($(button)));

		        });

		        $('#report').show();
            }
        });		
	}

	function showMonthlySale() {

		$('#total_sales').empty();

		$('#total_sales').empty();
		
		$('#sale_table').empty();

		var  daily = $('#monthly').val();

		var  type  = 3;

		$.ajax({
           type:'POST',
           url:'/getTotalSaleReport',
           data:{   
            "value": daily,
            "type": type,
            "_token":"{{csrf_token()}}"
           },

           	success:function(data){

            	console.log(data);

            	$('#total_sales').text(data.total_sales);

            	$('#profit').text(data.total_profit);

		        $.each(data.voucher_lists,function(i,value){

		            let url = "{{url('/Order/Voucher-Details')}}/"+value.id;

		            let button = `<a href="${url}" class="btn btn-success">@lang('lang.details')</a>`

		            $('#sale_table').append($('<tr>')).append($('<td>').text(value.voucher_code)).append($('<td>').text(value.total_price)).append($('<td>').text(value.total_quantity)).append($('<td>').text(value.voucher_date)).append($('<td>').append($(button)));

		        });

		        $('#report').show();
            }
        });
		
	}
	
</script>

@endsection