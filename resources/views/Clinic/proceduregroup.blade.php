@extends('master')
@section('title', 'Building Information')
@section('content')

<div class="row">
    <div class="col-sm-5 col-5">
        <h4 class="page-title font-weight-bold">Procedure Group</h4>
    </div>
</div>


<div class="row">

	<div class="offset-2 col-md-8">

        <div class="card shadow">

    		<div class="card-header">
    			<div class="col-sm-7 col-10 text-right pull-right">
			        <a href="" class="btn bpinkcolor text-white btn-rounded" data-target="#add_building" data-toggle="modal">
			        	<i class="fa fa-plus"></i> Add Procedure Group
			        </a>
			    </div>

			    <div id="add_building" class="modal fade delete-modal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body text-center">

								<h3 class="pinkcolor">Procedure Group!</h3>
								<hr>
								<form action="{{route('procuduregroup_store')}}" method="POST">
									@csrf

									<div class="form-group">
										<label> Procedure Group</label>
										<input class="form-control" type="text"  name="name">
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
		                    </tr>
						</thead>
						 <?php
		                    $i = 1;
		                ?>
						<tbody>
							@foreach($proceduregroup as $pro)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$pro->name}}</td>
							</tr>

							@endforeach

						</tbody>
					</table>
				</div>
    		</div>

        </div>

    </div>

</div>

@endsection
