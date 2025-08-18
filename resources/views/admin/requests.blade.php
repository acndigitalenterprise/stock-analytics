@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
    <div class="admin-content-container">
        @php $isAdminLayout = true; @endphp
        
        <div class="flex-between">
            <div>
                <h2>Requests</h2>
                @php
                    $winningStats = app(\App\Services\PriceMonitoringService::class)->getWinningRateStats();
                @endphp
                @if($winningStats['total_requests'] > 0)
                    <p style="margin: 5px 0; color: #666; font-size: 0.9em;">
                        🎯 AI Accuracy: <strong style="color: {{ $winningStats['winning_rate'] >= 70 ? '#28a745' : ($winningStats['winning_rate'] >= 50 ? '#ffc107' : '#dc3545') }};">{{ $winningStats['winning_rate'] }}%</strong>
                        ({{ $winningStats['total_wins'] }} Win, {{ $winningStats['losses'] }} Loss)
                    </p>
                @endif
            </div>
            @php
                $currentHour = now()->setTimezone('Asia/Jakarta')->format('H');
                $isTradingHours = $currentHour >= 9 && $currentHour < 16;
                $currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i');
            @endphp
            
            <div style="flex-shrink: 0; margin-left: auto;">
                @if($isTradingHours)
                    <button class="btn" onclick="showPopup('new-request')" style="margin-bottom:20px; background: #333; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; cursor: pointer; width: auto !important; display: inline-block !important; flex-shrink: 0;">New Request</button>
                @else
                    <button class="btn" disabled style="margin-bottom:20px; background: #ccc; color: #666; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; cursor: not-allowed; width: auto !important; display: inline-block !important; flex-shrink: 0;" 
                            title="Trading hours: 09:00-16:00 WIB (Current: {{ $currentTime }} WIB)">
                        New Request (Market Closed)
                    </button>
                @endif
            </div>
        </div>

<style>
@media (max-width: 768px) {
    .flex-between > div:last-child {
        align-self: flex-start !important;
        width: auto !important;
    }
}
</style>

        @if(session('success'))
            <div style="color:green;margin-bottom:16px;">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div style="color:red;margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        <!-- Search Form -->
        <form method="GET" action="{{ route('stock-analytics.admin.requests') }}" class="admin-filter-bar">
            <div class="admin-filter-bar-inner" style="display: flex; gap: 8px; align-items: center; margin-bottom: 24px;">
                <div class="form-group" style="width:300px; margin-bottom: 0;">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? 'Search by name, email, stock code...' : 'Search by stock code...' }}">
                </div>
                <div>
                    <button type="submit" class="btn" style="background: #333; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; cursor: pointer; width: auto !important;">Search</button>
                </div>
            </div>
        </form>

        <!-- Requests Table (Desktop) -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 100px; font-size: 0.85em;" class="sortable" data-sort="created_at">
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
                    <th style="width: 100px;" class="sortable" data-sort="stock_code">
                        SC
                        <span class="sort-indicator">
                            @if(request('sort') == 'stock_code')
                                {{ request('order') == 'asc' ? '↑' : '↓' }}
                            @else
                                ↕
                            @endif
                        </span>
                    </th>
                    <th style="width: 200px;" class="sortable" data-sort="company_name">
                        Company Name
                        <span class="sort-indicator">
                            @if(request('sort') == 'company_name')
                                {{ request('order') == 'asc' ? '↑' : '↓' }}
                            @else
                                ↕
                            @endif
                        </span>
                    </th>
                    <th style="width: 100px;">
                        TF
                    </th>
                    <th>
                        Advice
                    </th>
                    <th style="width: 100px;" class="sortable" data-sort="result">
                        Result
                        <span class="sort-indicator">
                            @if(request('sort') == 'result')
                                {{ request('order') == 'asc' ? '↑' : '↓' }}
                            @else
                                ↕
                            @endif
                        </span>
                    </th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr class="row-link" data-href="{{ route('stock-analytics.admin.detail', $request->id) }}">
                        <td style="font-size: 0.75em;">{{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}</td>
                        @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                        <td>{{ $request->full_name }}</td>
                        @endif
                        <td>{{ $request->stock_code }}</td>
                        <td>{{ $request->company_name }}</td>
                        <td>{{ \App\Providers\AppServiceProvider::formatTimeframe($request->timeframe) }}</td>
                        <td>
                            <div id="advice-text-{{ $request->id }}" class="advice-content" style="max-height: 60px; overflow: hidden; line-height: 1.4;">
                                {!! $request->advice ? nl2br(e(str_replace('```markdown', '', $request->advice))) : '-' !!}
                            </div>
                        </td>
                        <td style="text-align: center; font-size: 0.85em;">
                            @php
                                $resultClass = '';
                                $resultText = '';
                                $resultEmoji = '';
                                
                                switch($request->result ?? 'MONITORING') {
                                    case 'SUPER_WIN':
                                        $resultClass = 'color: #ff6b35; font-weight: bold;';
                                        $resultText = 'SUPER WIN';
                                        $resultEmoji = '🏆';
                                        break;
                                    case 'WIN':
                                        $resultClass = 'color: #28a745; font-weight: bold;';
                                        $resultText = 'WIN';
                                        $resultEmoji = '✅';
                                        break;
                                    case 'LOSS':
                                        $resultClass = 'color: #dc3545; font-weight: bold;';
                                        $resultText = 'LOSS';
                                        $resultEmoji = '❌';
                                        break;
                                    case 'TIMEOUT':
                                        $resultClass = 'color: #ffc107; font-weight: bold;';
                                        $resultText = 'TIMEOUT';
                                        $resultEmoji = '⏰';
                                        break;
                                    case 'MONITORING':
                                    default:
                                        $resultClass = 'color: #17a2b8;';
                                        $resultText = 'MONITORING';
                                        $resultEmoji = '⏳';
                                        break;
                                }
                            @endphp
                            
                            <div style="{{ $resultClass }}" title="@if($request->result_achieved_at)Achieved at: {{ \Carbon\Carbon::parse($request->result_achieved_at)->setTimezone('Asia/Jakarta')->format('H:i:s T') }}@endif">
                                <span style="font-size: 1.2em;">{{ $resultEmoji }}</span><br>
                                <span style="font-size: 0.7em;">{{ $resultText }}</span>
                                @if($request->highest_price_reached && $request->entry_price)
                                    <br><span style="font-size: 0.6em; color: #666;">
                                        Peak: {{ number_format($request->highest_price_reached, 0) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td onclick="event.stopPropagation();" style="text-align: center;">
                            <div style="display: flex; flex-direction: column; gap: 1px; align-items: center;">
                                <a href="{{ route('stock-analytics.admin.detail', $request->id) }}" class="btn" style="padding:2px 4px;font-size:0.7em;background:#007bff;text-decoration:none;width:40px;text-align:center;border:none;display:inline-block;box-sizing:border-box;">View</a>
                                <form action="{{ route('stock-analytics.admin.delete', $request->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn" style="padding:2px 4px;font-size:0.7em;background:#dc3545;width:40px;text-align:center;border:none;display:inline-block;box-sizing:border-box;" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                                <button type="button" class="btn advice-btn" 
                                        style="padding:2px 4px;font-size:0.7em;background:{{ !empty($request->advice) ? '#6c757d' : '#17a2b8' }};width:40px;text-align:center;border:none;display:inline-block;box-sizing:border-box;cursor:{{ !empty($request->advice) ? 'not-allowed' : 'pointer' }};"
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
                        <td colspan="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? '8' : '7' }}" style="text-align:center;padding:32px;">
                            No requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Mobile Cards -->
        @forelse($requests as $request)
            <div class="mobile-card">
                <div class="mobile-detail">
                    <!-- Date -->
                    <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
                        <b>Date</b><br>
                        {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}
                    </div>
                    
                    <!-- User Name (Admin only) -->
                    @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                    <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
                        <b>Full Name</b><br>
                        {{ $request->full_name }}
                    </div>
                    @endif
                    
                    <!-- Stock Code -->
                    <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
                        <b>Stock Code</b><br>
                        {{ $request->stock_code }}
                    </div>
                    
                    <!-- Company Name -->
                    <div style="font-size: 0.9em; color: #000000; margin-bottom: 8px;">
                        <b>Company Name</b><br>
                        {{ $request->company_name }}
                    </div>
                    
                    <!-- Timeframe -->
                    <div style="font-size: 0.9em; color: #000000; margin-bottom: 16px;">
                        <b>Timeframe</b><br>
                        {{ \App\Providers\AppServiceProvider::formatTimeframe($request->timeframe) }}
                    </div>
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;" onclick="event.stopPropagation();">
                        <a href="{{ route('stock-analytics.admin.detail', $request->id) }}" class="btn" style="padding:6px 12px;font-size:0.8em;background:#007bff;text-decoration:none;flex:1;text-align:center;">View</a>
                        <form action="{{ route('stock-analytics.admin.delete', $request->id) }}" method="POST" style="margin:0;flex:1;">
                            @csrf
                            <button type="submit" class="btn" style="padding:6px 12px;font-size:0.8em;background:#dc3545;width:100%;" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                        <button type="button" class="btn advice-btn" 
                                style="padding:6px 12px;font-size:0.8em;background:{{ !empty($request->advice) ? '#6c757d' : '#17a2b8' }};flex:1;cursor:{{ !empty($request->advice) ? 'not-allowed' : 'pointer' }};"
                                data-request-id="{{ $request->id }}" 
                                {{ !empty($request->advice) ? 'disabled' : '' }}
                                onclick="checkAdvice({{ $request->id }})">
                            Advice
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="mobile-card" style="text-align:center;padding:32px;">
                No requests found.
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination-left">
                <select id="perPage" onchange="changePerPage(this.value)">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            
            @if($requests->hasPages())
                <div class="pagination-right">
                    <span class="pagination-info">
                        Showing {{ $requests->firstItem() }}-{{ $requests->lastItem() }} of {{ $requests->total() }}
                    </span>
                    
                    <div class="pagination-buttons">
                        @if($requests->onFirstPage())
                            <button class="pagination-btn" disabled>&lt;</button>
                        @else
                            <button class="pagination-btn" onclick="window.location='{{ $requests->appends(request()->query())->previousPageUrl() }}'">&lt;</button>
                        @endif
                        
                        @if($requests->hasMorePages())
                            <button class="pagination-btn" onclick="window.location='{{ $requests->appends(request()->query())->nextPageUrl() }}'">&gt;</button>
                        @else
                            <button class="pagination-btn" disabled>&gt;</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- New Request Popup -->
        <div class="popup" id="new-request-popup" style="display:none;">
            <div class="popup-content">
                <h3>New Request</h3>
                <form id="newRequestForm">
                    @csrf
                    <div class="form-group">
                        <label for="new_stock_code">Stock Code</label>
                        <input type="text" name="stock_code" id="new_stock_code" required 
                            oninput="handleStockSearch('new_stock_code', 'admin-autocomplete-list')" 
                            onkeydown="handleKeyDown(event, 'admin-autocomplete-list', 'new_stock_code')"
                            autocomplete="off"
                            placeholder="Type to search stocks (e.g., BBCA, TLKM, BBRI)">
                        <div id="admin-autocomplete-list" style="position:absolute;z-index:10;background:#fff;border:1px solid #ccc;width:100%;max-height:300px;overflow-y:auto;display:none;box-shadow:0 2px 8px rgba(0,0,0,0.1);"></div>
                    </div>
                    <div class="form-group">
                        <label for="new_timeframe">Timeframe</label>
                        <select name="timeframe" id="new_timeframe" required>
                            <option value="">-- Select --</option>
                            <option value="1h">1h</option>
                            <option value="1d">1d</option>
                        </select>
                    </div>
                    <button type="submit" class="btn" id="submitBtn">Submit</button>
                    <button type="button" class="btn secondary" onclick="closePopup('new-request')">Cancel</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// AJAX form submission for new request
document.getElementById('newRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Creating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('{{ route("stock-analytics.admin.request") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closePopup('new-request');
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the request.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Check advice and update UI
function checkAdvice(requestId) {
    const button = document.querySelector(`[data-request-id="${requestId}"].advice-btn`);
    
    // Show loading on button
    const originalText = button.textContent;
    button.textContent = 'Checking...';
    button.disabled = true;
    
    // Fetch advice status
    fetch(`/stock-analytics/admin/request/${requestId}/advice`)
        .then(response => response.json())
        .then(data => {
            if (data.has_advice) {
                // Update advice in table
                updateAdviceInTable(requestId, data.advice);
                
                // Disable button
                button.style.background = '#ccc';
                button.style.cursor = 'not-allowed';
                button.disabled = true;
                button.textContent = 'Advice';
                
                // Show success message
                showMessage('Advice generated successfully!', 'success');
            } else {
                // Re-enable button for next check
                button.textContent = 'Advice';
                button.disabled = false;
                showMessage('Advice not ready yet. Try again in a moment.', 'info');
            }
        })
        .catch(error => {
            console.error('Error checking advice:', error);
            button.textContent = 'Advice';
            button.disabled = false;
            showMessage('Error checking advice. Please try again.', 'error');
        });
}

// Update advice in table and mobile card
function updateAdviceInTable(requestId, advice) {
    // Update desktop table
    const tableAdviceElement = document.getElementById(`advice-text-${requestId}`);
    if (tableAdviceElement) {
        tableAdviceElement.innerHTML = advice.replace(/\n/g, '<br>');
    }
    
    // Update mobile card
    const mobileAdviceElement = document.getElementById(`mobile-advice-${requestId}`);
    if (mobileAdviceElement) {
        mobileAdviceElement.innerHTML = advice.replace(/\n/g, '<br>');
    }
}

// Show message
function showMessage(message, type = 'info') {
    const messageDiv = document.getElementById('message-container') || createMessageContainer();
    messageDiv.textContent = message;
    messageDiv.className = `message ${type}`;
    messageDiv.style.display = 'block';
    
    // Hide after 3 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}

// Create message container
function createMessageContainer() {
    const div = document.createElement('div');
    div.id = 'message-container';
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 16px;
        border-radius: 6px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        display: none;
    `;
    document.body.appendChild(div);
    return div;
}

// Mobile advice toggle function
function toggleAdvice(requestId) {
    const adviceContent = document.getElementById('mobile-advice-' + requestId);
    const toggleButton = adviceContent.nextElementSibling;
    
    if (adviceContent.classList.contains('expanded')) {
        adviceContent.classList.remove('expanded');
        toggleButton.textContent = 'Show More';
    } else {
        adviceContent.classList.add('expanded');
        toggleButton.textContent = 'Show Less';
    }
}

// Using common popup functions from app.js
// Using common stock search functions from app.js  
// Using common pagination and sorting functions from app.js
// Using consolidated CSS from layout.blade.php
</script>
@endsection