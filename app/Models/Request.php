<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'mobile_number',
        'email',
        'stock_code',
        'company_name',
        'timeframe',
        'action',
        'advice',
        'advice_chatgpt',
        'user_id',
        'result',
        'entry_price',
        'target_1',
        'target_2',
        'stop_loss',
        'monitoring_until',
        'highest_price_reached',
        'result_achieved_at',
    ];

    /**
     * Get the user that owns the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
