@extends('master')
@section('title', 'History | '. $appointment->clinic_patient->name)
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card shadow p-2">
            <div class="row" id="doc_info">
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Patient ID</h5>
                    <span class="custom-badge  status-blue" id="book_count"> {{$appointment->clinic_patient->code}}</span>
                </div>
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Name</h5>
                    <span class="custom-badge  status-blue" id=""> {{$appointment->clinic_patient->name}} </span>
                </div>
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Age</h5>
                    <span class="custom-badge  status-blue" id="doc_dept">{{$appointment->clinic_patient->age}}-y / {{$appointment->clinic_patient->age_month}}-m</span>
                </div>
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Phone</h5>
                    <span class="custom-badge  status-blue" id="doc_dept">{{$appointment->clinic_patient->phone}}</span>
                </div>
            </div>

        </div>
    </div>
</div>
            <div class="row">
                <div class="card-body">

                            @if (session()->get('user')->role == 'Doctor')
                            <div class="form-group row">
                                <label for="temperature" class="col-sm-4 col-form-label">&deg;T</label>
                                <div class="col-sm-7 pt-2">
                                  <p>{{$appointmentinfo ? $appointmentinfo->body_temperature : ''}} <span class="bluecolor ">&deg;F</span></p>
                                </div>
                              </div>
                              <div class="form-group row">
                                  <label for="lowerpressure" class="col-sm-4 col-form-label">BP</label>
                                    <div class="col-sm-7 pt-2">
                                      <p>{{$appointmentinfo ? $appointmentinfo->bloodpressure_higher : ''}} <span class="bluecolor">/</span> <span>{{$appointmentinfo ? $appointmentinfo->bloodpressure_lower : ''}} </span> <span class="bluecolor"> mmHg</span></p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                  <label for="oxygen" class="col-sm-4 col-form-label">SPO<sub>2</sub></label>
                                  <div class="col-sm-7 pt-2">
                                    <p>{{$appointmentinfo ? $appointmentinfo->oxygen : ''}} <span class="bluecolor">% on air</span></p>
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label for="weight" class="col-sm-4 col-form-label">Body Weight</label>
                                  <div class="col-sm-7 pt-2">
                                    <p>{{$appointmentinfo ? $appointmentinfo->weight_kg : ''}} <span class="bluecolor">kg</span>  <span> {{$appointmentinfo ? $appointmentinfo->weight_lb : ''}}</span><span class="bluecolor"> lb</span></p>
                                  </div>
                                </div>
                              <div class="form-group row">
                                <label for="heightfeet" class="col-sm-4 col-form-label">PR</label>
                                <div class="col-sm-3 pt-2">
                                  <p>{{$appointmentinfo ? $appointmentinfo->pr : ''}} <span class="bluecolor"> /min</span></span></p>
                                </div>
                              </div>
                            <div class="form-group row">
                                <label for="diagnosis" class="col-sm-4 col-form-label">Diagnosis</label>
                                <div class="col-sm-7 mt-2">
                                  @forelse ($appointment->diagnosis as $diag)
                                  <span>{{$diag->name}} , </span>
                                  @empty
                                  <span>...........</span>
                                  @endforelse
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="weight" class="col-sm-4 col-form-label">Complaint H/O</label>
                                <div class="col-sm-7 mt-2">
                                  <p>{{$appointmentinfo ? $appointmentinfo->complaint : ''}} </p>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="weight" class="col-sm-4 col-form-label">Procedure & etc</label>
                                <div class="col-sm-7 mt-2">
                                  <p>{{$appointmentinfo ? $appointmentinfo->procedure : ''}} </p>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="weight" class="col-sm-4 col-form-label">Physicial Examination</label>
                                <div class="col-sm-7 mt-2">
                                  <div class="row">
                                    <div class="col-md-2 bluecolor">GC - </div>
                                    <div class="col-md-5">{{$appointmentinfo ? $appointmentinfo->gc : ''}}</div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-2 bluecolor">Ht - </div>
                                    <div class="col-md-5 ">{{$appointmentinfo ? $appointmentinfo->ht : ''}}</div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-2 bluecolor">Ht - </div>
                                    <div class="col-md-5 ">{{$appointmentinfo ? $appointmentinfo->ht : ''}}</div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-2 bluecolor">Lgs - </div>
                                    <div class="col-md-5 ">{{$appointmentinfo ? $appointmentinfo->lgs : ''}}</div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-2 bluecolor">Abd - </div>
                                    <div class="col-md-5 ">{{$appointmentinfo ? $appointmentinfo->abd : ''}}</div>
                                  </div>
                                  @if (!empty($appointmentinfo->titles))
                                  @php
                                      $titles = json_decode($appointmentinfo->titles);
                                      $descriptions = json_decode($appointmentinfo->descriptions);
                                  @endphp
                                      @foreach ($titles as $key => $title )
                                      <div class="row">
                                        <div class="col-md-2 bluecolor">{{$appointmentinfo ? $titles[$key] : ''}} - </div>
                                        <div class="col-md-5 ">{{$appointmentinfo ? $descriptions[$key] : ''}}</div>
                                      </div>
                                      @endforeach
                                  @endif
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="weight" class="col-sm-4 col-form-label">Next Appointment Date</label>
                                <div class="col-sm-7 mt-2">
                                  <p>{{$appointmentinfo ? $appointmentinfo->next_appointmentdate : ''}} </p>
                                </div>
                              </div>

                            {{-- <div class="form-group row pt-3 {{$dNoneemployee}}"> --}}

                              <div class="form-group row">
                                <label for="weight" class="col-sm-4 col-form-label">Documents</label>
                                <div class="col-sm-7 pt-2">
                                  @foreach ($appointment->attachments as $document)
                                      <p>{{$document->description}}</p>
                                      <a type="button" class="text-primary " style="cursor: pointer" data-toggle='modal' data-target="#file2_{{$document->id}}_modal">see here..</a> <button class="btn btn-danger text-white float-right deletedoument" data-id="{{$document->id}}"><i class="fas fa-trash-alt"></i></button>
                                      <hr>

                                      <div class="modal fade" id="file2_{{$document->id}}_modal">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                          <div class="modal-content">
                                            <div class="modal-body">
                                               <iframe src ="{{$document->attachment}}" class="text-center" style="width: -webkit-fill-available;height: 90vh;"></iframe>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                  @endforeach

                                </div>
                              </div>
                            @endif

                              @if (session()->get('user')->role == 'Employee')
                              @if ($look_pro)
                              {{-- <div class="form-group row">
                                <label for="weight" class="col-sm-4 col-form-label pinkcolor">Procedure & etc</label>
                                <div class="col-sm-7 mt-2">
                                  <p class="bluecolor">{{$appointmentinfo ? $appointmentinfo->procedure : ''}} </p>
                                </div>
                              </div> --}}
                              @endif
                              @endif

                              <button type="button" class="btn bbluecolor text-white text-bold" id="seevoucher">
                                See Voucher
                              </button>

                              @if (session()->get('user')->role == 'Employee')

                              <button class="btn px-4 bneonblue  text-white print" data-voucher="{{$appointment->clinic_voucher ? $appointment->clinic_voucher->id : null}}">Print</button>
                              <button type="button"  class="btn btn-secondary
                              @if($appointment->voucher)
                                @if($appointment->voucher->clinicvoucher_status==0)
                                btn-secondary
                                @else
                                btn-danger
                                @endif
                              @endif
                               storevoucher px-4" data-voucher="{{$appointment->clinic_voucher ? $appointment->clinic_voucher->id : null}}">Store Voucher</button>
                              {{-- <button type="button" data-voucher="{{$appointment->clinic_voucher ? $appointment->clinic_voucher->id : null}}"  class="btn btn-primary px-4" onclick="chooseService({{$appointment->id}},{{$appointment->clinic_voucher ? $appointment->clinic_voucher->id : null}})"><i class="fa fa-plus"></i> Service</button> --}}

                              <div class="custom-control custom-switch float-right">
                                <input type="checkbox" class="custom-control-input bg-danger" id="customSwitch2">
                                {{-- <label class="custom-control-label pinkcolor" for="customSwitch2"> Voucher Details</label> --}}
                              </div>

                              @endif
                            <div class=" row pt-3">

                            <table class="table table-striped custom-table mt-4">
                                <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Unit Name</th>
                                        <th>Qty/Dose</th>
                                        <th>Duration/Days</th>
                                        <th>Look Procedure</th>
                                        <th>Dose</th>
                                        <th>Total-Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="sale">

                                  @if (!empty($appointment->clinic_voucher->counting_unit))
                                  <tr>
                                    <td colspan="8">
                                    Medicine
                                    </td>
                                  </tr>
                                  @php
                                      $j=1
                                  @endphp
                                  @foreach ($appointment->clinic_voucher->counting_unit as $medicine)

                                      <tr class="text-center">
                                        <td class="font-weight-normal">{{$j++}}.</td>

                                        <td class=" font-weight-normal">{{$medicine->item->item_name}}</td>

                                        <td class=" font-weight-normal">{{$medicine->unit_name}}</td>

                                        <td>
                                            {{$medicine->pivot->doseper_qty}}
                                        </td>
                                        <td>
                                          {{$medicine->pivot->duration}}
                                        </td>
                                        <td>
                                          <div class="form-check pb-3">
                                            <input class="form-check-input" onclick="return false;"
                                            @if ($medicine->pivot->look_procedure)
                                                checked
                                            @endif type="checkbox">
                                          </div>
                                        </td>
                                      <td>
                                        {{$medicine->pivot->dose}}
                                      </td>
                                      <td>
                                        {{$medicine->pivot->quantity}}
                                      </td>
                                        </tr>
                                  @endforeach
                                  @endif
                                    <tr>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td colspan="2">
                                      Medicine Total Charges
                                      </td>
                                      <td>
                                      {{$appointment->clinic_voucher->medicine_charges?? null}}

                                      </td>
                                  </tr>
                                </tbody>
                                @if (!empty($appointment->procedure_items))
                                <tbody id="pro_item">
                                    <tr>
                                      <td colspan="8">
                                     Procedure Items
                                      </td>
                                  </tr>
                                {{-- @foreach ($appointment->services as $service) --}}
                                @for ($i = 0; $i<count($appointment->procedure_items);$i++)
                                <tr class="text-center">
                                    <td class="font-weight-normal">{{$j++}}</td>

                                    <td class=" font-weight-normal">{{$appointment->procedure_items[$i]->name}}</td>

                                    <td class=" font-weight-normal">procedure_item</td>

                                    <td>
                                        {{$appointment->procedure_items[$i]->pivot->qty}}
                                    </td>

                                    <td class=" font-weight-normal"></td>
                                    <td>
                                     </td>
                                     <td></td>
                                     <td></td>
                                    </tr>
                                @endfor

                                {{-- @endforeach --}}
                                @endif

                                  <tr>
                                    <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td colspan="2">
                                     Procedure Item Charges
                                    </td>
                                    <td>
                                    {{$appointment->clinic_voucher->procedure_item_charges?? null}}

                                    </td>
                                </tr>
                                <tbody>

                                <tbody id="ott">

                                @if ($appointment->clinic_voucher->ot_room_charges!=0)
                                    <tr>
                                      <td colspan="8">
                                      OT Room Usage
                                      </td>
                                    </tr>

                                    <tr class="text-center">
                                        <td class="font-weight-normal">{{$j++}}</td>

                                        <td class=" font-weight-normal">{{$ot_room->room_type}}</td>

                                        <td class=" font-weight-normal">ot room</td>

                                        <td>
                                            {{$ot_room->duration}}
                                        </td>

                                        <td class=" font-weight-normal"></td>
                                        <td>
                                         </td>
                                         <td></td>
                                         <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2">
                                            OT Room Charges
                                            </td>
                                            <td>
                                            {{$appointment->clinic_voucher->ot_room_charges?? null}}

                                            </td>
                                        </tr>
                                    @endif

                                  </tbody>
                                  <tbody id="ott">

                                    @if (!empty($appointment->clinic_voucher->surgen_charges))
                                        {{-- <tr>
                                          <td colspan="8">
                                         Surgen Fees
                                          </td>
                                        </tr> --}}

                                        {{-- <tr class="text-center">
                                            <td class="font-weight-normal">{{$j++}}</td>

                                            <td class=" font-weight-normal">{{$ot_room->room_type}}</td>

                                            <td class=" font-weight-normal">ot room</td>

                                            <td>
                                                {{$ot_room->duration}}
                                            </td>

                                            <td class=" font-weight-normal"></td>
                                            <td>
                                             </td>
                                             <td></td>
                                             <td></td>
                                            </tr> --}}
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2">
                                                Surgen Fees
                                                </td>
                                                <td>
                                                {{$appointment->clinic_voucher->surgen_charges?? null}}

                                                </td>
                                            </tr>
                                        @endif

                                      </tbody>


                                @if (!empty($appointment->services))
                                <tbody id="serVice">
                                    <tr>
                                      <td colspan="8">
                                      Services And Package
                                      </td>
                                  </tr>
                                @for ($i = 0; $i<count($appointment->services);$i++)
                                
                                <tr class="text-center">
                                    <td class="font-weight-normal">{{$j++}}</td>

                                    <td class=" font-weight-normal">{{$appointment->services[$i]->name}}</td>

                                    <td class=" font-weight-normal">service</td>

                                    <td>
                                        {{$appointment->services[$i]->pivot->qty}}
                                    </td>

                                    <td class=" font-weight-normal"></td>
                                    <td>
                                     </td>
                                     <td></td>
                                     <td></td>
                                    </tr>
                                    @foreach($other_related_fee as $other)
                                    @if($other->service_id == $appointment->services[$i]->id)
                                     <tr class="text-center">
                                        <td></td>
                                         <td class=" font-weight-normal">*{{$other->title}}*</td>

                                        <td class=" font-weight-normal">service related fee ({{$other->fee}})</td>
    
                                        <td class=" font-weight-normal"></td>
                                            
                                         </tr>
                                    @endif
                                    @endforeach
                                @endfor

                                {{-- @endforeach --}}
                                @endif

                                  <tr>
                                    <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td colspan="2">
                                    Service Total Charges
                                    </td>
                                    <td>
                                    {{$appointment->clinic_voucher->service_charges+$relate_fee?? null}}

                                    </td>
                                </tr>
                                
                                
                                  <tr>
                                    <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td colspan="2">
                                    Consultation Fee
                                    </td>
                                    <td>
                                      6500
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td colspan="2">
                                  Total
                                  </td>
                                  <td>
                                   {{$total_charges}}
                                  </td>
                              </tr>

                              </tbody>
                            </table>
                            </div>
                            {{-- @endif --}}

                </div>
            </div>

            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title pinkcolor" id="exampleModalLongTitle">Voucher</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body col-11 offset-1 ml-5 pl-4">
                    <div class="row">
                      <div class="">
                        {{-- <div class="card card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <address>
                                            <h5> &nbsp;<b class="text-center">Marlarmyaing Specialist Clinic</b></h5>
                                            <h6>(ဆေးခန်းသုံး POS )</h6>
                                            <h6><i class="fas fa-mobile-alt"></i> 01-9669013,9669014,663371 , 09-8623171 , 09-5171618,Fax: 01-9669014 </h6>
                                        </address>
                                    </div>
                                    <div class="pull-right text-left">
                                        <h6>Date : <i class="fa fa-calendar"></i> {{date('d-m-Y', strtotime($appointment->clinic_voucher->voucher_date ?? null))}}</h6>
                                        <h6>Voucher Number :{{$appointment->clinic_voucher->voucher_code ?? null}} </h6>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="table-responsive" style="clear: both;">
                                          <table class="table" style="font-size: 0.75rem">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>No.</th>
                                                    <th>Name</th>
                                                    <th>Unit Name</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sale">
                                              @if (!empty($appointment->clinic_voucher->counting_unit))
                                              <tr class="detail">
                                                <td colspan="5">
                                                Medicine
                                                </td>
                                              </tr>
                                              @php
                                                  $j=1
                                              @endphp
                                              @foreach ($appointment->clinic_voucher->counting_unit as $medicine)

                                                  <tr class="text-center detail" >
                                                    <td class="font-weight-normal">{{$j++}}</td>

                                                    <td class=" font-weight-normal">{{$medicine->item->item_name}}</td>

                                                    <td class=" font-weight-normal">{{$medicine->unit_name}}</td>

                                                    <td>
                                                        {{$medicine->pivot->quantity}}
                                                    </td>
                                                    <td></td>
                                                  </tr>
                                              @endforeach
                                              @endif
                                                <tr>
                                                  <td colspan="4">
                                                  Medicine Total Charges
                                                  </td>
                                                  <td>
                                                  {{$appointment->clinic_voucher->medicine_charges ?? null}}

                                                  </td>
                                              </tr>

                                              <tr class="detail">
                                                <td colspan="5">
                                                Services And Package
                                                </td>
                                            </tr>

                                            @if (!empty($appointment->clinic_voucher->services))
                                            @foreach ($appointment->clinic_voucher->services as $service)
                                            <tr class="text-center detail">
                                              <td class="font-weight-normal">{{$j++}}</td>

                                              <td class=" font-weight-normal">{{$service->name}}</td>

                                              <td class=" font-weight-normal">service</td>

                                              <td>
                                                  {{$service->pivot->quantity}}
                                              </td>
                                              <td></td>
                                              </tr>
                                            @endforeach
                                            @endif

                                              <tr>
                                                <td colspan="4">
                                                Service Total Charges
                                                </td>
                                                <td>
                                                {{$appointment->clinic_voucher->service_charges ?? null}}

                                                </td>
                                            </tr>

                                              <tr>
                                                <td colspan="4">
                                                Doctor Charges
                                                </td>
                                                <td>
                                                  {{$appointment->clinic_voucher->doctor_charges ?? null}}
                                                </td>
                                            </tr>

                                            <tr>
                                              <td colspan="4">
                                              Total
                                              </td>
                                              <td>
                                                {{$appointment->clinic_voucher->total_price ?? null}}
                                              </td>
                                          </tr>
                                            </tbody>
                                        </table>
                                        <h6 class="text-center font-weight-bold">**ကျေးဇူးတင်ပါသည်***</h6>
                                    </div>
                                </div>
                            </div>

                        </div> --}}
                        <div class="row" id="a5_voucher">
                        <div class="col-md-12">
                        <div class="card card-body printableArea">
                            <div style="display:flex;justify-content:space-around">
                                <div>
                                    <img src="{{ asset('image/marlar_myaing_logo.jpg') }}" width="250" height="200">
                                </div>

                                <div class="mt-3">
                                    <h3 class="mt-1 text-center"> &nbsp;<b
                                            style="font-size: 25px;">Marlarmyaing Specialist Clinic</b>
                                    </h3>
                                    <h4 class="mt-1 text-center"><span>(ဆေးခန်းသုံး POS )</span></h4>

                                    <p class="mt-2" style="font-size: 18px;"> အမှတ်-၆၆၃၊
                                        သမိန်ဗရမ်းလမ်း၊ ၃၅ရပ်ကွက်၊
                                        ဒဂုံမြို့သစ်မြောက်ပိုင်းမြို့နယ်၊ ရန်ကုန်မြို့။
                                        <br /><i class="fas fa-mobile-alt"></i> 09-420022490 ,
                                        09-444345502 ,
                                    </p>
                                </div>

                                <div></div>
                            </div>
                            <div class="row text-black mt-2">

                                <div class="col-md-12">
                                    <h5>Date : <i class="fa fa-calendar"></i> {{date('d-m-Y', strtotime($appointment->clinic_voucher->voucher_date ?? null))}}</h5>
                                    <h5>Voucher Number :{{$appointment->clinic_voucher->voucher_code ?? null}} </h5>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" style="clear: both;">
                                          <table class="table" style="font-size: 0.8rem">
                                            <thead class="thead-light">
                                                <tr class="text-center">
                                                    <th>No.</th>
                                                    <th>Name</th>
                                                    <th>Unit Name</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sale">
                                              @if (!empty($appointment->clinic_voucher->counting_unit))
                                              <tr class="">
                                                <td colspan="5">
                                                Medicine
                                                </td>
                                              </tr>
                                              @php
                                                  $j=1
                                              @endphp
                                              @foreach ($appointment->clinic_voucher->counting_unit as $medicine)

                                                  <tr class="" >
                                                    <td class="">{{$j++}}</td>

                                                    <td class=" ">{{$medicine->item->item_name}}</td>

                                                    <td class=" ">{{$medicine->unit_name}}</td>

                                                    <td>
                                                        {{$medicine->pivot->quantity}}
                                                    </td>
                                                    <td></td>
                                                  </tr>
                                              @endforeach
                                              @endif
                                                <tr>
                                                  <td colspan="4" class="font-weight-bold">
                                                  Medicine Total Charges
                                                  </td>
                                                  <td class="font-weight-bold">
                                                  {{$appointment->clinic_voucher->medicine_charges?? null}}

                                                  </td>
                                              </tr>

                                              @if (!empty($appointment->procedure_items))
                                              <tr class="">
                                                <td colspan="5">
                                               Procedure Items
                                                </td>
                                              </tr>

                                              @foreach ($appointment->procedure_items as $pro_it)

                                                  <tr class="" >
                                                    <td class="">{{$j++}}</td>

                                                    <td class=" ">{{$pro_it->name}}</td>

                                                    <td class=" ">procedure item</td>

                                                    <td>
                                                        {{$pro_it->pivot->qty}}
                                                    </td>
                                                    <td></td>
                                                  </tr>

                                              @endforeach
                                              <tr>
                                                <td colspan="4" class="font-weight-bold">
                                                 Procedure Item Charges
                                                </td>
                                                <td class="font-weight-bold">
                                                {{$appointment->clinic_voucher->procedure_item_charges?? null}}

                                                </td>
                                            </tr>
                                              @endif



                                            @if ($appointment->clinic_voucher->ot_room_charges != 0)
                                            <tr class="">
                                                <td colspan="5">
                                                OT Room Usage
                                                </td>
                                            </tr>

                                            <tr class="">
                                                <td class="">{{$j++}}</td>

                                                <td class="">{{$ot_room->room_type}}</td>

                                                <td class="">ot room</td>

                                                <td>
                                                    {{$ot_room->duration}}
                                                </td>
                                                <td></td>
                                         </tr>

                                                <tr>
                                                    <td colspan="4" class="font-weight-bold">
                                                        OT Room Charges
                                                    </td>
                                                    <td class="font-weight-bold">
                                                        {{$appointment->clinic_voucher->ot_room_charges?? null}}

                                                    </td>
                                                </tr>
                                            @endif

                                            @if ($appointment->clinic_voucher->surgen_charges != 0)
                                            {{-- <tr class="">
                                                <td colspan="5">
                                                Surgen Fees
                                                </td>
                                            </tr>

                                            <tr class="">
                                                <td class="">{{$j++}}</td>

                                                <td class="">{{$ot_room->room_type}}</td>

                                                <td class="">ot room</td>

                                                <td>
                                                    {{$ot_room->duration}}
                                                </td>
                                                <td></td>
                                         </tr> --}}

                                                <tr>
                                                    <td colspan="4" class="font-weight-bold">
                                                        Surgen Fees
                                                    </td>
                                                    <td class="font-weight-bold">
                                                        {{$appointment->clinic_voucher->surgen_charges?? null}}

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (!empty($appointment->services))
                                            <tr>
                                                <td colspan="5">
                                                {{-- Services And Package --}}
                                                Services
                                                </td>
                                            </tr>
                                            {{-- @foreach ($appointment->services as $service) --}}
                                            @for ($a=0;$a<count($appointment->services);$a++)
                                                <tr class="">
                                                    <td class="">{{$j++}}</td>

                                                    <td class="">{{$appointment->services[$a]->name}}</td>

                                                    <td class="">service</td>

                                                    <td>
                                                        {{$appointment->services[$a]->pivot->qty}}
                                                    </td>
                                                    <td></td>
                                                    </tr>
                                                     @foreach($other_related_fee as $other)
                                                    @if($other->service_id == $appointment->services[$a]->id)
                                                     <tr class="">
                                                        <td></td>
                                                         <td class=" ">*{{$other->title}}*</td>
                
                                                        <td class=" ">service related fee ({{$other->fee}})</td>
                    
                                                        <td class=" "></td>
                                                            
                                                         </tr>
                                                    @endif
                                                    @endforeach
                                            @endfor

                                            {{-- @endforeach --}}
                                            <tr>
                                                <td colspan="4" class="font-weight-bold">
                                                Service Total Charges
                                                </td>
                                                <td class="font-weight-bold">
                                                {{$appointment->clinic_voucher->service_charges+$relate_fee?? null}}

                                                </td>
                                            </tr>


                                            @endif
                                            
                                          


                                              <tr>
                                                <td colspan="4" class="font-weight-bold">
                                                Consultation Fee
                                                </td>
                                                <td class="font-weight-bold">
                                                  {{-- {{$appointment->clinic_voucher->doctor_charges ?? null}} --}}
                                                  6500
                                                </td>
                                            </tr>

                                            <tr>
                                              <td colspan="4" class="font-weight-bold">
                                              Total
                                              </td>
                                              <td class="font-weight-bold">
                                                {{$total_charges}}
                                              </td>
                                          </tr>
                                            </tbody>
                                        </table>
                                        <h3 class="text-center font-weight-bold">**ကျေးဇူးတင်ပါသည်***</h3>
                                    </div>
                                </div>


                            </div>
                        </div>
                        </div>
                        </div>
                      </div>
                    </div>
                    <div class="row offset-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if (session()->get('user')->role = 'Employee')
                    <button type="button"  class="btn btn-primary print px-4 mx-2" data-voucher="{{$appointment->clinic_voucher ? $appointment->clinic_voucher->id : null}}">Print</button>
                    <button type="button"  class="btn btn-secondary storevoucher px-4" data-voucher="{{$appointment->clinic_voucher ? $appointment->clinic_voucher->id : null}}">Store Voucher</button>
                    @endif
                  </div>

                  </div>

                </div>
              </div>
            </div>



@endsection

@section('js')
<script src="{{asset('assets/js/jquery.PrintArea.js')}}" type="text/JavaScript"></script>

    <script>
        $(document).ready(function() {
        $(".print").click(function() {
          detail();
          var voucher_id=$(this).data('voucher');
          if(!voucher_id){
            swal({
                    title: "Failed!",
                    text: "Doctor still look the patient!",
                    icon: "error",
                    timer: 3000,
                });
          }else{

            $.ajax({
                type:'POST',
                url:'/clinic/storevoucher',
                dataType:'json',
                data:{
                        "_token": "{{ csrf_token() }}",
                        "voucher_id":voucher_id,
                    },

                success:function(data){
                if(data=="success")
                // $(".printableArea").css("width", "45%");
                // $("#exampleModalCenter").modal('hide');
                  var mode = 'iframe'; //popup
                  var close = mode == "popup";
                  var options = {
                      mode: mode,
                      popClose: close
                  };
                  $("div.printableArea").printArea(options);
                }

                });

          }

        });

        $('.deletedoument').click(function() {
          var documentId = $(this).data('id');

          swal({
                title: "Are you sure ?",
                icon:'warning',
                buttons: ["no", "yes"]
            })

              .then((isConfirm)=>{

              if(isConfirm){

                $.ajax({
                  type:'POST',
                  url:'',
                  dataType:'json',
                  data:{
                          "_token": "{{ csrf_token() }}",
                          "document_id":documentId,
                  },

                  success:function(data){
                    if(data)
                    {
                      swal({
                        title: "Success!",
                        text: "Voucher is successfully Delete!",
                        icon: "success",
                        timer: 3000,
                      });
                      location.reload();
                    }
                    else{
                      swal({
                        title: "Error!",
                        text: "Something Went Wrong!",
                        icon: "error",
                        timer: 3000,
                      });
                    }
                  }
              });
              }
          });
        });


        $(".storevoucher").click(function() {
          var voucher_id=$(this).data('voucher');
          if(!voucher_id){
            swal({
                    title: "Failed!",
                    text: "Doctor still look the patient!",
                    icon: "error",
                    timer: 3000,
                });
          }else{

            $.ajax({
                type:'POST',
                url:'/clinic/storevoucher',
                dataType:'json',
                data:{
                        "_token": "{{ csrf_token() }}",
                        "voucher_id":voucher_id,
                    },

                success:function(data){
                if(data=="success")
                {
                  swal({
                    title: "Success!",
                    text: "Voucher is successfully store!",
                    icon: "success",
                    timer: 3000,
                  });
                  location.reload();
                }
                }
                });

          }

        });
        }); //jquery end

        $('#seevoucher').click(function(){
          detail();
          $(".printableArea").css("width", "");
          $("#exampleModalCenter").modal('show');
        })

        function detail(){
          if(!$('#customSwitch2').is(':checked')){
            $(".detail").addClass("d-none");
          }
          else{
            $(".detail").removeClass("d-none");
          }
        }


        function showmodal() {
            var allTotal=0;
            var allQty=0;
            $('#total_quantity').empty();
            $('#sub_total').empty();
            $('#doctorSer').empty();
            $('#serVice').empty();
            var mycart = localStorage.getItem('mycart');
            var grandTotal = localStorage.getItem('grandTotal');

            var grandTotal_obj = JSON.parse(grandTotal);
            if (grandTotal_obj) {
                var mycartobj = JSON.parse(mycart);

                var html = '';
                var increNo = 1;
                if (mycartobj.length > 0) {
                    var medicineCharges = 0;
                    $.each(mycartobj, function(i, v) {

                        var id = v.id;

                        var item = v.item_name;

                        var qty = v.order_qty;

                        var count_name = v.unit_name;

                        html += `<tr class="text-center">
                                <td class="font-weight-normal">${increNo++}</td>

                                <td class=" font-weight-normal">${item}</td>

                                <td class=" font-weight-normal">${count_name}</td>

                                <td>
                                    <i class="fa fa-plus-circle btnplus" onclick="plus(${id},'medicine')" id="${id}"></i>
                                    ${qty}
                                    <i class="fa fa-minus-circle btnminus"  onclick="minus(${id},'medicine')" id="${id}"></i>
                                </td>

                                <td class="font-weight-normal">
                                <div class="row">
                                    <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dose${i}[]" value="1" id="morning${i}">
                                    <label class="form-check-label" for="morning${i}">
                                        Morning
                                    </label>
                                    </div>
                                    <div class="form-check" >
                                    <input class="form-check-input" type="checkbox" name="dose${i}[]" value="2" id="noon${i}">
                                    <label class="form-check-label" for="noon${i}">
                                        Noon
                                    </label>
                                    </div>
                                    <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dose${i}[]" value="3" id="evening${i}">
                                    <label class="form-check-label" for="evening${i}">
                                        Evening
                                    </label>
                                    </div>
                                </div>

                                </td>
                                <td class="font-weight-normal">
                                    <input type="text" class="text-primary w-50 text-center mt-2 plaintext" value="1" name="qtyDose[]">
                                </td>
                                <td class="font-weight-normal">${v.selling_price}</td>
                                <td><i class="fa fa-times" onclick="remove(${id},${qty},'medicine')" id="${id}"></i> </td>
                                </tr>`;
                                medicineCharges += v.selling_price * qty;
                    });
                }
                $("#sale").html(`
                <tr>
                    <td colspan="7">
                    Medicine
                    </td>
                </tr>
                `);
                $("#sale").append(html);
                $("#sale").append(`
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2">
                    Medicine Total Charges
                    </td>
                    <td>
                        <input type="number" class="text-primary w-100 text-center mt-2 plaintext"  value="${medicineCharges}" name="medicineTotalbyHand">
                    </td>
                </tr>
                `);
             allQty+= grandTotal_obj.total_qty??0;

             allTotal+= grandTotal_obj.sub_total??0;

        }

        var pagService_cart = localStorage.getItem('pagServiceCart');

        var pagService_grandTotal = localStorage.getItem('pagServicegrandTotal');

        var pagService_grandTotal_obj = JSON.parse(pagService_grandTotal);

        if (pagService_grandTotal_obj) {

            var pagService_cartobj = JSON.parse(pagService_cart);
            var increNo= increNo ? increNo :1;
            var pshtml = '';
            if (pagService_cartobj.length > 0) {
                var serviceTotalCharges= 0;
                $.each(pagService_cartobj, function(l, ps) {

                    pshtml += `<tr class="text-center">
                            <td class="font-weight-normal">${increNo++}</td>

                            <td class=" font-weight-normal">${ps.service_name}</td>

                            <td class=" font-weight-normal">${ps.type}</td>

                            <td>
                                <i class="fa fa-plus-circle btnplus" onclick="plus(${ps.id},'pagService')" id="${ps.id}"></i>
                                ${ps.qty}
                                <i class="fa fa-minus-circle btnminus"  onclick="minus(${ps.id},'pagService')" id="${ps.id}"></i>
                            </td>

                            <td class=" font-weight-normal">${ps.charges}</td>
                            <td><i class="fa fa-times" onclick="remove(${ps.id},${ps.qty},'pagService')" id="${ps.id}"></i> </td>
                            </tr>`;
                            serviceTotalCharges+= ps.charges * ps.qty;
                });
            }
            $('#serVice').html(`
            <tr>
                <td colspan="7">
                Services And Package
                </td>
            </tr>
            `);
            $("#serVice").append(pshtml);
            $("#serVice").append(`
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2">
                    Service Total Charges
                    </td>
                    <td>
                        <input type="number" class="text-primary w-100 text-center mt-2 plaintext"  value="${serviceTotalCharges}" name="serviceTotalbyHand">
                    </td>
                </tr>
                `);
                $("#serVice").append(`
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2">
                    Doctor Charges
                    </td>
                    <td>
                        <input type="number" class="text-primary w-100 text-center mt-2" style="
                        outline:0;
                        border-width:0 0 1px;
                        " name="doctorChargesbyHand">
                    </td>
                </tr>
                `);
            allQty+= pagService_grandTotal_obj.total_qty??0;

            allTotal+= pagService_grandTotal_obj.sub_total??0;

        }
            $("#total_quantity").text(allQty);

            $("#sub_total").text(allTotal);
        }

        function storeRecord() {

var mycart = localStorage.getItem('mycart');

var grand_total = localStorage.getItem('grandTotal');

var pagServiceCart = localStorage.getItem('pagServiceCart');

var pagServicegrandTotal = localStorage.getItem('pagServicegrandTotal');

// if (!mycart) {

//     swal({
//         title: "Please Check",
//         text: "Item Cannot be Empty to Check Out",
//         icon: "info",
//     });

// } else {

    $("#item").attr('value', mycart);

    $("#grand").attr('value', grand_total);

    $("#pagServiceItem").attr('value', pagServiceCart);

    $("#pagServicegrandTotal").attr('value', pagServicegrandTotal);

    $("#storeRecordForm").submit();


// }
}
function chooseService(appointment_id,voucher_id){

    // alert(voucher_id);
    var mycart = localStorage.getItem('recordAppointId');

          if(!voucher_id){
            swal({
                    title: "Failed!",
                    text: "Doctor still look the patient!",
                    icon: "error",
                    timer: 3000,
                });
          }else{

            localStorage.setItem('recordAppointId', JSON.stringify(appointment_id));
                localStorage.removeItem('mycart');
                localStorage.removeItem('grandTotal');
                localStorage.removeItem('docServiceCart');
                localStorage.removeItem('docServiceGrandTotal');
                localStorage.removeItem('pagServiceCart');
                localStorage.removeItem('pagServicegrandTotal');
            window.location.href = '{{route('clinic_sale_page')}}';

        }

  }
    </script>


@endsection

