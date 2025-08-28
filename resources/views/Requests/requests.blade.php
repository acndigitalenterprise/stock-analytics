@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('Requests/requests.css') }}?v={{ time() }}">

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="requests-flex-between">
        <div>
            <h2 class="requests-title">Requests</h2>
            @php
                $winningStats = app(\App\Services\PriceMonitoringService::class)->getWinningRateStats();
            @endphp
            @if($winningStats['total_requests'] > 0)
                <p class="requests-stats">
                    🎯 AI Accuracy: <strong class="requests-stats-value {{ $winningStats['winning_rate'] >= 70 ? 'requests-stats-excellent' : ($winningStats['winning_rate'] >= 50 ? 'requests-stats-good' : 'requests-stats-poor') }}">{{ $winningStats['winning_rate'] }}%</strong>
                    ({{ $winningStats['total_wins'] }} Win, {{ $winningStats['losses'] }} Loss)
                </p>
            @endif
        </div>
        @php
            $currentHour = now()->setTimezone('Asia/Jakarta')->format('H');
            $isTradingHours = $currentHour >= 9 && $currentHour < 16;
            $currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i');
        @endphp
        
        <div>
            @if($isTradingHours)
                <button class="btn requests-new-btn" onclick="document.getElementById('request-modal-final').style.cssText='display:flex !important;position:absolute !important;top:0 !important;left:0 !important;width:100vw !important;min-height:100vh !important;background:rgba(0,0,0,0.8) !important;z-index:999999 !important;justify-content:center !important;align-items:flex-start !important;padding:40px 0 !important;overflow-y:auto !important;'">New Request</button>
            @else
                <button class="btn requests-new-btn" disabled title="Trading hours: 09:00-16:00 WIB (Current: {{ $currentTime }} WIB)">
                    New Request (Market Closed)
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="requests-message requests-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="requests-message requests-error">{{ session('error') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('stock-analytics.admin.requests') }}" class="requests-filter-bar requests-search-form">
        <div class="requests-filter-bar-inner">
            <div class="form-group">
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? 'Search by name, email, stock code...' : 'Search by stock code...' }}">
            </div>
            <div>
                <button type="submit" class="btn requests-search-btn">Search</button>
            </div>
        </div>
    </form>

    <!-- Requests Table (Desktop) -->
    <div class="table-responsive">
        <table class="table requests-table">
        <thead>
            <tr>
                <th class="requests-date-column sortable" data-sort="created_at">
                    Date
                    <span class="sort-indicator">
                        @if(request('sort') == 'created_at')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                <th class="sortable" data-sort="full_name">
                    Full Name
                    <span class="sort-indicator">
                        @if(request('sort') == 'full_name')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                @endif
                <th class="requests-stock-column sortable" data-sort="stock_code">
                    SC
                    <span class="sort-indicator">
                        @if(request('sort') == 'stock_code')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="requests-company-column sortable" data-sort="company_name">
                    Company Name
                    <span class="sort-indicator">
                        @if(request('sort') == 'company_name')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="requests-timeframe-column">
                    TF
                </th>
                <th>
                    Advice
                </th>
                <th class="requests-result-column sortable" data-sort="result">
                    Result
                    <span class="sort-indicator">
                        @if(request('sort') == 'result')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="requests-action-column">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr class="row-link" data-href="{{ route('stock-analytics.admin.detail', $request->id) }}">
                    <td class="requests-date-cell">{{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}</td>
                    @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                    <td>{{ $request->full_name }}</td>
                    @endif
                    <td>{{ $request->stock_code }}</td>
                    <td>{{ $request->company_name }}</td>
                    <td>{{ \App\Providers\AppServiceProvider::formatTimeframe($request->timeframe) }}</td>
                    <td>
                        <div id="advice-text-{{ $request->id }}" class="requests-advice-content">
                            {!! $request->advice ? nl2br(e(str_replace('```markdown', '', $request->advice))) : '-' !!}
                        </div>
                    </td>
                    <td class="requests-result-cell">
                        @php
                            $resultData = [
                                'SUPER_WIN' => ['emoji' => '🏆', 'text' => 'Super Win', 'class' => 'requests-result-super-win'],
                                'WIN' => ['emoji' => '✅', 'text' => 'Win', 'class' => 'requests-result-win'],
                                'LOSS' => ['emoji' => '❌', 'text' => 'Loss', 'class' => 'requests-result-loss'],
                                'TIMEOUT' => ['emoji' => '⏰', 'text' => 'Timeout', 'class' => 'requests-result-timeout'],
                                'MONITORING' => ['emoji' => '⏳', 'text' => 'Monitor', 'class' => 'requests-result-monitoring']
                            ];
                            $result = $resultData[$request->result ?? 'MONITORING'];
                        @endphp
                        
                        <span class="requests-result-badge {{ $result['class'] }}" title="@if($request->result_achieved_at)Achieved at: {{ \Carbon\Carbon::parse($request->result_achieved_at)->setTimezone('Asia/Jakarta')->format('H:i:s T') }}@endif">
                            {{ $result['text'] }}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <div class="requests-action-container">
                            <a href="{{ route('stock-analytics.admin.detail', $request->id) }}" class="requests-action-btn requests-btn-view">View</a>
                            <form action="{{ route('stock-analytics.admin.delete', $request->id) }}" method="POST" onsubmit="return confirmRequestDeletion(event, {{ $request->id }})">
                                @csrf
                                <button type="submit" class="requests-action-btn requests-btn-delete">Delete</button>
                            </form>
                            <button type="button" class="requests-action-btn {{ !empty($request->advice) ? 'requests-btn-disabled' : 'requests-btn-edit' }}" 
                                    data-request-id="{{ $request->id }}" 
                                    {{ !empty($request->advice) ? 'disabled' : '' }}
                                    onclick="checkAdvice({{ $request->id }})">
                                Advice
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? '8' : '7' }}" class="requests-no-results">
                        No requests found.
                    </td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    @forelse($requests as $request)
        <div class="requests-mobile-card">
            <div class="requests-mobile-detail">
                <!-- Date -->
                <div class="requests-mobile-field">
                    <b>Date</b><br>{{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}
                </div>
                
                <!-- User Name (Admin only) -->
                @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                <div class="requests-mobile-field">
                    <b>Full Name</b><br>{{ $request->full_name }}
                </div>
                @endif
                
                <!-- Stock Code -->
                <div class="requests-mobile-field">
                    <b>Stock Code</b><br>{{ $request->stock_code }}
                </div>
                
                <!-- Timeframe -->
                <div class="requests-mobile-field">
                    <b>Timeframe</b><br>{{ \App\Providers\AppServiceProvider::formatTimeframe($request->timeframe) }}
                </div>
                
                <!-- Action Buttons -->
                <div class="requests-mobile-actions" onclick="event.stopPropagation();">
                    <a href="{{ route('stock-analytics.admin.detail', $request->id) }}" class="requests-action-btn requests-btn-view">View</a>
                    <form action="{{ route('stock-analytics.admin.delete', $request->id) }}" method="POST" onsubmit="return confirmRequestDeletion(event, {{ $request->id }})">
                        @csrf
                        <button type="submit" class="requests-action-btn requests-btn-delete">Delete</button>
                    </form>
                    <button type="button" class="requests-action-btn {{ !empty($request->advice) ? 'requests-btn-disabled' : 'requests-btn-edit' }}" 
                            data-request-id="{{ $request->id }}" 
                            {{ !empty($request->advice) ? 'disabled' : '' }}
                            onclick="checkAdvice({{ $request->id }})">
                        Advice
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="requests-mobile-card requests-no-results">
            No requests found.
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="requests-pagination-container">
        <div class="requests-pagination-left">
            <select id="perPageRequests" onchange="changeRequestsPerPage(this.value)">
                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
        
        @if($requests->hasPages())
            <div class="requests-pagination-right">
                <span class="requests-pagination-info">
                    Showing {{ $requests->firstItem() }}-{{ $requests->lastItem() }} of {{ $requests->total() }}
                </span>
                
                <div class="requests-pagination-buttons">
                    @if($requests->onFirstPage())
                        <button class="requests-pagination-btn" disabled>&lt;</button>
                    @else
                        <button class="requests-pagination-btn" onclick="window.location='{{ $requests->appends(request()->query())->previousPageUrl() }}'">&lt;</button>
                    @endif
                    
                    @if($requests->hasMorePages())
                        <button class="requests-pagination-btn" onclick="window.location='{{ $requests->appends(request()->query())->nextPageUrl() }}'">&gt;</button>
                    @else
                        <button class="requests-pagination-btn" disabled>&gt;</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- New Request Modal (MOVED OUTSIDE CONTAINER) -->
<div id="request-modal-final" style="display:none !important;position:absolute !important;top:0 !important;left:0 !important;width:100vw !important;min-height:100vh !important;background:rgba(0,0,0,0.8) !important;z-index:999999 !important;justify-content:center !important;align-items:flex-start !important;padding:40px 0 !important;overflow-y:auto !important;">
        <div class="auth-form-container requests-auth-form-container">
            <div class="auth-info-note">
                <strong>New Stock Request</strong><br>
                Submit a new stock analysis request with the required information.
            </div>
            
            <form action="{{ route('stock-analytics.admin.request') }}" method="POST" id="newRequestForm" class="auth-form">
                @csrf
                <div class="auth-form-group">
                    <label for="stock_code">Stock Code<span class="auth-required">*</span></label>
                    <input type="text" name="stock_code" id="stock_code" required placeholder="e.g., BBCA.JK">
                </div>
                
                <div class="auth-form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" name="company_name" id="company_name" placeholder="Optional">
                </div>
                
                <div class="auth-form-group">
                    <label for="timeframe">Timeframe<span class="auth-required">*</span></label>
                    <select name="timeframe" id="timeframe" required>
                        <option value="">Select Timeframe</option>
                        <option value="1h">1 Hour</option>
                        <option value="1d">1 Day</option>
                    </select>
                </div>
                
                <div class="requests-modal-actions">
                    <button type="submit" class="auth-btn auth-btn-primary requests-modal-btn-primary">Submit Request</button>
                    <button type="button" class="auth-btn requests-modal-btn-cancel" onclick="document.getElementById('request-modal-final').style.display='none'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Set URLs for JavaScript
    window.stockSearchUrl = '{{ route("api.stocks.search") }}';
</script>
<script src="{{ asset('Requests/requests.js') }}"></script>
@endsection