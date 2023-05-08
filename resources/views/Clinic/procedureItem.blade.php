@extends('master')
@section('title', 'Building Information')
@section('content')

<div class="row">
    <div class="col-sm-5 col-5">
        <h4 class="page-title font-weight-bold">Procedure Item</h4>
    </div>
</div>


<div class="row">

	<div class="offset-2 col-md-8">

        <div class="card shadow">

    		<div class="card-header">
    			<div class="col-sm-7 col-10 text-right pull-right">
			        <a href="" class="btn bpinkcolor text-white btn-rounded" data-target="#add_building" data-toggle="modal">
			        	<i class="fa fa-plus"></i> Add Procedure Item
			        </a>
			    </div>

			    <div id="add_building" class="modal fade delete-modal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body text-center">

								<h3 class="pinkcolor">Procedure Item!</h3>
								<hr>
								<form action="{{route('procedureitem_store')}}" method="POST">
									@csrf

									<div class="form-group">
										<label> Procedure Item Name</label>
										<input class="form-control" type="text"  name="name">
									</div>

                                    <div class="form-group">
										<label> Related Procedure Group</label>
										<select name="pg_id" id="pg_id" class="form-control">
                                            <option value="">Select Procedure Group</option>
                                            @foreach ($proceduregroup as $pg)
                                                <option value="{{$pg->id}}">{{$pg->name}}</option>
                                            @endforeach
                                        </select>
									</div>
									
									<div class="form-group">
									<label> Price</label>
									<input class="form-control" type="text"  name="price">
									</div>

									<div class="m-t-20">
										<a href="#" class="btn btn-danger ml-3" data-dismiss="modal">Close</a>
										<button type="submit" class="btn btn-primary">Save</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

    		</div>

    		<div class="card-body">
    			<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
                                <th>Related Procedure Group</th>
                                <th>Price</th>
                                <th>Action</th>
		                    </tr>
						</thead>
						 <?php
		                    $i = 1;
		                ?>
						<tbody>
							@foreach($procedureItem as $pro)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$pro->name}}</td>
                                <td>{{$pro->procedure_group->name}}</td>
                                <td>{{$pro->price}}</td>
                                <td>
                                    <a href="" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#edit_proitem{{$pro->id}}">Update</a>
                                    <a href="{{route('procedureitem_delete',$pro->id)}}" class="btn btn-sm btn-danger">Delete</a>
                                </td>
							</tr>
                            <div class="modal fade" id="edit_proitem{{$pro->id}}" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title pinkcolor">Edit Procedure Item Form</h4>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>

                                <div class="modal-body">
                                    <form class="form-material m-t-40" method="post" action="{{route('procedureitem_update', $pro->id)}}">
                                        @csrf

                                        <div class="form-group">
                                            <label class="font-weight-bold">Name</label>
                                            <input type="text" name="name" class="form-control" value="{{$pro->name}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Procedure Group</label>
                                            <select id="services" class=" form-control" name="pg_id">
                                                @foreach ($proceduregroup as $pg)
                                                    <option value="{{$pg->id}}">{{$pg->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Price</label>
                                            <input type="text" name="price" class="form-control" value="{{$pro->price}}">
                                        </div>

                                        <input type="submit" name="btnsubmit" class="btnsubmit float-right btn btn-primary" value="Update">
                                    </form>
                                </div>

                          </div>
							@endforeach

						</tbody>

                            </div>
                        </div>
					</table>
				</div>
    		</div>

        </div>

    </div>

</div>

@endsection
