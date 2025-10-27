<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Blade::directive('formatTimeframe', function ($expression) {
            return "<?php echo \\App\\Providers\\AppServiceProvider::formatTimeframe($expression); ?>";
        });
    }

    // Helper static
    public static function formatTimeframe($timeframe) {
        switch ($timeframe) {
            case '1h': return '1h';
            case '1d': return '1d';
            case '1w': return '1w';
            case '1m': return '1m';
            default: return $timeframe;
        }
    }

    public static function extractAnalyticsSummary($advice, $entryPrice = null) {
        if (empty($advice)) {
            return '-';
        }

        // Extract recommendation from advice text
        $advice = strtolower($advice);
        $recommendation = 'Hold';
        
        if (strpos($advice, 'buy') !== false || strpos($advice, 'long') !== false || strpos($advice, 'bullish') !== false) {
            $recommendation = 'Buy';
        } elseif (strpos($advice, 'sell') !== false || strpos($advice, 'short') !== false || strpos($advice, 'bearish') !== false) {
            $recommendation = 'Sell';
        }

        // If recommendation is Hold, return just "Hold" without price
        if ($recommendation === 'Hold') {
            return 'Hold';
        }

        // For Buy/Sell actions, include price information
        // Use entry price if available, otherwise try to extract from advice
        if (!$entryPrice) {
            // Try to extract price from advice (look for IDR followed by numbers)
            if (preg_match('/idr\s*([\d,\.]+)/i', $advice, $matches)) {
                $entryPrice = floatval(str_replace(',', '', $matches[1]));
            }
        }

        if ($entryPrice) {
            return $recommendation . ' at IDR ' . number_format($entryPrice, 0, '.', ',');
        }

        // If no price found but action is Buy/Sell, still show the action
        return $recommendation;
    }
}
