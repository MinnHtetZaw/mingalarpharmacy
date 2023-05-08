@extends('master')

@section('title','Supplier Page')

@section('place')

@endsection
@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-6">
                <h3 class="text-info">Supplier Lists</h3>
            </div>
            <div class="col-md-6">
                <a href="" class="btn bg-primary text-white float-right" data-toggle="modal" data-target="#addSupplier"><i class="fas fa-plus mr-2"></i>Add Supplier</a>
            </div>
            {{-- start add supplier --}}
            <div class="modal fade" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <form action="{{route('store_supplier')}}" method="POST">
                          @csrf
                    <div class="modal-header bg-info">
                      <h5 class="modal-title text-white" id="exampleModalLabel">Registration Supplier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="text-success">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Name">
                            <label for="" class="text-success">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" placeholder="Enter Phone Number">
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-info">Save</button>
                    </div>
                    </form>
                  </div>
                </div>
              </div>
            {{-- end add supplier --}}
        </div>
        <section id="plan-features">

                <div class="card">
                    <div class="card-body shadow">
                        <div class="tab-content br-n pn">
                            <div id="navpills-1" class="tab-pane active">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>id</th>
                                    <th>Name</th>

                                    <th>Phone</th>
                                    <th>Credit Amount</th>
                                    <th>Detail</th>
                                </tr>
                                </thead>
                                <?php $i = 1 ?>
                                <tbody>
                                    @foreach ($suppliers as $sup)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$sup->id}}</td>
                                            <td>{{$sup->name}}</td>
                                            <td>{{$sup->phone_number}}</td>
                                            <td>{{$sup->credit_amount}}</td>
                                            <td>
                                                <a href="" class="btn btn-primary">Credit Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            @endsection


                                </div>
                            </div>
                        </div>
                    </div>

        </section>
    </div>
</div>













