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
use DB;
//for password encryption or hash protected

use Hash;
use App\Administrator;

//for authenitcate login data
use Auth;
//for redirect
use Illuminate\Support\Facades\Redirect;


//for requesting a value 
use Illuminate\Http\Request;

class CustomerTierLabel extends Controller
{
// List All Tier labels
	public function Alltierlistings(Request $request){
	if(session('customers_view')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
			
		$title = array('pageTitle' => Lang::get("labels.ListingTier"));
		$language_id            				=   '1';			
		
		$customerData = array();
		$message = array();
		$errorMessage = array();
		
		$product_user_tier = DB::table('product_user_tier')
			->select('product_user_tier.*')
			->orderBy('product_user_tier.id','ASC')
			->paginate(50);
			
		$result = array();
		$index = 0;
		// foreach($customers as $customers_data){
		// 	array_push($result, $customers_data);
			
		// 	$devices = DB::table('devices')->where('customers_id','=',$customers_data->customers_id)->orderBy('register_date','DESC')->take(1)->get();
		// 	$result[$index]->devices = $devices;
		// 	$index++;
		// }
		
		$customerData['message'] = $message;
		$customerData['errorMessage'] = $errorMessage;
		$customerData['result'] = $product_user_tier;
		
		return view("admin.tierlabels",$title)->with('tierlables', $customerData);
		}	
	}
	//Edit the Pricing 
	public function Edittierlabelpercent(Request $request){
		if(session('customers_view')==0){
			print Lang::get("labels.You do not have to access this route");
		}else{
		$title = array('pageTitle' => Lang::get("labels.EditCustomer"));
		$language_id         =   '1';	
		$product_id        	 =   $request->id;			
		
		$customerData = array();
		$message = array();
		$errorMessage = array();
		//DB::table('product_user_tier')->where('customers_id', '=', $customers_id)->update(['is_seen' => 1 ]);
		
		$customers = DB::table('product_user_tier')->where('product_id','=', $product_id)->get();
		$customerData['message'] = $message;
		$customerData['errorMessage'] = $errorMessage;
		$customerData['customers'] = $customers;
		return view("admin.tierlabelsedit",$title)->with('productinfo', $customerData);
		}
	}
	//Update the pricing Details
	public function updatepricingtier(Request $request){		
		$language_id            		=   '1';			
		$product_id					    =	$request->product_id;
		
		$customerData = array();
		$message = array();
		$errorMessage = array();		
		
		$customer_data = array(
			'price_tier1'   		=>   $request->price_tier1,
			'price_tier2'		 	=>   $request->price_tier2,
			'price_tier3'		 	=>   $request->price_tier3,
			'price_tier4'	 		=>	 $request->price_tier4,
			'price_tier5'   		=>   $request->price_tier5,
			'price_tier6'	 		=>   $request->price_tier6,
			'price_tier7' 	        =>   $request->price_tier7,
			'price_tier8'	 		=>	 $request->price_tier8,
			'price_tier9'   	    =>   $request->price_tier9,
			'price_tier10'		 	=>   $request->price_tier10
		);
		
		//check email already exists
		DB::table('product_user_tier')->where('product_id', '=', $product_id)->update($customer_data);				 
				return redirect('admin/editpricing/'.$product_id);
	}
	// Delete the price Tier	
	public function DeletePriceTier(Request $request){	
		$product_id					=	$request->product_id;
		$customer_data = array(
			'price_tier1'   		=>   '',
			'price_tier2'		 	=>   '',
			'price_tier3'		 	=>   '',
			'price_tier4'	 		=>	 '',
			'price_tier5'   		=>   '',
			'price_tier6'	 		=>   '',
			'price_tier7' 	        =>   '',
			'price_tier8'	 		=>	 '',
			'price_tier9'   	    =>   '',
			'price_tier10'		 	=>   ''
		);
		
		//check email already exists
		DB::table('product_user_tier')->where('product_id', '=', $product_id)->update($customer_data);				 
				return redirect('admin/tierlistings');
		
		return redirect()->back()->withErrors([Lang::get("labels.DeleteCustomerMessage")]);
	}
}