@extends('layout')
@section('content')
<?php //echo "<pre>";print_r(@$result['five_banners_images'][0]);echo "</pre>";?>
<div class="container">
        <div class="banner">
          <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators">
              @foreach($result['slides'] as $key=>$slides_data)
              <li data-target="#carouselExampleIndicators" data-slide-to="{{ $key }}" class="@if($key==0) active @endif"></li>
              @endforeach
            </ol>

            <div class="carousel-inner">
              @foreach($result['slides'] as $key=>$slides_data)
              <div class="carousel-item  @if($key==0) active @endif"">
               <!-- @if($slides_data->type == 'category')
        <a href="{{ URL::to('/shop?category='.$slides_data->url)}}">
      @elseif($slides_data->type == 'product')
        <a href="{{ URL::to('/product-detail/'.$slides_data->url)}}">
      @elseif($slides_data->type == 'mostliked')
        <a href="{{ URL::to('shop?type=mostliked')}}">
      @elseif($slides_data->type == 'topseller')
        <a href="{{ URL::to('shop?type=topseller')}}">
      @elseif($slides_data->type == 'deals')
        <a href="{{ URL::to('shop?type=deals')}}">
      @endif -->
        <img class="d-block w-100"  src="{{asset('').$slides_data->image}}" width="100%" alt="First slide">
        <!-- </a> -->
              </div>
              @endforeach
            </div>

            <!-- <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">

              <span class="carousel-control-prev-icon" aria-hidden="true"></span>

              <span class="sr-only">Previous</span>

            </a>

            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">

              <span class="carousel-control-next-icon" aria-hidden="true"></span>

              <span class="sr-only">Next</span>

            </a> -->

          </div>

      </div>

    </div>

    <div class="container">

        <div class="promo">

        <div class="row">

          <div class="firstcolumn">
          <div class="row">
            <div class="imgpromo col-md-12 col-sm-6">
              <a href="{{ @$result['five_banners_images'][0]->banners_url ? $result['five_banners_images'][0]->banners_url : 'javascript:void(0)' }}">
              <img src="{{ @$result['five_banners_images'][0] ? asset('').$result['five_banners_images'][0]->banners_image : asset('public').'/images/tp_06.jpg' }}" class="img-fluid"></a>
            </div>

            <div class="imgpromo col-md-12 col-sm-6">
              <a href="{{ @$result['five_banners_images'][1]->banners_url ? $result['five_banners_images'][1]->banners_url : 'javascript:void(0)' }}">
              <img src="{{ @$result['five_banners_images'][1] ? asset('').$result['five_banners_images'][1]->banners_image : asset('public').'/images/tp_06.jpg' }}" class="img-fluid"></a>
            </div>
          </div>
          </div>

          <div class="secondcolumn">
            <div class="imgpromo">
              <a href="{{ @$result['five_banners_images'][2]->banners_url ? $result['five_banners_images'][2]->banners_url : 'javascript:void(0)' }}">
              <img src="{{ @$result['five_banners_images'][2] ? asset('').$result['five_banners_images'][2]->banners_image : asset('public').'/images/tp_08.jpg' }}" class="img-fluid"></a>
            </div>
          </div>

          <div class="thirdcolumn">
            <div class="row">
            <div class="imgpromo col-md-12 col-sm-6">
              <a href="{{ @$result['five_banners_images'][3]->banners_url ? $result['five_banners_images'][3]->banners_url : 'javascript:void(0)' }}">
              <img src="{{ @$result['five_banners_images'][3] ? asset('').$result['five_banners_images'][3]->banners_image : asset('public').'/images/tp_06.jpg' }}" class="img-fluid"></a>
            </div>
            <div class="imgpromo col-md-12 col-sm-6">
              <a href="{{ @$result['five_banners_images'][4]->banners_url ? $result['five_banners_images'][4]->banners_url : 'javascript:void(0)' }}">
              <img src="{{ @$result['five_banners_images'][4] ? asset('').$result['five_banners_images'][4]->banners_image : asset('public').'/images/tp_03.jpg' }}" class="img-fluid"></a>
            </div>
          </div>
          </div>



        </div>

        <div class="clearfix"></div>

      </div>

    </div>

    <div class="container">

        <div class="bestprice">

        <div class="imgplace">

          <img src="{{asset('public')}}/images/bestprice.png" class="img-fluid">

        </div>

        <div class="row">

          <div class="col-md-12">

            <div class="row">

              <div class="col-md-7 offset-sm-5">

                <h2>Want the best prices?</h2>

                <h4>Create a Tradie Rewards Account and start <u>SAVING</u> on all products today.</h4>

                <button class="btn btn-white"><i class="fa fa-user"></i> CREATE AN ACCOUNT</button>

                <button class="btn btn-default-outline"><i class="fa fa-phone"></i> 1300 71 81 91</button>

              </div>

            </div>

        </div>

        </div>

        <div class="clearfix"></div>

      </div>

    </div>

    <div class="container">

      <div class="productrange">

        <div class="heading"><h2>@lang('website.Product Range')</h2></div>
      <div class="row">
         @foreach($result['commonContent']['categories'] as $categories_data)
        <div class="col-md-3 col-sm-6">

          <div class="product">
            <a href="{{ URL::to('/shop')}}?category={{$categories_data->slug}}" class="">
            <img src="{{asset('').$categories_data->image}}" class="img-fluid">
            </a>
            <div class="disc">{{$categories_data->name}}</div>

          </div>

        </div>
       @endforeach
      </div>

      
      </div>
    </div>
@endsection
