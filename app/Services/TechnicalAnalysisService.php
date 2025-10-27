<?php

namespace App\Services;

/**
 * Technical Analysis Service
 * 
 * Provides comprehensive technical indicators calculation
 * including RSI, MACD, Bollinger Bands, Moving Averages, etc.
 */
class TechnicalAnalysisService
{
    /**
     * Calculate RSI (Relative Strength Index)
     */
    public function calculateRSI(array $prices, int $period = 14): ?float
    {
        if (count($prices) < $period + 1) {
            return null;
        }

        $gains = [];
        $losses = [];

        for ($i = 1; $i < count($prices); $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? abs($change) : 0;
        }

        $avgGain = array_sum(array_slice($gains, -$period)) / $period;
        $avgLoss = array_sum(array_slice($losses, -$period)) / $period;

        if ($avgLoss == 0) {
            return 100;
        }

        $rs = $avgGain / $avgLoss;
        return 100 - (100 / (1 + $rs));
    }

    /**
     * Calculate MACD (Moving Average Convergence Divergence)
     */
    public function calculateMACD(array $prices, int $fastPeriod = 12, int $slowPeriod = 26, int $signalPeriod = 9): array
    {
        $emaFast = $this->calculateEMA($prices, $fastPeriod);
        $emaSlow = $this->calculateEMA($prices, $slowPeriod);
        
        if (!$emaFast || !$emaSlow) {
            return ['macd' => null, 'signal' => null, 'histogram' => null];
        }

        $macd = $emaFast - $emaSlow;
        $macdLine = [$macd];
        
        // For proper MACD, we need historical MACD values to calculate signal line
        // This is simplified - in production, you'd need more historical data
        $signal = $macd; // Simplified
        $histogram = $macd - $signal;

        return [
            'macd' => round($macd, 4),
            'signal' => round($signal, 4),
            'histogram' => round($histogram, 4)
        ];
    }

    /**
     * Calculate Bollinger Bands
     */
    public function calculateBollingerBands(array $prices, int $period = 20, float $stdDev = 2): array
    {
        if (count($prices) < $period || $period <= 0) {
            return ['upper' => null, 'middle' => null, 'lower' => null];
        }

        $sma = $this->calculateSMA($prices, $period);
        $recentPrices = array_slice($prices, -$period);
        
        $variance = 0;
        foreach ($recentPrices as $price) {
            $variance += pow($price - $sma, 2);
        }
        $standardDeviation = sqrt($variance / $period);

        return [
            'upper' => round($sma + ($stdDev * $standardDeviation), 2),
            'middle' => round($sma, 2),
            'lower' => round($sma - ($stdDev * $standardDeviation), 2),
            'bandwidth' => $sma != 0 ? round((($sma + ($stdDev * $standardDeviation)) - ($sma - ($stdDev * $standardDeviation))) / $sma * 100, 2) : 0
        ];
    }

    /**
     * Calculate Simple Moving Average
     */
    public function calculateSMA(array $prices, int $period): ?float
    {
        if (count($prices) < $period) {
            return null;
        }

        $recentPrices = array_slice($prices, -$period);
        return array_sum($recentPrices) / $period;
    }

    /**
     * Calculate Exponential Moving Average
     */
    public function calculateEMA(array $prices, int $period): ?float
    {
        if (count($prices) < $period) {
            return null;
        }

        $multiplier = 2 / ($period + 1);
        $ema = $prices[0];

        for ($i = 1; $i < count($prices); $i++) {
            $ema = ($prices[$i] * $multiplier) + ($ema * (1 - $multiplier));
        }

        return $ema;
    }

    /**
     * Calculate Stochastic Oscillator
     */
    public function calculateStochastic(array $highs, array $lows, array $closes, int $kPeriod = 14, int $dPeriod = 3): array
    {
        if (count($highs) < $kPeriod || count($lows) < $kPeriod || count($closes) < $kPeriod) {
            return ['%K' => null, '%D' => null];
        }

        $recentHighs = array_slice($highs, -$kPeriod);
        $recentLows = array_slice($lows, -$kPeriod);
        $currentClose = $closes[0];

        $highestHigh = max($recentHighs);
        $lowestLow = min($recentLows);

        if ($highestHigh == $lowestLow) {
            $percentK = 50;
        } else {
            $percentK = (($currentClose - $lowestLow) / ($highestHigh - $lowestLow)) * 100;
        }

        // Simplified %D calculation (normally would need more historical %K values)
        $percentD = $percentK;

        return [
            '%K' => round($percentK, 2),
            '%D' => round($percentD, 2)
        ];
    }

    /**
     * Calculate Williams %R
     */
    public function calculateWilliamsR(array $highs, array $lows, array $closes, int $period = 14): ?float
    {
        if (count($highs) < $period || count($lows) < $period || count($closes) < $period) {
            return null;
        }

        $recentHighs = array_slice($highs, -$period);
        $recentLows = array_slice($lows, -$period);
        $currentClose = $closes[0];

        $highestHigh = max($recentHighs);
        $lowestLow = min($recentLows);

        if ($highestHigh == $lowestLow) {
            return -50;
        }

        return round((($highestHigh - $currentClose) / ($highestHigh - $lowestLow)) * -100, 2);
    }

    /**
     * Calculate ADX (Average Directional Index) - Simplified
     */
    public function calculateADX(array $highs, array $lows, array $closes, int $period = 14): ?float
    {
        if (count($highs) < $period + 1 || count($lows) < $period + 1 || count($closes) < $period + 1) {
            return null;
        }

        // Simplified ADX calculation - in production would need more complex calculation
        $trueRanges = [];
        $directionalMovements = [];

        for ($i = 1; $i < count($closes); $i++) {
            $high = $highs[$i];
            $low = $lows[$i];
            $close = $closes[$i];
            $prevHigh = $highs[$i - 1];
            $prevLow = $lows[$i - 1];
            $prevClose = $closes[$i - 1];

            $tr = max(
                $high - $low,
                abs($high - $prevClose),
                abs($low - $prevClose)
            );
            $trueRanges[] = $tr;

            $plusDM = ($high - $prevHigh > $prevLow - $low) ? max($high - $prevHigh, 0) : 0;
            $minusDM = ($prevLow - $low > $high - $prevHigh) ? max($prevLow - $low, 0) : 0;
            
            $directionalMovements[] = ['plus' => $plusDM, 'minus' => $minusDM];
        }

        // Simplified ADX - normally would calculate DI+, DI-, then ADX
        $avgTR = array_sum(array_slice($trueRanges, -$period)) / $period;
        
        if ($avgTR == 0) {
            return 0;
        }

        // This is a simplified trending strength indicator
        $recentPrices = array_slice($closes, -$period);
        $priceRange = max($recentPrices) - min($recentPrices);
        $volatility = ($recentPrices[0] != 0) ? ($priceRange / $recentPrices[0] * 100) : 0;

        return min(100, max(0, round($volatility * 2, 2)));
    }

    /**
     * Calculate Support and Resistance Levels
     */
    public function calculateSupportResistance(array $highs, array $lows, array $closes): array
    {
        if (count($highs) < 20 || count($lows) < 20 || count($closes) < 20) {
            return ['support' => null, 'resistance' => null];
        }

        $recentHighs = array_slice($highs, -20);
        $recentLows = array_slice($lows, -20);
        $currentPrice = $closes[0];

        // Find resistance (recent highs above current price)
        $resistanceLevels = array_filter($recentHighs, function($high) use ($currentPrice) {
            return $high > $currentPrice;
        });

        // Find support (recent lows below current price)
        $supportLevels = array_filter($recentLows, function($low) use ($currentPrice) {
            return $low < $currentPrice;
        });

        $resistance = !empty($resistanceLevels) ? min($resistanceLevels) : max($recentHighs);
        $support = !empty($supportLevels) ? max($supportLevels) : min($recentLows);

        return [
            'support' => round($support, 2),
            'resistance' => round($resistance, 2),
            'support_strength' => $this->calculateLevelStrength($support, $lows),
            'resistance_strength' => $this->calculateLevelStrength($resistance, $highs)
        ];
    }

    /**
     * Calculate how strong a support/resistance level is
     */
    private function calculateLevelStrength(float $level, array $prices): string
    {
        $touches = 0;
        $tolerance = $level * 0.005; // 0.5% tolerance

        foreach ($prices as $price) {
            if (abs($price - $level) <= $tolerance) {
                $touches++;
            }
        }

        if ($touches >= 3) return 'Strong';
        if ($touches >= 2) return 'Moderate';
        return 'Weak';
    }

    /**
     * Get comprehensive technical analysis
     */
    public function getComprehensiveAnalysis(array $ohlcvData): array
    {
        $closes = array_column($ohlcvData, 'close');
        $highs = array_column($ohlcvData, 'high');
        $lows = array_column($ohlcvData, 'low');
        $volumes = array_column($ohlcvData, 'volume');

        return [
            'rsi' => $this->calculateRSI($closes),
            'rsi_signal' => $this->getRSISignal($this->calculateRSI($closes)),
            'macd' => $this->calculateMACD($closes),
            'bollinger_bands' => $this->calculateBollingerBands($closes),
            'sma_20' => $this->calculateSMA($closes, 20),
            'sma_50' => $this->calculateSMA($closes, 50),
            'ema_12' => $this->calculateEMA($closes, 12),
            'ema_26' => $this->calculateEMA($closes, 26),
            'stochastic' => $this->calculateStochastic($highs, $lows, $closes),
            'williams_r' => $this->calculateWilliamsR($highs, $lows, $closes),
            'adx' => $this->calculateADX($highs, $lows, $closes),
            'support_resistance' => $this->calculateSupportResistance($highs, $lows, $closes),
            'volume_sma' => $this->calculateSMA($volumes, 20),
            'price_vs_sma20' => $this->getPriceVsSMA($closes, 20),
            'price_vs_sma50' => $this->getPriceVsSMA($closes, 50),
            'trend_direction' => $this->getTrendDirection($closes),
            'volatility' => $this->calculateVolatility($closes)
        ];
    }

    /**
     * Get scalping-specific technical analysis optimized for multiple timeframes
     * Now supports: 1h/1d (scalping), 1w (swing), 1m (position)
     */
    public function getScalpingAnalysis(array $ohlcvData, ?string $stockSymbol = null, string $timeframe = '1h'): array
    {
        $closes = array_column($ohlcvData, 'close');
        $highs = array_column($ohlcvData, 'high');
        $lows = array_column($ohlcvData, 'low');
        $opens = array_column($ohlcvData, 'open');
        $volumes = array_column($ohlcvData, 'volume');

        return [
            // Scalping-optimized indicators
            'ema_9' => $this->calculateEMA($closes, 9),
            'ema_9_signal' => $this->getEMA9Signal($closes),
            'vwap' => $this->calculateVWAP($ohlcvData),
            'vwap_signal' => $this->getVWAPSignal($closes, $this->calculateVWAP($ohlcvData)),
            'rsi_7' => $this->calculateRSI($closes, 7),
            'rsi_7_signal' => $this->getRSI7Signal($this->calculateRSI($closes, 7)),
            'bollinger_bands_scalping' => $this->calculateBollingerBands($closes, 7, 1.5),
            'bb_scalping_signal' => $this->getBBScalpingSignal($closes, $this->calculateBollingerBands($closes, 7, 1.5)),
            'stochastic_scalping' => $this->calculateStochasticScalping($highs, $lows, $closes),
            'stoch_scalping_signal' => $this->getStochScalpingSignal($this->calculateStochasticScalping($highs, $lows, $closes)),
            'macd_scalping' => $this->calculateMACDScalping($closes),
            'macd_scalping_signal' => $this->getMACDScalpingSignal($this->calculateMACDScalping($closes)),
            'parabolic_sar' => $this->calculateParabolicSAR($highs, $lows),
            'sar_signal' => $this->getSARSignal($closes, $this->calculateParabolicSAR($highs, $lows)),
            'cpr' => $this->calculateCPR($ohlcvData),
            'cpr_signal' => $this->getCPRSignal($closes, $this->calculateCPR($ohlcvData)),

            // Combined scalping signals
            'scalping_score' => $this->calculateScalpingScore($ohlcvData, $stockSymbol),
            'scalping_action' => $this->getScalpingAction($ohlcvData, $stockSymbol),

            // Use timeframe-aware targets for entry, targets, and stop loss
            ...$this->calculateTimeframeTargets($ohlcvData, $timeframe)
        ];
    }

    /**
     * Get RSI signal interpretation
     */
    private function getRSISignal(?float $rsi): string
    {
        if ($rsi === null) return 'N/A';
        if ($rsi > 70) return 'Overbought';
        if ($rsi < 30) return 'Oversold';
        if ($rsi > 50) return 'Bullish';
        return 'Bearish';
    }

    /**
     * Compare price vs Simple Moving Average
     */
    private function getPriceVsSMA(array $closes, int $period): array
    {
        $currentPrice = $closes[0];
        $sma = $this->calculateSMA($closes, $period);
        
        if (!$sma || $sma == 0) {
            return ['position' => 'N/A', 'percentage' => 0];
        }

        $percentage = (($currentPrice - $sma) / $sma) * 100;
        $position = $currentPrice > $sma ? 'Above' : 'Below';

        return [
            'position' => $position,
            'percentage' => round($percentage, 2)
        ];
    }

    /**
     * Determine overall trend direction
     */
    private function getTrendDirection(array $closes): string
    {
        if (count($closes) < 10) return 'N/A';

        $recent = array_slice($closes, -10);
        $older = array_slice($closes, -20, 10);

        if (empty($older)) return 'N/A';

        $recentAvg = array_sum($recent) / count($recent);
        $olderAvg = array_sum($older) / count($older);

        if ($olderAvg == 0) return 'N/A';
        
        $change = (($recentAvg - $olderAvg) / $olderAvg) * 100;

        if ($change > 2) return 'Strong Uptrend';
        if ($change > 0.5) return 'Uptrend';
        if ($change < -2) return 'Strong Downtrend';
        if ($change < -0.5) return 'Downtrend';
        return 'Sideways';
    }

    /**
     * Calculate price volatility
     */
    private function calculateVolatility(array $closes, int $period = 20): ?float
    {
        if (count($closes) < $period) return null;

        $recentPrices = array_slice($closes, -$period);
        $returns = [];

        for ($i = 1; $i < count($recentPrices); $i++) {
            if ($recentPrices[$i - 1] != 0) {
                $returns[] = ($recentPrices[$i] - $recentPrices[$i - 1]) / $recentPrices[$i - 1];
            }
        }

        if (empty($returns)) return null;

        $returnCount = count($returns);
        if ($returnCount == 0) return 0;
        
        $mean = array_sum($returns) / $returnCount;
        $variance = 0;

        foreach ($returns as $return) {
            $variance += pow($return - $mean, 2);
        }

        $volatility = sqrt($variance / $returnCount) * sqrt(252) * 100; // Annualized volatility
        return round($volatility, 2);
    }

    // ==================== SCALPING-SPECIFIC METHODS ====================

    /**
     * Calculate VWAP (Volume Weighted Average Price)
     */
    public function calculateVWAP(array $ohlcvData): ?float
    {
        if (empty($ohlcvData)) return null;
        
        $totalVolumePrice = 0;
        $totalVolume = 0;
        
        foreach ($ohlcvData as $candle) {
            $typical_price = ($candle['high'] + $candle['low'] + $candle['close']) / 3;
            $volume = $candle['volume'] ?? 0;
            
            $totalVolumePrice += $typical_price * $volume;
            $totalVolume += $volume;
        }
        
        return $totalVolume > 0 ? round($totalVolumePrice / $totalVolume, 2) : null;
    }

    /**
     * Calculate Stochastic for Scalping (5,3,3)
     */
    public function calculateStochasticScalping(array $highs, array $lows, array $closes): array
    {
        return $this->calculateStochastic($highs, $lows, $closes, 5, 3);
    }

    /**
     * Calculate MACD for Scalping (5,13,1)
     */
    public function calculateMACDScalping(array $prices): array
    {
        return $this->calculateMACD($prices, 5, 13, 1);
    }

    /**
     * Calculate Parabolic SAR
     */
    public function calculateParabolicSAR(array $highs, array $lows, float $step = 0.02, float $maximum = 0.2): ?float
    {
        if (count($highs) < 3 || count($lows) < 3) return null;
        
        $currentHigh = $highs[0];
        $currentLow = $lows[0];
        $prevHigh = $highs[1]; // Previous high (second in array)
        $prevLow = $lows[1]; // Previous low (second in array)
        
        // Simplified SAR calculation - in production would need full historical calculation
        $trend = $currentHigh > $prevHigh ? 'up' : 'down';
        
        if ($trend === 'up') {
            $sar = $currentLow - (($currentHigh - $currentLow) * $step);
        } else {
            $sar = $currentHigh + (($currentHigh - $currentLow) * $step);
        }
        
        return round($sar, 2);
    }

    /**
     * Calculate CPR (Central Pivot Range)
     */
    public function calculateCPR(array $ohlcvData): array
    {
        if (empty($ohlcvData)) {
            return ['pivot' => null, 'bc' => null, 'tc' => null, 'r1' => null, 's1' => null];
        }
        
        $yesterday = $ohlcvData[1] ?? $ohlcvData[0];
        $high = $yesterday['high'];
        $low = $yesterday['low'];
        $close = $yesterday['close'];
        
        $pivot = ($high + $low + $close) / 3;
        $bc = ($high + $low) / 2;
        $tc = ($pivot - $bc) + $pivot;
        $r1 = (2 * $pivot) - $low;
        $s1 = (2 * $pivot) - $high;
        
        return [
            'pivot' => round($pivot, 2),
            'bc' => round($bc, 2),
            'tc' => round($tc, 2),
            'r1' => round($r1, 2),
            's1' => round($s1, 2),
            'width' => round($tc - $bc, 2)
        ];
    }

    // ==================== SIGNAL INTERPRETATION METHODS ====================

    public function getEMA9Signal(array $closes): string
    {
        $ema9 = $this->calculateEMA($closes, 9);
        $currentPrice = $closes[0];
        
        if (!$ema9) return 'N/A';
        
        if ($currentPrice > $ema9) return 'Bullish Trend';
        return 'Bearish Trend';
    }

    public function getVWAPSignal(array $closes, ?float $vwap): string
    {
        if (!$vwap) return 'N/A';
        
        $currentPrice = $closes[0];
        if ($currentPrice > $vwap) return 'Above VWAP (Bullish)';
        return 'Below VWAP (Bearish)';
    }

    public function getRSI7Signal(?float $rsi): string
    {
        if ($rsi === null) return 'N/A';
        if ($rsi > 70) return 'Overbought';
        if ($rsi < 30) return 'Oversold - Buy Signal';
        if ($rsi > 50) return 'Bullish Zone';
        return 'Bearish Zone';
    }

    public function getBBScalpingSignal(array $closes, array $bb): string
    {
        if (!isset($bb['upper']) || !isset($bb['lower'])) return 'N/A';
        
        $currentPrice = $closes[0];
        
        if ($currentPrice <= $bb['lower']) return 'Oversold - Strong Buy Signal';
        if ($currentPrice >= $bb['upper']) return 'Overbought - Avoid Buy';
        if ($currentPrice < $bb['middle']) return 'Below Middle - Potential Buy';
        return 'Above Middle - Momentum';
    }

    public function getStochScalpingSignal(array $stoch): string
    {
        if (!isset($stoch['%K']) || !isset($stoch['%D'])) return 'N/A';
        
        $k = $stoch['%K'];
        $d = $stoch['%D'];
        
        if ($k < 20 && $d < 20 && $k > $d) return 'Oversold Bullish Crossover - Buy Signal';
        if ($k > 80 && $d > 80) return 'Overbought - Avoid Buy';
        if ($k > $d) return 'Bullish Momentum';
        return 'Bearish Momentum';
    }

    public function getMACDScalpingSignal(array $macd): string
    {
        if (!isset($macd['macd']) || !isset($macd['signal'])) return 'N/A';
        
        $macdLine = $macd['macd'];
        $signalLine = $macd['signal'];
        $histogram = $macd['histogram'];
        
        if ($macdLine > $signalLine && $histogram > 0) return 'Strong Bullish - Buy Signal';
        if ($macdLine > $signalLine) return 'Bullish Crossover';
        return 'Bearish - Avoid Buy';
    }

    public function getSARSignal(array $closes, ?float $sar): string
    {
        if (!$sar) return 'N/A';
        
        $currentPrice = $closes[0];
        if ($currentPrice > $sar) return 'Uptrend - Buy Signal';
        return 'Downtrend - Avoid Buy';
    }

    public function getCPRSignal(array $closes, array $cpr): string
    {
        if (!isset($cpr['pivot'])) return 'N/A';
        
        $currentPrice = $closes[0];
        $pivot = $cpr['pivot'];
        $bc = $cpr['bc'];
        $tc = $cpr['tc'];
        
        if ($currentPrice > $tc) return 'Above CPR - Strong Bull';
        if ($currentPrice > $pivot) return 'Above Pivot - Bullish';
        if ($currentPrice < $bc) return 'Below CPR - Bearish';
        return 'Inside CPR - Consolidation';
    }

    // ==================== COMBINED SCALPING LOGIC ====================

    public function calculateScalpingScore(array $ohlcvData, ?string $stockSymbol = null): int
    {
        $score = 0;
        $dataCount = count($ohlcvData);
        $closes = array_column($ohlcvData, 'close');
        $highs = array_column($ohlcvData, 'high');
        $lows = array_column($ohlcvData, 'low');
        $currentPrice = $closes[0];
        
        // Market hours consideration for Indonesian stocks
        $isIDXStock = $stockSymbol && (strpos($stockSymbol, '.JK') !== false);
        $marketHoursBonus = $this->getMarketHoursBonus($isIDXStock);
        
        
        // Base score berdasarkan basic price action jika data terbatas
        if ($dataCount >= 2) {
            $prevClose = $closes[1]; // Previous price (second in array)
            $priceChange = ($prevClose != 0) ? (($currentPrice - $prevClose) / $prevClose * 100) : 0;
            
            // Basic momentum scoring
            if ($priceChange > 2) $score += 3; // Strong upward momentum
            elseif ($priceChange > 0.5) $score += 2; // Moderate upward 
            elseif ($priceChange > 0) $score += 1; // Slight upward
            
            // Volume analysis (if available)
            $volumes = array_column($ohlcvData, 'volume');
            if (count($volumes) >= 2) {
                $currentVol = $volumes[0];
                $avgVol = array_sum($volumes) / count($volumes);
                $volumeRatio = $avgVol > 0 ? ($currentVol / $avgVol) : 1;
                
                if ($currentVol > $avgVol * 1.2) $score += 1; // High volume
            }
        }
        
        // Technical indicators (only if enough data)
        if ($dataCount >= 9) {
            // EMA 9 check
            $ema9 = $this->calculateEMA($closes, 9);
            if ($ema9 && $currentPrice > $ema9) $score += 2;
            
            // VWAP check
            $vwap = $this->calculateVWAP($ohlcvData);
            if ($vwap && $currentPrice > $vwap) $score += 1;
        }
        
        if ($dataCount >= 7) {
            // RSI 7 check
            $rsi7 = $this->calculateRSI($closes, 7);
            if ($rsi7 && $rsi7 < 30) $score += 3; // Oversold = strong buy
            elseif ($rsi7 && $rsi7 < 50 && $rsi7 > 30) $score += 1;
            
            // BB Scalping check
            $bb = $this->calculateBollingerBands($closes, 7, 1.5);
            if (isset($bb['lower']) && $currentPrice <= $bb['lower']) $score += 2;
            elseif (isset($bb['middle']) && $currentPrice < $bb['middle']) $score += 1;
        }
        
        if ($dataCount >= 5) {
            // Stochastic check
            $stoch = $this->calculateStochasticScalping($highs, $lows, $closes);
            if (isset($stoch['%K']) && $stoch['%K'] < 20 && $stoch['%K'] > $stoch['%D']) $score += 1;
            
            // MACD Scalping check
            $macd = $this->calculateMACDScalping($closes);
            if (isset($macd['macd']) && $macd['macd'] > $macd['signal']) $score += 1;
        }
        
        // Support/Resistance level
        if ($dataCount >= 3) {
            $highest = max($highs);
            $lowest = min($lows);
            $range = $highest - $lowest;
            
            // If price near support, add score
            if ($range > 0 && ($currentPrice - $lowest) / $range < 0.3) $score += 2;
        }
        
        // Apply market hours bonus/penalty
        $score += $marketHoursBonus;
        
        return min($score, 10); // Max score 10
    }

    /**
     * Calculate market hours bonus/penalty for scoring
     */
    private function getMarketHoursBonus(bool $isIDXStock): int
    {
        $jakartaTime = now()->setTimezone('Asia/Jakarta');
        $currentHour = (int) $jakartaTime->format('H');
        $currentMinute = (int) $jakartaTime->format('i');
        
        if ($isIDXStock) {
            // Indonesian stock market hours: 09:00-16:00 WIB
            if ($currentHour >= 9 && $currentHour < 16) {
                // During market hours
                if ($currentHour >= 9 && $currentHour < 11) {
                    // Opening hours (9:00-11:00) - High activity
                    return 1;
                } elseif ($currentHour >= 13 && $currentHour < 15) {
                    // Afternoon session (13:00-15:00) - Good activity
                    return 1;
                } else {
                    // Normal trading hours
                    return 0;
                }
            } else {
                // Outside market hours - penalize strongly
                return -2;
            }
        } else {
            // Global stocks - different considerations
            // Check major market hours overlap
            
            // US market hours in WIB: 21:30-04:00 (winter) or 20:30-03:00 (summer)
            // European market hours in WIB: 15:00-23:00
            // Asian market hours in WIB: 08:00-17:00
            
            if (($currentHour >= 15 && $currentHour < 23) || // European hours
                ($currentHour >= 21 && $currentHour <= 23) || // US pre-market
                ($currentHour >= 0 && $currentHour < 4) ||    // US main hours
                ($currentHour >= 8 && $currentHour < 17)) {   // Asian hours
                return 0; // Normal global trading activity
            } else {
                // Low activity hours
                return -1;
            }
        }
    }

    public function getScalpingAction(array $ohlcvData, ?string $stockSymbol = null): string
    {
        $score = $this->calculateScalpingScore($ohlcvData, $stockSymbol);
        
        // More balanced thresholds for Indonesian market
        if ($score >= 7) return 'STRONG BUY';
        if ($score >= 5) return 'BUY';
        if ($score >= 3) return 'WEAK BUY';
        if ($score >= 1) return 'HOLD';
        return 'WEAK SELL';
    }

    public function calculateEntryPrice(array $ohlcvData): ?float
    {
        $closes = array_column($ohlcvData, 'close');
        $highs = array_column($ohlcvData, 'high');
        $lows = array_column($ohlcvData, 'low');
        $currentPrice = $closes[0];
        
        // Intelligent entry price prediction like ChatGPT
        $signals = $this->analyzeEntrySignals($ohlcvData);
        
        // Simplified intelligent entry logic
        if ($signals['momentum'] === 'strong_bullish') {
            // Scenario 1: Strong Rally - enter at current price (don't miss it!)
            return round($currentPrice, 2);
        } 
        elseif ($signals['at_resistance']) {
            // Scenario 2: At Resistance - wait for pullback to support
            $pullbackEntry = $this->findNearestSupport($lows, $currentPrice);
            return round($pullbackEntry, 2);
        }
        else {
            // Default: slight pullback entry (balanced approach)
            return round($currentPrice * 0.995, 2); // 0.5% below current
        }
    }
    
    /**
     * Analyze multiple signals for intelligent entry prediction
     */
    private function analyzeEntrySignals(array $ohlcvData): array
    {
        $closes = array_column($ohlcvData, 'close');
        $highs = array_column($ohlcvData, 'high');
        $lows = array_column($ohlcvData, 'low');
        $volumes = array_column($ohlcvData, 'volume');
        
        $currentPrice = $closes[0];
        $signals = [];
        
        // 1. Momentum analysis
        $recentCloses = array_slice($closes, 0, 5);
        
        // Check if we have enough data points and avoid division by zero
        if (count($recentCloses) >= 5 && $recentCloses[4] != 0) {
            $priceChange = ($recentCloses[0] - $recentCloses[4]) / $recentCloses[4];
        } elseif (count($recentCloses) >= 2 && $recentCloses[1] != 0) {
            // Fallback to comparing current vs previous close
            $priceChange = ($recentCloses[0] - $recentCloses[1]) / $recentCloses[1];
        } else {
            // No valid comparison possible
            $priceChange = 0;
        }
        
        if ($priceChange > 0.02) {
            $signals['momentum'] = 'strong_bullish';
        } elseif ($priceChange > 0.005) {
            $signals['momentum'] = 'bullish';
        } elseif ($priceChange < -0.02) {
            $signals['momentum'] = 'bearish';
        } else {
            $signals['momentum'] = 'neutral';
        }
        
        // 2. Support/Resistance analysis
        $recentHighs = array_slice($highs, 0, 10);
        $recentLows = array_slice($lows, 0, 10);
        
        $resistance = max($recentHighs);
        $support = min(array_filter($recentLows, function($low) { return $low > 0; }));
        
        $signals['at_resistance'] = ($currentPrice >= $resistance * 0.995);
        $signals['support_nearby'] = ($currentPrice <= $support * 1.01);
        
        // 3. Volume analysis
        if (count($volumes) >= 5) {
            $avgVolume = array_sum(array_slice($volumes, 1, 4)) / 4;
            $signals['volume_low'] = ($volumes[0] < $avgVolume * 0.8);
        } else {
            $signals['volume_low'] = false;
        }
        
        // 4. Oversold conditions
        $rsi = $this->calculateRSI($closes, 14);
        $signals['oversold'] = ($rsi !== null && $rsi < 35);
        $signals['bounce_expected'] = ($rsi !== null && $rsi < 30);
        
        // 5. Consolidation detection
        $minPrice = min($recentCloses);
        if ($minPrice > 0) {
            $priceRange = (max($recentCloses) - $minPrice) / $minPrice;
            $signals['consolidation'] = ($priceRange < 0.02); // Less than 2% range
        } else {
            $signals['consolidation'] = false; // Cannot calculate with zero price
        }
        
        return $signals;
    }
    
    /**
     * Find nearest support level for pullback entry
     */
    private function findNearestSupport(array $lows, float $currentPrice): float
    {
        $validLows = array_filter($lows, function($low) use ($currentPrice) { 
            return $low > 0 && $low < $currentPrice; 
        });
        
        if (empty($validLows)) {
            return $currentPrice * 0.995; // Fallback
        }
        
        // Find the highest low (nearest support)
        $nearestSupport = max($validLows);
        
        // Don't go too far down (max 1% below current)
        $maxPullback = $currentPrice * 0.99;
        
        return max($nearestSupport, $maxPullback);
    }

    public function calculateTarget1(array $ohlcvData): ?float
    {
        $closes = array_column($ohlcvData, 'close');
        $currentPrice = $closes[0];
        
        // Target 1: 1-2% profit untuk scalping
        return round($currentPrice * 1.015, 2); // 1.5% target
    }

    public function calculateTarget2(array $ohlcvData): ?float
    {
        $closes = array_column($ohlcvData, 'close');
        $currentPrice = $closes[0];
        
        // Target 2: 2-4% profit
        return round($currentPrice * 1.03, 2); // 3% target
    }

    public function calculateStopLoss(array $ohlcvData): ?float
    {
        $closes = array_column($ohlcvData, 'close');
        $lows = array_column($ohlcvData, 'low');
        $currentPrice = $closes[0];
        
        // Stop loss berdasarkan recent support atau 2% below current price
        $percentageStop = $currentPrice * 0.98; // 2% stop loss
        
        // Try to find recent support, but fallback to percentage stop
        $validLows = array_filter($lows, function($low) { return $low > 0; });
        
        if (count($validLows) >= 3) {
            $recentLows = array_slice($validLows, -5); // Last 5 valid lows
            $recentLow = min($recentLows);
            
            // Use the LOWER of: recent low or 2% below current (stop loss must be below entry)
            return round(min($recentLow, $percentageStop), 2);
        } else {
            // Fallback: just use 2% below current price
            return round($percentageStop, 2);
        }
    }

    /**
     * Calculate targets based on timeframe (1h/1d = scalping, 1w = swing, 1m = position)
     * User requirements:
     * - 1h/1d: 1.5-3% targets (scalping)
     * - 1w: 5-10% targets (swing trading)
     * - 1m: 15-25% targets (position trading)
     */
    public function calculateTimeframeTargets(array $ohlcvData, string $timeframe): array
    {
        $closes = array_column($ohlcvData, 'close');
        $currentPrice = $closes[0];

        // Define target percentages based on timeframe
        [$target1Pct, $target2Pct, $stopLossPct] = match($timeframe) {
            '1h', '1d' => [1.5, 3.0, 2.0],   // Scalping: 1.5% and 3% targets, 2% stop
            '1w' => [5.0, 10.0, 3.0],         // Swing: 5% and 10% targets, 3% stop
            '1m' => [15.0, 25.0, 7.0],        // Position: 15% and 25% targets, 7% stop
            default => [1.5, 3.0, 2.0]        // Fallback to scalping
        };

        return [
            'entry_price' => round($currentPrice, 2),
            'target_1' => round($currentPrice * (1 + $target1Pct/100), 2),
            'target_2' => round($currentPrice * (1 + $target2Pct/100), 2),
            'stop_loss' => round($currentPrice * (1 - $stopLossPct/100), 2),
        ];
    }
}