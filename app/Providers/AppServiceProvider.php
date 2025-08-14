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
            default: return $timeframe;
        }
    }
}
