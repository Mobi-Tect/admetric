<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class ChildsAccount extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
        'account_id', 'caccount_id','account_name',
    ];
    protected $table = 'chlidaccount';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
