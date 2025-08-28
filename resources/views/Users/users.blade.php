@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('Users/users.css') }}?v={{ time() }}">

<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="users-flex-between">
        <div>
            <h2>Users</h2>
        </div>
        <div>
            <button class="btn users-new-btn" onclick="document.getElementById('user-modal-final').style.cssText='display:flex !important;position:absolute !important;top:0 !important;left:0 !important;width:100vw !important;min-height:100vh !important;background:rgba(0,0,0,0.8) !important;z-index:999999 !important;justify-content:center !important;align-items:flex-start !important;padding:40px 0 !important;overflow-y:auto !important;'">New User</button>
        </div>
    </div>

    @if(session('success'))
        <div class="users-message users-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="users-message users-error">{{ session('error') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('stock-analytics.admin.users') }}" class="users-filter-bar users-search-form">
        <div class="users-filter-bar-inner">
            <div class="form-group">
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name, email, mobile number...">
            </div>
            <div>
                <button type="submit" class="btn users-search-btn">Search</button>
            </div>
        </div>
    </form>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table users-table">
        <thead>
            <tr>
                <th class="users-date-column sortable" data-sort="created_at">
                    Date
                    <span class="sort-indicator">
                        @if(request('sort') == 'created_at')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="sortable" data-sort="name">
                    Full Name
                    <span class="sort-indicator">
                        @if(request('sort') == 'name')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="sortable" data-sort="email">
                    Email Address
                    <span class="sort-indicator">
                        @if(request('sort') == 'email')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="users-mobile-column sortable" data-sort="mobile_number">
                    Mobile Number
                    <span class="sort-indicator">
                        @if(request('sort') == 'mobile_number')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="users-requests-column sortable" data-sort="requests_count">
                    Total Request
                    <span class="sort-indicator">
                        @if(request('sort') == 'requests_count')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="users-role-column sortable" data-sort="role">
                    Role
                    <span class="sort-indicator">
                        @if(request('sort') == 'role')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th class="users-action-column">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="row-link" data-href="{{ route('stock-analytics.admin.users.detail', $user->id) }}">
                <td class="users-date-cell">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}</td>
                <td>{{ $user->full_name ?? $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->mobile_number ?? '-' }}</td>
                <td>{{ $user->requests_count }}</td>
                <td class="users-role-cell">{{ $user->role ?? 'user' }}</td>
                <td onclick="event.stopPropagation();">
                    <div class="users-action-container">
                        <a href="{{ route('stock-analytics.admin.users.detail', $user->id) }}" class="users-action-btn users-btn-view">View</a>
                        <form action="{{ route('stock-analytics.admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirmUserDeletion(event, '{{ $user->full_name ?? $user->name }}')">
                            @csrf
                            <button type="submit" class="users-action-btn users-btn-delete">Delete</button>
                        </form>
                        @if($user->email_verified_at)
                            <button class="users-action-btn users-btn-verified" disabled>Verified</button>
                        @else
                            <form action="{{ route('stock-analytics.admin.users.verify', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="users-action-btn users-btn-verify">Verify</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="users-no-results">No users found</td>
            </tr>
            @endforelse
        </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    @forelse($users as $user)
        <div class="users-mobile-card">
            <div class="users-mobile-detail">
                <!-- Date -->
                <div class="users-mobile-field">
                    <b>Date</b><br>{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i T') : '-' }}
                </div>
                
                <!-- Full Name -->
                <div class="users-mobile-field">
                    <b>Full Name</b><br>{{ $user->full_name ?? $user->name }}
                </div>
                
                <!-- Email -->
                <div class="users-mobile-field">
                    <b>Email Address</b><br>{{ $user->email }}
                </div>
                
                <!-- Mobile Number -->
                <div class="users-mobile-field">
                    <b>Mobile Number</b><br>{{ $user->mobile_number ?? '-' }}
                </div>
                
                <!-- Total Request -->
                <div class="users-mobile-field">
                    <b>Total Request</b><br>{{ $user->requests_count }}
                </div>
                
                <!-- Role -->
                <div class="users-mobile-field">
                    <b>Role</b><br>{{ ucfirst($user->role ?? 'user') }}
                </div>
                
                <!-- Action Buttons -->
                <div class="users-mobile-actions" onclick="event.stopPropagation();">
                    <a href="{{ route('stock-analytics.admin.users.detail', $user->id) }}" class="users-action-btn users-btn-view">View</a>
                    <form action="{{ route('stock-analytics.admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirmUserDeletion(event, '{{ $user->full_name ?? $user->name }}')">
                        @csrf
                        <button type="submit" class="users-action-btn users-btn-delete">Delete</button>
                    </form>
                    @if($user->email_verified_at)
                        <button class="users-action-btn users-btn-verified" disabled>Verified</button>
                    @else
                        <form action="{{ route('stock-analytics.admin.users.verify', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="users-action-btn users-btn-verify">Verify</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="users-mobile-card users-no-results">
            No users found.
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="users-pagination-container">
        <div class="users-pagination-left">
            <select id="perPageUsers" onchange="changeUsersPerPage(this.value)">
                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
        
        @if($users->hasPages())
            <div class="users-pagination-right">
                <span class="users-pagination-info">
                    Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}
                </span>
                
                <div class="users-pagination-buttons">
                    @if($users->onFirstPage())
                        <button class="users-pagination-btn" disabled>&lt;</button>
                    @else
                        <button class="users-pagination-btn" onclick="window.location='{{ $users->appends(request()->query())->previousPageUrl() }}'">&lt;</button>
                    @endif
                    
                    @if($users->hasMorePages())
                        <button class="users-pagination-btn" onclick="window.location='{{ $users->appends(request()->query())->nextPageUrl() }}'">&gt;</button>
                    @else
                        <button class="users-pagination-btn" disabled>&gt;</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- New User Modal (MOVED OUTSIDE LOOP) -->
<div id="user-modal-final" style="display:none !important;position:absolute !important;top:0 !important;left:0 !important;width:100vw !important;min-height:100vh !important;background:rgba(0,0,0,0.8) !important;z-index:999999 !important;justify-content:center !important;align-items:flex-start !important;padding:40px 0 !important;overflow-y:auto !important;">
        <div class="auth-form-container">
            <div class="auth-info-note">
                <strong>New User</strong><br>
                Create a new user account with the required information.
            </div>
            
            <form id="userFormFinal" class="auth-form">
                @csrf
                <div class="auth-form-group">
                    <label for="new_full_name">Full Name<span class="auth-required">*</span></label>
                    <input type="text" name="full_name" id="user_full_name" required>
                </div>
                
                <div class="auth-form-group">
                    <label for="new_email">Email Address<span class="auth-required">*</span></label>
                    <input type="email" name="email" id="user_email" required>
                </div>
                
                <div class="auth-form-group">
                    <label for="new_mobile_number">Mobile Number</label>
                    <input type="text" name="mobile_number" id="user_mobile" placeholder="Optional">
                </div>
                
                <div class="auth-form-group">
                    <label for="new_password">Password<span class="auth-required">*</span></label>
                    <div class="auth-password-container">
                        <input type="password" name="password" id="user_password" class="auth-password-input" required>
                        <span class="auth-toggle-password" onclick="toggleModalPassword('new_password', this)">
                            <svg id="icon-new_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </span>
                    </div>
                </div>
                
                <div class="auth-form-group">
                    <label for="new_password_confirmation">Confirm Password<span class="auth-required">*</span></label>
                    <div class="auth-password-container">
                        <input type="password" name="password_confirmation" id="user_password_confirm" class="auth-password-input" required>
                        <span class="auth-toggle-password" onclick="toggleModalPassword('new_password_confirmation', this)">
                            <svg id="icon-new_password_confirmation" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </span>
                    </div>
                </div>
                
                @php
                    // Get fresh user data directly from database
                    $currentUser = session('user');
                    $dbUser = \App\Models\User::find($currentUser->id);
                @endphp
                
                @if($dbUser && in_array($dbUser->role, ['admin', 'super_admin']))
                <div class="auth-form-group">
                    <label for="new_role">Role</label>
                    <select name="role" id="user_role">
                        <option value="user">User</option>
                        @if($dbUser->role === 'super_admin')
                        <option value="admin">Admin</option>
                        @endif
                    </select>
                </div>
                @endif
                
                <div class="users-modal-actions">
                    <button type="submit" class="auth-btn auth-btn-primary users-modal-btn-primary" id="userSubmitBtn">Submit</button>
                    <button type="button" class="auth-btn users-modal-btn-cancel" onclick="document.getElementById('user-modal-final').style.display='none'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Set the create user URL for the JS
    window.createUserUrl = '{{ route("stock-analytics.admin.users.create") }}';
</script>
<script src="{{ asset('Users/users.js') }}"></script>
@endsection