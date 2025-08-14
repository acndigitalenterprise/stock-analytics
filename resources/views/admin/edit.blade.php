@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
    @php $isAdminLayout = true; @endphp
    
    <h2>Edit Request</h2>

    @if(session('success'))
        <div style="color:green;margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom:24px;">
        <a href="{{ route('stock-analytics.admin') }}" class="btn secondary">Back to List</a>
    </div>

    <form action="{{ route('stock-analytics.admin.update', $stockRequest->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" value="{{ $stockRequest->full_name }}" disabled>
        </div>
        <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" value="{{ $stockRequest->mobile_number }}" disabled>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" value="{{ $stockRequest->email }}" disabled>
        </div>
        <div class="form-group">
            <label>Stock Code</label>
            <input type="text" value="{{ $stockRequest->stock_code }}" disabled>
        </div>
        <div class="form-group">
            <label>Company Name</label>
            <input type="text" value="{{ $stockRequest->company_name }}" disabled>
        </div>
        <div class="form-group">
            <label>Timeframe</label>
                            <input type="text" value="{{ \App\Providers\AppServiceProvider::formatTimeframe($stockRequest->timeframe) }}" disabled>
        </div>
        <div class="form-group">
            <label for="advice">Advice</label>
            <textarea name="advice" id="advice" rows="5">{{ old('advice', $stockRequest->advice) }}</textarea>
            @error('advice')<div class="error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn">Update Request</button>
    </form>
@endsection 