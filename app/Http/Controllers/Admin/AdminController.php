<?php

/*
Project Name: IonicEcommerce
Project URI: http://ionicecommerce.com
Author: VectorCoder Team
Author URI: http://vectorcoder.com/
*/

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Validator;
use App;
use Lang;

use App\Admin;
use App\Categories_Description;
use App\Categories;
use App\TierPrice;
use App\SaleTierPrice;
use App\ProductImages;
use App\AssignCategory;

use DB;
//for password encryption or hash protected
use Hash;
//use App\Administrator;

//for authenitcate login data
use Auth;
use Session;
//for requesting a value 
use Illuminate\Http\Request;

class AdminController extends Controller
{
	public function dashboard(Request $request){
		$title 			  = 	array('pageTitle' => Lang::get("labels.title_dashboard"));
		$language_id      = 	'1';
		$result 		  =		array();

		$reportBase		  = 	$request->reportBase;

		//recently order placed
		$orders = DB::table('orders')
			->LeftJoin('currencies', 'currencies.code', '=', 'orders.currency')
			->where('customers_id','!=','')
			->orderBy('date_purchased','DESC')
			->get();


		$index = 0;
		$purchased_price = 0;
		$sold_cost = 0;
		foreach($orders as $orders_data){
			//$total_price += $orders_data->order_price;
			$orders_products = DB::table('orders_products')
				->select('final_price', DB::raw('SUM(final_price) as total_price') ,'products_id','products_quantity' )
				->where('orders_id', '=' ,$orders_data->orders_id)
				->groupBy('final_price')
				->get();


			if(count($orders_products)>0 and !empty($orders_products[0]->total_price)){
				$orders[$index]->total_price = $orders_products[0]->total_price;
			}else{
				$orders[$index]->total_price = 0;
			}
			
			
			
			foreach($orders_products as $orders_product){	
			
				$sold_cost += $orders_product->total_price;
				$single_purchased_price = DB::table('inventory')->where('products_id',$orders_product->products_id)->sum('purchase_price');
				$single_stock = DB::table('inventory')->where('products_id',$orders_product->products_id)->where('stock_type','in')->sum('stock');
				if($single_stock>0){
				    $single_product_purchase_price = $single_purchased_price/$single_stock;
				}else{
				    $single_product_purchase_price = 0;
				}
				$purchased_price = $single_product_purchase_price*$orders_product->products_quantity;				
				$purchased_price += $purchased_price;
			
			}

			$orders_status_history = DB::table('orders_status_history')
				->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
				->select('orders_status.orders_status_name', 'orders_status.orders_status_id')
				->where('orders_id', '=', $orders_data->orders_id)->orderby('orders_status_history.date_added', 'DESC')->limit(1)->get();

			$orders[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
			$orders[$index]->orders_status = $orders_status_history[0]->orders_status_name;

			$index++;

		}
		
		//products profit
		if($purchased_price==0){
			$profit = 0;
		}else{
			$profit = abs($purchased_price - $sold_cost);
		}
		
		$result['profit'] = number_format($profit,2);
		$result['total_money'] = number_format($sold_cost,2);
		
		$compeleted_orders = 0;
		$pending_orders = 0;
		foreach($orders as $orders_data){

			if($orders_data->orders_status_id=='2')
			{
				$compeleted_orders++;
			}
			if($orders_data->orders_status_id=='1')
			{
				$pending_orders++;
			}
		}


		$result['orders'] = $orders->chunk(10);
		$result['pending_orders'] = $pending_orders;
		$result['compeleted_orders'] = $compeleted_orders;
		$result['total_orders'] = count($orders);

		$result['inprocess'] = count($orders)-$pending_orders-$compeleted_orders;
		//add to cart orders
		$cart = DB::table('customers_basket')->get();

		$result['cart'] = count($cart);

		//Rencently added products
		$recentProducts = DB::table('products')
			->leftJoin('products_description','products_description.products_id','=','products.products_id')
			->where('products_description.language_id','=', $language_id)
			->orderBy('products.products_id', 'DESC')
			->paginate(8);

		$result['recentProducts'] = $recentProducts;

		//products
		$products = DB::table('products')
			->leftJoin('products_description','products_description.products_id','=','products.products_id')
			->where('products_description.language_id','=', $language_id)
			->orderBy('products.products_id', 'DESC')
			->get();

		//low products & out of stock
		$lowLimit = 0;
		$outOfStock = 0;
		//$total_money = 0;
		$products_ids = array();
		$data = array();
		foreach($products as $products_data){
			//$total_money += $products_data->products_price;
			$currentStocks = DB::table('inventory')->where('products_id',$products_data->products_id)->get();
			//$total_money += DB::table('inventory')->where('products_id',$products_data->products_id)->sum('purchase_price');

			if(count($currentStocks)>0){

				if($products_data->products_type!=1){
					$c_stock_in = DB::table('inventory')->where('products_id',$products_data->products_id)->where('stock_type','in')->sum('stock');
					$c_stock_out = DB::table('inventory')->where('products_id',$products_data->products_id)->where('stock_type','out')->sum('stock');

					if(($c_stock_in-$c_stock_out)==0){
						if(!in_array($products_data->products_id, $products_ids)){
							$products_ids[] = $products_data->products_id;

							array_push($data,$products_data);
							$outOfStock++;
						}
					}
				}

			}else{
				$outOfStock++;
			}
		}

		$result['lowLimit'] = $lowLimit;
		$result['outOfStock'] = $outOfStock;
		$result['totalProducts'] = count($products);

		$customers = DB::table('customers')
			->LeftJoin('customers_info','customers_info.customers_info_id','=', 'customers.customers_id')
			->orderBy('customers_info.customers_info_date_account_created','DESC')
			->get();

		$result['recentCustomers'] = $customers->chunk(21);
		$result['totalCustomers'] = count($customers);
		$result['reportBase'] = $reportBase;

		//get function from other controller
		$myVar = new AdminSiteSettingController();
		$currency = $myVar->getSetting();
		$result['currency'] = $currency;

		return view("admin.dashboard",$title)->with('result', $result);
		}
	
	
	public function login(){
		if (Auth::check()) {
		  return redirect('/admin/dashboard/this_month');
		}else{
			$title = array('pageTitle' => Lang::get("labels.login_page_name"));
			return view("admin.login",$title);
		}
	}
	
	public function admininfo(){
		$administor = administrators::all();		
		return view("admin.login",$title);
	}
	
	//login function
	public function checkLogin(Request $request){
		$validator = Validator::make(
			array(
					'email'    => $request->email,
					'password' => $request->password
				), 
			array(
					'email'    => 'required | email',
					'password' => 'required',
				)
		);
		//check validation
		if($validator->fails()){
			return redirect('admin/login')->withErrors($validator)->withInput();
		}else{
			//check authentication of email and password
			$adminInfo = array("email" => $request->email, "password" => $request->password);
			
			if(auth()->guard('admin')->attempt($adminInfo)) {
				$admin = auth()->guard('admin')->user();
				
				$administrators = DB::table('administrators')->where('myid', $admin->myid)->get();					
				if(!empty(auth()->guard('admin')->user()->adminType)){	
					if(auth()->guard('admin')->user()->adminType != '1'){
					$roles = DB::table('manage_role')->where('admin_type_id', auth()->guard('admin')->user()->adminType)->get();
					
					if(count($roles)>0){		
						$dashboard_view = $roles[0]->dashboard_view;	
						
						$manufacturer_view = $roles[0]->manufacturer_view;
						$manufacturer_create = $roles[0]->manufacturer_create;
						$manufacturer_update = $roles[0]->manufacturer_update;
						$manufacturer_delete = $roles[0]->manufacturer_delete;
						
						$categories_view   = $roles[0]->categories_view;	
						$categories_create = $roles[0]->categories_create;
						$categories_update = $roles[0]->categories_update;
						$categories_delete = $roles[0]->categories_delete;
							
						$products_view = $roles[0]->products_view;
						$products_create = $roles[0]->products_create;
						$products_update = $roles[0]->products_update;
						$products_delete = $roles[0]->products_delete;			
						
						$news_view   = $roles[0]->news_view;
						$news_create = $roles[0]->news_create;
						$news_update = $roles[0]->news_update;
						$news_delete = $roles[0]->news_delete;						
						
						$customers_view = $roles[0]->customers_view;
						$customers_create = $roles[0]->customers_create;
						$customers_update = $roles[0]->customers_update;
						$customers_delete = $roles[0]->customers_delete;			
						
						$tax_location_view = $roles[0]->tax_location_view;
						$tax_location_create = $roles[0]->tax_location_create;
						$tax_location_update = $roles[0]->tax_location_update;
						$tax_location_delete = $roles[0]->tax_location_delete;
						
						$coupons_view = $roles[0]->coupons_view;
						$coupons_create = $roles[0]->coupons_create;
						$coupons_update = $roles[0]->coupons_update;
						$coupons_delete = $roles[0]->coupons_delete;			
						
						$notifications_view = $roles[0]->notifications_view;
						$notifications_send = $roles[0]->notifications_send;
						
						$orders_view = $roles[0]->orders_view;
						$orders_confirm = $roles[0]->orders_confirm;			
						
						$shipping_methods_view = $roles[0]->shipping_methods_view;
						$shipping_methods_update = $roles[0]->shipping_methods_update;
						
						$payment_methods_view = $roles[0]->payment_methods_view;
						$payment_methods_update = $roles[0]->payment_methods_update;
						
						$reports_view = $roles[0]->reports_view;
						
						$website_setting_view = $roles[0]->website_setting_view;
						$website_setting_update = $roles[0]->website_setting_update;
						
						$application_setting_view = $roles[0]->application_setting_view;
						$application_setting_update = $roles[0]->application_setting_update;
						
						
						$general_setting_view = $roles[0]->general_setting_view;
						$general_setting_update = $roles[0]->general_setting_update;
						
						$manage_admins_view   = $roles[0]->manage_admins_view;
						$manage_admins_create = $roles[0]->manage_admins_create;
						$manage_admins_update = $roles[0]->manage_admins_update;
						$manage_admins_delete = $roles[0]->manage_admins_delete;
						
						
						$language_view = $roles[0]->language_view;
						$language_create = $roles[0]->language_create;
						$language_update = $roles[0]->language_update;
						$language_delete = $roles[0]->language_delete;
						
						$admintype_view = $roles[0]->admintype_view;
						$admintype_create = $roles[0]->admintype_create;
						$admintype_update = $roles[0]->admintype_update;
						$admintype_delete = $roles[0]->language_delete;
						$manage_admins_role = $roles[0]->manage_admins_role;
						
						$profile_view = $roles[0]->profile_view;
						$profile_update = $roles[0]->profile_update;
					
					}else{
						$dashboard_view = '0';
						
						$manufacturer_view = '0';			
						$manufacturer_create = '0';
						$manufacturer_update = '0';
						$manufacturer_delete = '0';
						
						$categories_view = '0';
						$categories_create = '0';
						$categories_update = '0';
						$categories_delete = '0';
						
						$products_view   = '0';	
						$products_create = '0';
						$products_update = '0';
						$products_delete = '0';
								
						$news_view = '0';	
						$news_create = '0';
						$news_update = '0';
						$news_delete = '0';
								
						$customers_view   = '0';
						$customers_create = '0';
						$customers_update = '0';
						$customers_delete = '0';
						
						$tax_location_view = '0';
						$tax_location_create = '0';
						$tax_location_update = '0';
						$tax_location_delete = '0';						
						
						$coupons_view = '0';
						$coupons_create = '0';
						$coupons_update = '0';
						$coupons_delete = '0';
						
						$notifications_view = '0';
						$notifications_send = '0';
						
						$orders_view = '0';
						$orders_confirm = '0';
						
						$shipping_methods_view = '0';
						$shipping_methods_update = '0';
						
						$payment_methods_view = '0';
						$payment_methods_update = '0';
						
						$reports_view = '0';
						
						$website_setting_view = '0';
						$website_setting_update = '0';
						
						$application_setting_view = '0';
						$application_setting_update = '0';			
						
						$general_setting_view = '0';
						$general_setting_update = '0';
						
						$manage_admins_view = '0';			
						$manage_admins_create = '0';
						$manage_admins_update = '0';
						$manage_admins_delete = '0';
						
						$language_view = '0';
						$language_create = '0';
						$language_update = '0';
						$language_delete = '0';		
							
						$profile_view = '0';
						$profile_update = '0';
						
						$admintype_view = '0';
						$admintype_create = '0';
						$admintype_update = '0';
						$admintype_delete = '0';
						$manage_admins_role = '0';
						
						}	
					}else{
						$dashboard_view = '1';
						
						$manufacturer_view = '1';			
						$manufacturer_create = '1';
						$manufacturer_update = '1';
						$manufacturer_delete = '1';
						
						$categories_view = '1';
						$categories_create = '1';
						$categories_update = '1';
						$categories_delete = '1';
						
						$products_view   = '1';	
						$products_create = '1';
						$products_update = '1';
						$products_delete = '1';
								
						$news_view = '1';	
						$news_create = '1';
						$news_update = '1';
						$news_delete = '1';
								
						$customers_view   = '1';
						$customers_create = '1';
						$customers_update = '1';
						$customers_delete = '1';
						
						$tax_location_view = '1';
						$tax_location_create = '1';
						$tax_location_update = '1';
						$tax_location_delete = '1';					
						
						$coupons_view = '1';
						$coupons_create = '1';
						$coupons_update = '1';
						$coupons_delete = '1';
						
						$notifications_view = '1';
						$notifications_send = '1';;
						
						$orders_view = '1';
						$orders_confirm = '1';
						
						$shipping_methods_view = '1';
						$shipping_methods_update = '1';
						
						$payment_methods_view = '1';
						$payment_methods_update = '1';
						
						$reports_view = '1';
						
						$website_setting_view = '1';
						$website_setting_update = '1';
						
						$application_setting_view = '1';
						$application_setting_update = '1';			
						
						$general_setting_view = '1';
						$general_setting_update = '1';
						
						$manage_admins_view = '1';			
						$manage_admins_create = '1';
						$manage_admins_update = '1';
						$manage_admins_delete = '1';
						
						$language_view = '1';
						$language_create = '1';
						$language_update = '1';
						$language_delete = '1';	
							
						$profile_view = '1';
						$profile_update = '1';
						
						$admintype_view = '1';
						$admintype_create = '1';
						$admintype_update = '1';
						$admintype_delete = '1';
						$manage_admins_role = '1';
					}		
					
				}else{
					$role = '';
				}
				
				session(['dashboard_view' => $dashboard_view]);
					
				session(['manufacturer_view' => $manufacturer_view]);
				session(['manufacturer_create' => $manufacturer_create]);
				session(['manufacturer_update' => $manufacturer_update]);
				session(['manufacturer_delete' => $manufacturer_delete]);
				
				session(['categories_view' => $categories_view]);
				session(['categories_create' => $categories_create]);
				session(['categories_update' => $categories_update]);
				session(['categories_delete' => $categories_delete]);
				
				session(['products_view' => $products_view]);
				session(['products_create' => $products_create]);
				session(['products_update' => $products_update]);
				session(['products_delete' => $products_delete]);
				
				session(['news_view' => $news_view]);
				session(['news_create' => $news_create]);
				session(['news_update' => $news_update]);
				session(['news_delete' => $news_delete]);
				
				session(['customers_view' => $customers_view]);
				session(['customers_create' => $customers_create]);
				session(['customers_update' => $customers_update]);
				session(['customers_delete' => $customers_delete]);		
				
				session(['tax_location_view' => $tax_location_view]);
				session(['tax_location_create' => $tax_location_create]);
				session(['tax_location_update' => $tax_location_update]);
				session(['tax_location_delete' => $tax_location_delete]);	
				
				session(['coupons_view' => $coupons_view]);
				session(['coupons_create' => $coupons_create]);
				session(['coupons_update' => $coupons_update]);
				session(['coupons_delete' => $coupons_delete]);		
				
				session(['notifications_view' => $notifications_view]);
				session(['notifications_send' => $notifications_send]);
				
				session(['orders_view' => $orders_view]);
				session(['orders_confirm' => $orders_confirm]);		
				
				session(['shipping_methods_view' => $shipping_methods_view]);
				session(['shipping_methods_update' => $shipping_methods_update]);	
				
				session(['payment_methods_view' => $payment_methods_view]);
				session(['payment_methods_update' => $payment_methods_update]);
						
				session(['reports_view' => $reports_view]);
				
				session(['website_setting_view' => $website_setting_view]);			
				session(['website_setting_update' => $website_setting_update]);			
				
				session(['application_setting_view' => $application_setting_view]);			
				session(['application_setting_update' => $application_setting_update]);	
				
				session(['general_setting_view' => $general_setting_view]);			
				session(['general_setting_update' => $general_setting_update]);	
				
				session(['manage_admins_view' => $manage_admins_view]);			
				session(['manage_admins_create' => $manage_admins_create]);	
				session(['manage_admins_update' => $manage_admins_update]);			
				session(['manage_admins_delete' => $manage_admins_delete]);	
				
				session(['language_view' => $language_view]);			
				session(['language_create' => $language_create]);	
				session(['language_update' => $language_update]);			
				session(['language_delete' => $language_delete]);	
				
				session(['profile_view' => $profile_view]);			
				session(['profile_update' => $profile_update]);	
				
				session(['admintype_view' => $admintype_view]);			
				session(['admintype_create' => $admintype_create]);
				session(['admintype_update' => $admintype_update]);			
				session(['admintype_delete' => $admintype_delete]);
				session(['manage_admins_role' => $manage_admins_role]);					
				
				$categories_id = '';
				//admin category role
				if(auth()->guard('admin')->user()->adminType != '1'){
					$categories_role = DB::table('categories_role')->where('admin_id', auth()->guard('admin')->user()->myid)->get();
					if(!empty($categories_role) and count($categories_role)>0){
						$categories_id = $categories_role[0]->categories_ids;
					}else{
						$categories_id = '';
					}
				}
				
				session(['categories_id' => $categories_id]);	
				return redirect()->intended('admin/dashboard/this_month')->with('administrators', $administrators);
			}else{
				return redirect('admin/login')->with('loginError',Lang::get("labels.EmailPasswordIncorrectText"));
			}
			
		}
		
	}
	
	
	//logout
	public function logout(){
		Auth::guard('admin')->logout();
		return redirect()->intended('admin/login');
	}
	
	//admin profile
	public function adminProfile(Request $request){
		//check permission
		if(session('profile_view')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		$title = array('pageTitle' => Lang::get("labels.Profile"));		
		
		$result = array();
		
		$countries = DB::table('countries')->get();
		$zones = DB::table('zones')->where('zone_country_id', '=', auth()->guard('admin')->user()->country)->get();
		
		$result['countries'] = $countries;
		$result['zones'] = $zones;
		
		return view("admin.adminProfile",$title)->with('result', $result);
		}
	}
	
	//updateProfile
	public function updateProfile(Request $request){
		
		if(session('profile_update')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
			
		$updated_at	= date('y-m-d h:i:s');
		
		$myVar = new AdminSiteSettingController();
		$languages = $myVar->getLanguages();		
		$extensions = $myVar->imageType();
		
		if($request->hasFile('newImage') and in_array($request->newImage->extension(), $extensions)){
			$image = $request->newImage;
			$fileName = time().'.'.$image->getClientOriginalName();
			$image->move('resources/views/admin/images/admin_profile/', $fileName);
			$uploadImage = 'resources/views/admin/images/admin_profile/'.$fileName; 
		}	else{
			$uploadImage = $request->oldImage;
		}	
		
		$orders_status = DB::table('administrators')->where('myid','=', auth()->guard('admin')->user()->myid)->update([
				'user_name'		=>	$request->user_name,
				'first_name'	=>	$request->first_name,
				'last_name'		=>	$request->last_name,
				'address'		=>	$request->address,
				'city'			=>	$request->city,
				'state'			=>	$request->state,
				'zip'			=>	$request->zip,
				'country'		=>	$request->country,
				'phone'			=>	$request->phone,
				'image'			=>	$uploadImage,
				'updated_at'	=>	$updated_at
				]);
		
		$message = Lang::get("labels.ProfileUpdateMessage");
		return redirect()->back()->withErrors([$message]);
		}
	}
	
	//updateProfile
	public function updateAdminPassword(Request $request){
		if(session('profile_update')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		$orders_status = DB::table('administrators')->where('myid','=', auth()->guard('admin')->user()->myid)->update([
				'password'		=>	Hash::make($request->password)
				]);
		
		$message = Lang::get("labels.PasswordUpdateMessage");
		return redirect()->back()->withErrors([$message]);
		}
	}
	
	//admins
	public function admins(Request $request){
		if(session('manage_admins_view')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
			
		$title = array('pageTitle' => Lang::get("labels.ListingCustomers"));
		$language_id            				=   '1';			
		
		$result = array();
		$message = array();
		$errorMessage = array();
		
		$admins = DB::table('administrators')
			->leftJoin('countries','countries.countries_id','=', 'administrators.country')
			->leftJoin('zones','zones.zone_id','=', 'administrators.state')
			->leftJoin('admin_types','admin_types.admin_type_id','=','administrators.adminType')
			->select('administrators.*', 'countries.*', 'zones.*','admin_types.*')
			->where('email','!=','vectorcoder@gmail.com')
			->where('adminType','!=','1')
			->paginate(50);
			
				
		$result['message'] = $message;
		$result['errorMessage'] = $errorMessage;
		$result['admins'] = $admins;
		
		return view("admin.admins",$title)->with('result', $result);
		}
	}
	
	//add admins
	public function addadmins(Request $request){
		
		$title = array('pageTitle' => Lang::get("labels.addadmin"));	
		
		$result = array();
		$message = array();
		$errorMessage = array();
		
		//get function from ManufacturerController controller
		$myVar = new AddressController();
		$result['countries'] = $myVar->getAllCountries();
		
		$adminTypes = DB::table('admin_types')->where('isActive', 1)->where('admin_type_id','!=','1')->get();
		$result['adminTypes'] = $adminTypes;
		
		return view("admin.addadmins",$title)->with('result', $result);
		
	}

	//addnewadmin
	public function addnewadmin(Request $request){ 
		if(session('manage_admins_create')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		//get function from other controller
		$myVar = new AdminSiteSettingController();	
		$extensions = $myVar->imageType();			
		
		$result = array();
		$message = array();
		$errorMessage = array();
		
		//check email already exists
		$existEmail = DB::table('administrators')->where('email', '=', $request->email)->get();
		if(count($existEmail)>0){
			$errorMessage = Lang::get("labels.Email address already exist");
			return redirect()->back()->with('errorMessage', $errorMessage);
		}else{
			if($request->hasFile('newImage') and in_array($request->newImage->extension(), $extensions)){
				$image = $request->newImage;
				$fileName = time().'.'.$image->getClientOriginalName();
				$image->move('resources/views/admin/images/admin_profile/', $fileName);
				$uploadImage = 'resources/views/admin/images/admin_profile/'.$fileName; 
			}	else{
				$uploadImage = '';
			}		
			
			$customers_id = DB::table('administrators')->insertGetId([
						'user_name'		 		    =>   $request->first_name.'_'.$request->last_name.time(),
						'first_name'		 		=>   $request->first_name,
						'last_name'			 		=>   $request->last_name,
						'phone'	 					=>	 $request->phone,
						'address'   				=>   $request->address,
						'city'		   				=>   $request->city,
						'state'		   				=>   $request->state,
						'address'   				=>   $request->address,
						'country'		   			=>   $request->country,
						'zip'   					=>   $request->zip,
						'email'	 					=>   $request->email,
						'password'		 			=>   Hash::make($request->password),
						'isActive'		 	 		=>   $request->isActive,
						'image'	 					=>	 $uploadImage,
						'adminType'					=>	 $request->adminType
						]);
					
			
			$message = Lang::get("labels.New admin has been added successfully");
			return redirect()->back()->with('message', $message);
		}
		}
	}
	
	//editadmin
	public function editadmin(Request $request){
		
		$title = array('pageTitle' => Lang::get("labels.EditAdmin"));
		$myid        	 =   $request->id;			
		
		$result = array();
		$message = array();
		$errorMessage = array();
		
		//get function from other controller
		$myVar = new AddressController();
		$result['countries'] = $myVar->getAllCountries();
		
		$adminTypes = DB::table('admin_types')->where('isActive', 1)->where('admin_type_id','!=','1')->get();
		$result['adminTypes'] = $adminTypes;
		
		$result['myid'] = $myid;
		
		$admins = DB::table('administrators')->where('myid','=', $myid)->get();
		$zones = DB::table('zones')->where('zone_country_id','=', $admins[0]->country)->get();
		
		if(count($zones)>0){
			$result['zones'] = $zones;
		}else{
			$zones = new \stdClass;
			$zones->zone_id = "others";
			$zones->zone_name = "Others";
			$result['zones'][0] = $zones;
		}
		
		
		$result['admins'] = $admins;
		return view("admin.editadmin",$title)->with('result', $result);
	}
	
	//update admin
	public function updateadmin(Request $request){
		if(session('manage_admins_update')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
			
		//get function from other controller
		$myVar = new AdminSiteSettingController();	
		$extensions = $myVar->imageType();			
		$myid = $request->myid;
		$result = array();
		$message = array();
		$errorMessage = array();
		
		//check email already exists
		$existEmail = DB::table('administrators')->where([['email','=',$request->email],['myid','!=',$myid]])->get();
		if(count($existEmail)>0){
			$errorMessage = Lang::get("labels.Email address already exist");
			return redirect()->back()->with('errorMessage', $errorMessage);
		}else{
			
			if($request->hasFile('newImage') and in_array($request->newImage->extension(), $extensions)){
				$image = $request->newImage;
				$fileName = time().'.'.$image->getClientOriginalName();
				$image->move('resources/views/admin/images/admin_profile/', $fileName);
				$uploadImage = 'resources/views/admin/images/admin_profile/'.$fileName; 
			}	else{
				$uploadImage = $request->oldImage;
			}		
			
			$admin_data = array(
				'first_name'		 		=>   $request->first_name,
				'last_name'			 		=>   $request->last_name,
				'phone'	 					=>	 $request->phone,
				'address'   				=>   $request->address,
				'city'		   				=>   $request->city,
				'state'		   				=>   $request->state,
				'address'   				=>   $request->address,
				'country'		   			=>   $request->country,
				'zip'   					=>   $request->zip,
				'email'	 					=>   $request->email,
				'isActive'		 	 		=>   $request->isActive,
				'image'	 					=>	 $uploadImage,
				'adminType'	 				=>	 $request->adminType,
			);
			
			if($request->changePassword == 'yes'){
				$admin_data['password'] = Hash::make($request->password);
			}
			
			$customers_id = DB::table('administrators')->where('myid', '=', $myid)->update($admin_data);
					
			
			$message = Lang::get("labels.Admin has been updated successfully");
			return redirect()->back()->with('message', $message);
		}
		}
	}
	
	//deleteProduct
	public function deleteadmin(Request $request){
		if(session('manage_admins_delete')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		$myid = $request->myid;
		
		DB::table('administrators')->where('myid','=', $myid)->delete();
		
		return redirect()->back()->withErrors([Lang::get("labels.DeleteAdminMessage")]);
		}
	}
	
	//manageroles
	public function manageroles(Request $request){
		if(session('admintype_view')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
			
		$title = array('pageTitle' => Lang::get("labels.manageroles"));
		$language_id            				=   '1';			
		
		$result = array();
		$message = array();
		$errorMessage = array();
		
		$adminTypes = DB::table('admin_types')->where('admin_type_id','!=',1)->paginate(50);			
				
		$result['message'] = $message;
		$result['errorMessage'] = $errorMessage;
		$result['adminTypes'] = $adminTypes;
		
		return view("admin.manageroles",$title)->with('result', $result);
		}
	}
	
	
	//add admins type
	public function addadmintype(Request $request){
		$title = array('pageTitle' => Lang::get("labels.addadmintype"));	
		
		$result = array();
		$message = array();
		$errorMessage = array();
		
		//get function from ManufacturerController controller
		$myVar = new AddressController();
		$result['countries'] = $myVar->getAllCountries();
		
		$adminTypes = DB::table('admin_types')->where('isActive', 1)->get();
		$result['adminTypes'] = $adminTypes;
		
		return view("admin.addadmintype",$title)->with('result', $result);
	}
	
	//addnewtype
	public function addnewtype(Request $request){
		if(session('admintype_create')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{				
		$result = array();
		$message = array();
		$errorMessage = array();
		
		$customers_id = DB::table('admin_types')->insertGetId([
						'admin_type_name'	 		=>   $request->admin_type_name,
						'created_at'			 	=>   time(),
						'isActive'		 	 		=>   $request->isActive,
						]);
								
		$message = Lang::get("labels.Admin type has been added successfully");
		return redirect()->back()->with('message', $message);	
		}
	}
	
	
	//editadmintype
	public function editadmintype(Request $request){
		$title = array('pageTitle' => Lang::get("labels.EditAdminType"));
		$admin_type_id        	 =   $request->id;			
		
		$result = array();
		
		$result['admin_type_id'] = $admin_type_id;
		
		$admin_types = DB::table('admin_types')->where('admin_type_id','=', $admin_type_id)->get();
		
		$result['admin_types'] = $admin_types;
		return view("admin.editadmintype",$title)->with('result', $result);
	}

	//updatetype
	public function updatetype(Request $request){
		if(session('admintype_update')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{					
		$result = array();
		$message = array();
		$errorMessage = array();
		
		$customers_id = DB::table('admin_types')->where('admin_type_id',$request->admin_type_id)->update([
						'admin_type_name'	 		=>   $request->admin_type_name,
						'updated_at'			 	=>   time(),
						'isActive'		 	 		=>   $request->isActive,
						]);
					
			
		$message = Lang::get("labels.Admin type has been updated successfully");
		return redirect()->back()->with('message', $message);
		}
	}
	
	
	//deleteProduct
	public function deleteadmintype(Request $request){
		if(session('admintype_delete')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
		$admin_type_id = $request->admin_type_id;
		
		DB::table('admin_types')->where('admin_type_id','=', $admin_type_id)->delete();
		
		return redirect()->back()->withErrors([Lang::get("labels.DeleteAdminTypeMessage")]);
		}
	}
	
	//managerole
	public function addrole(Request $request){
		if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
		
		$title = array('pageTitle' => Lang::get("labels.EditAdminType"));
		$result = array();
		$admin_type_id = $request->id;
		$result['admin_type_id'] = $admin_type_id;
		
		$adminType = DB::table('admin_types')->where('admin_type_id',$admin_type_id)->get();
		$result['adminType'] = $adminType;
		
		$roles = DB::table('manage_role')->where('admin_type_id','=', $admin_type_id)->get();
		
		if(count($roles)>0){		
			$dashboard_view = $roles[0]->dashboard_view;	
			
			$manufacturer_view = $roles[0]->manufacturer_view;
			$manufacturer_create = $roles[0]->manufacturer_create;
			$manufacturer_update = $roles[0]->manufacturer_update;
			$manufacturer_delete = $roles[0]->manufacturer_delete;
			
			$categories_view   = $roles[0]->categories_view;	
			$categories_create = $roles[0]->categories_create;
			$categories_update = $roles[0]->categories_update;
			$categories_delete = $roles[0]->categories_delete;
				
			$products_view = $roles[0]->products_view;
			$products_create = $roles[0]->products_create;
			$products_update = $roles[0]->products_update;
			$products_delete = $roles[0]->products_delete;			
			
			$news_view   = $roles[0]->news_view;
			$news_create = $roles[0]->news_create;
			$news_update = $roles[0]->news_update;
			$news_delete = $roles[0]->news_delete;						
			
			$customers_view = $roles[0]->customers_view;
			$customers_create = $roles[0]->customers_create;
			$customers_update = $roles[0]->customers_update;
			$customers_delete = $roles[0]->customers_delete;			
			
			$tax_location_view = $roles[0]->tax_location_view;
			$tax_location_create = $roles[0]->tax_location_create;
			$tax_location_update = $roles[0]->tax_location_update;
			$tax_location_delete = $roles[0]->tax_location_delete;
			
			$coupons_view = $roles[0]->coupons_view;
			$coupons_create = $roles[0]->coupons_create;
			$coupons_update = $roles[0]->coupons_update;
			$coupons_delete = $roles[0]->coupons_delete;			
			
			$notifications_view = $roles[0]->notifications_view;
			$notifications_send = $roles[0]->notifications_send;
			
			$orders_view = $roles[0]->orders_view;
			$orders_confirm = $roles[0]->orders_confirm;			
			
			$shipping_methods_view = $roles[0]->shipping_methods_view;
			$shipping_methods_update = $roles[0]->shipping_methods_update;
			
			$payment_methods_view = $roles[0]->payment_methods_view;
			$payment_methods_update = $roles[0]->payment_methods_update;
			
			$reports_view = $roles[0]->reports_view;
			
			$website_setting_view = $roles[0]->website_setting_view;
			$website_setting_update = $roles[0]->website_setting_update;
			
			$application_setting_view = $roles[0]->application_setting_view;
			$application_setting_update = $roles[0]->application_setting_update;
			
			
			$general_setting_view = $roles[0]->general_setting_view;
			$general_setting_update = $roles[0]->general_setting_update;
			
			$manage_admins_view   = $roles[0]->manage_admins_view;
			$manage_admins_create = $roles[0]->manage_admins_create;
			$manage_admins_update = $roles[0]->manage_admins_update;
			$manage_admins_delete = $roles[0]->manage_admins_delete;
			
			
			$language_view = $roles[0]->language_view;
			$language_create = $roles[0]->language_create;
			$language_update = $roles[0]->language_update;
			$language_delete = $roles[0]->language_delete;
			
			$profile_view = $roles[0]->profile_view;
			$profile_update = $roles[0]->profile_update;
			
			$admintype_view = $roles[0]->admintype_view;
			$admintype_create = $roles[0]->admintype_create;
			$admintype_update = $roles[0]->admintype_update;
			$admintype_delete = $roles[0]->language_delete;
			$manage_admins_role = $roles[0]->manage_admins_role;
		
		}else{
			$dashboard_view = '0';
			
			$manufacturer_view = '0';			
			$manufacturer_create = '0';
			$manufacturer_update = '0';
			$manufacturer_delete = '0';
			
			$categories_view = '0';
			$categories_create = '0';
			$categories_update = '0';
			$categories_delete = '0';
			
			$products_view   = '0';	
			$products_create = '0';
			$products_update = '0';
			$products_delete = '0';
					
			$news_view = '0';	
			$news_create = '0';
			$news_update = '0';
			$news_delete = '0';
					
			$customers_view   = '0';
			$customers_create = '0';
			$customers_update = '0';
			$customers_delete = '0';
			
			$tax_location_view = '0';
			$tax_location_create = '0';
			$tax_location_update = '0';
			$tax_location_delete = '0';
			
			
			$coupons_view = '0';
			$coupons_create = '0';
			$coupons_update = '0';
			$coupons_delete = '0';
			
			$notifications_view = '0';
			$notifications_send = '0';
			
			$orders_view = '0';
			$orders_confirm = '0';
			
			$shipping_methods_view = '0';
			$shipping_methods_update = '0';
			
			$payment_methods_view = '0';
			$payment_methods_update = '0';
			
			$reports_view = '0';
			
			$website_setting_view = '0';
			$website_setting_update = '0';
			
			$application_setting_view = '0';
			$application_setting_update = '0';			
			
			$general_setting_view = '0';
			$general_setting_update = '0';
			
			$manage_admins_view = '0';			
			$manage_admins_create = '0';
			$manage_admins_update = '0';
			$manage_admins_delete = '0';
			
			$language_view = '0';
			$language_create = '0';
			$language_update = '0';
			$language_delete = '0';		
				
			$profile_view = '0';
			$profile_update = '0';
			
			$admintype_view = '0';
			$admintype_create = '0';
			$admintype_update = '0';
			$admintype_delete = '0';
			$manage_admins_role = '0';
		}
		
			
		$result2[0]['link_name'] = 'dashboard';
		$result2[0]['permissions'] = array('0'=>array('name'=>'dashboard_view','value'=>$dashboard_view));
		
		$result2[1]['link_name'] = 'manufacturer';
		$result2[1]['permissions'] = array(
					'0'=>array('name'=>'manufacturer_view','value'=>$manufacturer_view),
					'1'=>array('name'=>'manufacturer_create','value'=>$manufacturer_create),
					'2'=>array('name'=>'manufacturer_update','value'=>$manufacturer_update),
					'3'=>array('name'=>'manufacturer_delete','value'=>$manufacturer_delete)
					);
		
		$result2[2]['link_name'] = 'categories';
		$result2[2]['permissions'] = array(
					'0'=>array('name'=>'categories_view','value'=>$categories_view),
					'1'=>array('name'=>'categories_create','value'=>$categories_create),
					'2'=>array('name'=>'categories_update','value'=>$categories_update),
					'3'=>array('name'=>'categories_delete','value'=>$categories_delete)
					);
		
		$result2[3]['link_name'] = 'products';
		$result2[3]['permissions'] = array(
					'0'=>array('name'=>'products_view','value'=>$products_view),
					'1'=>array('name'=>'products_create','value'=>$products_create),
					'2'=>array('name'=>'products_update','value'=>$products_update),
					'3'=>array('name'=>'products_delete','value'=>$products_delete)
					);
		
		$result2[4]['link_name'] = 'news';
		$result2[4]['permissions'] = array(
					'0'=>array('name'=>'news_view','value'=>$news_view),
					'1'=>array('name'=>'news_create','value'=>$news_create),
					'2'=>array('name'=>'news_update','value'=>$news_update),
					'3'=>array('name'=>'news_delete','value'=>$news_delete)
					);
					
		$result2[5]['link_name'] = 'customers';
		$result2[5]['permissions'] = array(
					'0'=>array('name'=>'customers_view','value'=>$customers_view),
					'1'=>array('name'=>'customers_create','value'=>$customers_create),
					'2'=>array('name'=>'customers_update','value'=>$customers_update),
					'3'=>array('name'=>'customers_delete','value'=>$customers_delete)
					);	
							
		$result2[6]['link_name'] = 'tax_location';
		$result2[6]['permissions'] = array(
					'0'=>array('name'=>'tax_location_view','value'=>$tax_location_view),
					'1'=>array('name'=>'tax_location_create','value'=>$tax_location_create),
					'2'=>array('name'=>'tax_location_update','value'=>$tax_location_update),
					'3'=>array('name'=>'tax_location_delete','value'=>$tax_location_delete)
					);	
									
		$result2[7]['link_name'] = 'coupons';
		$result2[7]['permissions'] = array(
					'0'=>array('name'=>'coupons_view','value'=>$coupons_view),
					'1'=>array('name'=>'coupons_create','value'=>$coupons_create),
					'2'=>array('name'=>'coupons_update','value'=>$coupons_update),
					'3'=>array('name'=>'coupons_delete','value'=>$coupons_delete)
					);
												
		$result2[8]['link_name'] = 'notifications';
		$result2[8]['permissions'] = array(
					'0'=>array('name'=>'notifications_view','value'=>$notifications_view),
					'1'=>array('name'=>'notifications_send','value'=>$notifications_send)
					);
															
		$result2[9]['link_name'] = 'orders';
		$result2[9]['permissions'] = array(
					'0'=>array('name'=>'orders_view','value'=>$orders_view),
					'1'=>array('name'=>'orders_confirm','value'=>$orders_confirm)
					);
																		
		$result2[10]['link_name'] = 'shipping_methods';
		$result2[10]['permissions'] = array(
					'0'=>array('name'=>'shipping_methods_view','value'=>$shipping_methods_view),
					'1'=>array('name'=>'shipping_methods_update','value'=>$shipping_methods_update)
					);					
																					
		$result2[11]['link_name'] = 'payment_methods';
		$result2[11]['permissions'] = array(
					'0'=>array('name'=>'payment_methods_view','value'=>$payment_methods_view),
					'1'=>array('name'=>'payment_methods_update','value'=>$payment_methods_update)
					);				
																					
		$result2[12]['link_name'] = 'reports';
		$result2[12]['permissions'] = array('0'=>array('name'=>'reports_view','value'=>$reports_view));					
																					
		$result2[13]['link_name'] = 'website_setting';
		$result2[13]['permissions'] = array(
					'0'=>array('name'=>'website_setting_view','value'=>$website_setting_view),
					'1'=>array('name'=>'website_setting_update','value'=>$website_setting_update)
					);				
																					
		$result2[14]['link_name'] = 'application_setting';
		$result2[14]['permissions'] = array(
					'0'=>array('name'=>'application_setting_view','value'=>$application_setting_view),
					'1'=>array('name'=>'application_setting_update','value'=>$application_setting_update)
					);			
																					
		$result2[15]['link_name'] = 'general_setting';
		$result2[15]['permissions'] = array(
					'0'=>array('name'=>'general_setting_view','value'=>$general_setting_view),
					'1'=>array('name'=>'general_setting_update','value'=>$general_setting_update)
					);	
									
		$result2[16]['link_name'] = 'manage_admins';
		$result2[16]['permissions'] = array(
					'0'=>array('name'=>'manage_admins_view','value'=>$manage_admins_view),
					'1'=>array('name'=>'manage_admins_create','value'=>$manage_admins_create),
					'2'=>array('name'=>'manage_admins_update','value'=>$manage_admins_update),
					'3'=>array('name'=>'manage_admins_delete','value'=>$manage_admins_delete)
					);
									
		$result2[17]['link_name'] = 'language';
		$result2[17]['permissions'] = array(
					'0'=>array('name'=>'language_view','value'=>$language_view),
					'1'=>array('name'=>'language_create','value'=>$language_create),
					'2'=>array('name'=>'language_update','value'=>$language_update),
					'3'=>array('name'=>'language_delete','value'=>$language_delete)
					);	
																					
		$result2[18]['link_name'] = 'profile';
		$result2[18]['permissions'] = array(
					'0'=>array('name'=>'profile_view','value'=>$profile_view),
					'1'=>array('name'=>'profile_update','value'=>$profile_update)
					);		
					
		
		$result2[19]['link_name'] = 'Admin Types';
		$result2[19]['permissions'] = array(
					'0'=>array('name'=>'admintype_view','value'=>$admintype_view),
					'1'=>array('name'=>'admintype_create','value'=>$admintype_create),
					'2'=>array('name'=>'admintype_update','value'=>$admintype_update),
					'3'=>array('name'=>'admintype_delete','value'=>$admintype_delete),
					'4'=>array('name'=>'manage_admins_role','value'=>$manage_admins_role)
					);	
		
		
		$result['data'] = $result2;
		return view("admin.addrole",$title)->with('result', $result);
		}
	}
	
	//addnewroles
	public function addnewroles(Request $request){
		if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
		
		$admin_type_id = $request->admin_type_id;
		DB::table('manage_role')->where('admin_type_id',$admin_type_id)->delete();
		
		$roles = DB::table('manage_role')->where('admin_type_id',$request->admin_type_id)->insert([
						'admin_type_id'			=>	 $request->admin_type_id,
						'dashboard_view'=>$request->dashboard_view,
									
						'manufacturer_view' => $request->manufacturer_view,
						'manufacturer_create' => $request->manufacturer_create,
						'manufacturer_update' => $request->manufacturer_update,
						'manufacturer_delete' => $request->manufacturer_delete,
						
						'categories_view' => $request->categories_view,
						'categories_create' => $request->categories_create,
						'categories_update' => $request->categories_update,
						'categories_delete' => $request->categories_delete,
						
						'products_view' => $request->products_view,
						'products_create' => $request->products_create,
						'products_update' => $request->products_update,
						'products_delete' => $request->products_delete,
						
						'news_view' => $request->news_view,
						'news_create' => $request->news_create,
						'news_update' => $request->news_update,
						'news_delete' => $request->news_delete,
						
						'customers_view' => $request->customers_view,
						'customers_create' => $request->customers_create,
						'customers_update' => $request->customers_update,
						'customers_delete' => $request->customers_delete,		
						
						'tax_location_view' => $request->tax_location_view,
						'tax_location_create' => $request->tax_location_create,
						'tax_location_update' => $request->tax_location_update,
						'tax_location_delete' => $request->tax_location_delete,	
						
						'coupons_view' => $request->coupons_view,
						'coupons_create' => $request->coupons_create,
						'coupons_update' => $request->coupons_update,
						'coupons_delete' => $request->coupons_delete,		
						
						'notifications_view' => $request->notifications_view,
						'notifications_send' => $request->notifications_send,
						
						'orders_view' => $request->orders_view,
						'orders_confirm' => $request->orders_confirm,		
						
						'shipping_methods_view' => $request->shipping_methods_view,
						'shipping_methods_update' => $request->shipping_methods_update,	
						
						'payment_methods_view' => $request->payment_methods_view,
						'payment_methods_update' => $request->payment_methods_update,
								
						'reports_view' => $request->reports_view,
						
						'website_setting_view' => $request->website_setting_view,			
						'website_setting_update' => $request->website_setting_update,			
						
						'application_setting_view' => $request->application_setting_view,			
						'application_setting_update' => $request->application_setting_update,	
						
						'general_setting_view' => $request->general_setting_view,			
						'general_setting_update' => $request->general_setting_update,	
						
						'manage_admins_view' => $request->manage_admins_view,			
						'manage_admins_create' => $request->manage_admins_create,	
						'manage_admins_update' => $request->manage_admins_update,			
						'manage_admins_delete' => $request->manage_admins_delete,	
						
						'language_view' => $request->language_view,			
						'language_create' => $request->language_create,	
						'language_update' => $request->language_update,			
						'language_delete' => $request->language_delete,	
						
						'profile_view' => $request->profile_view,			
						'profile_update' => $request->profile_update,
						
						'admintype_view' => $request->admintype_view,			
						'admintype_create' => $request->admintype_create,	
						'admintype_update' => $request->admintype_update,			
						'admintype_delete' => $request->admintype_delete,			
						'manage_admins_role' => $request->manage_admins_role,		
						]);
						
		$message = Lang::get("labels.Roles has been added successfully");
		return redirect()->back()->with('message', $message);
	}
	}
	
	
	//managerole
	public function categoriesroles(Request $request){
		if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
		
			$title = array('pageTitle' => Lang::get("labels.CategoriesRoles"));
			$result = array();
			$language_id = 1;
			
			$categories_role = DB::table('administrators')->join('categories_role','categories_role.admin_id','=','administrators.myid')->where('administrators.adminType','!=','1')->get();
			
			$data = array();
			$index = 0;
			foreach($categories_role as $categories){				
				array_push($data,$categories);
				$cat_array = explode(',',$categories->categories_ids);
				$categories_descrtiption = DB::table('categories_description')->whereIn('categories_id', $cat_array)->where('language_id',$language_id)->get();
				$data[$index++]->description = $categories_descrtiption;
			}
			
			$result['data'] = $data;
			return view("admin.categoriesroles",$title)->with('result', $result);
		}
		
	}
	
	//addCategoriesRoles
	public function addCategoriesRoles(Request $request){
		if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
			$title = array('pageTitle' => Lang::get("labels.AddCategoriesRoles"));
			$result = array();
			$language_id = 1;
			$categories_role = DB::table('categories_role')->get();
			
			//get function from other controller
			$myVar = new AdminCategoriesController();
			$result['categories'] = $myVar->allCategories($language_id);			
			
			$result['admins'] = DB::table('administrators')->where('adminType','!=','1')->get();
			
			$result['data'] = $categories_role;
			return view("admin.addcategoriesroles",$title)->with('result', $result);
		}		
	}
	
	
	//addCategoriesRoles
	public function addNewCategoriesRoles(Request $request){

		if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
		
			$title = array('pageTitle' => Lang::get("labels.AddCategoriesRoles"));
			$result = array();
			
			$language_id = 1;
			
			$exist = DB::table('categories_role')->where('admin_id',$request->admin_id)->get();
			
			if(count($exist)>0){			
				return redirect()->back()->with('error', Lang::get("labels.AlreadyCategoryAssignToadmin"));
			}else{
				
				$categories = array();
				foreach($request->categories as $category){
					$categories[] = $category;
				}
				
				$categories = implode(',',$categories);
				$roles = DB::table('categories_role')->insert([
							'categories_ids'	=>	$categories,
							'admin_id'			=>	$request->admin_id,									
							]);
							
				return redirect()->back()->with('success', Lang::get("labels.CategoryRolesAddedSucceccfully"));
			}
		}		
	}
	
	//editCategoriesRoles
	public function editCategoriesRoles(Request $request){	
	if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{		
				
		$title = array('pageTitle' => Lang::get("labels.AddCategoriesRoles"));
		$result = array();
		$language_id = 1;
		
		//get function from other controller
		$myVar = new AdminCategoriesController();
		$result['categories'] = $myVar->allCategories($language_id);	
				
		$categories_role = DB::table('categories_role')->where('categories_role_id',$request->id)->get();
		
		$result['admins'] = DB::table('administrators')->where('adminType','!=','1')->get();
		
		$result['data'] = $categories_role;
		
		return view("admin.editcategoriesroles",$title)->with('result', $result);
		
		}
	}
	
	//updatecategoriesroles
	public function updatecategoriesroles(Request $request){

		if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{	
		
			$result = array();		
							
			$categories = array();
			foreach($request->categories as $category){
				$categories[] = $category;
			}
			print_r($request->admin_id);
			$categories = implode(',',$categories);
			
			$roles = DB::table('categories_role')->where('categories_role_id',$request->categories_role_id)->update([
						'categories_ids'	=>	$categories,								
						]);
						
			return redirect()->back()->with('success', Lang::get("labels.CategoryRolesUpdatedSucceccfully"));
		}		
	}
	
	
	//deleteCountry
	public function deletecategoriesroles(Request $request){
		if(session('application_setting_update')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
			DB::table('categories_role')->where('categories_role_id', $request->id)->delete();
			return redirect()->back()->withErrors([Lang::get("labels.AdminRemoveCategoryMessage")]);
		}
	}
	//UploadCSVFILE
	public function UploadProductCsvFile(){
	if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		    //print_r($_POST);die('hiiii');	
			$title = array('pageTitle' => Lang::get("labels.AddCategoriesRoles"));
			$result = array();
			$language_id = 1;
			$categories_role = DB::table('categories_role')->get();
			
			//get function from other controller
			$myVar = new AdminCategoriesController();
			$result['categories'] = $myVar->allCategories($language_id);			
			
			$result['admins'] = DB::table('administrators')->where('adminType','!=','1')->get();
			
			$result['data'] = $categories_role;
			return view("admin.uploadCSV",$title)->with('result', $result);
		}
    }
    //insert the data into DB
    public function UploadProductCsvFileIntoDB($filename = '', $delimiter = ','){
	if(session('manage_admins_role')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		    $filename = $_FILES['upload_products']['tmp_name'];
		    //print_r($filename);
		    //die('hiiii');	
			$title = array('pageTitle' => Lang::get("labels.AddCategoriesRoles"));
			    if (!file_exists($filename) || !is_readable($filename))
				return false;
				$header = null;
				$data = array();
				if (($handle = fopen($filename, 'r')) !== false)
				{
				while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
				{
				if (!$header)
				$header = $row;
				else
				$data[] =  $row;
				}
				fclose($handle);
				}
				$i=1;
				foreach($data as $datainfo) {
				$keys_val = array_keys($datainfo);
				//echo "<pre>";print_r($datainfo[12]);echo "</pre>";
				//echo "<pre>";print_r($datainfo[21]);echo "</pre>";
				//die('hiiiiiiiii');
				$get_all_options_info = array();
				for ($k=12; $k < 21; $k++) {
				if($k%2==0){
				if(!empty($datainfo[$k])){
				$options_name      = isset($datainfo[$k]) ? $datainfo[$k] : '';
				//echo $i.'=='.$options_name.'<br/>';
                $options_name_slug = str_replace(' ','-', strtolower(trim($options_name)));
                $options_value     = isset($datainfo[$k+1]) ? $datainfo[$k+1] : '';
                $options_value_slug= str_replace(' ','-',strtolower(trim($options_value)));
                if(!empty($options_name)){
                // echo "=====================================================<br/>";	
			   	$user_favorites = DB::table('products_options')
								    ->where('products_options_name','=',$options_name)->first();
				//echo "<pre>";print_r($user_favorites);echo "</pre>";
				if(is_null($user_favorites)){
				//echo 'ayaaaaa1111'.'<br/>';
				DB::table('products_options')->updateOrInsert(['products_options_name' => $options_name]);
			    $option_id =  DB::getPdo()->lastInsertId();
			    $get_all_options_info['options_id'][]= $option_id;
			    DB::table('products_options_descriptions')->updateOrInsert(['language_id'=>1,'options_name'=>$options_name_slug,'products_options_id'=>$option_id]);
			    $option_id2  =  DB::getPdo()->lastInsertId();
			    DB::table('products_options_values')->updateOrInsert(['products_options_id'=>$option_id,'products_options_values_name'=>$options_value]);
			     $option_id3 =  DB::getPdo()->lastInsertId();
			     $get_all_options_info['options_values_id'][]= $option_id3;
			     DB::table('products_options_values_descriptions')->updateOrInsert(['language_id'=>1,'options_values_name'=>$options_value_slug,'products_options_values_id'=>$option_id3]);
			     $option_id4 =  DB::getPdo()->lastInsertId();
				} else {
				//echo 'ayaaaaa2222'.'<br/>';
				$option_id = $user_favorites->products_options_id;
				$get_all_options_info['options_id'][]= $option_id;
				$check_options_exist = DB::table('products_options_values')
								    ->where('products_options_values_name', '=',$options_value)->first();
				if(is_null($check_options_exist)){  //check option exist conditions
				//echo '1111'.$options_value;
				DB::table('products_options_values')->updateOrInsert(['products_options_id'=>$option_id,'products_options_values_name'=>$options_value]);
				$option_id3 =  DB::getPdo()->lastInsertId();
				$get_all_options_info['options_values_id'][]= $option_id3;
				DB::table('products_options_values_descriptions')->updateOrInsert(['language_id'=>1,'options_values_name'=>$options_value_slug,'products_options_values_id'=>$option_id3]);
				$option_id4 =  DB::getPdo()->lastInsertId();
				}else{
					//echo 'ayaaaaa3333'.'<br/>';
					$products_options_values = DB::table('products_options_values')->where('products_options_values_name',$options_value)->first();
						$get_all_options_info['options_values_id'][]= $products_options_values->products_options_values_id;
					 }
				} //else end
				} // not empty contion end
				} // if not empty end
			    } // if %2 condition end
				} //for loop end
				$allCategories = array();
				// //echo "<pre>";print_r($datainfo);echo "</pre>";
				$default_image  ='resources/assets/images/category_images/1502285654.boys.jpg';
				$default_icon   ='resources/assets/images/category_icons/1502103958.female-silhouette.png';
				$parent_id      ='';
				$sort_order     ='';
				$date_added     = date('Y-m-d H:i:s');
				$last_modified  = date('Y-m-d H:i:s');
				//subcategories
				$result = Categories_Description::where('categories_name', $datainfo[0])->count();
		        if($result==0){
				$categories_slug= str_replace(' ','-',$datainfo[0]);
				/*----------insert category-------------*/
				$insert_category= new Categories;
				$insert_category->categories_image = $default_image;
				$insert_category->categories_icon  = $default_icon;
				$insert_category->date_added       = $date_added;
				$insert_category->last_modified    = $last_modified;
				$insert_category->categories_slug  = $categories_slug;
				$insert_category->save();
				$parent_ids =  DB::getPdo()->lastInsertId();
				/*----------insert categories_description-------------*/
				$insert_description= new Categories_Description;
				$insert_description->categories_id    = $parent_ids;
				$insert_description->language_id      = 1;
				$insert_description->categories_name  = $datainfo[0];
				$insert_description->save();
				}else{
				$results = Categories_Description::where('categories_name', $datainfo[0])->get();
							foreach ($results as $result) {
							$parent_ids = $result->categories_id;
							}
				}
				//subcategories
				$allCategories['parent'] = $parent_ids;
				for ($j=1; $j<=5; $j++) {
				if(!empty($datainfo[$j])){
				$finalcategory = Categories_Description::where('categories_name', $datainfo[$j])->count();
				if($finalcategory==0){

										$categories_slug= str_replace(' ','-',trim($datainfo[$j]));
										$insert_child= new Categories;
										$insert_child->categories_image = $default_image;
										$insert_child->categories_icon  = $default_icon;
										$insert_child->parent_id        = $parent_ids;
										$insert_child->date_added       = $date_added;
										$insert_child->last_modified    = $last_modified;
										$insert_child->categories_slug  = $categories_slug;
										$insert_child->save();
										$child_ids =  DB::getPdo()->lastInsertId();
						Categories_Description:: updateOrInsert(['categories_name' => $datainfo[$j]],
			                                             ['categories_id' => $child_ids ]);
						$allCategories['child_'.$child_ids] = $child_ids;
				}else{
				$child_results = Categories_Description::where('categories_name', $datainfo[$j])->get();
							foreach ($child_results as $child_result) {
							$child_ids = $child_result->categories_id;
							$allCategories['child_'.$child_ids] = $child_ids;
							}
				}
			    }
				}
				//echo "<pre>";print_r($get_all_options_info);echo "</pre>";

				//product Entries
				$product_image="resources/assets/images/product_images/1562567366.IMG_1588.JPG";
                $remove_space = preg_replace('/\s+/',' ',$datainfo[6]);
                $title = str_replace(array( '\'', '"', ',' , ';', '<', '>'), '',$remove_space);
                $product_slug = str_replace(' ','-',strtolower($title));
                //$money = isset($datainfo2[12]) ? $datainfo2[12] : '0';
                if(isset($datainfo2[11])){
                $product_price= $datainfo[11];	
	            }else{
	            $product_price= $datainfo[11]!=''?$datainfo[11]:$datainfo[12];
	            }
	            $products_status= isset($datainfo[14]) ? $datainfo[14] : 0;
	            //echo $datainfo[15];
                //$product_price= $datainfo[12]!=''?$datainfo[12]: '$11.40';
                //echo $i.$product_price.'<br/>';
                //echo "<pre>";print_r($get_all_options_info);echo "</pre>";
                if(!empty($get_all_options_info)){
                $counts = count($get_all_options_info['options_id']);
                $producty_type = $counts>0 ? 1 :0;
                }else{
                 $producty_type = 0;
                }
                $int = preg_replace("/([^0-9\\.])/i", "", $product_price);
                $values = array('products_image' =>$product_image,'products_price'=>$int,'products_date_added' =>$date_added,'products_weight' =>1,'products_slug' =>$product_slug,'products_weight_unit' => 1,'products_type'=>$producty_type,'products_status' => $products_status,'products_tax_class_id' => 1);
                DB::table('products')->insert($values);
                $product_ids   =  DB::getPdo()->lastInsertId();
				$variation =0;
				if(!empty($get_all_options_info)){
				for ($v=0; $v < $counts; $v++) { 
				$optin_id  = $get_all_options_info['options_id'][$v];
				$optin_val = $get_all_options_info['options_values_id'][$v];
				$date      = strtotime($date_added);
				$variations= array('products_id' =>$product_ids,'options_id'=>$optin_id,'options_values_id' =>$optin_val,'options_values_price' =>0.00,'price_prefix' =>'+','is_default' => 0);
                DB::table('products_attributes')->insert($variations);
                //Add inventory of the Product
                $attribut_id      = DB::getPdo()->lastInsertId();
                $inventory_details= array('admin_id' =>1,'added_date'=>$date,'stock' =>5,'products_id' =>$product_ids,'purchase_price' =>0.00,'stock_type' =>'in');
                DB::table('inventory')->insert($inventory_details);
                $inventory_ref_id = DB::getPdo()->lastInsertId();
                $inventory_info   = array('inventory_ref_id' =>$inventory_ref_id,'products_id'=>$product_ids,'attribute_id' =>$attribut_id);
                DB::table('inventory_detail')->insert($inventory_info);
				}
			    }

                $products_name = isset($datainfo[6]) ? $datainfo[6]:'';
                $products_dscrp= isset($datainfo[8]) ? $datainfo[8]:'';
                $values2 = array('products_id' =>$product_ids,'products_name'=>$products_name,'products_description' =>$products_dscrp);
                DB::table('products_description')->insert($values2);
                //Assign product categories
                foreach ($allCategories as $productCategory) {
                $assign_category = new AssignCategory;
                $assign_category->products_id  = $product_ids;
                $assign_category->categories_id= $productCategory;
				$assign_category->save();
                }
                //echo $productCategory.'=='.$product_ids.'<br/>';
                
                // Price Tier entries
                $products_sku= isset($datainfo[9]) ? $datainfo[9]:'';
                $sku = $products_sku;
                $ids = $product_ids;
                $insert_price_tier= new TierPrice;
                $insert_price_tier->product_id  = $ids;
                $insert_price_tier->sku         = $sku;
                $insert_price_tier->price_tier1 = isset($datainfo[12])?preg_replace("/([^0-9\\.])/i", "", $datainfo[12]):'';
                $insert_price_tier->price_tier2 = isset($datainfo[41])?preg_replace("/([^0-9\\.])/i", "", $datainfo[41]):'';
                $insert_price_tier->price_tier3 = isset($datainfo[43])?preg_replace("/([^0-9\\.])/i", "", $datainfo[43]):'';
                $insert_price_tier->price_tier4 = isset($datainfo[45])?preg_replace("/([^0-9\\.])/i", "", $datainfo[45]):'';
                $insert_price_tier->price_tier5 = isset($datainfo[47])?preg_replace("/([^0-9\\.])/i", "", $datainfo[47]):'';
                $insert_price_tier->price_tier6 = isset($datainfo[49])?preg_replace("/([^0-9\\.])/i", "", $datainfo[49]):'';
                $insert_price_tier->price_tier7 = isset($datainfo[51])?preg_replace("/([^0-9\\.])/i", "", $datainfo[51]):'';
                $insert_price_tier->price_tier8 = isset($datainfo[53])?preg_replace("/([^0-9\\.])/i", "", $datainfo[53]):'';
                $insert_price_tier->price_tier9 = isset($datainfo[55])?preg_replace("/([^0-9\\.])/i", "", $datainfo[55]):'';
                $insert_price_tier->price_tier10= isset($datainfo[57])?preg_replace("/([^0-9\\.])/i", "", $datainfo[57]):'';
                $insert_price_tier->save();
				$last_id =  DB::getPdo()->lastInsertId();
				// Price Tier entries
				$sale_price= isset($datainfo[13])?$datainfo[13]: 0;
                $sale      = preg_replace("/([^0-9\\.])/i", "", $sale_price);
                $insert_price_tier_sale = new SaleTierPrice;
                $insert_price_tier_sale->product_id       = $ids;
                $insert_price_tier_sale->sku              = $sku;
                $insert_price_tier_sale->price_tier_sale1 = $sale;
                $insert_price_tier_sale->price_tier_sale2 = isset($datainfo[40])?preg_replace("/([^0-9\\.])/i", "", $datainfo[40]):'';
                $insert_price_tier_sale->price_tier_sale3 = isset($datainfo[42])?preg_replace("/([^0-9\\.])/i", "", $datainfo[42]):'';
                $insert_price_tier_sale->price_tier_sale4 = isset($datainfo[44])?preg_replace("/([^0-9\\.])/i", "", $datainfo[44]):'';
                $insert_price_tier_sale->price_tier_sale5 = isset($datainfo[46])?preg_replace("/([^0-9\\.])/i", "", $datainfo[46]):'';
                $insert_price_tier_sale->price_tier_sale6 = isset($datainfo[48])?preg_replace("/([^0-9\\.])/i", "", $datainfo[48]):'';
                $insert_price_tier_sale->price_tier_sale7 = isset($datainfo[50])?preg_replace("/([^0-9\\.])/i", "", $datainfo[50]):'';
                $insert_price_tier_sale->price_tier_sale8 = isset($datainfo[52])?preg_replace("/([^0-9\\.])/i", "", $datainfo[52]):'';
                $insert_price_tier_sale->price_tier_sale9 = isset($datainfo[54])?preg_replace("/([^0-9\\.])/i", "", $datainfo[54]):'';
                $insert_price_tier_sale->price_tier_sale10= isset($datainfo[56])?preg_replace("/([^0-9\\.])/i", "", $datainfo[56]):'';
                $insert_price_tier_sale->save();
                echo $i.'<br/>';
                $i++;
				//break;
			    } // foreach loop end
				die('helloooooooooooooo');
                //}
			return redirect()->back()->with('success', Lang::get("labels.CategoryRolesAddedSucceccfully"));
		}
    }
}
