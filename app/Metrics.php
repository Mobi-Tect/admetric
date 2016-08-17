<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Metrics extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 protected $fillable = [
        'type', 'metric_id','account_id','position', 'sort','board_id', 'metric_value','set_aacount','report','date_time','date_type'
    ];
    protected $table = 'metrics';
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
