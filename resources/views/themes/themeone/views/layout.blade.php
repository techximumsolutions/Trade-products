<!doctype html>
<html>
<!-- meta contains meta taga, css and fontawesome icons etc -->
@include('common.meta')
<!-- ./end of meta -->
<!--dir="rtl"-->
<body dir="{{ session('direction')}}">
	<!-- header -->
@include('common.header')
<!-- ./end of header -->  
@yield('content')
@include('common.footer')
	<!-- all js scripts including custom js -->
@include('common.scripts')
<!-- ./end of js scripts -->
@if(!empty($result['commonContent']['setting'][77]->value))
<?=stripslashes($result['commonContent']['setting'][77]->value)?>
@endif
</body>
</html>

