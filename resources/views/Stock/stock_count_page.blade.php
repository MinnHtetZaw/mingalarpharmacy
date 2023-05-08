@extends('master')

@section('title','Stock Count')

@section('place')

{{-- <div class="col-md-5 col-8 align-self-center">
    <h3 class="text-themecolor m-b-0 m-t-0">@lang('lang.stock_count')</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('index')}}">@lang('lang.back_to_dashboard')</a></li>
        <li class="breadcrumb-item active">@lang('lang.stock_count')</li>
    </ol>
</div> --}}

@endsection

@section('content')
@php
$from_id = session()->get('from')
@endphp 
<input type="hidden" id="isowner" value="{{session()->get('user')->role}}">
<input type="hidden" id="isshop" value="{{session()->get('from')}}">
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">        
        <h4 class="font-weight-normal">@lang('lang.stock_count')</h4>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">               
                <div class="row">
                    @if(session()->get('user')->role == "Owner")
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label text-black">ဆိုင်ရွေးရန်</label>
                            <select class="form-control select2" onchange="getItems(this.value)" id="shop_id">
                                @foreach($shops as $shop)
                                <option value="{{$shop->id}}"
                                @if ($from_id==$shop->id)
                                    selected
                                @endif
                                >{{$shop->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <!--/span-->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label text-black">@lang('lang.select_item')</label>
                            <select class="form-control select2" id="item_list">
                                <option></option>
                                @foreach ($items as $item)
                                @if ($item->counting_units)
                                    @foreach ($item->counting_units as $unit)
                                    @foreach ($unit->stockcount as $key=>$stockcount)
                                  
                        
                                    @endforeach
                                        <option value="{{$unit->id}}"
                                            >{{$unit->unit_code}} - {{$unit->unit_name}}</option>
                                        
                                    @endforeach
                                @endif
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('stockcountexport') }}" class="btn btn-primary mx-2">Export</a>
                    </div>
                </div>
{{-- 
                <div class="row justify-content-end">
                    <button class="btn btn-success" onclick="checkUnit()"> 
                        <i class="fa fa-check"></i> @lang('lang.check_unit')
                    </button>
                </div> --}}
    
            </div>        
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">

        <div class="card card-outline-info">
            <div class="card-header">
                <h4 class="m-b-0 text-white">@lang('lang.counting_unit') @lang('lang.list')</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive text-black">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th></th>
                                <th>Item Code</th>
                                <th>@lang('lang.item') @lang('lang.name')</th>
                                <th>@lang('lang.current') @lang('lang.quantity')</th>
                                <th>Pharmacy Price</th>
                                <th>Patient Price</th>
                                
                                @if(session()->get('user')->role == "Owner")
                                <th>@lang('lang.action')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="units_table">
                            @php
                            $jj=1;
                            @endphp
                            @foreach($items as $item)
                          
                                @foreach ($item->counting_units as $unit)
                                <tr>
                                    <td>{{$jj++}}</td>
                                    <td>

                                        <div class="col-6 form-check form-switch">
                                            <input class="form-check-input" name="assign_check" type="checkbox" value="{{$unit->id}}" id="assign_check{{$unit->id}}">
                                            <label class="form-check-label" for="assign_check{{$unit->id}}"></label>
                                        </div>

                                    </td>
                                    <td>{{$unit->unit_code}}</td>
                                    <td>{{$unit->unit_name}}</td>
                                    @foreach ($unit->stockcount as $key=>$stockcount)
                                        @php
                                            if($unit->stockcount[$key]->from_id== $from_id){
                                                $stockcountt= $unit->stockcount[$key]->stock_qty;
                                            }
                                        @endphp
                            
                                    @endforeach
                                    {{-- @if(session()->get('user')->role == "Owner") --}}
                                    <td>
                                        <input type="number" class="form-control w-50 stockinput text-black" data-stockinputid="stockinput{{$unit->id}}" id="stockinput{{$unit->id}}" data-id="{{$unit->id}}"value="{{$stockcountt}}">
                                    </td>
                                    {{-- @else
                                    <td>
                                      {{$stockcountt}}  
                                    </td>
                                    @endif --}}
                                    <td>
                                        <input type="number" class="form-control w-50 pharmpriceinput text-black" data-pharmpriceinputid="pharmpriceinput{{$unit->id}}" id="pharmpriceinput{{$unit->id}}" data-id="{{$unit->id}}"value="{{$unit->normal_sale_price}}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control w-50 patientpriceinput text-black" data-patientpriceinputid="patientpriceinput{{$unit->id}}" id="patientpriceinput{{$unit->id}}" data-id="{{$unit->id}}"value="{{$unit->whole_sale_price}}">
                                    </td>
                                    
                                    @if(session()->get('user')->role == "Owner")
                                    <td> 
                                        <div class="row">
                                            <a href="#" class="btn btn-warning unitupdate" 
                                            data-unitid="{{$unit->id}}" data-code="{{$unit->unit_code}}" data-unitname="{{$unit->unit_name}}"
                                                >                      
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger delete_stock" data-id="{{$unit->id}}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                   
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                       
                            @endforeach

                            <div class="modal fade" id="edit_unit_qty" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">@lang('lang.update_counting_unit_quantity') @lang('lang.form')</h4>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                              </button>
                                        </div>

                                        <div class="modal-body">
                                            <form class="form-horizontal m-t-40" method="post" action="{{route('update_stock_count')}}">
                                                @csrf
                                                <input type="hidden" name="unit_id" id="unit_id">
                                                <div class="form-group row">
                                                    <label class="control-label text-right col-md-6 text-black">Code </label>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="unique_unit_code" name="unit_code"> 
                                                        
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="control-label text-right col-md-6 text-black">ပစ္စည်း အမည်</label>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" name="unit_name" id="unique_unit_name"> 
                                                        
                                                    </div>
                                                </div>

                                                <input type="submit" name="btnsubmit" class="btnsubmit float-right btn btn-primary" value="@lang('lang.save')">
                                            </form>           
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>
@endsection

@section('js')

<script>

    $(document).ready(function(){

        $(".select2").select2();
        $("#item_list").select2({
            placeholder:"ကုန်ပစ္စည်း ရှာရန်",
        });
    });

    function getItems(value){

        var shop_id = value;

        $.ajax({

            type:'POST',

            url:'{{route('AjaxGetItem')}}',

            data:{
                "_token":"{{csrf_token()}}",
                "shop_id": shop_id,           
            },

            success:function(data){
                console.log(data);
                $('#item_list').empty();             

                $('#item_list').append($('<option>').text("ရှာရန်").attr('value', ""));
                var html = "";
                $.each(data, function(i, value) {

                $('#item_list').append($('<option>').text(value.item_name).attr('value', value.id));
                
                $.each(value.counting_units,function(j,unit){
                    var stockcountt=0;
                    $.each(unit.stockcount,function(k,stock){
                        if(stock.from_id==shop_id){
                             stockcountt= unit.stockcount[k].stock_qty;
                        }
                    })
                    html += `
                    <tr>
                                    <td>${unit.unit_code}</td>
                                    <td>${unit.unit_name}</td>
                                    <td>
                                        <input type="number" class="form-control w-25 stockinput text-black" data-stockinputid="stockinput${unit.id}" id="stockinput${unit.id}" data-id="${unit.id}" value="${stockcountt}">
                                        </td>
                                    <td>
                                        <input type="number" class="form-control w-50 pharmpriceinput text-black" data-pharmpriceinputid="pharmpriceinput${unit.id}" id="pharmpriceinput${unit.id}" data-id="${unit.id}"value="${unit.normal_sale_price}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control w-50 patientpriceinput text-black" data-patientpriceinputid="patientpriceinput${unit.id}" id="patientpriceinput${unit.id}" data-id="${unit.id}"value="${unit.whole_sale_price}">
                                    </td>
                                    
                                    <td> 
                                        <div class="row">
                                            <a href="#" class="btn btn-warning unitupdate" 
                                            data-unitid="${unit.id}" data-code="${unit.unit_code}" data-unitname="${unit.unit_name}"

                                            >
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger delete_stock" data-id="${unit.id}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    
                                    </td>
                                </tr>
                    `;
                });    
                

            }),
            $('#units_table').empty();
            $('#units_table').html(html); 
            swal({
                toast:true,
                position:'top-end',
                title:"Success",
                text:"Shop Changed!",
                button:false,
                timer:500  
            }); 
        }

    })
}
    $('.delete_stock').click(function(){
        var id = $(this).data('id');
        var idArray= [];
        $("input:checkbox[name=assign_check]:checked").each(function(){
        idArray.push(parseInt($(this).val()));
        });
        if(idArray.length >0){
            var unit_ids = idArray;
            var multi_delete = 1;
        }else{
            var unit_ids = id;
            var multi_delete = 0;
        }
        $.ajax({

            type:'POST',

            url:'{{route('delete_units')}}',

            data:{
                "_token":"{{csrf_token()}}",
                "unit_ids": unit_ids,
                "multi_delete":multi_delete
            },

            success:function(data){
                swal({
                    title: "@lang('lang.success')!",
                    text : "@lang('lang.successfully_deleted')!",
                    icon : "success",
                        });

                setTimeout(function(){
                window.location.reload();
            }, 1000);
                
            },
            });
    })
    $('#item_list').change(function(){

        //shop id for owner . isshop for counter
        let shop_id = $('#shop_id').val() ?? $('#isshop').val();

        let unit_id = $('#item_list').val();
        console.log(unit_id);
        var isowner = $('#isowner').val();

        $('#units_table').empty();

        $.ajax({

            type:'POST',

            url:'{{route('AjaxGetCountingUnit')}}',

            data:{
                "_token":"{{csrf_token()}}",
                "unit_id": unit_id,
                "shop_id":shop_id
            },

            success:function(data){
                    var value = data;
                    var stockcountt=0;
                    $.each(value.stockcount,function(k,stock){
                        if(stock.from_id==shop_id){
                             stockcountt= stock.stock_qty;
                            console.log('stockcount',stock.stock_qty);
                        }
                    })
                    let button = `
                    <div class="row">
                        <a  href="#" class="btn btn-warning unitupdate" 
                        
                        data-unitid="${value.id}" data-code="${value.unit_code}" data-unitname="${value.unit_name}"

                        >Edit</a>
                        <button class="btn btn-danger delete_stock" data-id="${value.id}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                        </div>
            
                    
                    `;
                    
                    let inputstock = `<input type="number" class="form-control w-50 stockinput text-black" data-stockinputid="stockinput${value.id}" id="stockinput${value.id}" data-id="${value.id}" value="${stockcountt}">`;
                    let inputpharmprice = `<input type="number" class="form-control w-50 pharmpriceinput text-black" data-pharmpriceinputid="pharmpriceinput${value.id}" id="pharmpriceinput${value.id}" data-id="${value.id}"value="${value.normal_sale_price}">`;
                    let inputpatientprice = `<input type="number" class="form-control w-50 patientpriceinput text-black" data-patientpriceinputid="patientpriceinput${value.id}" id="patientpriceinput${value.id}" data-id="${value.id}"value="${value.whole_sale_price}">`;
                    // if(isowner == "Owner"){
                        $('#units_table').append($('<tr>')).append($('<td>').text(1)).append($('<td>').text("")).append($('<td>').text(value.unit_code)).append($('<td>').text(value.unit_name)).append($('<td>').append(inputstock)).append($('<td>').append(inputpharmprice)).append($('<td>').append(inputpatientprice)).append($('<td>').append($(button)));
                    // }
                    // else{
                    //     $('#units_table').append($('<tr>')).append($('<td>').text(value.item.category.category_name)).append($('<td>').text(value.item.item_name)).append($('<td>').text(value.unit_name)).append($('<td>').append(stockcountt)).append($('<td>').append(value.reorder_quantity));
                    // }
         


                
            },
        });

    })
    
        $('.row').on('click','.unitupdate',function(){
              event.preventDefault()
        var id = $(this).data('unitid');
        var code = $(this).data('code');
        var name = $(this).data('unitname');
        console.log(id,code,name);
        $("#unit_id").val(id);   
        $("#unique_unit_code").val(code);   
        $("#unique_unit_name").val(name);   
        $("#edit_unit_qty").modal("show");  
        })
    
  
    
    
    $('#units_table').on('keypress','.stockinput',function(){
        var keycode= (event.keyCode ? event.keyCode : event.which);
        if(keycode=='13'){
            // var shop_id = $('#shop_id option:selected').val();
            var shop_id = $('#shop_id').val() ?? $('#isshop').val();
            var stock_qty = $(this).val();
            var unit_id= $(this).data('id');
            var stockinputid = $(this).data('stockinputid');
            $.ajax({

                type:'POST',

                url:'{{route('stockupdate-ajax')}}',

                data:{
                    "_token":"{{csrf_token()}}",
                    "stock_qty": stock_qty,
                    "shop_id":shop_id,
                    "unit_id":unit_id
                },

                success:function(data){
                    if(data){
                        swal({
                            toast:true,
                            position:'top-end',
                            title:"Success",
                            text:"Stock Changed!",
                            button:false,
                            timer:500,
                            icon:"success"  
                        });
                        $(`#${stockinputid}`).addClass("is-valid");
                        $(`#${stockinputid}`).blur();
                    }
                    else{
                        swal({
                            toast:true,
                            position:'top-end',
                            title:"Error",
                            button:false,
                            timer:1500  
                        });
                        $(`#${stockinputid}`).addClass("is-invalid");
                    }
                },
                });
        }
    })
    
     $('#units_table').on('keypress','.pharmpriceinput',function(){
        var keycode= (event.keyCode ? event.keyCode : event.which);
        if(keycode=='13'){
            // var shop_id = $('#shop_id option:selected').val();
            var shop_id = $('#shop_id').val() ?? $('#isshop').val();
            var pharm_price = $(this).val();
            var unit_id= $(this).data('id');
            var pharmpriceinputid = $(this).data('pharmpriceinputid');
            $.ajax({

                type:'POST',

                url:'{{route('pharmpriceupdate-ajax')}}',

                data:{
                    "_token":"{{csrf_token()}}",
                    "pharm_price": pharm_price,
                    "shop_id":shop_id,
                    "unit_id":unit_id
                },

                success:function(data){
                    if(data){
                        swal({
                            toast:true,
                            position:'top-end',
                            title:"Success",
                            text:"Pharmacy Price Changed!",
                            button:false,
                            timer:500,
                            icon:"success"  
                        });
                        $(`#${pharmpriceinputid}`).addClass("is-valid");
                        $(`#${pharmpriceinputid}`).blur();
                    }
                    else{
                        swal({
                            toast:true,
                            position:'top-end',
                            title:"Error",
                            button:false,
                            timer:1500  
                        });
                        $(`#${pharmpriceinputid}`).addClass("is-invalid");
                    }
                },
                });
        }
    })
    
    $('#units_table').on('keypress','.patientpriceinput',function(){
        var keycode= (event.keyCode ? event.keyCode : event.which);
        if(keycode=='13'){
            // var shop_id = $('#shop_id option:selected').val();
            var shop_id = $('#shop_id').val() ?? $('#isshop').val();
            var patient_price = $(this).val();
            var unit_id= $(this).data('id');
            var patientpriceinputid = $(this).data('stockinputid');
            $.ajax({

                type:'POST',

                url:'{{route('patientpriceupdate-ajax')}}',

                data:{
                    "_token":"{{csrf_token()}}",
                    "patient_price": patient_price,
                    "shop_id":shop_id,
                    "unit_id":unit_id
                },

                success:function(data){
                    if(data){
                        swal({
                            toast:true,
                            position:'top-end',
                            title:"Success",
                            text:"Patient Price Changed!",
                            button:false,
                            timer:500,
                            icon:"success"  
                        });
                        $(`#${patientpriceinputid}`).addClass("is-valid");
                        $(`#${patientpriceinputid}`).blur();
                    }
                    else{
                        swal({
                            toast:true,
                            position:'top-end',
                            title:"Error",
                            button:false,
                            timer:1500  
                        });
                        $(`#${patientpriceinputid}`).addClass("is-invalid");
                    }
                },
                });
        }
    })
    
   
  

</script>
@endsection