@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
@php $isAdminLayout = true; @endphp

<h2>Requests > Detail</h2>

<!-- TradingView Chart - Full Width -->
<div style="margin: 30px 0;">
    <div style="height: 400px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
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
</div>

<div style="max-width:500px;margin-top:32px;">
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
        <a href="{{ route('stock-analytics.admin.requests') }}" class="btn secondary">Back</a>
    </div>
</div>
@endsection 