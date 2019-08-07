<?php
/*
Project Name: IonicEcommerce
Project URI: http://ionicecommerce.com
Author: VectorCoder Team
Author URI: http://vectorcoder.com/
*/
namespace App\Http\Controllers\Web;
//use Mail;
//validator is builtin class in laravel
use Validator;

use DB;
//for password encryption or hash protected
use Hash;

//for authenitcate login data
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;

//for requesting a value 
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lang;
//for Carbon a value 
use Carbon;

//email
use Illuminate\Support\Facades\Mail;
use Session;
class GalleryConstantController extends DataController
{
	
// for popup gallery
	public function PopupGallery(Request $request){
		$title = array('pageTitle' => Lang::get("website.gallery"));
		$result = array();			
		$result['commonContent'] = $this->commonContent();
		$gallery = DB::table('constant_gallery')->orderBy('constant_gallery.gallery_id','ASC')->get();
		$result['gallery_data'] = $gallery;
		return view("gallery", $title)->with('result', $result);
	}

}