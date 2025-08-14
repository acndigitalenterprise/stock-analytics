<?php

namespace App\Services;

class StockService
{
    public static function ensureJKFormat(string $stockCode): string
    {
        $upper = strtoupper(trim($stockCode));
        if (str_ends_with($upper, '.JK')) {
            return $upper;
        }
        return $upper . '.JK';
    }

    public static function getCompanyName(string $stockCode): string
    {
        $baseCode = strtoupper(str_replace('.JK', '', trim($stockCode)));
        $companies = config('companies', []);
        return $companies[$baseCode] ?? 'Unknown Company';
    }
}




