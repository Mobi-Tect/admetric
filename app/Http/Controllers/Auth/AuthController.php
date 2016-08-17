<?php

namespace App\Http\Controllers\Auth;
use DB;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = array(
			'required' => 'This field is required.',
		);
		return Validator::make($data, [
            'name' => 'required|max:255',
			//'lname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
			'company',
        ],$messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        
		$msg = "Hi ".$data['name']."<br> Your Admetric trial start now.";
		
		$msg = wordwrap($msg,70);
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
		$headers .= 'From: Admetric <info@admetric.com>' . "\r\n";
		mail($data['email'],"Admetric Trial Start",$msg,$headers);
		$user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
			//'lname' => $data['lname'],
			'company' => $data['company'],
            'password' => bcrypt($data['password']),
        ]);
		$package = DB::insert('INSERT INTO `user_package`(`user_id`, `package_id`, `package_start_date`) VALUES ('.$user->id.',1,"'.date('Y-m-d').'")');
		return $user;
    }
}
