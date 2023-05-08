@extends('master')
@section('title', 'Medical Record')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card shadow p-2">
            <div class="row" id="doc_info">
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Patient ID</h5>
                    <span class="custom-badge  status-blue" id="book_count">{{$patient->code}}</span>
                </div>
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Name</h5>
                    <span class="custom-badge  status-blue" id=""> {{$patient->name}} </span>
                </div>
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Age</h5>
                    <span class="custom-badge  status-blue" id="doc_dept">{{$patient->age}}-y /{{$patient->age_month}}-m </span>
                </div>
                <div class="col-sm-3 col-3 text-center">
                    <h5 class="page-title font-weight-bold text-info">Phone</h5>
                    <span class="custom-badge  status-blue" id="doc_dept">{{$patient->phone}}</span>
                </div>
            </div>
        </div>
    </div>
</div>

    <input type="hidden" value="{{ $patient->id }}" id="patient_id">

<div class="row">
    <button class="btn btn-info ml-3 px-3" id="print">Print</button>
</div>
            <div class="row">
                <div class="card-body ">
                    <div class="card p-2 printableArea">
                        <div class="d-none mt-4" id="medical_title">
                            <div class="mt-1 ml-5 pb-4 float-left" style="width: 20%">
                                <img src="{{asset('assets/img/bahosi.png')}}" width="60" height="60" alt="">
                            </div>
                            <div style="width: 80%" class="d-inline">
                                <p class="ml-5 pl-5">
                                    <span class="pr-5 mr-5">Name  : {{$patient->name ?? null}}</span>
                                    <span class="pr-5 mr-5">Father Name  : {{$patient->father_name ?? null}}</span>
                                    <span class="pr-5 mr-5">Age  : {{$patient->age ?? null}} y / {{$patient->age_month}} m</span>
                                </p>
                                <p class="ml-5 pl-5">
                                    <span class="pr-5 mr-5">Phone  : {{$patient->phone}}</span>
                                    <span class="pr-5 mr-5">Address  : {{$patient->address}}</span>
                                </p>
                            </div>
                        </div>


                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    {{-- <th><i class="fa fa-check-square checkall"></i></th> --}}
                                    <th>No.</th>
                                    <th> Date</th>
                                    <th>Clinical Sings(CS)</th>
                                    <th>Diagnosis</th>
                                    <th>Procedure & Treatments</th>
                                    <th>Precribed Medication</th>
                                    <th>Attending verterinanian</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                @php
                                    $j=1;
                                @endphp
                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>{{$j++}}.</td>
                                        <td>{{$appointment->date}}</td>
                                        <td>{{$appointment->complaint}}</td>
                                        <td>
                                            <ul>
                                                @forelse ($appointment->diagnosis as $diag)
                                                <li>{{$diag->name}}</li>
                                                @empty
                                                @endforelse


                                            </ul>

                                        </td>
                                        <td>{{$appointment->procedure}}</td>
                                        <td>
                                            @if (isset($appointment->clinic_voucher->counting_unit))
                                            <ul>
                                                @foreach ($appointment->clinic_voucher->counting_unit as $medicine)
                                                    <li>{{$medicine->item->item_name}}</li>

                                                @endforeach
                                            </ul>
                                            @endif
                                        </td>
                                        <td>{{$appointment->doctor->name}}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@endsection

@section('js')

<script src="{{asset('assets/js/jquery.PrintArea.js')}}" type="text/JavaScript"></script>

    <script>
        $(document).ready(function() {

        $("#print").click(function() {
                $('#medical_title').removeClass("d-none");
                $('#medical_title').addClass("d-block");
                  var mode = 'iframe'; //popup
                  var close = mode == "popup";
                  var options = {
                      mode: mode,
                      popClose: close
                  };
                  $("div.printableArea").printArea(options);

        });

        }); //jquery end



    </script>


@endsection

