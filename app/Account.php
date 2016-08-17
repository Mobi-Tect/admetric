<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
        'name', 'type', 'metric_id','mccaccount_id','token','created',
    ];
    protected $table = 'accounts';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
