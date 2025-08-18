@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<div class="admin-content-container">
@php $isAdminLayout = true; @endphp


<h2>Requests > Detail</h2>

<!-- Desktop Layout -->
<div class="desktop-detail" style="max-width:500px;margin-top:32px;">
    <div class="form-group">
        <label>Date</label>
        <div>{{ $stockRequest->created_at ? \Carbon\Carbon::parse($stockRequest->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}</div>
    </div>
    @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
    <div class="form-group">
        <label>Full Name</label>
        <div>{{ $stockRequest->full_name }}</div>
    </div>
    <div class="form-group">
        <label>Mobile Number</label>
        <div>{{ $stockRequest->mobile_number }}</div>
    </div>
    <div class="form-group">
        <label>Email Address</label>
        <div>{{ $stockRequest->email }}</div>
    </div>
    @endif
    <div class="form-group">
        <label>Stock Code</label>
        <div>{{ $stockRequest->stock_code }}</div>
    </div>
    <div class="form-group">
        <label>Company Name</label>
        <div>{{ $stockRequest->company_name }}</div>
    </div>
    <div class="form-group">
        <label>Timeframe</label>
        <div>{{ \App\Providers\AppServiceProvider::formatTimeframe($stockRequest->timeframe) }}</div>
    </div>
    <div class="form-group">
        <label>Advice by Claude</label>
        <div class="advice-content" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 16px; margin-top: 8px; line-height: 1.6;">
            {!! $stockRequest->advice ? nl2br(e(str_replace('```markdown', '', $stockRequest->advice))) : '-' !!}
        </div>
    </div>
    <div class="form-group">
        <label>Advice by ChatGPT</label>
        <div class="advice-content" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 16px; margin-top: 8px; line-height: 1.6;">
            {!! $stockRequest->advice_chatgpt ? nl2br(e(str_replace('```markdown', '', $stockRequest->advice_chatgpt))) : 'ChatGPT analysis not available' !!}
        </div>
    </div>
    <div class="form-group">
        <label>Result</label>
        <div>
            @php
                $resultClass = '';
                $resultIcon = '';
                $resultText = $stockRequest->result ?? 'PENDING';
                
                switch($resultText) {
                    case 'WIN':
                        $resultClass = 'text-success fw-bold';
                        $resultIcon = '✅';
                        break;
                    case 'SUPER_WIN':
                        $resultClass = 'text-success fw-bold';
                        $resultIcon = '🏆';
                        $resultText = 'SUPER WIN';
                        break;
                    case 'LOSS':
                        $resultClass = 'text-danger fw-bold';
                        $resultIcon = '❌';
                        break;
                    case 'TIMEOUT':
                        $resultClass = 'text-warning fw-bold';
                        $resultIcon = '⏰';
                        break;
                    case 'MONITORING':
                        $resultClass = 'text-info fw-bold';
                        $resultIcon = '👁️';
                        break;
                    default:
                        $resultClass = 'text-muted';
                        $resultIcon = '⏳';
                }
            @endphp
            <span class="{{ $resultClass }}">{{ $resultIcon }} {{ $resultText }}</span>
            <div style="font-size: 12px; color: #666; margin-top: 4px; font-style: italic;">
                This monitoring system tracks performance based on Claude's advice only.
            </div>
        </div>
    </div>
    <div style="margin-top:24px;">
        <a href="{{ route('stock-analytics.admin.requests') }}" class="btn" style="background: #333; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; width: auto !important;">Back</a>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-card">
    <div class="mobile-detail" style="display:none;">
        <!-- TradingView Chart -->
        <div style="height: 400px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container" style="height:100%;width:100%">
              <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
              <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/symbols/IDX-{{ str_replace('.JK', '', $stockRequest->stock_code) }}/?exchange=IDX" rel="noopener nofollow" target="_blank"><span class="blue-text">{{ str_replace('.JK', '', $stockRequest->stock_code) }} chart by TradingView</span></a></div>
              <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
              {
              "allow_symbol_change": true,
              "calendar": false,
              "details": false,
              "hide_side_toolbar": true,
              "hide_top_toolbar": false,
              "hide_legend": false,
              "hide_volume": false,
              "hotlist": false,
              "interval": "D",
              "locale": "en",
              "save_image": true,
              "style": "1",
              "symbol": "IDX:{{ str_replace('.JK', '', $stockRequest->stock_code) }}",
              "theme": "light",
              "timezone": "Asia/Jakarta",
              "backgroundColor": "#ffffff",
              "gridColor": "rgba(46, 46, 46, 0.06)",
              "watchlist": [],
              "withdateranges": false,
              "compareSymbols": [],
              "studies": [],
              "autosize": true
            }
              </script>
            </div>
            <!-- TradingView Widget END -->
        </div>
        
        <!-- Date -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
            <b>Date</b><br>
            {{ $stockRequest->created_at ? \Carbon\Carbon::parse($stockRequest->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}
        </div>
        
        @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
        <!-- Full Name -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
            <b>Full Name</b><br>
            {{ $stockRequest->full_name }}
        </div>
        
        <!-- Mobile Number -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
            <b>Mobile Number</b><br>
            {{ $stockRequest->mobile_number }}
        </div>
        
        <!-- Email -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
            <b>Email Address</b><br>
            {{ $stockRequest->email }}
        </div>
        @endif
        
        <!-- Stock Code -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
            <b>Stock Code</b><br>
            {{ $stockRequest->stock_code }}
        </div>
        
        <!-- Company Name -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
            <b>Company Name</b><br>
            {{ $stockRequest->company_name }}
        </div>
        
        <!-- Timeframe -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 20px;">
            <b>Timeframe</b><br>
            {{ \App\Providers\AppServiceProvider::formatTimeframe($stockRequest->timeframe) }}
        </div>
        
        <!-- Advice by Claude -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 20px;">
            <b>Advice by Claude</b><br>
            {!! $stockRequest->advice ? nl2br(e(str_replace('```markdown', '', $stockRequest->advice))) : '-' !!}
        </div>
        
        <!-- Advice by ChatGPT -->
        <div style="font-size: 0.9em; color: #000000; margin-bottom: 20px;">
            <b>Advice by ChatGPT</b><br>
            {!! $stockRequest->advice_chatgpt ? nl2br(e(str_replace('```markdown', '', $stockRequest->advice_chatgpt))) : 'ChatGPT analysis not available' !!}
        </div>
        
        <!-- Back Button -->
        <div style="margin-top: 24px;">
            <a href="{{ route('stock-analytics.admin.requests') }}" class="btn" style="background: #333; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; width: auto !important;">Back</a>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .desktop-detail {
        display: none !important;
    }
    .mobile-detail {
        display: block !important;
        margin-top: 16px;
        padding: 0 4px;
    }
}
</style>
</div>
@endsection 