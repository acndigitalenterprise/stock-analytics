@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
    <div class="admin-content-container">
        @php $isAdminLayout = true; @endphp
        

        <div class="flex-between">
            <div>
                <h2>Stock Analytics</h2>
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
            
            @if($isTradingHours)
                <button class="btn" onclick="showPopup('new-request')" style="margin-bottom:20px;">New Request</button>
            @else
                <button class="btn" disabled style="margin-bottom:20px; background-color:#ccc; cursor:not-allowed; opacity:0.6;" 
                        title="Trading hours: 09:00-16:00 WIB (Current: {{ $currentTime }} WIB)">
                    New Request (Market Closed)
                </button>
            @endif
        </div>

        @if(session('success'))
            <div style="color:green;margin-bottom:16px;">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div style="color:red;margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        <!-- Search Form -->
        <form method="GET" action="{{ route('stock-analytics.admin') }}" class="admin-filter-bar">
            <div class="admin-filter-bar-inner">
                <div class="form-group" style="width:300px;">
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? 'Search by name, email, stock code...' : 'Search by stock code...' }}">
                </div>
                <div class="admin-filter-actions">
                    <button type="submit" class="btn">Search</button>
                    <a href="{{ route('stock-analytics.admin') }}" class="btn secondary">Clear</a>
                </div>
            </div>
        </form>

        <!-- Requests Table (Desktop) -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 100px; font-size: 0.85em;">
                        Date
                    </th>
                    @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                    <th style="width: 150px;">
                        Full Name
                    </th>
                    @endif
                    <th style="width: 100px;">
                        SC
                    </th>
                    <th style="width: 150px;">
                        Company Name
                    </th>
                    <th style="width: 50px;">
                        TF
                    </th>
                    <th>
                        Advice
                    </th>
                    <th style="width: 80px;">
                        Result
                    </th>
                    <th style="width: 50px;">Action</th>
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
                                <a href="{{ route('stock-analytics.admin.detail', $request->id) }}" class="btn" style="padding:2px 4px;font-size:0.7em;background:#007bff;text-decoration:none;width:40px;text-align:center;">View</a>
                                <form action="{{ route('stock-analytics.admin.delete', $request->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn" style="padding:2px 4px;font-size:0.7em;background:#b00;width:40px;" onclick="return confirm('Are you sure?')">Del</button>
                                </form>
                                <button type="button" class="btn advice-btn" 
                                        style="padding:2px 4px;font-size:0.7em;background:{{ !empty($request->advice) ? '#ccc' : '#17a2b8' }};width:40px;border:none;cursor:{{ !empty($request->advice) ? 'not-allowed' : 'pointer' }};"
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
                        <td colspan="{{ isset($user) && in_array($user->role, ['admin', 'super_admin']) ? '7' : '6' }}" style="text-align:center;padding:32px;">No requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Mobile Cards (Hidden on Desktop) -->
        @forelse($requests as $request)
            <div class="mobile-card" onclick="window.location.href='{{ route('stock-analytics.admin.detail', $request->id) }}'">
                <div class="mobile-card-header">
                    <div class="mobile-card-date">
                        {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}
                    </div>
                    <div class="mobile-card-actions" onclick="event.stopPropagation();">
                        <a href="{{ route('stock-analytics.admin.detail', $request->id) }}" class="btn" style="padding:4px 8px;font-size:0.8em;background:#007bff;text-decoration:none;">View</a>
                        <form action="{{ route('stock-analytics.admin.delete', $request->id) }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn" style="padding:4px 8px;font-size:0.8em;background:#b00;" onclick="return confirm('Are you sure?')">Del</button>
                        </form>
                        <button type="button" class="btn advice-btn" 
                                style="padding:4px 8px;font-size:0.8em;background:{{ !empty($request->advice) ? '#ccc' : '#17a2b8' }};border:none;cursor:{{ !empty($request->advice) ? 'not-allowed' : 'pointer' }};"
                                data-request-id="{{ $request->id }}" 
                                {{ !empty($request->advice) ? 'disabled' : '' }}
                                onclick="checkAdvice({{ $request->id }})">
                            Advice
                        </button>
                    </div>
                </div>
                
                <div class="mobile-card-info">
                    @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
                    <div class="mobile-card-item">
                        <div class="mobile-card-label">Full Name</div>
                        <div class="mobile-card-value">{{ $request->full_name }}</div>
                    </div>
                    @endif
                    <div class="mobile-card-item">
                        <div class="mobile-card-label">Stock Code</div>
                        <div class="mobile-card-value">{{ $request->stock_code }}</div>
                    </div>
                    <div class="mobile-card-item">
                        <div class="mobile-card-label">Company</div>
                        <div class="mobile-card-value">{{ $request->company_name }}</div>
                    </div>
                    <div class="mobile-card-item">
                        <div class="mobile-card-label">Timeframe</div>
                        <div class="mobile-card-value">{{ \App\Providers\AppServiceProvider::formatTimeframe($request->timeframe) }}</div>
                    </div>
                </div>

                @if($request->advice)
                <div class="mobile-card-advice">
                    <div class="mobile-card-advice-label">AI Advice</div>
                    <div class="mobile-card-advice-content advice-content" id="mobile-advice-{{ $request->id }}" style="max-height: 60px; overflow: hidden;">
                        {!! nl2br(e(str_replace('```markdown', '', $request->advice))) !!}
                    </div>
                    <div class="mobile-card-advice-toggle" onclick="toggleAdvice({{ $request->id }})">
                        Show More
                    </div>
                </div>
                @endif
            </div>
        @empty
            <div class="mobile-card" style="text-align:center;padding:32px;">
                No requests found.
            </div>
        @endforelse

        <!-- Pagination -->
        @if($requests->hasPages())
            <div style="margin-top:24px;text-align:center;">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif

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
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close popup and reload page directly
            closePopup('new-request');
            window.location.reload();
        } else {
            alert('Error: ' + data.error);
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



// Using common popup functions from app.js
// Using common stock search functions from app.js
// These functions are called directly from the HTML input elements

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

// Row click functionality
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.row-link');
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Only navigate if click is not on button or form
            if (!e.target.closest('button') && !e.target.closest('form') && !e.target.closest('a')) {
                const href = this.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            }
        });
        
        // Add cursor pointer style for clickable rows
        row.style.cursor = 'pointer';
    });
});

</script>
@endsection 