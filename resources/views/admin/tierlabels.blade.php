@extends('admin.layout')
@section('content')
<div class="content-wrapper"> 
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> {{ trans('labels.ListingTier') }} <!-- <small>{{ trans('labels.ListingAllCustomers') }}...</small> --> </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
      <li class="active">{{ trans('labels.TierLabels') }}</li>
    </ol>
  </section>
  
  <!-- Main content -->
  <section class="content"> 
    <!-- Info boxes --> 
    
    <!-- /.row -->

    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><!-- {{ trans('labels.ListingAllCustomers') }} --> </h3>
            <div class="box-tools pull-right">
            	<a href="{{ URL::to('admin/addcustomers')}}" type="button" class="btn btn-block btn-primary">{{ trans('labels.addtierlables') }}</a>
            </div>
          </div>
          
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div class="col-xs-12">
              		
				  @if (count($errors) > 0)
					  @if($errors->any())
						<div class="alert alert-success alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  {{$errors->first()}}
						</div>
					  @endif
				  @endif
              </div>
              
            </div>
            <div class="row">
              <div class="col-xs-12">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>{{ trans('labels.ID') }}</th>
                      <!-- <th>{{ trans('labels.Picture') }}</th> -->
                      <th>{{ trans('labels.TierLabelname') }}1</th>
                      <th>{{ trans('labels.TierLabelname') }}2</th>
                      <th>{{ trans('labels.TierLabelname') }}3</th>
                      <th>{{ trans('labels.TierLabelname') }}4</th>
                      <th>{{ trans('labels.TierLabelname') }}5</th>
                      <th>{{ trans('labels.TierLabelname') }}6</th>
                      <th>{{ trans('labels.TierLabelname') }}7</th>
                      <th>{{ trans('labels.TierLabelname') }}8</th>
                      <th>{{ trans('labels.TierLabelname') }}9</th>
                      <th>{{ trans('labels.TierLabelname') }}10</th>
                      <th>{{ trans('labels.Action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                   @if (count($tierlables['result']) > 0)
						       @foreach ($tierlables['result']  as $key=>$listingCustomers)
							<tr>
								<td>{{$listingCustomers->product_id }}</td>							
								<td>{{ $listingCustomers->price_tier1 }}</td>
								<td>{{ $listingCustomers->price_tier2 }}</td>
                <td>{{ $listingCustomers->price_tier3 }}</td>
                <td>{{ $listingCustomers->price_tier4 }}</td>
                <td>{{ $listingCustomers->price_tier5 }}</td>
                <td>{{ $listingCustomers->price_tier6 }}</td>
                <td>{{ $listingCustomers->price_tier7 }}</td>
                <td>{{ $listingCustomers->price_tier8 }}</td>
                <td>{{ $listingCustomers->price_tier9 }}</td>
                <td>{{ $listingCustomers->price_tier10 }}</td>
								<td>
                                <ul class="nav table-nav">
                              <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                  {{ trans('labels.Action') }} <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="editpricing/{{ $listingCustomers->product_id }}">{{ trans('labels.EditPricing') }}</a></li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation"><a data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.Delete') }}" id="deleteCustomerFrom" customers_id="{{ $listingCustomers->product_id }}">{{ trans('labels.DeletePricing') }}</a></li>
                                </ul>
                              </li>
                            </ul>
								</td>
							</tr>
						@endforeach
                    @else
                    	<tr>
							<td colspan="4">{{ trans('labels.NoRecordFound') }}</td>							
						</tr>
                    @endif
                  </tbody>
                </table>
                @if (count($tierlables['result']) > 0)
					<div class="col-xs-12 text-right">
						{{$tierlables['result']->links('vendor.pagination.default')}}
					</div>
                 @endif
              </div>
            </div>
          </div>
          <!-- /.box-body --> 
        </div>
        <!-- /.box --> 
      </div>
      <!-- /.col --> 
    </div>
    
    <!-- /.row --> 
   
    <!-- deleteCustomerModal -->
	<div class="modal fade" id="deleteCustomerModal" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="deleteCustomerModalLabel">{{ trans('labels.DeletePricing') }}</h4>
		  </div>
		  {!! Form::open(array('url' =>'admin/deletepricetier', 'name'=>'deleteCustomer', 'id'=>'deleteCustomer', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
				  {!! Form::hidden('action',  'delete', array('class'=>'form-control')) !!}
				  {!! Form::hidden('product_id',  '', array('class'=>'form-control', 'id'=>'customers_id')) !!}
		  <div class="modal-body">						
			  <p>{{ trans('labels.DeletePricingText') }}</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
			<button type="submit" class="btn btn-primary">{{ trans('labels.DeletePricing') }}</button>
		  </div>
		  {!! Form::close() !!}
		</div>
	  </div>
	</div>
    
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content notificationContent">

		</div>
	  </div>
	</div>

    <!-- Main row --> 
    
    <!-- /.row --> 
  </section>
  <!-- /.content --> 
</div>
@endsection 