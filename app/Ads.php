<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
       'account_id', 'caccount_id','campaign_id','adgroup_id', 'ad_id','ad_name'
    ];
    protected $table = 'ads';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
