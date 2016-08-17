<?php
namespace App\Http\Controllers;

require_once app_path(). '/g/examples/AdWords/v201601/init.php';
require_once app_path(). '/g/src/Google/Api/Ads/Common/Util/ChoiceUtils.php';
require_once app_path(). '/g/src/Google/Api/Ads/Common/Util/OgnlUtils.php';
//require_once app_path().'/g/src/Google/Api/Ads/AdWords/Util/v201601/ReportClasses.php';
//require_once app_path().'/g/src/Google/Api/Ads/AdWords/Util/v201601/ReportUtils.php';


use DB;
use App\Account;

use App\Metrics;

use App\Adgroups;

use App\Ads;

use App\Campaign;

use App\ChildsAccount;

use App\Keyword;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Repositories\AccountRepository;

use App\Repositories\MetricsRepository;

use App\Repositories\AdgroupsRepository;

use App\Repositories\AdsRepository;

use App\Repositories\CampaingnRepository;

use App\Repositories\KeywordRepository;

use App\Repositories\ChildAccountRepository;

use Illuminate\Routing\Redirector;

class MetricController extends DashboardController
{
    
	 /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $accounts;
	
	protected $metrics;
	
	protected $chlids;
	
	protected $campaign;
	
	protected $ads;
	
	protected $adgroup;
	
	protected $keyword;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
	
	
	public function __construct(Request $request ,AccountRepository $accounts,MetricsRepository $metrics,ChildAccountRepository $chlids,CampaingnRepository $campaign,AdsRepository $ads,AdgroupsRepository $adgroup,KeywordRepository $keyword,Redirector $redirect)
    {
        $this->middleware('auth');
		
		$userId = $request->user()->id;
		
		$check = checkPackage($userId);
		
		if(!$check){
			
			$redirect->to('logout')->send();
			
		}
		
		$this->accounts = $accounts;
		 
		$this->metrics = $metrics;
		
		$this->chlids = $chlids;
		
		$this->campaign = $campaign;
		
		$this->ads = $ads;
		
		$this->adgroup = $adgroup;
		
		$this->keyword = $keyword;

    }
	
	 /**
     * Add New Google MCC Account
     * And Metric Informatiom Into Database.
     * @param  Request  $request
     * @return Response
     */
	public function createAccount(Request $request)
    {
        $data = getRootAccount(session('apiAccessToken'));
		$result = Account::where(array('mccaccount_id'=>$data['customerId'],'user_id'=>$request->user()->id))->first();
		if(count($data)>0){
			if(empty($result)){
				$id = $request->user()->accounts()->create([
					'name' => $data['accountName'],
					'mccaccount_id' => $data['customerId'],
					'type' => session('apiVal'),
					'metric_id' => session('apiDiv'),
					'token' => session('apiAccessToken'),
					'created' => date('Y-m-d'),
				])->id;
				$request->user()->Metrics()->create([
					'type' => session('apiVal'),
					'metric_id' => session('apiDiv'),
					'metric_value' => session('apiDivType'),
					'board_id' => session('board'),
					'position' => 'left',
					'sort' => 0,
					'account_id' => $id,
				]);
				$request->session()->flash('msgs', 'Account Connect Successfully.');
			}else{
				$request->session()->flash('msg', 'This account already in use.');
			}
		}else{
			$request->session()->flash('msg', 'This is not Mcc Account.');
		}
		return redirect( 'dashboard');
    }
	
	
	/**
     * Store Metric Informatiom Into Database.
     * @param  Request  $request
     * @return Response
     */
	 public function accountName(Request $request)
    {
        session(['acName' => $request->acnam,'apiVal' => $request->apival,'apiDiv' => $request->apidiv,'apiDivType'=>$request->apidivtype,'board'=>$request->board]);
		return redirect( 'adwords');
    }
	
	
	/**
     * Store Metric Informatiom Into Database.
     * @param  Request  $request
     * @return Response
     */
	public function addMetric(Request $request)
    {
        $rawData = $this->getChildAccounts11($request->account,$request->user()->id);
		$current = date('Y-m-d');
		$lastDay = date('Ymd',strtotime($current.' -1 days'));
		$last30Days = date('Ymd',strtotime($current.' -30 days'));
		$reqDate = $last30Days.','.$lastDay;
		$id = $request->user()->Metrics()->create([
			'type' => $request->apival,
			'metric_id' => $request->apidiv,
			'account_id' => $request->account,
			'metric_value' => $request->apidivtype,
			'board_id' => $request->board,
			'position' => 'left',
			'sort' => 0,
			'date_time' => $reqDate,
			'date_type' => 'lower',
			'set_aacount' => $rawData['accounts'][0]['account_id'],
			'report' => 'ACCOUNT_PERFORMANCE_REPORT',
		]);
		$this->metricDataRequesr($id->id);
		$this->dataPersontage($id->id);
		$request->session()->flash('msgs', 'Metric Added Successfully.');
		return redirect( 'dashboard');
    }
	
	
	
	function childAccounts($ClientID){
		$result = Account::where('id', $ClientID)->first();
		return getChildAccount($result->token,$ClientID);
	}
	
	/**
     * Get the average of child account.
     * @param  Request  $request
     * @return Response
     */
	
	function childAccountsCpc($id,$ClientID,$f){
		$result = Account::where('id', $id)->first();
		$data = array();
		$data['aCPC'] = getAccountCpc($result->token,$ClientID,$f);
		$data['camaigns'] = getAccountCampaignsAsString($result->token,$ClientID,$f);
		return json_encode($data);
	}
	
	
	/**
     * Get the all campaigns of child account.
     * @param  Request  $request
     * @return Response
     */
	
	function getCampaigns($id,$ClientID){
		$result = Account::where('id', $id)->first();
		return getAccountCampaigns($result->token,$ClientID);
	}
	
	/**
     * Get the all campaigns of child account.
     * @param  Request  $request
     * @return Response
     */
	
	function getCampaignsString(Request $request,$id,$ClientID,$f){
		$result = Account::where('id', $id)->first();
		$campaigns = Campaign::where(array('caccount_id'=>$ClientID))->get();
		
		$data['camaigns'] = array();
		if(count($campaigns)==0){
			$data['camaigns'] = getAccountCampaignsAsString($result->token,$ClientID,$f);
			foreach($data['camaigns'] as $row){
				$request->user()->Campaign()->create([
					'account_id' => $id,
					'caccount_id' => $ClientID,
					'campaign_id' => $row['id'],
					'campaign_name' => $row['name'],
					'cpc' => $row['cpc'],
				]);
			}
		}else{
			$arr = array();
			
			foreach($campaigns as $row){
					$data1 = array();
					$data1['id'] = $row['campaign_id'];
					$data1['name'] = $row['campaign_name'];
					$data1['cpc'] = $row['cpc'];
					$data1['a'] = $row['caccount_id'];
					$arr[] = $data1;
			}
			
			$data['camaigns'] = $arr;
		}
		return json_encode($data);
	}
	
	/**
     * Get the all campaigns of child account.
     * @param  Request  $request
     * @return Response
     */
	
	function getChildAccounts(Request $request,$id){
		$result = Account::where('id', $id)->first();
		$accounts = ChildsAccount::where(array('user_id'=>$request->user()->id,'account_id'=>$id))->get();
		$account['accounts'] = array();
		if(count($accounts)==0){
			$account['accounts'] = getChildAccount($result->token,$id);
			
			foreach($account['accounts'] as $row){
				
				/*$request->user()->ChildsAccount()->create([
					'account_id' => $id,
					'caccount_id' => $row['account_id'],
					'account_name' => $row['name'],

				]);*/
				//DB::insert('insert into chlidaccount (account_id, user_id,caccount_id,account_name) values ('.$id.', '.$request->user()->id.','.$row['account_id'].',"'.$row['name'].'")');
				$dbDataAccount = NULL;
				$dbDataAccount = new ChildsAccount;
				$dbDataAccount->account_id = $id;
				$dbDataAccount->user_id = $request->user()->id;
				$dbDataAccount->caccount_id = $row['account_id'];
				$dbDataAccount->account_name = $row['name'];
				$dbDataAccount->save();
				/*echo '<pre>';
				print_r($row);*/
			}
			//exit;
		}else{
			$arr = array();
			
			foreach($accounts as $row){
					$data = array();
					$data['client_id'] = $row['account_id'];
					$data['name'] = $row['account_name'];
					$data['account_id'] = $row['caccount_id'];
					$arr[] = $data;
			}
			
			$account['accounts'] = $arr;
		}
		return json_encode($account);
	}
	
	/**
     * Past the all information of sorting metrics.
     * @param  Request  $request
     * @return Response
     */
	
	function metricSorting(Request $request){
		$data = explode(',',$request->data);
		if(count($data)>0){
			foreach($data as $metric){
				$row = explode('-',$metric);
				$result = Metrics::where('id', $row[0])->update(['sort' => $row[1],'position'=>$row[2]]);
			}
		}
		
		exit;
	}
	
	/**
     * Delete metrics.
     * @param  Request  $request
     * @return Response
     */
	function metricDelete(Request $request,$id){
		$result = Metrics::destroy($id);
		$request->session()->flash('msgs', 'Metric Deleted Successfully.');
		return redirect( 'dashboard');
		exit;
	}
	
	/**
     * Get the all adgroups of campaign.
     * @param  Request  $request
     * @return Response
     */
	
	function getAdGroups(Request $request,$id,$ClientID,$cId){
		$result = Account::where('id', $id)->first();
		$data['id'] = $id;
		$data['cl'] = $ClientID;
		$data['cm'] = $cId;
		$adgroups = Adgroups::where(array('campaign_id'=>$cId))->get();
		$data['adgroups'] = array();
		if(count($adgroups)==0){
			$data['adgroups'] = getCampaignsAdgroups($result->token,$ClientID,$cId);
			foreach($data['adgroups'] as $row){
				$request->user()->Adgroups()->create([
					'account_id' => $id,
					'caccount_id' => $ClientID,
					'campaign_id' => $cId,
					'adgroup_id' => $row['id'],
					'adgroup_name' => $row['name'],
				]);
			}
		}else{
			$arr = array();
			
			foreach($adgroups as $row){
					$data1 = array();
					$data1['id'] = $row['adgroup_id'];
					$data1['name'] = $row['adgroup_name'];
					$data1['c'] = $row['campaign_id'];
					$data1['a'] = $row['caccount_id'];
					$arr[] = $data1;
			}
			
			$data['adgroups'] = $arr;
		}
		return json_encode($data);
	}
	
	
	/**
     * Get the all ads of adgroups.
     * @param  Request  $request
     * @return Response
     */
	function getAds(Request $request,$id,$ClientID,$cId,$aId){
		$result = Account::where('id', $id)->first();
		$ads = Ads::where(array('adgroup_id'=>$aId))->get();
		$data['ads'] = array();
		$data['id'] = $id;
		$data['cl'] = $ClientID;
		$data['cm'] = $cId;
		$data['a'] = $aId;
		if(count($ads)==0){
			$data['ads'] = getAdgroupAds($result->token,$ClientID,$aId);
			foreach($data['ads'] as $row){
				$request->user()->Ads()->create([
					'account_id' => $id,
					'caccount_id' => $ClientID,
					'campaign_id' => $cId,
					'adgroup_id' => $aId,
					'ad_id' => $row['id'],
					'ad_name' => $row['name'],
				]);
			}
		}else{
			$arr = array();
			
			foreach($ads as $row){
					$data1 = array();
					$data1['id'] = $row['ad_id'];
					$data1['name'] = $row['ad_name'];
					$data1['a'] = $row['caccount_id'];
					$data1['ai'] = $row['adgroup_id'];
					$arr[] = $data1;
			}
			
			$data['ads'] = $arr;
		}
		return json_encode($data);
	}
	
	/**
     * Get the all Keywords of adgroups.
     * @param  Request  $request
     * @return Response
     */
	function getKeywords(Request $request,$id,$ClientID,$cId,$aId){
		$result = ChildsAccount::where('id', $id)->first();
		$keywords = Keyword::where(array('adgroup_id'=>$aId))->get();
		$data['keywords'] = array();
		$data['id'] = $id;
		$data['cl'] = $ClientID;
		$data['cm'] = $cId;
		$data['a'] = $aId;
		if(count($keywords)==0){
			$data['keywords'] = getAdgroupKeywords($result->token,$ClientID,$aId);
			foreach($data['keywords'] as $row){
				$request->user()->Keyword()->create([
					'account_id' => $id,
					'caccount_id' => $ClientID,
					'campaign_id' => $cId,
					'adgroup_id' => $aId,
					'keyword_id' => $row['id'],
					'keywords' => $row['name'],
				]);
			}
		}else{
			$arr = array();
			
			foreach($campaigns as $row){
					$data1 = array();
					$data1['id'] = $row['keyword_id'];
					$data1['name'] = $row['adgroup_name'];
					$data1['a'] = $row['caccount_id'];
					$data1['ai'] = $row['adgroup_id'];
					$arr[] = $data1;
			}
			
			$data['keywords'] = $arr;
		}
		return json_encode($data);
	}
	
	
	
	/**
     * Save Metric Settings Into Database.
     * @param  Request  $request
     * @return Response
     */
	/* public function saveSettings(Request $request)
    {
       	$id = $request->metric;
		$account = $request->account;
		$campaign = $request->campaign;
		$adgroup = $request->adgroup;
		$keywords = $request->keywords;
		$ads = $request->ads;
		$report = $request->report;
		$date = $request->date;
		$type = $request->type;
		$affected = DB::update('update metrics set set_aacount = '.$account.',set_campaign = '.$campaign.',set_adgroup = '.$adgroup.',set_keyword = '.$keywords.',set_ad = '.$ads.', report = "'.$report.'",date_time = "'.$date.'",date_type = "'.$type.'" where id = '.$id.'');
		$result = Metrics::where('id', $id)->first();
		$resultA = Account::where('id', $result->account_id)->first();
		$cpc = 0;
		$where = '';
		$dateRange = '';
		if($type != '' && $date != ''){
			if($type == 'middle'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($d[0])), date('Ymd', strtotime($d[1])));
			}else if($type == 'lower'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime('-'.$date.' day')), date('Ymd', strtotime('-1 day')));
			}else{
				if($date == 'LAST_3_MONTHS'){
					$d = strtotime(' -4 months');
					$n =  date('Y/m',$d).'/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +90 day')));
				}else if($date == 'LAST_YEAR'){
					$d = strtotime(' -1 year');
					$n =  date('Y',$d).'/1/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +12 months')));
				}else if($date == 'ALL_TIME'){
					$dateRange = '';
				}else{
					$dateRange = ' DURING '.$date;
				}
			}
		}
		
		if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'ExternalCustomerId';
			$cpc = getSettingCpc($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'CampaignId';
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
			$where = 'AdGroupId';
			$cpc = getSettingCpc($resultA->token,$account,$adgroup,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
			$where = 'Id';
			$cpc = getSettingCpc($resultA->token,$account,$keywords,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
			$where = 'Id';
			$cpc = getSettingCpc($resultA->token,$account,$ads,$where,$report,$dateRange,$result->metric_id);
		}
		$data['cpc'] = $cpc;
		return json_encode($data);
    }*/
	
	 public function saveSettings(Request $request)
    {
       	$id = $request->metric;
		$account = $request->account;
		$campaign = $request->campaign;
		$adgroup = $request->adgroup;
		$keywords = $request->keywords;
		$ads = $request->ads;
		$report = $request->report;
		$date = $request->date;
		$type = $request->type;
		$affected = DB::update('update metrics set set_aacount = '.$account.',set_campaign = '.$campaign.',set_adgroup = '.$adgroup.',set_keyword = '.$keywords.',set_ad = '.$ads.', report = "'.$report.'",date_time = "'.$date.'",date_type = "'.$type.'" where id = '.$id.'');
		$result = Metrics::where('id', $id)->first();
		//$resultA = Account::where('id', $result->account_id)->first();
		
		$this->metricDataRequesr($id);
		$this->dataPersontage($id);
		
		
		$f = $result->metric_id;
		$cpc = 0;
		$cpc1 = 0;
		$p = 0;
		$result = DB::select('
			SELECT
			*
			FROM
			metric_data
			WHERE
			metric_id = '.$id.'
			ORDER BY `date` ASC
		');
		$resultp = DB::select('
			SELECT
			*
			FROM
			metric_compare
			WHERE
			metric_id = '.$id.'
		');
		if(count($resultp)>0){
			foreach($resultp as $rowp){
				$p = $rowp->data;
			}
		}
		$bigArray = array();
		if(count($result)>0){
			foreach($result as $row){
				$array = array();
				$array['v'] = $row->value;
				$array['d'] = $row->date;
				$bigArray[] = $array;
			}
			if($f == 'Ctr' || $f == 'ConversionRate'){
				$cpc = $bigArray[(count($result)-1)]['v'].'%';
			}else{
				$cpc = '$'.$bigArray[(count($result)-1)]['v'];
				
			}
			unset($bigArray[(count($result)-1)]);
		}
		
		/*echo '<pre>';
		print_r($bigArray);exit;*/
		$data['cpc'] = $cpc;
		$data['cpc1'] = $bigArray;
		$data['p'] = $p;
		return json_encode($data);
    }
	
	/**
     * Get Metric Settings From Database.
     * @param  Request  $request
     * @return Response
     */
	 public function getSettingsCPC(Request $request)
    {
       	$id = $request->metric;
		$account = $request->account;
		$campaign = $request->campaign;
		$adgroup = $request->adgroup;
		$keywords = $request->keywords;
		$ads = $request->ads;
		$report = $request->report;
		$date = $request->date;
		$type = $request->type;
		/*$result = DB::select('
			SELECT
			*
			FROM
			metrics
			WHERE
			id = '.$id.'
		');
		$result = $result[0];*/
		$result = Metrics::where('id', $id)->first();
		$resultA = Account::where('id', $result->account_id)->first();
		$this->metricDataRequesr($id);
		$this->dataPersontage($id);
		
		
		$f = $result->metric_id;
		$cpc = 0;
		$cpc1 = 0;
		$p = 0;
		$result = DB::select('
			SELECT
			*
			FROM
			metric_data
			WHERE
			metric_id = '.$id.'
			ORDER BY `date` ASC
		');
		$resultp = DB::select('
			SELECT
			*
			FROM
			metric_compare
			WHERE
			metric_id = '.$id.'
		');
		if(count($resultp)>0){
			foreach($resultp as $rowp){
				$p = $rowp->data;
			}
		}
		$bigArray = array();
		if(count($result)>0){
			foreach($result as $row){
				$array = array();
				$array['v'] = $row->value;
				$array['d'] = $row->date;
				$bigArray[] = $array;
			}
			if($f == 'Ctr' || $f == 'ConversionRate'){
				$cpc = $bigArray[(count($result)-1)]['v'].'%';
			}else{
				$cpc = '$'.$bigArray[(count($result)-1)]['v'];
				
			}
			unset($bigArray[(count($result)-1)]);
		}
		
		/*echo '<pre>';
		print_r($bigArray);exit;*/
		$data['cpc'] = $cpc;
		$data['cpc1'] = $bigArray;
		$data['p'] = $p;
		return json_encode($data);
    }
	
	/**
     * Get the all campaigns of child account.
     * @param  Request  $request
     * @return Response
     */
	
	function getChildAccounts1($id,$uid){
		$result = Account::where('id', $id)->first();
		$accounts = ChildsAccount::where(array('user_id'=>$uid,'account_id'=>$id))->get();
		$account['accounts'] = array();
		if(count($accounts)==0){
			$account['accounts'] = getChildAccount($result->token,$id);
			
			foreach($account['accounts'] as $row){
				
				/*$request->user()->ChildsAccount()->create([
					'account_id' => $id,
					'caccount_id' => $row['account_id'],
					'account_name' => $row['name'],
				]);*/
				//DB::insert('insert into chlidaccount (account_id, user_id,caccount_id,account_name) values ('.$id.', '.$request->user()->id.','.$row['account_id'].',"'.$row['name'].'")');
				$dbDataAccount = NULL;
				$dbDataAccount = new ChildsAccount;
				$dbDataAccount->account_id = $id;
				$dbDataAccount->user_id = $request->user()->id;
				$dbDataAccount->caccount_id = $row['account_id'];
				$dbDataAccount->account_name = $row['name'];
				$dbDataAccount->save();
				/*echo '<pre>';
				print_r($row);*/
			}
			//exit;
		}else{
			$arr = array();
			
			foreach($accounts as $row){
					$data = array();
					$data['client_id'] = $row['account_id'];
					$data['name'] = $row['account_name'];
					$data['account_id'] = $row['caccount_id'];
					$arr[] = $data;
			}
			
			$account['accounts'] = $arr;
		}
		return 1;
	}
	
	function saveAccount(Request $request,$ClientID,$mId){
		$current = date('Y-m-d');
		$lastDay = date('Ymd',strtotime($current.' -1 days'));
		$last30Days = date('Ymd',strtotime($current.' -30 days'));
		$reqDate = $last30Days.','.$lastDay;
		$affected = DB::update('update metrics set set_aacount = '.$ClientID.',set_campaign = 0,set_adgroup = 0,set_keyword = 0,set_ad = 0, report = "ACCOUNT_PERFORMANCE_REPORT",date_time = "'.$reqDate.'",date_type = "lower" where id = '.$mId.'');
		$this->metricDataRequesr($mId);
		$this->dataPersontage($mId);
		return 'yes';
	}
	
	/*public function getSettings(Request $request){
		$id = $request->id;
		$result = Metrics::where('id', $id)->first();
		$account = $result->set_aacount;
		$campaign = $result->set_campaign;
		$adgroup = $result->set_adgroup;
		$keywords = $result->set_keyword;
		$ads = $result->set_ad;
		$report = $result->report;
		$value = $result->metric_value;
		if(isset($request->d)){
			$date = $request->d;
			$type = 'lower';
		}else{
			$date = $result->date_time;
			$type = $result->date_type;
		}
				
		$resultA = Account::where('id', $result->account_id)->first();
		$cpc = 0;
		$cpc1 = 0;
		$where = '';
		$dateRange = '';
		if($type != '' && $date != ''){
			if($type == 'middle'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($d[0])), date('Ymd', strtotime($d[1])));
			}else if($type == 'lower'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime('-'.$date.' day')), date('Ymd', strtotime('-1 day')));
			}else{
				if($date == 'LAST_3_MONTHS'){
					$d = strtotime(' -4 months');
					$n =  date('Y/m',$d).'/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +90 day')));
				}else if($date == 'LAST_YEAR'){
					$d = strtotime(' -1 year');
					$n =  date('Y',$d).'/1/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +12 months')));
				}else if($date == 'ALL_TIME'){
					$dateRange = '';
				}else{
					$dateRange = ' DURING '.$date;
				}
			}
		}
		
		if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'ExternalCustomerId';
			$cpc = getSettingCpc($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
			$cpc1 = getSettingCpcDate($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'CampaignId';
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
			$where = 'AdGroupId';
			$cpc = getSettingCpc($resultA->token,$account,$adgroup,$where,$report,$dateRange,$result->metric_id);
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
			$where = 'Id';
			$cpc = getSettingCpc($resultA->token,$account,$keywords,$where,$report,$dateRange,$result->metric_id);
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
			$where = 'Id';
			$cpc = getSettingCpc($resultA->token,$account,$ads,$where,$report,$dateRange,$result->metric_id);
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
		}
		$data['cpc'] = $cpc;
		$data['cpc1'] = $cpc1;
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return json_encode($data);
	}*/
	
	/*public function getSettings(Request $request){
		$id = $request->id;
		$result = Metrics::where('id', $id)->first();
		$account = $result->set_aacount;
		$campaign = $result->set_campaign;
		$adgroup = $result->set_adgroup;
		$keywords = $result->set_keyword;
		$ads = $result->set_ad;
		$report = $result->report;
		$value = $result->metric_value;
		if(isset($request->d)){
			$date = $request->d;
			$type = 'lower';
		}else{
			$date = $result->date_time;
			$type = $result->date_type;
		}
		//$result = DB::select('
			//SELECT
			//*
			//FROM
			//metrics
			//WHERE
			//id = '.$id.'
		//');
		//$result = $result[0];
		
		$resultA = Account::where('id', $result->account_id)->first();
		$cpc = 0;
		$cpc1 = 0;
		$where = '';
		$dateRange = '';
		if($type != '' && $date != ''){
			if($type == 'middle'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($d[0])), date('Ymd', strtotime($d[1])));
			}else if($type == 'lower'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime('-'.$date.' day')), date('Ymd', strtotime('-1 day')));
			}else{
				if($date == 'LAST_3_MONTHS'){
					$d = strtotime(' -4 months');
					$n =  date('Y/m',$d).'/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +90 day')));
				}else if($date == 'LAST_YEAR'){
					$d = strtotime(' -1 year');
					$n =  date('Y',$d).'/1/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +12 months')));
				}else if($date == 'ALL_TIME'){
					$dateRange = '';
				}else{
					$dateRange = ' DURING '.$date;
				}
			}
		}
		
		if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'ExternalCustomerId';
			if($value == 1){
				$cpc = getSettingCpc($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
			}else{
				$cpc1 = getSettingCpcDate($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
			}
		}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'CampaignId';
			if($value == 1){
				$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			}else{
				$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			}
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
			$where = 'AdGroupId';
			if($value == 1){
				$cpc = getSettingCpc($resultA->token,$account,$adgroup,$where,$report,$dateRange,$result->metric_id);
			}else{
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			}
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
			$where = 'Id';
			if($value == 1){
				$cpc = getSettingCpc($resultA->token,$account,$keywords,$where,$report,$dateRange,$result->metric_id);
			}else{
				$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			}
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
			$where = 'Id';
			if($value == 1){
				$cpc = getSettingCpc($resultA->token,$account,$ads,$where,$report,$dateRange,$result->metric_id);
			}else{
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			}
		}
		$data['cpc'] = $cpc;
		$data['cpc1'] = $cpc1;
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return json_encode($data);
	}*/
	
	public function getSettings(Request $request){
		$id = $request->id;
		$result1 = Metrics::where('id', $id)->first();
		$f = $result1->metric_id;
		$cpc = 0;
		$cpc1 = 0;
		$p = 0;
		$result = DB::select('
			SELECT
			*
			FROM
			metric_data
			WHERE
			metric_id = '.$id.'
			ORDER BY `date` ASC
		');
		$resultp = DB::select('
			SELECT
			*
			FROM
			metric_compare
			WHERE
			metric_id = '.$id.'
		');
		if(count($resultp)>0){
			foreach($resultp as $rowp){
				$p = $rowp->data;
			}
		}
		$bigArray = array();
		if(count($result)>0){
			foreach($result as $row){
				$array = array();
				$array['v'] = $row->value;
				$array['d'] = $row->date;
				$bigArray[] = $array;
			}
			if($f == 'Ctr' || $f == 'ConversionRate'){
				$cpc = $bigArray[(count($result)-1)]['v'].'%';
			}else{
				$cpc = '$'.$bigArray[(count($result)-1)]['v'];
				
			}
			unset($bigArray[(count($result)-1)]);
		}
		
		/*echo '<pre>';
		print_r($bigArray);exit;*/
		$data['cpc'] = $cpc;
		$data['cpc1'] = $bigArray;
		$data['p'] = $p;
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return json_encode($data);
	}
	
	
	public function getPersontage(Request $request){
		$id = $request->id;
		$result = Metrics::where('id', $id)->first();
		$account = $result->set_aacount;
		$campaign = $result->set_campaign;
		$adgroup = $result->set_adgroup;
		$keywords = $result->set_keyword;
		$ads = $result->set_ad;
		$report = $result->report;
		if(isset($request->d)){
			$date = $request->d;
			$type = 'lower';
		}else{
			$date = $result->date_time;
			$type = $result->date_type;
		}
		/*$result = DB::select('
			SELECT
			*
			FROM
			metrics
			WHERE
			id = '.$id.'
		');
		$result = $result[0];*/
		
		$resultA = Account::where('id', $result->account_id)->first();
		$cpc = 0;
		$cpc2 = 0;
		$where = '';
		$dateRange = '';
		$dateRange2 = '';
		if($type != '' && $date != ''){
			if($type == 'middle'){
				$d = explode('-',$date);
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($d[0])), date('Ymd', strtotime($d[1])));
				$start = strtotime($d[0]);
				$end = strtotime($d[1]);
				
				$days_between = ceil(abs($end - $start) / 86400);
				$d_1 = date('Y/m/d',strtotime($d[0].' -'.($days_between+1).' days'));
				$d_2 = date('Y/m/d',strtotime($d_1.' +'.($days_between).' days')); 
				$dateRange2 = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($d_1)), date('Ymd', strtotime($d_2)));
			}else if($type == 'lower'){
				$d = explode('-',$date);
				$d_1 = date('Y/m/d', strtotime('-'.$date.' day'));
				$d_1 = date('Y/m/d', strtotime($d_1.' -'.($date+1).' days'));
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime('-'.$date.' day')), date('Ymd', strtotime('-1 day')));
				$dateRange2 = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($d_1)), date('Ymd', strtotime($d_1.'+'.$date.' day')));
			}else{
				if($date == 'LAST_3_MONTHS'){
					$d = strtotime(' -7 months');
					$n =  date('Y/m',$d).'/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +90 day')));
				}else if($date == 'LAST_YEAR'){
					$d = strtotime(' -2 year');
					$n =  date('Y',$d).'/1/1';
					$dateRange = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +12 months')));
				}else if($date == 'ALL_TIME'){
					$dateRange = '';
				}else{
					$dateRange = ' DURING '.$date;
				}
				
				if($date == 'LAST_3_MONTHS'){
					$d = strtotime(' -4 months');
					$n =  date('Y/m',$d).'/1';
					$dateRange2 = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +90 day')));
				}else if($date == 'LAST_YEAR'){
					$d = strtotime(' -1 year');
					$n =  date('Y',$d).'/1/1';
					$dateRange2 = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +12 months')));
				}else if($date == 'ALL_TIME'){
					$dateRange2 = '';
				}else{
					$s = explode('_',$date);
					$date = $s[1];
					$d_1 = date('Y/m/d', strtotime('-'.$date.' day'));
					$d_1 = date('Y/m/d', strtotime($d_1.' -'.($date+1).' days'));
					$dateRange2 = ' DURING '.sprintf('%d,%d',
					date('Ymd', strtotime($d_1)), date('Ymd', strtotime($d_1.'+'.$date.' day'))); 
				}
				
			}
		}
		if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'ExternalCustomerId';
			$cpc = getSettingCpc($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'CampaignId';
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
			$where = 'AdGroupId';
			$cpc = getSettingCpc($resultA->token,$account,$adgroup,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
			$where = 'Id';
			$cpc = getSettingCpc($resultA->token,$account,$keywords,$where,$report,$dateRange,$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
			$where = 'Id';
			$cpc = getSettingCpc($resultA->token,$account,$ads,$where,$report,$dateRange,$result->metric_id);
		}
		if($dateRange2 != ''){
			if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
				$where = 'ExternalCustomerId';
				$cpc2 = getSettingCpc($resultA->token,$account,0,$where,$report,$dateRange2,$result->metric_id);
			}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
				$where = 'CampaignId';
				$cpc2 = getSettingCpc($resultA->token,$account,$campaign,$where,$report,$dateRange2,$result->metric_id);
			}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
				$where = 'AdGroupId';
				$cpc2 = getSettingCpc($resultA->token,$account,$adgroup,$where,$report,$dateRange2,$result->metric_id);
			}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
				$where = 'Id';
				$cpc2 = getSettingCpc($resultA->token,$account,$keywords,$where,$report,$dateRange2,$result->metric_id);
			}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
				$where = 'Id';
				$cpc2 = getSettingCpc($resultA->token,$account,$ads,$where,$report,$dateRange2,$result->metric_id);
			}
		}
		$data['cpc'] = $cpc;
		$data['cpc2'] = $cpc2;
		
		$r1 = filter_var($cpc, FILTER_SANITIZE_NUMBER_INT);
		$r2 = filter_var($cpc2, FILTER_SANITIZE_NUMBER_INT); 
		if($r2 > 0){
			$data['p'] = ((($r2-$r1)/($r1))*100);
		}else{
			$data['p'] = 0;
		}
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return json_encode($data);
	}
	
	public function masterSetting(Request $request){
		$affected = DB::update('update metrics set date_time = "'.$request->d.'" where user_id = '.$request->user()->id.' and board_id = '.$request->user()->current_board.'');
		
	}
	
	public function getSettings2(Request $request){
		$id = $request->id;
		$result1 = Metrics::where('id', $id)->first();
		$this->metricDataRequesr($id);
		$this->dataPersontage($id);
		$f = $result1->metric_id;
		$cpc = 0;
		$cpc1 = 0;
		$p = 0;
		$result = DB::select('
			SELECT
			*
			FROM
			metric_data
			WHERE
			metric_id = '.$id.'
			ORDER BY `date` ASC
		');
		$resultp = DB::select('
			SELECT
			*
			FROM
			metric_compare
			WHERE
			metric_id = '.$id.'
		');
		if(count($resultp)>0){
			foreach($resultp as $rowp){
				$p = $rowp->data;
			}
		}
		$bigArray = array();
		if(count($result)>0){
			foreach($result as $row){
				$array = array();
				$array['v'] = $row->value;
				$array['d'] = $row->date;
				$bigArray[] = $array;
			}
			if($f == 'Ctr' || $f == 'ConversionRate'){
				$cpc = $bigArray[(count($result)-1)]['v'].'%';
			}else{
				$cpc = '$'.$bigArray[(count($result)-1)]['v'];
				
			}
			unset($bigArray[(count($result)-1)]);
		}
		
		/*echo '<pre>';
		print_r($bigArray);exit;*/
		$data['cpc'] = $cpc;
		$data['cpc1'] = $bigArray;
		$data['p'] = $p;
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return json_encode($data);
	}
	

}
