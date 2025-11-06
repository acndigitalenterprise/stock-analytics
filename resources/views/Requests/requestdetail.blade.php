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

            <div class="requests-detail-divider"></div>
            <div class="requests-detail-field-item">
                <label class="requests-detail-label">Action Type</label>
                <div class="requests-detail-value">
                    @if(isset($stockRequest->action))
                        <span class="requests-action-badge {{ $stockRequest->action == 'BUY' ? 'requests-action-buy' : 'requests-action-sell' }}">
                            {{ $stockRequest->action == 'BUY' ? '🟢 BUY (Entry Analysis)' : '🔴 SELL (Exit Analysis)' }}
                        </span>
                    @else
                        <span class="requests-action-badge requests-action-buy">🟢 BUY (Entry Analysis)</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Insight Box with Tabs -->
    <div class="requests-detail-section">
        <div class="admin-stat-box requests-detail-box">
            <h3 class="requests-detail-box-title">Analytics</h3>

            <!-- AI Tabs Navigation -->
            <div class="ai-tabs-nav">
                <button class="ai-tab-btn active" data-tab="claude">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    Claude
                </button>
                <button class="ai-tab-btn" data-tab="chatgpt">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1686a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4944zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5346-3.0137l.142.0852 4.783 2.7582a.7712.7712 0 0 0 .7806 0l5.8428-3.3685v2.3324a.0804.0804 0 0 1-.0332.0615L9.74 19.9502a4.4992 4.4992 0 0 1-6.1408-1.6464zM2.3408 7.8956a4.485 4.485 0 0 1 2.3655-1.9728V11.6a.7664.7664 0 0 0 .3879.6765l5.8144 3.3543-2.0201 1.1685a.0757.0757 0 0 1-.071 0l-4.8303-2.7865A4.504 4.504 0 0 1 2.3408 7.872zm16.5963 3.8558L13.1038 8.364 15.1192 7.2a.0757.0757 0 0 1 .071 0l4.8303 2.7913a4.4944 4.4944 0 0 1-.6765 8.1042v-5.6772a.79.79 0 0 0-.407-.667zm2.0107-3.0231l-.142-.0852-4.7735-2.7818a.7759.7759 0 0 0-.7854 0L9.409 9.2297V6.8974a.0662.0662 0 0 1 .0284-.0615l4.8303-2.7866a4.4992 4.4992 0 0 1 6.6802 4.66zM8.3065 12.863l-2.02-1.1638a.0804.0804 0 0 1-.038-.0567V6.0742a4.4992 4.4992 0 0 1 7.3757-3.4537l-.142.0805L8.704 5.459a.7948.7948 0 0 0-.3927.6813zm1.0976-2.3654l2.602-1.4998 2.6069 1.4998v2.9994l-2.5974 1.4997-2.6067-1.4997Z"/>
                    </svg>
                    ChatGPT
                </button>
                <button class="ai-tab-btn" data-tab="qwen">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                    </svg>
                    Qwen
                </button>
            </div>

            <!-- AI Tab Content Panels -->
            <div class="ai-tabs-content">
                <!-- Claude Tab -->
                <div class="ai-tab-panel active" id="tab-claude">
                    <div class="requests-detail-insight-content">
                        {{ $stockRequest->advice ? str_replace('```markdown', '', $stockRequest->advice) : '-' }}
                    </div>
                </div>

                <!-- ChatGPT Tab -->
                <div class="ai-tab-panel" id="tab-chatgpt">
                    <div class="requests-detail-insight-content">
                        {{ $stockRequest->advice_chatgpt ? str_replace('```markdown', '', $stockRequest->advice_chatgpt) : 'ChatGPT analysis not available' }}
                    </div>
                </div>

                <!-- Qwen Tab -->
                <div class="ai-tab-panel" id="tab-qwen">
                    <div class="requests-detail-insight-content">
                        {{ $stockRequest->advice_qwen ? str_replace('```markdown', '', $stockRequest->advice_qwen) : 'Qwen analysis not available' }}
                    </div>
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