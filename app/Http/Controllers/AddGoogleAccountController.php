<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;


class AddGoogleAccountController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');

    }
	
	 /**
     * Add Google Account.
     *
     * @param  Request  $request
     * @return Response
     */
	 public function index(Request $request)
    {
        
		$user = new AdWordsUser(config_path('googleads/adwords.ini'));
		$user->SetClientLibraryUserAgent('Admetric'); 
		$user->SetUserAgent('Admetric'); 
		$user->SetDeveloperToken('2DuyW5Ec19q8U2wFiT_pJw');
		$IsSandBoxMode = strpos($user->GetDeveloperToken(), "++USD") !== false && strpos($user->GetDeveloperToken(), "@") !== false;
		$user->SetDefaultServer($IsSandBoxMode ? "https://adwords-sandbox.google.com" : "https://adwords.google.com");
		print_r($user);exit; 
    }
}
