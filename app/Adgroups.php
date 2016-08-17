<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Adgroups extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
        'account_id', 'caccount_id','campaign_id', 'adgroup_id','adgroup_name',
    ];
    protected $table = 'adgroups';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
