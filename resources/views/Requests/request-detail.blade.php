@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Requests/requests.css') }}">
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp

    <h2 class="requests-detail-title">Requests &gt; Detail</h2>

    <!-- Request Details Box -->
    <div style="margin: 32px 0;">
        <div class="admin-stat-box" style="width: 100%; text-align: left;">
            <div class="requests-detail-field">
                <label class="requests-detail-label">Date</label>
                <div class="requests-detail-value">{{ $stockRequest->created_at ? \Carbon\Carbon::parse($stockRequest->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}</div>
            </div>
            
            @if(isset($user) && in_array($user->role, ['admin', 'super_admin']))
            <div class="requests-detail-field">
                <label class="requests-detail-label">Full Name</label>
                <div class="requests-detail-value">{{ $stockRequest->full_name }}</div>
            </div>
            <div class="requests-detail-field">
                <label class="requests-detail-label">Mobile Number</label>
                <div class="requests-detail-value">{{ $stockRequest->mobile_number }}</div>
            </div>
            <div class="requests-detail-field">
                <label class="requests-detail-label">Email Address</label>
                <div class="requests-detail-value">{{ $stockRequest->email }}</div>
            </div>
            @endif
            
            <div class="requests-detail-field">
                <label class="requests-detail-label">Stock Code</label>
                <div class="requests-detail-value">{{ $stockRequest->stock_code }}</div>
            </div>
            
            <div class="requests-detail-field">
                <label class="requests-detail-label">Company Name</label>
                <div class="requests-detail-value">{{ $stockRequest->company_name }}</div>
            </div>
            
            <div class="requests-detail-field">
                <label class="requests-detail-label">Timeframe</label>
                <div class="requests-detail-value">{{ \App\Providers\AppServiceProvider::formatTimeframe($stockRequest->timeframe) }}</div>
            </div>
        </div>
    </div>

    <!-- Advice Box -->
    <div style="margin: 32px 0;">
        <div class="admin-stat-box" style="width: 100%; text-align: left;">
            <div class="requests-detail-field">
                <label class="requests-detail-label">Advice by Claude</label>
                <div class="requests-detail-advice">
                    {!! $stockRequest->advice ? nl2br(e(str_replace('```markdown', '', $stockRequest->advice))) : '-' !!}
                </div>
            </div>
            
            <div class="requests-detail-field">
                <label class="requests-detail-label">Advice by ChatGPT</label>
                <div class="requests-detail-advice">
                    {!! $stockRequest->advice_chatgpt ? nl2br(e(str_replace('```markdown', '', $stockRequest->advice_chatgpt))) : 'ChatGPT analysis not available' !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Result Box -->
    <div style="margin: 32px 0;">
        <div class="admin-stat-box" style="width: 100%; text-align: left;">
            <div class="requests-detail-field">
                <label class="requests-detail-label">Result</label>
                <div class="requests-detail-value">Monitor</div>
            </div>
            
            <div style="margin-top: 16px; color: rgba(255, 255, 255, 0.8);">
                This monitoring system tracks performance based on Claude's advice only.
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div style="margin: 32px 0; text-align: center;">
        <a href="{{ route('stock-analytics.admin.requests') }}" class="auth-btn" style="background: rgba(255, 255, 255, 0.1); text-decoration: none; text-align: center;">Back</a>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{ asset('Requests/requests.js') }}"></script>
@endsection