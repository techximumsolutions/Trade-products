@extends('admin.layout')
@section('content')
<?php //echo "<pre>";print_r($productinfo);//die(); ?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> {{ trans('labels.EditCustomers') }} <small>{{ trans('labels.EditCustomers') }}...</small> </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
      <li><a href="{{ URL::to('admin/customers')}}"><i class="fa fa-users"></i> {{ trans('labels.ListingAllCustomers') }}</a></li>
      <li class="active">{{ trans('labels.EditCustomers') }}</li>
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
            <h3 class="box-title">{{ trans('labels.EditCustomers') }} </h3>
          </div>

          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div class="col-xs-12">
              		<div class="box box-info">
                        <!--<div class="box-header with-border">
                          <h3 class="box-title">Edit category</h3>
                        </div>-->
                        <!-- /.box-header -->
                        <br>
                       	@if(count($productinfo['message'])>0)
						<div class="alert alert-success alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						 {{ $productinfo['message'] }}
						</div>
						@endif

                       @if(count($productinfo['errorMessage'])>0)
						<div class="alert alert-danger" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						 {{ $productinfo['errorMessage'] }}
						</div>
						@endif

                        <!-- form start -->
                         <div class="box-body">

                            {!! Form::open(array('url' =>'admin/updatepricing', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}
                            	{!! Form::hidden('product_id',  $productinfo['customers'][0]->product_id, array('class'=>'form-control', 'id'=>'product_id')) !!}
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}1 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier1',  $productinfo['customers'][0]->price_tier1, array('class'=>'form-control field-validate', 'id'=>'price_tier1')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span> -->
                                   <!--  <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}2 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier2',  $productinfo['customers'][0]->price_tier2, array('class'=>'form-control field-validate', 'id'=>'price_tier2')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}3 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier3',  $productinfo['customers'][0]->price_tier3, array('class'=>'form-control field-validate', 'id'=>'price_tier3')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}4 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier4',  $productinfo['customers'][0]->price_tier4, array('class'=>'form-control field-validate', 'id'=>'price_tier4')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}5 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier5',  $productinfo['customers'][0]->price_tier5, array('class'=>'form-control field-validate', 'id'=>'price_tier5')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}6 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier6',  $productinfo['customers'][0]->price_tier6, array('class'=>'form-control', 'id'=>'price_tier6')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}7 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier7',  $productinfo['customers'][0]->price_tier7, array('class'=>'form-control ', 'id'=>'price_tier7')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}8 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier8',  $productinfo['customers'][0]->price_tier8, array('class'=>'form-control ', 'id'=>'price_tier8')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}9 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier9',  $productinfo['customers'][0]->price_tier9, array('class'=>'form-control ', 'id'=>'price_tier9')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TierLabelname') }}10 </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('price_tier10',  $productinfo['customers'][0]->price_tier10, array('class'=>'form-control', 'id'=>'price_tier10')) !!}
                                     <!-- <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.TierLabelnameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span> -->
                                  </div>
                                </div>

                              <!-- /.box-body -->
                              <div class="box-footer text-center">
                                <button type="submit" class="btn btn-primary">{{ trans('labels.Update') }} </button>
                                <a href="{{ URL::to('admin/tierlistings')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
                              </div>
                              <!-- /.box-footer -->
                            {!! Form::close() !!}
                        </div>
                  </div>
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

    <!-- Main row -->

    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@endsection