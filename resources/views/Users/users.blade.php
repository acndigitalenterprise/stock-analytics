<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Users - Ticker AI</title>
    <link rel="stylesheet" href="{{ asset('Admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('page-users/users.css') }}?v={{ time() }}">
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
                <form method="GET" action="{{ route('users.index') }}" class="users-filter-bar users-search-form">
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
                                Email
                                <span class="sort-indicator">
                                    @if(request('sort') == 'email')
                                        {{ request('order') == 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </th>
                            <th class="users-status-column sortable" data-sort="email_verified_at">
                                Status
                                <span class="sort-indicator">
                                    @if(request('sort') == 'email_verified_at')
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
                            <tr class="row-link" data-href="{{ route('users.show', $user->id) }}">
                                <td class="users-date-cell">{{ $user->created_at ? $user->created_at->setTimezone('Asia/Jakarta')->format('d/m/y H:i') : '-' }}</td>
                                <td>{{ $user->full_name ?? $user->name ?? 'N/A' }}</td>
                                <td>{{ $user->email ?? 'N/A' }}</td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="users-status-verified">Verified</span>
                                    @else
                                        <span class="users-status-unverified">Unverified</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div class="users-action-container">
                                        <a href="{{ route('users.show', $user->id) }}" class="users-action-btn users-btn-view">View</a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirmUserDeletion(event, '{{ $user->full_name ?? $user->name }}')">
                                            @csrf
                                            <button type="submit" class="users-action-btn users-btn-delete">Delete</button>
                                        </form>
                                        @if($user->email_verified_at)
                                            <button class="users-action-btn users-btn-verified" disabled>Verified</button>
                                        @else
                                            <form action="{{ route('users.verify', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="users-action-btn users-btn-verify">Verify</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="users-no-results">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>

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

                <!-- New User Modal -->
                <div id="user-modal-final" style="display:none !important;position:absolute !important;top:0 !important;left:0 !important;width:100vw !important;min-height:100vh !important;background:rgba(0,0,0,0.8) !important;z-index:999999 !important;justify-content:center !important;align-items:flex-start !important;padding:40px 0 !important;overflow-y:auto !important;">
                    <div class="auth-form-container users-auth-form-container">
                        <div class="auth-info-note">
                            <strong>New User</strong><br>
                            Create a new user account.
                        </div>
                        
                        <form action="{{ route('users.store') }}" method="POST" id="newUserForm" class="auth-form">
                            @csrf
                            <div class="auth-form-group">
                                <label for="full_name">Full Name<span class="auth-required">*</span></label>
                                <input type="text" name="full_name" id="full_name" required>
                            </div>
                            
                            <div class="auth-form-group">
                                <label for="email">Email Address<span class="auth-required">*</span></label>
                                <input type="email" name="email" id="email" required>
                            </div>
                            
                            <div class="auth-form-group">
                                <label for="mobile_number">Mobile Number</label>
                                <input type="text" name="mobile_number" id="mobile_number">
                            </div>
                            
                            <div class="auth-form-group">
                                <label for="role">Role<span class="auth-required">*</span></label>
                                <select name="role" id="role" required>
                                    <option value="">Select Role</option>
                                    <option value="user">User</option>
                                    @if(session('user') && session('user')->role === 'super_admin')
                                        <option value="admin">Admin</option>
                                    @endif
                                </select>
                            </div>
                            
                            <div class="users-modal-actions">
                                <button type="submit" class="auth-btn auth-btn-primary users-modal-btn-primary">Create User</button>
                                <button type="button" class="auth-btn users-modal-btn-cancel" onclick="document.getElementById('user-modal-final').style.display='none'">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    @include('Components.footer')
</div>

@include('Components.admin-scripts')

<script src="{{ asset('page-users/users.js') }}"></script>

</body>
</html>