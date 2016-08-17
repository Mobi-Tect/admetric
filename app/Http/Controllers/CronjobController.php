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

class CronjobController extends Controller
{
    
	 
    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
	
	
	public function __construct()
    {
       

    }
	
	
	 /**
     * Display a Dashboard.
     *
     * @param  Request  $request
     * @return Response
     */
	 public function index(Request $request)
    {
         //ini_set("display_errors", 1);
         //ini_set("memory_limit", 1000000);
         //ini_set("max_execution_time",0);
         //echo $max_time;
         //exit;
         
         $a = 0x01;
		 $b = 0x01;
		 printf("%x",($a <<($b.$b)));
        exit;
		$result = DB::select('SELECT * FROM metrics');
		if(count($result)>0){
			foreach($result as $row){
				$this->metricDataRequesr($row->id);
				$this->dataPersontage($row->id);
				
			}
		}
		echo '<pre>';
		print_r($result);
		exit;
	}
	
	 /**
     * Add New Google MCC Account
	 * Create User Default Dashboard
     * And Metrics Informatiom Into Database.
     * @param  Request  $request
     * @return Response
     */
	
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
		$cpc  = filter_var($cpc, FILTER_SANITIZE_NUMBER_INT);
		//$cpc = filter_var($cpc, FILTER_SANITIZE_NUMBER_INT);
		
		$updateUser = DB::delete('DELETE FROM metric_data where metric_id = '.$id.'');
		$updateUser = DB::insert('INSERT INTO `metric_data`(`metric_id`, `date`, `value`) VALUES ('.$id.',"'.date('Y-m-d').'","'.$cpc.'")');
		if(count($cpc1)>0){
			foreach($cpc1 as $row){
				$updateUser = DB::insert('INSERT INTO `metric_data`(`metric_id`, `date`, `value`) VALUES ('.$id.',"'.$row['d'].'","'.$row['v'].'")');
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
			$data['p'] = 0.00;
		}
		$updateUser = DB::delete('DELETE FROM metric_compare where metric_id = '.$id.'');
		$updateUser = DB::insert('INSERT INTO `metric_compare`(`metric_id`, `data`) VALUES ('.$id.','.$data['p'].')');
		
		//$data['acpc'] = getAccountCpcH($result->account_id,$account,$result->metric_id);
		return 1;
	}
	
	
}
