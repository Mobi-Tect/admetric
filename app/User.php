<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','company','lname',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	// Other Eloquent Properties...

    /**
     * Get all of the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
	
	public function accounts()
    {
        return $this->hasMany(Account::class);
    }
	
	public function Metrics()
    {
        return $this->hasMany(Metrics::class);
    }
	public function ChildsAccount()
    {
        return $this->hasMany(ChildsAccount::class);
    }
	public function Campaign()
    {
        return $this->hasMany(Campaign::class);
    }
	public function Adgroups()
    {
        return $this->hasMany(Adgroups::class);
    }
	public function Keyword()
    {
        return $this->hasMany(Keyword::class);
    }
	public function Ads()
    {
        return $this->hasMany(Ads::class);
    }
}
