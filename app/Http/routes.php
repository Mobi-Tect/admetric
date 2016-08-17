<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//define('SpecSeparatorStr', ',}/$^&');
Route::get('/', function () {
    return redirect()->guest('login');
});
Route::auth();
Route::get('/dashboard', 'DashboardController@index');
Route::get('/createDashboard', 'DashboardController@createDashboard');
Route::get('/newDashboard', 'DashboardController@newDashboard');
Route::post('/storeDashboard', 'DashboardController@storeDashboard');
Route::get('/setDashboard/{id}', 'DashboardController@setDashboard');
Route::get('/myDashboard', 'DashboardController@myDashboard');
Route::post('/storeNewDashboard', 'DashboardController@storeNewDashboard');
Route::get('/deleteDashboard/{id}', 'DashboardController@deleteDashboard');
Route::get('/editDashboard/{id}', 'DashboardController@editDashboard');
Route::post('/updateDashboard/{id}', 'DashboardController@updateDashboard');
//Route::get('/cronJob', 'DashboardController@cronJob');
Route::post('/accountName', 'MetricController@accountName');
Route::post('/addMetric', 'MetricController@addMetric');
Route::get('/createAccount', 'MetricController@createAccount');
Route::get('/childAccounts/{id}', 'MetricController@childAccounts');
Route::get('/getChildAccounts/{id}', 'MetricController@getChildAccounts');
Route::get('/getCpc/{id}/{ac}/{f}', 'MetricController@childAccountsCpc');
Route::get('/getCampaigns/{id}/{ac}', 'MetricController@getCampaigns');
Route::get('/getCampaignsString/{id}/{ac}/{f}', 'MetricController@getCampaignsString');
Route::post('/metricSorting', 'MetricController@metricSorting');
Route::get('/getAdGroups/{id}/{ac}/{adg}', 'MetricController@getAdGroups');
Route::get('/getAds/{id}/{ac}/{c}/{g}/', 'MetricController@getAds');
Route::get('/getKeywords/{id}/{ac}/{c}/{g}/', 'MetricController@getKeywords');
Route::get('/metricDelete/{id}', 'MetricController@metricDelete');
Route::post('/saveSettings', 'MetricController@saveSettings');
Route::post('/getSettingsCPC', 'MetricController@getSettingsCPC');
Route::get('/saveAccount/{id}/{ac}', 'MetricController@saveAccount');
Route::post('/progressBar', 'MetricController@getSettings');
Route::post('/persontage', 'MetricController@getPersontage');
Route::post('/masterSetting', 'MetricController@masterSetting');
Route::post('/progressBar2', 'MetricController@getSettings2');

Route::get('/testdata/{id}/', 'DashboardController@metricDataRequesr');
Route::get('/cronjob', 'CronjobController@index');


Route::get('/plans', 'MembershipController@index');
Route::post('/upgrade', 'MembershipController@upgrade');
Route::post('/charge', 'MembershipController@charge');
/*
|--------------------------------------------------------------------------
| Authenticate And Add Google MCC Account
|--------------------------------------------------------------------------
*/

Route::get('adwords', function () {
    $user = new AdWordsUser(config_path('googleads/adwords.ini'));
	$user->SetClientLibraryUserAgent('Admetric'); 
	$user->SetUserAgent('Admetric'); 
    $user->SetDeveloperToken('2DuyW5Ec19q8U2wFiT_pJw');
	$IsSandBoxMode = strpos($user->GetDeveloperToken(), "++USD") !== false && strpos($user->GetDeveloperToken(), "@") !== false;
	$user->SetDefaultServer($IsSandBoxMode ? "https://adwords-sandbox.google.com" : "https://adwords.google.com");
	$RedirectURL = url('adwords');
	if ( isset($_GET['code']) )     
  		{ 
		//echo 'here got code';
		$user->SetOAuth2Info( array('client_id' => '947744393545-er9au06qtncan9nlkbdjt14sgu007vi0.apps.googleusercontent.com', 'client_secret' => 'aDLsbyvAofRObJl8NVtNZ7y1'));        

        $user->SetOAuth2Info( $user->GetOAuth2Handler()->GetAccessToken($user->GetOAuth2Info(), $_GET['code'], $RedirectURL));        
		$OneCSVStr = "";
		$AssociatedArray = $user->GetOAuth2Info();
		/*echo '<pre>';
		print_r($AssociatedArray);exit;*/
		session(['apiToken' => $AssociatedArray['access_token']]);
        foreach ($AssociatedArray as $Cur_key => $Cur_value)      
       		$OneCSVStr .= (strlen($OneCSVStr) > 0 ? SpecSeparatorStr : "").$Cur_key.SpecSeparatorStr.$Cur_value;
		$AccessTokenAsOneCSVStr = $OneCSVStr;
		session(['apiAccessToken' => $AccessTokenAsOneCSVStr]);
		return redirect('/createAccount');
		exit;	
	}else{   
		//echo 'not found code';
           

      $user->SetOAuth2Info(array('client_id' => '947744393545-er9au06qtncan9nlkbdjt14sgu007vi0.apps.googleusercontent.com', 'client_secret' => 'aDLsbyvAofRObJl8NVtNZ7y1'));            

      $authorizationUrl = $user->GetOAuth2Handler()->GetAuthorizationUrl($user->GetOAuth2Info(), $RedirectURL, true);       

      $authorizationUrl .= "&approval_prompt=force";   
      header("Location: ".$authorizationUrl);
	  exit;
  }
	
});


/*
|--------------------------------------------------------------------------
| Authenticate And Add Google MCC Account For new User
|--------------------------------------------------------------------------
*/
Route::get('connection', function () {
    $user = new AdWordsUser(config_path('googleads/adwords.ini'));
	$user->SetClientLibraryUserAgent('Admetric'); 
	$user->SetUserAgent('Admetric'); 
    $user->SetDeveloperToken('2DuyW5Ec19q8U2wFiT_pJw');
	$IsSandBoxMode = strpos($user->GetDeveloperToken(), "++USD") !== false && strpos($user->GetDeveloperToken(), "@") !== false;
	$user->SetDefaultServer($IsSandBoxMode ? "https://adwords-sandbox.google.com" : "https://adwords.google.com");
	$RedirectURL = url('connection');
	if ( isset($_GET['code']) )     
  		{ 
		//echo 'here got code';
		$user->SetOAuth2Info( array('client_id' => '947744393545-er9au06qtncan9nlkbdjt14sgu007vi0.apps.googleusercontent.com', 'client_secret' => 'aDLsbyvAofRObJl8NVtNZ7y1'));        

        $user->SetOAuth2Info( $user->GetOAuth2Handler()->GetAccessToken($user->GetOAuth2Info(), $_GET['code'], $RedirectURL));        
		$OneCSVStr = "";
		$AssociatedArray = $user->GetOAuth2Info();
		/*echo '<pre>';
		print_r($AssociatedArray);exit;*/
		session(['apiToken' => $AssociatedArray['access_token']]);
        foreach ($AssociatedArray as $Cur_key => $Cur_value)      
       		$OneCSVStr .= (strlen($OneCSVStr) > 0 ? SpecSeparatorStr : "").$Cur_key.SpecSeparatorStr.$Cur_value;
		$AccessTokenAsOneCSVStr = $OneCSVStr;
		session(['apiAccessToken' => $AccessTokenAsOneCSVStr]);
		return redirect('/createDashboard');
		exit;	
	}else{   
		//echo 'not found code';
           

      $user->SetOAuth2Info(array('client_id' => '947744393545-er9au06qtncan9nlkbdjt14sgu007vi0.apps.googleusercontent.com', 'client_secret' => 'aDLsbyvAofRObJl8NVtNZ7y1'));            

      $authorizationUrl = $user->GetOAuth2Handler()->GetAuthorizationUrl($user->GetOAuth2Info(), $RedirectURL, true);       

      $authorizationUrl .= "&approval_prompt=force";   
      header("Location: ".$authorizationUrl);
	  exit;
  }
	
    //print_r($user); // for debug only
    // other actions here
    //return view('adwords');
});

