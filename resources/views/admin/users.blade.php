@extends('layout')

@section('body-class', 'admin-layout')

@section('content')
<div class="admin-content-container">
    @php $isAdminLayout = true; @endphp
    
    <div class="flex-between">
        <div>
            <h2>Users</h2>
        </div>
        <button class="btn" onclick="showNewUserModal()" style="margin-bottom:20px;">New User</button>
    </div>

    @if(session('success'))
        <div style="color:green;margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="color:red;margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('stock-analytics.admin.users') }}" class="admin-filter-bar">
        <div class="admin-filter-bar-inner">
            <div class="form-group" style="width:300px;">
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name, email, mobile number...">
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="btn">Search</button>
                <a href="{{ route('stock-analytics.admin.users') }}" class="btn secondary">Clear</a>
            </div>
        </div>
    </form>

    <!-- Users Table -->
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
                <th style="width: 200px;" class="sortable" data-sort="mobile_number">
                    Mobile Number
                    <span class="sort-indicator">
                        @if(request('sort') == 'mobile_number')
                            {{ request('order') == 'asc' ? '↑' : '↓' }}
                        @else
                            ↕
                        @endif
                    </span>
                </th>
                <th style="width: 100px;" class="sortable" data-sort="requests_count">
                    Total Request
                    <span class="sort-indicator">
                        @if(request('sort') == 'requests_count')
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
            @forelse($users as $user)
            <tr>
                <td style="font-size: 0.75em;">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}</td>
                <td>{{ $user->full_name ?? $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->mobile_number ?? '-' }}</td>
                <td>{{ $user->requests_count }}</td>
                <td onclick="event.stopPropagation();" style="text-align: center;">
                    <div style="display: flex; flex-direction: column; gap: 1px; align-items: center;">
                        <a href="{{ route('stock-analytics.admin.users.detail', $user->id) }}" class="btn" style="padding:2px 4px;font-size:0.7em;background:#007bff;text-decoration:none;width:40px;text-align:center;border:none;display:inline-block;box-sizing:border-box;">View</a>
                        <form action="{{ route('stock-analytics.admin.users.delete', $user->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            <button type="submit" class="btn" style="padding:2px 4px;font-size:0.7em;background:#dc3545;width:40px;text-align:center;border:none;display:inline-block;box-sizing:border-box;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="no-results">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

<!-- Pagination -->
<div class="pagination-container">
    <div class="pagination-left">
        <select id="perPageUsers" onchange="changePerPage(this.value)">
            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>
    </div>
    
    @if($users->hasPages())
        <div class="pagination-right">
            <span class="pagination-info">
                Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}
            </span>
            
            <div class="pagination-buttons">
                @if($users->onFirstPage())
                    <button class="pagination-btn" disabled>&lt;</button>
                @else
                    <button class="pagination-btn" onclick="window.location='{{ $users->appends(request()->query())->previousPageUrl() }}'">&lt;</button>
                @endif
                
                @if($users->hasMorePages())
                    <button class="pagination-btn" onclick="window.location='{{ $users->appends(request()->query())->nextPageUrl() }}'">&gt;</button>
                @else
                    <button class="pagination-btn" disabled>&gt;</button>
                @endif
            </div>
        </div>
    @endif
</div>

    <!-- New User Modal -->
    <div class="popup" id="new-user-popup" style="display:none;">
        <div class="popup-content">
            <h3>New User</h3>
            <form id="newUserForm">
                @csrf
                <div class="form-group">
                    <label for="new_full_name">Full Name</label>
                    <input type="text" name="full_name" id="new_full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="new_email">Email Address</label>
                    <input type="email" name="email" id="new_email" required>
                </div>
                
                <div class="form-group">
                    <label for="new_mobile_number">Mobile Number</label>
                    <input type="text" name="mobile_number" id="new_mobile_number" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="new_password" required style="padding-right:36px;">
                        <span class="toggle-password" onclick="togglePassword('new_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
                            <svg id="icon-new_password" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </span>
                    </div>
                </div>
                
                @php
                    // Get fresh user data directly from database
                    $currentUser = session('user');
                    $dbUser = \App\Models\User::find($currentUser->id);
                @endphp
                
                @if($dbUser && in_array($dbUser->role, ['admin', 'super_admin']))
                <div class="form-group">
                    <label for="new_role">Role</label>
                    <select name="role" id="new_role">
                        <option value="user">User</option>
                        @if($dbUser->role === 'super_admin')
                        <option value="admin">Admin</option>
                        @endif
                    </select>
                </div>
                @endif
                
                <button type="submit" class="btn" id="submitUserBtn">Submit</button>
                <button type="button" class="btn secondary" onclick="closeUserPopup()">Cancel</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// AJAX form submission for new user
document.getElementById('newUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitUserBtn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Creating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('{{ route("stock-analytics.admin.users.create") }}', {
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
            closeUserPopup();
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to create user'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the user.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

function showNewUserModal() {
    showPopup('new-user');
}

function closeUserPopup() {
    closePopup('new-user');
}

// Using common popup functions from app.js
// Using common pagination and sorting functions from app.js
</script>
@endsection