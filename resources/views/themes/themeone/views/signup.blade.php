@extends('layout')
@section('content')
<?php //echo "<pre>";print_r(@$result['signup_error']);echo "</pre>";?>
<div class="cartpage">
   <div class="container">
      <div class="row">
         <div class="col-md-6 offset-md-3 ">
            <div class="cartheading">
               <h3>@lang('website.New Customers')</h3>
               <p>@lang('website.login page text for customer')</p>

                @foreach(@$result['signup_error'] as $signup_error)
                <p class="error_class">{{$signup_error}}</p>
                @endforeach

            </div>
            <form name="signup" enctype="multipart/form-data" class="form-validate" action="{{ URL::to('/signupProcess')}}" method="post">
            <div class="youraccount">
               <div class="loginform">
                  <div class="row">
                     <div class="col-12 col-sm-6 pdbt20">
                        <div class="input-group flot">
                           <label for="firstName" class="">First Name *</label>
                           <input type="text" name="firstName" id="firstName" required1="" value="{{@$_POST['firstName']}}">
                        </div>
                     </div>
                     <div class="col-12 col-sm-6 pdbt20">
                        <div class="input-group flot">
                           <label for="lastName" class="">Last Name *</label>
                           <input type="text" name="lastName" id="lastName" required1="" value="{{@$_POST['lastName']}}">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-12 col-sm-6 pdbt20">
                        <div class="input-group flot">
                           <label for="email" class="">Email Address *</label>
                           <input type="email" name="email" id="email" required1="" value="{{@$_POST['email']}}">
                        </div>
                     </div>
                     <div class="col-12 col-sm-6 pdbt20">
                        <div class="input-group flot">
                           <label for="phone" class="">Phone Number *</label>
                           <input type="text" name="phone" id="phone" required1="" value="{{@$_POST['phone']}}">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-12 col-sm-6 pdbt20">
                        <div class="input-group flot">
                           <label for="pass" class="">Password *</label>
                           <input type="password" name="password" id="pass" required1="" value="{{@$_POST['password']}}">
                        </div>
                     </div>
                     <div class="col-12 col-sm-6 pdbt20">
                        <div class="input-group flot">
                           <label for="confirmpass" class="">Confirm Password *</label>
                           <input type="password" name="confirmpassword" id="confirmpass" required1="" value="{{@$_POST['confirmpassword']}}">
                        </div>
                     </div>
                  </div>
                  <div class="pdbt20">
                     <div class="input-group flot">
                        <label for="company_name" class="">Company Name *</label>
                        <input type="text" name="company_name" id="company_name" required1="" value="{{@$_POST['company_name']}}">
                     </div>
                  </div>
                  <div class="pdbt20">
                     <div class="input-group flot">
                        <label for="com" class="animate-label">Do you have and ABN? *</label>
                        <select class="flotin" id="com" name="ABN">
                           <option value="">-Select-</option>
                           <option value="yes" {{ @$_POST['ABN'] == 'yes' ? 'selected' : '' }}>Yes</option>
                           <option value="no" {{ @$_POST['ABN'] == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                     </div>
                  </div>
                  <div class="pdbt20">
                     <button class="btn btn-success btn-block">CONFIRM</button>
                  </div>
               </div>
            </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection 	


