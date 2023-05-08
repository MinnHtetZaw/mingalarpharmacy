@extends('master')
@section('title', 'Edit Profile')
@section('content')

    <div class="row">

        <div class="col-sm-5 col-md-8">
            <h4 class="page-title font-weight-bold">Edit Profile</h4>
        </div>
    </div>


    <div class="profile-tabs" id="booking_list">

        <div class="row ">
            <div class="card-body">
                {{-- <div class="btn btn-success checkallConfirm float-right mx-3">Confirm</div> --}}
            <form action="{{route('patient_profile_update')}}" method="post" id="appointmentStoreForm">
                @csrf
                <input type="hidden" value="{{$patient->id}}" name="patient_id">
                <div class="row col-md-6 offset-md-3">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="focus-label">Name</label>
                                <input class="form-control " type="text" name="name" value="{{$patient->name}}">

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="focus-label">Father Name</label>
                                <input class="form-control " type="text" name="fathername" value="{{$patient->father_name}}">

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="focus-label">Age</label>
                            <div class="row">
                                <input class="form-control col-md-5 mx-3" type="number" name="age" placeholder="year" value="{{$patient->age}}">
                                <input class="form-control col-md-5" type="number" name="age_month" placeholder="month" value="{{$patient->age_month}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="focus-label">Phone</label>
                                <input class="form-control " type="number" name="phone" value="{{$patient->phone}}">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group">
                            <label class="focus-label">Address</label>
                            <textarea class="form-control " name="address" cols="30" rows="10">{{$patient->address}}</textarea>
                                {{-- <input class="form-control floating" type="text" id="address"> --}}
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <button class="btn bbluecolor  w-100 text-white">Edit</button>
                    </div>

                </div>
            </form>
            </div>
        </div>

    </div>

@endsection

@section('js')

    <script>
        $(document).ready(function() {
            $(".select2").select2();
            $("#check_date").datetimepicker({
                format: 'YYYY-MM-DD'
            });

        $('#datetimepicker3').datetimepicker({
            format: 'LT'
        });


        }); //jquery end



    </script>


@endsection
