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

class DashboardController extends Controller
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
     * Display a Dashboard.
     *
     * @param  Request  $request
     * @return Response
     */
	 public function index(Request $request)
    {
		
		$mcc = $this->accounts->forUser($request->user());
		if(count($mcc)==0){
			return view('dashboard.conect',[
				'msg' => $request->session()->get('msg'),
				'msgs' => $request->session()->get('msgs'),
			]);
		}else{
			$metric = DB::select('
							SELECT
							*
							FROM
							metrics
							WHERE
							user_id = '.$request->user()->id.'
							AND
							board_id = '.$request->user()->current_board.'
						');
			//$metric = Metrics::where(array('user_id'=>$request->user()->id,'board_id'=>$request->user()->current_board));
			$boards = DB::select('
							SELECT
							*
							FROM
							dashboard
							WHERE
							user_id = '.$request->user()->id.'
						');
			/*$boardid = DB::select('
							SELECT
							id
							FROM
							dashboard
							WHERE
							user_id = '.$request->user()->id.'
							AND
							no_of_board = '.$request->user()->current_board.'
						');*/
			$accounts = array();
			$boardid = $request->user()->current_board;
			/*if(count($boardid)>0){
				$boardid = $boardid[0]->id;
			}else{
				$boardid = 0;
			}*/
			foreach($metric as $m){
				$result = Account::where('id', $m->account_id)->first();
				$rawData = $this->getChildAccounts1($m->account_id,$request->user()->id);
				if(!in_array($m->account_id,$accounts)){
					$accounts[] = $m->account_id;
				}
				
			}
			$mcc = $this->accounts->forUser($request->user());
			return view('dashboard.index',[
				'accounts' => $this->accounts->forUser($request->user()),
				'metrics' => $metric,
				'childs' => $accounts,
				'boardid' => $boardid,
				'boards' => $boards,
				'msg' => $request->session()->get('msg'),
				'msgs' => $request->session()->get('msgs'),
			]);
		}
    }
	
	 /**
     * Add New Google MCC Account
	 * Create User Default Dashboard
     * And Metrics Informatiom Into Database.
     * @param  Request  $request
     * @return Response
     */
	public function createDashboard(Request $request)
    {
        $data = getRootAccount(session('apiAccessToken'));
		$result = Account::where(array('mccaccount_id'=>$data['customerId'],'user_id'=>$request->user()->id))->first();
		$current = date('Y-m-d');
		$lastDay = date('Ymd',strtotime($current.' -1 days'));
		$last30Days = date('Ymd',strtotime($current.' -30 days'));
		$reqDate = $last30Days.','.$lastDay;
		if(count($data)>0){
			if(empty($result)){
				$boardId = DB::table('dashboard')->insertGetId(
    ['user_id' => $request->user()->id, 'no_of_board' =>1,'name'=>'Default']
,'id');
				$id = $request->user()->accounts()->create([
					'name' => $data['accountName'],
					'mccaccount_id' => $data['customerId'],
					'type' => 'Google Adwords',
					'metric_id' => 1,
					'token' => session('apiAccessToken'),
					'created' => date('Y-m-d'),
				])->id;
				$updateUser = DB::update('update users set current_board = 1 where id = '.$request->user()->id.'');
				$rawData = $this->getChildAccounts11($id,$request->user()->id);
				
				$m1 = $request->user()->Metrics()->create([
					'type' => 'Google Adwords',
					'metric_id' => 'AverageCpc',
					'position' => 'left',
					'sort' => 1,
					'account_id' => $id,
					'board_id' => 1,
					'metric_value' => 2,
					'set_aacount' => $rawData['accounts'][0]['account_id'],
					'report' => 'ACCOUNT_PERFORMANCE_REPORT',
					'date_time' => $reqDate,
					'date_type' => 'lower',
				]);
				$m2 = $request->user()->Metrics()->create([
					'type' => 'Google Adwords',
					'metric_id' => 'Ctr',
					'position' => 'right',
					'sort' => 1,
					'account_id' => $id,
					'board_id' => 1,
					'metric_value' => 2,
					'set_aacount' => $rawData['accounts'][0]['account_id'],
					'report' => 'ACCOUNT_PERFORMANCE_REPORT',
					'date_time' => $reqDate,
					'date_type' => 'lower',
				]);
				$m3 = $request->user()->Metrics()->create([
					'type' => 'Google Adwords',
					'metric_id' => 'ConversionRate',
					'position' => 'left',
					'sort' => 2,
					'account_id' => $id,
					'board_id' => 1,
					'metric_value' => 2,
					'set_aacount' => $rawData['accounts'][0]['account_id'],
					'report' => 'ACCOUNT_PERFORMANCE_REPORT',
					'date_time' => $reqDate,
					'date_type' => 'lower',
				]);
				$m4 = $request->user()->Metrics()->create([
					'type' => 'Google Adwords',
					'metric_id' => 'Conversions',
					'position' => 'right',
					'sort' => 2,
					'account_id' => $id,
					'board_id' => 1,
					'metric_value' => 2,
					'set_aacount' => $rawData['accounts'][0]['account_id'],
					'report' => 'ACCOUNT_PERFORMANCE_REPORT',
					'date_time' => $reqDate,
					'date_type' => 'lower',
				]);
				$m5 = $request->user()->Metrics()->create([
					'type' => 'Google Adwords',
					'metric_id' => 'Cpa',
					'position' => 'left',
					'sort' => 3,
					'account_id' => $id,
					'board_id' => 1,
					'metric_value' => 2,
					'set_aacount' => $rawData['accounts'][0]['account_id'],
					'report' => 'ACCOUNT_PERFORMANCE_REPORT',
					'date_time' => $reqDate,
					'date_type' => 'lower',
				]);
				$this->metricDataRequesr($m1->id);
				$this->metricDataRequesr($m2->id);
				$this->metricDataRequesr($m3->id);
				$this->metricDataRequesr($m4->id);
				$this->metricDataRequesr($m5->id);
				$this->dataPersontage($m1->id);
				$this->dataPersontage($m2->id);
				$this->dataPersontage($m3->id);
				$this->dataPersontage($m4->id);
				$this->dataPersontage($m5->id);
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
     * Load Form To Create New Dashboard Of User
     * @param  Request  $request
     * @return Response
     */
	public function newDashboard(Request $request)
    {
         return view('dashboard.new');
    }
	
	
	/**
     * Save User Selected Dashboard
     * @param  Request  $request
     * @return Response
     */
	public function setDashboard(Request $request,$id)
    {
        $updateUser = DB::update('update users set current_board = '.$id.' where id = '.$request->user()->id.'');
		return redirect( 'dashboard');
    }
	
	/**
	 * Create A New Dashboard.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function storeDashboard(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:255',
		]);
		$affected = DB::select('
				SELECT
				MAX(no_of_board) as no
				FROM
				dashboard
				WHERE
				user_id = '.$request->user()->id.'
			');
		$boardId = DB::table('dashboard')->insertGetId(['user_id' => $request->user()->id, 'no_of_board' =>($affected[0]->no+1),'name'=>$request->name]
,'id');
		$updateUser = DB::update('update users set current_board = '.($affected[0]->no+1).' where id = '.$request->user()->id.'');
		$request->session()->flash('msgs', 'Dashboard Created Successfully.');
		return redirect('dashboard');
	
		// Create The Task...
	}
	
	
	
	/**
     * Get the all Data of MCC account.
     * @param  Request  $request
     * @return Response
     */
	
	function cronJob(Request $request){
		ini_set('max_execution_time', 3000);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$result = Account::all();
		foreach($result as $row){
			$childsAccount = getChildAccount($row->token,$row->id);
			if(count($childsAccount)>0){
				foreach($childsAccount as $account){
					$dbAccount = ChildsAccount::where(array('user_id'=>$row->user_id,'account_id'=>$row->id,'caccount_id'=>$account['account_id']))->get();
					if(count($dbAccount)==0){
						$dbDataAccount = new ChildsAccount;
						$dbDataAccount->account_id = $row->id;
						$dbDataAccount->user_id = $row->user_id;
						$dbDataAccount->caccount_id = $account['account_id'];
						$dbDataAccount->account_name = $account['name'];
						//$dbDataAccount->save();
					}
					
					$allCampaigns = getAccountCampaignsAsString($row->token,$account['account_id']);
					if(count($allCampaigns)>0){
						foreach($allCampaigns as $campaign){
							$dbCampaigns = Campaign::where(array('user_id'=>$row->user_id,'id'=>$row->id,'caccount_id'=>$account['account_id'],'campaign_id'=>$campaign['id']))->get();
							if(count($dbCampaigns) == 0){
								$dbDataCampaign = new Campaign;
								$dbDataCampaign->account_id = $row->id;
								$dbDataCampaign->user_id = $row->user_id;
								$dbDataCampaign->caccount_id = $account['account_id'];
								$dbDataCampaign->campaign_id = $campaign['id'];
								$dbDataCampaign->campaign_name = $campaign['name'];
								$dbDataCampaign->cpc = $campaign['cpc'];
								//$dbDataCampaign->save();
							}
							$allAdgroups = getCampaignsAdgroups($row->token,$account['account_id'],$campaign['id']);
							if(count($allAdgroups)>0){
								foreach($allAdgroups as $adgroup){
									$dbAdgroups = Adgroups::where(array('user_id'=>$row->user_id,'account_id'=>$row->id,'caccount_id'=>$account['account_id'],'campaign_id'=>$campaign['id'],'adgroup_id'=>$adgroup['id']))->get();
									if(count($dbAdgroups)==0){
										$dbDataAdgroup = new Adgroups;
										$dbDataAdgroup->account_id = $row->id;
										$dbDataAdgroup->user_id = $row->user_id;
										$dbDataAdgroup->caccount_id = $account['account_id'];
										$dbDataAdgroup->campaign_id = $campaign['id'];
										$dbDataAdgroup->adgroup_id = $adgroup['id'];
										$dbDataAdgroup->adgroup_name = $adgroup['name'];
										$dbDataAdgroup->save();
									}
								}
								/*echo '<pre>';
								print_r($allAdgroups);exit;*/
							}
						}
					}
				}
			}
		}
		echo 'Success';
		//print_r($allCampaigns);exit;
		exit;
		//return getAccountCampaigns($result->token,$ClientID);
	}
	
	function getChildAccounts1($id,$uid){
		$result = Account::where('id', $id)->first();
		//$accounts = ChildsAccount::where(array('user_id'=>$uid,'account_id'=>$id))->get();
		$accounts = ChildsAccount::where(array('account_id'=>$result->mccaccount_id))->get();
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
				$dbDataAccount->account_id = $result->mccaccount_id;
				//$dbDataAccount->account_id = $id;
				//$dbDataAccount->user_id = $uid;
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
	
	function getChildAccounts11($id,$uid){
		$result = Account::where('id', $id)->first();
		//$accounts = ChildsAccount::where(array('user_id'=>$uid,'account_id'=>$id))->get();
		$accounts = ChildsAccount::where(array('account_id'=>$result->mccaccount_id))->get();
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
				$dbDataAccount->account_id = $result->mccaccount_id;
				//$dbDataAccount->account_id = $id;
				//$dbDataAccount->user_id = $uid;
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
		return $account;
	}
	
	public function metricDataRequesr($id){
		
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
		$cpc1 = 0;
		$where = '';
		$dateRange = ' DURING '.$date;
		/*if($type != '' && $date != ''){
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
		}*/
		if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'ExternalCustomerId';
			$cpc1 = getSettingCpcDate($resultA->token,$account,0,$where,$report,$dateRange,$result->metric_id);
			$cpc = getSettingCpc($resultA->token,$account,0,$where,$report,' DURING TODAY',$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
			$where = 'CampaignId';
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report, ' DURING TODAY',$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
			$where = 'AdGroupId';
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,' DURING TODAY',$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
			$where = 'Id';
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,' DURING TODAY',$result->metric_id);
		}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
			$where = 'Id';
			$cpc1 = getSettingCpcDate($resultA->token,$account,$campaign,$where,$report,$dateRange,$result->metric_id);
			$cpc = getSettingCpc($resultA->token,$account,$campaign,$where,$report,' DURING TODAY',$result->metric_id);
		}
		
		$cpc  = floatval($cpc);
	
		//$cpc = filter_var($cpc, FILTER_SANITIZE_NUMBER_INT);
		
		$updateUser = DB::update('DELETE FROM metric_data where metric_id = '.$id.'');
		$updateUser = DB::update('INSERT INTO `metric_data`(`metric_id`, `date`, `value`) VALUES ('.$id.',"'.date('Y-m-d').'","'.$cpc.'")');
		
		if(count($cpc1)>0){
			foreach($cpc1 as $row){
				
				$updateUser = DB::update('INSERT INTO `metric_data`(`metric_id`, `date`, `value`) VALUES ('.$id.',"'.$row['d'].'","'.$row['v'].'")');
			}
		}
		return 1;
	}
	
	
	public function dataPersontage($id){
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
		$dateRange = ' DURING '.$date;
		$dateRange2 = '';
		$d = explode(',',$date);
		/*$dateRange = ' DURING '.sprintf('%d,%d',
		date('Ymd', strtotime($d[0])), date('Ymd', strtotime($d[1])));*/
		$start = strtotime($d[0]);
		$end = strtotime($d[1]);
		
		$days_between = ceil(abs($end - $start) / 86400);
		$d_1 = date('Y/m/d',strtotime($d[0].' -'.($days_between+1).' days'));
		$d_2 = date('Y/m/d',strtotime($d_1.' +'.($days_between).' days')); 
		$dateRange2 = ' DURING '.sprintf('%d,%d',
		date('Ymd', strtotime($d_1)), date('Ymd', strtotime($d_2)));
		/*if($type != '' && $date != ''){
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
		}*/
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
			$data['p'] = 0.00;
		}
		$updateUser = DB::update('DELETE FROM metric_compare where metric_id = '.$id.'');
		$updateUser = DB::update('INSERT INTO `metric_compare`(`metric_id`, `data`) VALUES ('.$id.','.$data['p'].')');
		
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return 1;
	}
	
	 /**
     * Display a list of all of the user's dashboard.
     *
     * @param  Request  $request
     * @return Response
     */
    public function myDashboard(Request $request)
    {
		$boards = DB::select('
							SELECT
							*
							FROM
							dashboard
							WHERE
							user_id = '.$request->user()->id.'
							AND
							no_of_board > 1
						');
        return view('dashboard.mydashboard', [
            'dashboards' => $boards,
			'msg' => $request->session()->get('msg'),
			'msgs' => $request->session()->get('msgs'),
        ]);
    }
	
	/**
	 * Create a new Dashboard.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function storeNewDashboard(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:255',
		]);
		$affected = DB::select('
				SELECT
				MAX(no_of_board) as no
				FROM
				dashboard
				WHERE
				user_id = '.$request->user()->id.'
			');
		$boardId = DB::table('dashboard')->insertGetId(['user_id' => $request->user()->id, 'no_of_board' =>($affected[0]->no+1),'name'=>$request->name]
,'id');
		
		$request->session()->flash('msgs', 'Dashboard Created Successfully.');
		return redirect('/myDashboard');
	
		// Create The Task...
	}
	
	/**
	 * Create a new Dashboard.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function deleteDashboard(Request $request,$id)
	{
		
		$affected = DB::delete('DELETE FROM `dashboard` WHERE id ='.$id.' AND no_of_board > 1');
		
		
		$request->session()->flash('msgs', 'Dashboard Deleted Successfully.');
		return redirect('/myDashboard');
	
		// Create The Task...
	}
	
	/**
     * Load Form To Edit Dashboard Of User
     * @param  Request  $request
     * @return Response
     */
	public function editDashboard(Request $request,$id)
    {
         $boards = DB::select('
							SELECT
							*
							FROM
							dashboard
							WHERE
							id = '.$id.'
							AND 
							no_of_board > 1
						');
		if(count($boards)>0){
			 return view('dashboard.edit',[
				'data' => $boards[0],
				'id' => $id,
			]);
		}else{
			$request->session()->flash('msg', 'Invalid Data.');
			return redirect('/myDashboard');
		}
			
    }
	
	public function updateDashboard(Request $request,$id)
	{
		$this->validate($request, [
			'name' => 'required|max:255',
		]);
		$affected = DB::update('UPDATE `dashboard` SET `name`="'.$request->name.'" WHERE id = '.$id.' AND no_of_board > 1');
		$request->session()->flash('msgs', 'Dashboard Updated Successfully.');
		return redirect('/myDashboard');
	
		// Create The Task...
	}
	
	
}
