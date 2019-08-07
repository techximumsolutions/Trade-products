@extends('layout')
@section('content')
<div class="container">
   <div class="galleryblock">
      <div class="heading">
         <h2>@lang('website.gallery')</h2>
      </div>
      <div class="row">
        @foreach($result['gallery_data'] as $gallery_data)
         <div class="col-md-3 col-sm-6">
            <div class="product">
               <a href="{{$gallery_data->gallery_image}}" data-toggle="lightbox" data-gallery="gallery">
                  <img src="{{$gallery_data->gallery_image}}" class="img-fluid">
                  <div class="disc">{{$gallery_data->gallery_title}}</div>
               </a>
            </div>
         </div>
         @endforeach
      </div>
   </div>
</div>
@endsection
