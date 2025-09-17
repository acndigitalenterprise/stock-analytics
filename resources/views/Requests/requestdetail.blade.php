<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Request Detail - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('Requests/requestdetail.css') }}">
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

    <h2 class="requests-detail-title">Request &gt; Detail</h2>

    <!-- Request Box -->
    <div class="requests-detail-section">
        <div class="admin-stat-box requests-detail-box">
            <h3 class="requests-detail-box-title">Request</h3>
            
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Date</label>
                <div class="requests-detail-value">{{ $stockRequest->created_at ? \Carbon\Carbon::parse($stockRequest->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}</div>
            </div>
            
            @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Full Name</label>
                <div class="requests-detail-value">{{ $stockRequest->full_name }}</div>
            </div>
            
            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Mobile Number</label>
                <div class="requests-detail-value">{{ $stockRequest->mobile_number }}</div>
            </div>
            
            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Email Address</label>
                <div class="requests-detail-value">{{ $stockRequest->email }}</div>
            </div>
            @endif
            
            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Stock Code</label>
                <div class="requests-detail-value">{{ $stockRequest->stock_code }}</div>
            </div>
            
            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Company Name</label>
                <div class="requests-detail-value">{{ $stockRequest->company_name }}</div>
            </div>
            
            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Timeframe</label>
                <div class="requests-detail-value">{{ \App\Providers\AppServiceProvider::formatTimeframe($stockRequest->timeframe) }}</div>
            </div>
        </div>
    </div>

    <!-- Insight Box -->
    <div class="requests-detail-section">
        <div class="admin-stat-box requests-detail-box">
            <h3 class="requests-detail-box-title">Analytics</h3>
            
            <div class="requests-detail-insight-section">
                <h4 class="requests-detail-insight-title">Claude</h4>
                <div class="requests-detail-insight-content">
                    {{ $stockRequest->advice ? str_replace('```markdown', '', $stockRequest->advice) : '-' }}
                </div>
            </div>
            
            <div class="requests-detail-divider"></div>
            
            <div class="requests-detail-insight-section">
                <h4 class="requests-detail-insight-title">ChatGPT</h4>
                <div class="requests-detail-insight-content">
                    {{ $stockRequest->advice_chatgpt ? str_replace('```markdown', '', $stockRequest->advice_chatgpt) : 'ChatGPT analysis not available' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Status Box -->
    <div class="requests-detail-section">
        <div class="admin-stat-box requests-detail-box">
            <h3 class="requests-detail-box-title">Status</h3>
            
            <div class="requests-detail-field-item">
                @php
                    $statusMap = [
                        'SUPER_WIN' => 'Super Win',
                        'WIN' => 'Win',
                        'LOSS' => 'Loss',
                        'TIMEOUT' => 'Timeout',
                        'MONITORING' => 'Monitor'
                    ];
                    $statusText = $statusMap[$stockRequest->result ?? 'MONITORING'];
                @endphp
                <div class="requests-detail-status-value">{{ $statusText }}</div>
            </div>
            
            <div class="requests-detail-divider"></div>
            
            <div class="requests-detail-field-item">
                <div class="requests-detail-note">This monitoring system tracks performance based on Claude's advice only.</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Section -->
    <div class="user-detail-actions requests-detail-actions">
        <div class="user-detail-buttons">
            <form action="{{ route('requests.destroy', $stockRequest->id) }}" method="POST" onsubmit="return confirmRequestDeletion(event, '{{ $stockRequest->stock_code }}')">
                @csrf
                <button type="submit" class="user-detail-btn user-detail-btn-delete">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
            
            @if(!$stockRequest->advice || !$stockRequest->advice_chatgpt)
                <a href="{{ route('requests.advice', $stockRequest->id) }}" class="user-detail-btn user-detail-btn-verify">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Analyze
                </a>
            @else
                <button class="user-detail-btn user-detail-btn-verified" disabled>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Analyzed
                </button>
            @endif
            
            <a href="{{ route('requests.index') }}" class="user-detail-btn user-detail-btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

<script src="{{ asset('Requests/requestdetail.js') }}"></script>

</body>
</html>