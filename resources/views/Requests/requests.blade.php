<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Requests - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('Requests/requests.css') }}?v={{ time() }}">
</head>
<body class="admin-layout">

<!-- DESKTOP ADMIN LAYOUT -->
<div class="admin-layout-container">
    @include('Components.header')
    
    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" onclick="closeMobileMenu()"></div>
    
    <!-- Main Content Area -->
    <div class="admin-main-content">
        @include('Components.sidebar')
        
        <!-- Admin Body Content -->
        <main class="admin-body">
            <div class="admin-content-container">
    
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
            $jakartaTime = now()->setTimezone('Asia/Jakarta');
            $currentDay = $jakartaTime->format('N'); // 1=Monday, 5=Friday, 6=Saturday, 7=Sunday
            $currentTime = $jakartaTime->format('H:i:s');

            // TEMPORARY BYPASS FOR TESTING - REMOVE AFTER TESTING
            $isTradingHours = true;

            // Market hours logic (DISABLED FOR TESTING)
            // $isTradingHours = false;

            // if ($currentDay >= 1 && $currentDay <= 4) { // Monday-Thursday
            //     $isTradingHours = (
            //         ($currentTime >= '09:00:00' && $currentTime <= '12:00:00') ||
            //         ($currentTime >= '13:30:00' && $currentTime <= '15:49:59')
            //     );
            // } elseif ($currentDay == 5) { // Friday
            //     $isTradingHours = (
            //         ($currentTime >= '09:00:00' && $currentTime <= '11:30:00') ||
            //         ($currentTime >= '14:00:00' && $currentTime <= '15:49:59')
            //     );
            // }
            // Saturday and Sunday: $isTradingHours remains false
        @endphp
        
        <div>
            @if($isTradingHours)
                <button class="btn requests-new-btn" onclick="showRequestModal()">New Request</button>
            @else
                <button class="btn requests-new-btn requests-new-btn-disabled" disabled title="Trading hours only">New Request</button>
                <div class="requests-trading-hours-info">
                    <p><strong>New requests can only be made during the following days and times:</strong></p>
                    <p><strong>Monday – Thursday</strong><br>
                    09:00:00 – 12:00:00<br>
                    13:30:00 – 15:49:59</p>
                    <p><strong>Friday</strong><br>
                    09:00:00 – 11:30:00<br>
                    14:00:00 – 15:49:59</p>
                </div>
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
<form method="GET" action="{{ route('requests.index') }}" class="requests-filter-bar requests-search-form">
<div class="requests-filter-bar-inner">
<div class="form-group">
<input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name, email, mobile number...">
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
                <th class="requests-fullname-column sortable" data-sort="full_name">
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
                <th class="requests-action-column-header">
                    Type
                </th>
                <th class="requests-insight-column">
                    Analytics
                </th>
                <th class="requests-status-column sortable" data-sort="result">
                    Status
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
                <tr class="row-link" data-href="{{ route('requests.show', $request->id) }}">
                    <td class="requests-date-cell">{{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}</td>
                    @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                    <td>{{ $request->full_name }}</td>
                    @endif
                    <td>{{ $request->stock_code }}</td>
                    <td>{{ $request->company_name }}</td>
                    <td>{{ \App\Providers\AppServiceProvider::formatTimeframe($request->timeframe) }}</td>
                    <td>
                        @if(isset($request->action))
                            <span class="requests-action-badge {{ $request->action == 'BUY' ? 'requests-action-buy' : 'requests-action-sell' }}">
                                {{ $request->action == 'BUY' ? '🟢 BUY' : '🔴 SELL' }}
                            </span>
                        @else
                            <span class="requests-action-badge requests-action-buy">🟢 BUY</span>
                        @endif
                    </td>
                    <td>
                        <div id="advice-text-{{ $request->id }}" class="requests-advice-content">
                            {{ \App\Providers\AppServiceProvider::extractAnalyticsSummary($request->advice, $request->entry_price) }}
                        </div>
                    </td>
                    <td class="requests-result-cell">
                        @if($request->result)
                            @php
                                $resultData = [
                                    'SUPER_WIN' => ['emoji' => '🏆', 'text' => 'Super Win', 'class' => 'requests-result-super-win'],
                                    'WIN' => ['emoji' => '✅', 'text' => 'Win', 'class' => 'requests-result-win'],
                                    'LOSS' => ['emoji' => '❌', 'text' => 'Loss', 'class' => 'requests-result-loss'],
                                    'TIMEOUT' => ['emoji' => '⏰', 'text' => 'Timeout', 'class' => 'requests-result-timeout'],
                                    'MONITORING' => ['emoji' => '⏳', 'text' => 'Monitor', 'class' => 'requests-result-monitoring']
                                ];
                                $result = $resultData[$request->result];
                            @endphp
                            
                            <span class="requests-result-badge {{ $result['class'] }}" title="@if($request->result_achieved_at)Achieved at: {{ \Carbon\Carbon::parse($request->result_achieved_at)->setTimezone('Asia/Jakarta')->format('H:i:s T') }}@endif">
                                {{ $result['text'] }}
                            </span>
                        @else
                            {{-- Empty for Hold recommendations --}}
                            -
                        @endif
                    </td>
                    <td onclick="event.stopPropagation();">
                        <div class="requests-action-container">
                            <a href="{{ route('requests.show', $request->id) }}" class="requests-action-btn requests-btn-view">View</a>
                            <form action="{{ route('requests.destroy', $request->id) }}" method="POST" onsubmit="return confirmRequestDeletion(event, {{ $request->id }})">
                                @csrf
                                <button type="submit" class="requests-action-btn requests-btn-delete">Delete</button>
                            </form>
                            <button type="button" class="requests-action-btn {{ !empty($request->advice) ? 'requests-btn-disabled' : 'requests-btn-edit' }}" 
                                    data-request-id="{{ $request->id }}" 
                                    {{ !empty($request->advice) ? 'disabled' : '' }}
                                    onclick="checkAdvice({{ $request->id }})">
                                {{ !empty($request->advice) ? 'Analyzed' : 'Analyze' }}
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? '9' : '8' }}" class="requests-no-results">
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

                <!-- Action Type -->
                <div class="requests-mobile-field">
                    <b>Type</b><br>
                    @if(isset($request->action))
                        <span class="requests-action-badge {{ $request->action == 'BUY' ? 'requests-action-buy' : 'requests-action-sell' }}">
                            {{ $request->action == 'BUY' ? '🟢 BUY' : '🔴 SELL' }}
                        </span>
                    @else
                        <span class="requests-action-badge requests-action-buy">🟢 BUY</span>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="requests-mobile-actions" onclick="event.stopPropagation();">
                    <a href="{{ route('requests.show', $request->id) }}" class="requests-action-btn requests-btn-view">View</a>
                    <form action="{{ route('requests.destroy', $request->id) }}" method="POST" onsubmit="return confirmRequestDeletion(event, {{ $request->id }})">
                        @csrf
                        <button type="submit" class="requests-action-btn requests-btn-delete">Delete</button>
                    </form>
                    <button type="button" class="requests-action-btn {{ !empty($request->advice) ? 'requests-btn-disabled' : 'requests-btn-edit' }}" 
                            data-request-id="{{ $request->id }}" 
                            {{ !empty($request->advice) ? 'disabled' : '' }}
                            onclick="checkAdvice({{ $request->id }})">
                        {{ !empty($request->advice) ? 'Analyzed' : 'Analyze' }}
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
<div id="request-modal-final" class="requests-modal-hidden">
        <div class="auth-form-container requests-auth-form-container">
            <div class="auth-info-note">
                <strong>New Stock Request</strong><br>
                Submit a new stock analysis request with the required information.
            </div>
            
            <form action="/requests" method="POST" id="newRequestForm" class="auth-form">
                @csrf
                <div class="auth-form-group">
                    <label for="stock_code">Stock Code<span class="auth-required">*</span></label>
                    <div class="requests-stock-search-container">
                        <input type="text" name="stock_code" id="stock_code" required placeholder="e.g., BBCA.JK">
                        <div id="stockSearchResults" class="requests-stock-search-results" style="display: none;"></div>
                    </div>
                </div>
                
                <div class="auth-form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" name="company_name" id="company_name" placeholder="Optional">
                </div>
                
                <div class="auth-form-group">
                    <label for="timeframe">Timeframe<span class="auth-required">*</span></label>
                    <select name="timeframe" id="timeframe" required>
                        <option value="">Select</option>
                        <option value="1h">1 Hour</option>
                        <option value="1d">1 Day</option>
                        <option value="1w">1 Week</option>
                        <option value="1m">1 Month</option>
                    </select>
                </div>

                <div class="auth-form-group">
                    <label>Action<span class="auth-required">*</span></label>
                    <div style="display: flex; gap: 20px; margin-top: 8px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="action" value="BUY" checked required style="margin-right: 8px;">
                            <span>🟢 BUY (Entry Analysis)</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="action" value="SELL" required style="margin-right: 8px;">
                            <span>🔴 SELL (Exit Analysis)</span>
                        </label>
                    </div>
                </div>

                <div class="requests-modal-actions">
                    <button type="submit" class="auth-btn auth-btn-primary requests-modal-btn-primary">Submit</button>
                    <button type="button" class="auth-btn requests-modal-btn-cancel" onclick="closeRequestModal()">Cancel</button>
                </div>
            </form>
        </div>
            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

<script>
    // Set URLs for JavaScript
    window.stockSearchUrl = '{{ route("api.stocks.search") }}';
</script>
<script src="{{ asset('Requests/requests.js') }}"></script>

</body>
</html>