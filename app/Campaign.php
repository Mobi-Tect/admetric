<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
        'account_id', 'caccount_id', 'campaign_id','campaign_name','cpc'
    ];
    protected $table = 'campaign';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
