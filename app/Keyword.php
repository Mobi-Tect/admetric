<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
        'account_id', 'caccount_id','campaign_id','adgroup_id', 'keyword_id','keywords'
    ];
    protected $table = 'adkeyword';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
