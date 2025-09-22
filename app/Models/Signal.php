<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_code',
        'company_name',
        'timeframe',
        'current_price',
        'entry_price',
        'target_1',
        'target_2',
        'stop_loss',
        'risk_reward',
        'confidence_level',
        'confidence_percentage',
        'analysis_reason',
        'rsi',
        'macd_signal',
        'volume',
        'scalping_score',
        'status',
        'expires_at',
        'result_achieved_at',
        'highest_price_reached',
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
        'entry_price' => 'decimal:2',
        'target_1' => 'decimal:2',
        'target_2' => 'decimal:2',
        'stop_loss' => 'decimal:2',
        'rsi' => 'decimal:2',
        'scalping_score' => 'decimal:1',
        'highest_price_reached' => 'decimal:2',
        'expires_at' => 'datetime',
        'result_achieved_at' => 'datetime',
        'volume' => 'integer',
        'confidence_percentage' => 'integer',
    ];

    // Scopes for filtering
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('expires_at', '>', now());
    }

    public function scopeHighConfidence($query, $minPercentage = 70)
    {
        return $query->where('confidence_percentage', '>=', $minPercentage);
    }

    public function scopeForTimeframe($query, $timeframe)
    {
        return $query->where('timeframe', $timeframe);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at < now() || $this->status === 'expired';
    }

    public function hasHitTarget()
    {
        return in_array($this->status, ['hit_target_1', 'hit_target_2']);
    }

    public function hasHitStopLoss()
    {
        return $this->status === 'hit_stop_loss';
    }

    public function getFormattedPriceAttribute($price)
    {
        return 'IDR ' . number_format($price, 2);
    }

    public function getFormattedCurrentPriceAttribute()
    {
        return $this->getFormattedPriceAttribute($this->current_price);
    }

    public function getFormattedEntryPriceAttribute()
    {
        return $this->getFormattedPriceAttribute($this->entry_price);
    }

    public function getFormattedTarget1Attribute()
    {
        return $this->getFormattedPriceAttribute($this->target_1);
    }

    public function getFormattedTarget2Attribute()
    {
        return $this->getFormattedPriceAttribute($this->target_2);
    }

    public function getFormattedStopLossAttribute()
    {
        return $this->getFormattedPriceAttribute($this->stop_loss);
    }

    public function getPotentialProfitTarget1Attribute()
    {
        return (($this->target_1 - $this->entry_price) / $this->entry_price) * 100;
    }

    public function getPotentialProfitTarget2Attribute()
    {
        return (($this->target_2 - $this->entry_price) / $this->entry_price) * 100;
    }

    public function getPotentialLossAttribute()
    {
        return (($this->entry_price - $this->stop_loss) / $this->entry_price) * 100;
    }
}
