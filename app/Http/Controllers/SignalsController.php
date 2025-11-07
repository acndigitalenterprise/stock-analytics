<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signal;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SignalsController extends Controller
{
    /**
     * Display signals page with active signals
     */
    public function index()
    {
        // Get active signals with high confidence (≥60%) ordered by confidence
        $signals = Signal::active()
            ->highConfidence(60)
            ->orderByDesc('confidence_percentage')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('Signals.index', compact('signals'));
    }

    /**
     * Get signals data for AJAX requests
     */
    public function getSignals(Request $request)
    {
        $timeframe = $request->get('timeframe', 'all');
        $confidence = $request->get('confidence', 60);

        $query = Signal::active()
            ->highConfidence($confidence)
            ->orderByDesc('confidence_percentage')
            ->orderByDesc('created_at');

        if ($timeframe !== 'all') {
            $query->forTimeframe($timeframe);
        }

        $signals = $query->get();

        return response()->json([
            'success' => true,
            'signals' => $signals->map(function ($signal) {
                return [
                    'id' => $signal->id,
                    'stock_code' => $signal->stock_code,
                    'company_name' => $signal->company_name,
                    'timeframe' => $signal->timeframe,
                    'current_price' => $signal->formatted_current_price,
                    'entry_price' => $signal->formatted_entry_price,
                    'target_1' => $signal->formatted_target_1,
                    'target_2' => $signal->formatted_target_2,
                    'stop_loss' => $signal->formatted_stop_loss,
                    'risk_reward' => $signal->risk_reward,
                    'confidence_level' => $signal->confidence_level,
                    'confidence_percentage' => $signal->confidence_percentage,
                    'analysis_reason' => $signal->analysis_reason,
                    'rsi' => $signal->rsi,
                    'macd_signal' => $signal->macd_signal,
                    'volume' => number_format($signal->volume),
                    'scalping_score' => $signal->scalping_score,
                    'potential_profit_target_1' => round($signal->potential_profit_target_1, 2),
                    'potential_profit_target_2' => round($signal->potential_profit_target_2, 2),
                    'potential_loss' => round($signal->potential_loss, 2),
                    'expires_at' => $signal->expires_at->format('Y-m-d H:i:s'),
                    'created_at' => $signal->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Get signal statistics for dashboard
     */
    public function getStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();

        $stats = [
            'total_active' => Signal::active()->count(),
            'high_confidence' => Signal::active()->highConfidence(80)->count(),
            'signals_today' => Signal::whereDate('created_at', $today)->count(),
            'signals_this_week' => Signal::where('created_at', '>=', $thisWeek)->count(),
            'win_rate_this_week' => $this->calculateWinRate($thisWeek),
            'by_timeframe' => [
                '1h' => Signal::active()->forTimeframe('1h')->count(),
                '1d' => Signal::active()->forTimeframe('1d')->count(),
            ],
            'by_confidence' => [
                'Conservative' => Signal::active()->where('confidence_level', 'Conservative')->count(),
                'Moderate' => Signal::active()->where('confidence_level', 'Moderate')->count(),
                'Aggressive' => Signal::active()->where('confidence_level', 'Aggressive')->count(),
                'Speculative' => Signal::active()->where('confidence_level', 'Speculative')->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Calculate win rate for given period
     */
    private function calculateWinRate($fromDate)
    {
        $completedSignals = Signal::where('created_at', '>=', $fromDate)
            ->whereIn('status', ['hit_target_1', 'hit_target_2', 'hit_stop_loss', 'expired'])
            ->get();

        if ($completedSignals->count() === 0) {
            return null;
        }

        $wins = $completedSignals->whereIn('status', ['hit_target_1', 'hit_target_2'])->count();

        return round(($wins / $completedSignals->count()) * 100, 1);
    }

    /**
     * Show individual signal details
     */
    public function show(Signal $signal)
    {
        return response()->json([
            'success' => true,
            'signal' => $signal
        ]);
    }
}
