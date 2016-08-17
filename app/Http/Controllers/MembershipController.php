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

use Cartalyst\Stripe\Laravel\Facades\Stripe;

use Cartalyst\Stripe\Laravel\StripeServiceProvider;

class MembershipController extends Controller
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
     * Display all membership plans.
     *
     * @param  Request  $request
     * @return Response
     */
	 public function index(Request $request)
    {
		
		$packages = DB::select('
						SELECT
						*
						FROM
						package
						WHERE
						id > 1
						ORDER BY price ASC
					');
		
		
		return view('membership.index',[
			'packages' => $packages,
		]);
    }
	
	
	/**
     *Upgrade user membership plans.
     *
     * @param  Request  $request
     * @return Response
     */
	 public function upgrade(Request $request)
    {
		
		$packages = DB::select('
						SELECT
						*
						FROM
						package
						WHERE
						id > 1
						AND
						id = '.$request->id.'
						ORDER BY price ASC
					');
		if(count($packages)>0){
			$packages = $packages[0];
			return view('membership.checkout',[
				'packages' => $packages,
			]);
			
			


		}else{
			redirect('/dashboard');
		}
		
    }
	
	/**
     *Upgrade and charge user membership plans.
     *
     * @param  Request  $request
     * @return Response
     */
	 public function charge(Request $request)
    {
		
		$packages = DB::select('
						SELECT
						*
						FROM
						package
						WHERE
						id > 1
						AND
						id = '.$request->pid.'
						ORDER BY price ASC
					');
		if(count($packages)>0){
			$packages = $packages[0];
			
			$stripe = Stripe::setApiKey("sk_test_6CLC5FKgRluooF2MTrdraOav");
			//\Stripe\Stripe::setApiKey(env('sk_test_6CLC5FKgRluooF2MTrdraOav'));
			$customer = $stripe->customers()->create([
				'email' => $request->stripeEmail,
				'source'  => $request->stripeToken,
			]);

			$charge =  Stripe::charges()->create([
				'customer' => $customer['id'],
				'currency' => 'USD',
				'amount'   => $packages->price,
				'metadata' => [
                    'product_name' => $packages->title
                ]
			]);
			
			if(isset($charge['id']) && !empty($charge['id'])){
				$packages = DB::update('UPDATE `user_package` SET `package_id`='.$packages->id.',`package_start_date`="'.date('Y-m-d').'" WHERE user_id = '.$request->user()->id.'');
			}
			return redirect('/dashboard');

		}else{
			return redirect('/dashboard');
		}
		
    }
	
	
	
	
}
