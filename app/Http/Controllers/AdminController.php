<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Jobs\GenerateStockAdvice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }

        // ALL users (including regular users) should go to dashboard first
        // This provides consistent experience across all roles
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'stock_code' => 'required|string|max:10',
                'company_name' => 'nullable|string|max:255',
                'timeframe' => 'required|in:1h,1d',
            ]);

            $user = session('user');
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Optimize: Use provided company name or fallback to simple default
            $companyName = $validated['company_name'];
            if (empty($companyName)) {
                // Quick fallback instead of API call
                $companyName = 'Unknown Company';
            }

            $stockRequest = \App\Models\Request::create([
                'full_name' => $user->name,
                'mobile_number' => $user->mobile_number ?? '000-000-0000',
                'email' => $user->email,
                'stock_code' => StockService::ensureJKFormat($validated['stock_code']),
                'company_name' => $companyName,
                'timeframe' => $validated['timeframe'],
                'user_id' => $user->id,
            ]);

            // Email will be sent after AI advice is generated (via GenerateStockAdvice job)
            
            // Clear dashboard cache since new request was added
            $this->clearDashboardCache();
            
            // Regular form submission - redirect with success message
            return redirect()->route('requests.index')
                ->with('success', 'Request created successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('requests.index')
                ->with('error', 'Failed to create request: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }

        $stockRequest = \App\Models\Request::findOrFail($id);

        // Check if user can edit this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return redirect()->route('requests.index')
                ->withErrors(['error' => 'You can only edit your own requests.']);
        }

        return view('admin.edit', compact('stockRequest'));
    }

    public function update(Request $request, $id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }

        $stockRequest = \App\Models\Request::findOrFail($id);

        // Check if user can edit this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return redirect()->route('requests.index')
                ->withErrors(['error' => 'You can only edit your own requests.']);
        }

        $validated = $request->validate([
            'advice' => 'nullable|string|max:1000',
        ]);

        $stockRequest->update($validated);

        return redirect()->route('requests.index')
            ->with('success', 'Request updated successfully!');
    }

    public function delete($id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }

        $stockRequest = \App\Models\Request::findOrFail($id);

        // Check if user can delete this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return redirect()->route('requests.index')
                ->withErrors(['error' => 'You can only delete your own requests.']);
        }

        $stockRequest->delete();

        return redirect()->route('requests.index')
            ->with('success', 'Request deleted successfully!');
    }

    public function detail($id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }
        $stockRequest = \App\Models\Request::findOrFail($id);
        // Hanya admin, super_admin atau owner yang boleh lihat
        if (!in_array($user->role, ['admin', 'super_admin']) && $stockRequest->user_id !== $user->id) {
            return redirect()->route('requests.index')->withErrors(['error' => 'Unauthorized.']);
        }
        return view('Requests.requestdetail', compact('stockRequest', 'user'));
    }

    // ensureJKFormat and getCompanyName moved to StockService



    public function getAdvice($id)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stockRequest = \App\Models\Request::findOrFail($id);
        
        // Check if user can access this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // If advice already exists, return it
        if (!empty($stockRequest->advice)) {
            return response()->json([
                'success' => true,
                'message' => 'Advice already generated',
                'has_advice' => true,
                'advice' => str_replace('```markdown', '', $stockRequest->advice),
                'updated_at' => $stockRequest->updated_at
            ]);
        }

        // Generate new advice using AI
        try {
            Log::info('Dispatching GenerateStockAdvice job from manual trigger', [
                'request_id' => $stockRequest->id,
                'stock_code' => $stockRequest->stock_code,
                'timeframe' => $stockRequest->timeframe,
                'user_id' => $user->id
            ]);
            
            // Try queue first
            try {
                GenerateStockAdvice::dispatch($stockRequest);
                
                return response()->json([
                    'success' => true,
                    'message' => 'AI advice generation started. Please refresh the page in a few moments.',
                    'has_advice' => false
                ]);
            } catch (\Exception $queueException) {
                Log::warning('Queue dispatch failed, executing synchronously', [
                    'request_id' => $stockRequest->id,
                    'queue_error' => $queueException->getMessage()
                ]);
                
                // Fallback: Execute synchronously
                $job = new \App\Jobs\GenerateStockAdvice($stockRequest);
                $job->handle(
                    app(\App\Services\YahooFinanceService::class),
                    app(\App\Services\AlphaVantageService::class),
                    app(\App\Services\TechnicalAnalysisService::class),
                    app(\App\Services\ChatGPTService::class),
                    app(\App\Services\PriceMonitoringService::class)
                );
                
                // Reload the request to get updated advice
                $stockRequest->refresh();
                
                return response()->json([
                    'success' => true,
                    'message' => 'AI advice generated successfully!',
                    'has_advice' => !empty($stockRequest->advice),
                    'advice' => $stockRequest->advice
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate advice', [
                'request_id' => $stockRequest->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to generate advice: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendNewRequestEmail($user, $request)
    {
        $data = [
            'user' => $user,
            'request' => $request,
        ];

        Mail::send('emails.new-request', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('New Stock Analytics Request');
        });
    }


    // Unified dashboard method for all roles
    public function dashboard()
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }

        // Cache key for metrics based on user role and ID
        $cacheKey = "dashboard_metrics_{$user->role}_{$user->id}";
        $cacheDuration = 60; // Reduced to 1 minute for more current data
        
        // Allow manual refresh of metrics
        if (request()->has('refresh_metrics') || request()->has('refresh')) {
            \Cache::forget($cacheKey);
        }
        
        $metrics = \Cache::remember($cacheKey, $cacheDuration, function() use ($user) {
            $isAdminOrSuperAdmin = in_array($user->role, ['admin', 'super_admin']);
            
            if ($isAdminOrSuperAdmin) {
                // Admin metrics - optimized single query where possible
                $requestStats = \App\Models\Request::selectRaw('
                    COUNT(*) as total_requests,
                    COUNT(DISTINCT stock_code) as total_stocks,
                    SUM(CASE WHEN result = "WIN" THEN 1 ELSE 0 END) as total_wins,
                    SUM(CASE WHEN result = "LOSS" THEN 1 ELSE 0 END) as total_loss,
                    SUM(CASE WHEN result = "TIMEOUT" THEN 1 ELSE 0 END) as total_timeout
                ')->first();
                
                $userStats = User::selectRaw('
                    COUNT(*) as total_users,
                    SUM(CASE WHEN EXISTS (
                        SELECT 1 FROM requests r WHERE r.user_id = users.id AND r.created_at >= ?
                    ) THEN 1 ELSE 0 END) as active_users
                ', [now()->subDays(30)])->first();
                
                return [
                    'totalRequests' => $requestStats->total_requests,
                    'totalStocks' => $requestStats->total_stocks,
                    'totalWins' => $requestStats->total_wins,
                    'totalLoss' => $requestStats->total_loss,
                    'totalTimeout' => $requestStats->total_timeout,
                    'totalUsers' => $userStats->total_users,
                    'activeUsers' => $userStats->active_users,
                    'inactiveUsers' => $userStats->total_users - $userStats->active_users,
                ];
            } else {
                // User metrics - single optimized query
                $userStats = \App\Models\Request::where('user_id', $user->id)
                    ->selectRaw('
                        COUNT(*) as total_requests,
                        COUNT(DISTINCT stock_code) as total_stocks,
                        SUM(CASE WHEN result = "WIN" THEN 1 ELSE 0 END) as total_wins,
                        SUM(CASE WHEN result = "LOSS" THEN 1 ELSE 0 END) as total_loss,
                        SUM(CASE WHEN result = "TIMEOUT" THEN 1 ELSE 0 END) as total_timeout
                    ')->first();
                
                return [
                    'totalRequests' => $userStats->total_requests,
                    'totalStocks' => $userStats->total_stocks,
                    'totalWins' => $userStats->total_wins,
                    'totalLoss' => $userStats->total_loss,
                    'totalTimeout' => $userStats->total_timeout,
                    'totalUsers' => null,
                    'activeUsers' => null,
                    'inactiveUsers' => null,
                ];
            }
        });
        
        // Extract metrics from cache
        extract($metrics);

        // Get market insights - reduced cache duration for more current data
        $marketInsights = \Cache::remember('market_insights', 300, function() { // 5 minutes cache
            try {
                $cacheService = app(\App\Services\CacheService::class);
                return $cacheService->getMarketInsights();
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => 'Market data temporarily unavailable',
                    'top_active' => [],
                    'top_promising' => []
                ];
            }
        });
        
        // Allow manual refresh if requested
        if (request()->has('refresh_market') || request()->has('refresh')) {
            try {
                \Cache::forget('market_insights');
                $cacheService = app(\App\Services\CacheService::class);
                $marketInsights = $cacheService->refreshMarketInsights();
                \Cache::put('market_insights', $marketInsights, 300); // Updated cache duration
            } catch (\Exception $e) {
                // Keep cached version if refresh fails
            }
        }

        return view('Dashboard.dashboard', compact(
            'totalRequests', 'totalStocks', 'totalWins', 'totalLoss', 'totalTimeout',
            'totalUsers', 'activeUsers', 'inactiveUsers', 'marketInsights', 'user'
        ));
    }

    /**
     * Clear dashboard cache for all users when data changes
     */
    private function clearDashboardCache()
    {
        // Clear cache for all user roles
        $patterns = [
            'dashboard_metrics_admin_*',
            'dashboard_metrics_super_admin_*',
            'dashboard_metrics_user_*'
        ];
        
        foreach ($patterns as $pattern) {
            \Cache::flush(); // For simplicity, flush all cache
        }
        
        // Also clear market insights
        \Cache::forget('market_insights');
    }

    public function stocks(Request $request)
    {
        $data = $this->getRequestsData($request);
        return view('Requests.requests', $data);
    }

    public function users(Request $request)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('dashboard');
        }

        // Refresh user data from database to ensure latest role information
        $freshUser = User::find($user->id);
        session(['user' => $freshUser]);
        $user = $freshUser;

        $query = User::query();

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        // Handle special case for requests count sorting
        if ($sortBy === 'requests_count') {
            $users = $query->withCount('requests')->orderBy('requests_count', $sortOrder)->paginate($request->get('per_page', 10));
        } else {
            $users = $query->withCount('requests')->orderBy($sortBy, $sortOrder)->paginate($request->get('per_page', 10));
        }

        return view('Users.users', compact('users', 'user'));
    }

    public function userDetail($id)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('dashboard');
        }

        $targetUser = User::findOrFail($id);
        
        return view('Users.userdetail', compact('targetUser', 'user'));
    }

    public function createUser(Request $request)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
            return redirect()->route('users.index')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:user,admin',
        ]);

        // Default role based on current user's permission
        $newUserRole = 'user';
        if ($user->role === 'super_admin' && !empty($validated['role'])) {
            // Super admin can create any role
            $newUserRole = $validated['role'];
        } elseif ($user->role === 'admin' && !empty($validated['role']) && $validated['role'] === 'user') {
            // Admin can only create users, not other admins
            $newUserRole = $validated['role'];
        }

        $newUser = User::create([
            'name' => $validated['full_name'], // Use name instead of full_name
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'password' => bcrypt($validated['password']),
            'role' => $newUserRole,
        ]);

        // Send email notification about user creation
        $this->sendUserActionNotification($user, $newUser, 'created', $newUserRole);

        // Check if request wants JSON response (AJAX)
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // Regular form submission - redirect with success message
        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    // Simple user creation method like signup
    public function createUserSimple(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile_number' => 'nullable|string|max:20',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'nullable|in:user,admin',
            ]);

            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'mobile_number' => $validated['mobile_number'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] ?? 'user',
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully!');
        } catch (Exception $e) {
            return redirect()->route('users.index')->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, $id)
    {
        \Log::info('🔥 AdminController updateUser called', [
            'user_id' => $id,
            'method' => $request->method(),
            'all_data' => $request->all()
        ]);

        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('users.index');
        }

        $targetUser = User::findOrFail($id);
        $oldRole = $targetUser->role;

        \Log::info('🔍 Before validation');
        
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'mobile_number' => 'nullable|string|max:20|unique:users,mobile_number,' . $id,
                'user_new_pwd' => 'nullable|string|min:6',
                'role' => 'nullable|in:user,admin,super_admin',
            ]);
            \Log::info('✅ Validation passed', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validation FAILED', $e->errors());
            throw $e;
        }

        // Handle password field (rename user_new_pwd to password for database)
        if (!empty($validated['user_new_pwd'])) {
            $validated['password'] = bcrypt($validated['user_new_pwd']);
            unset($validated['user_new_pwd']);
        }

        // Handle role changes
        $newRole = $targetUser->role; // Default to current role
        if ($user->role === 'super_admin' && !empty($validated['role'])) {
            $newRole = $validated['role'];
            $validated['role'] = $newRole;
        } else {
            // Remove role from validation if user is not super_admin
            unset($validated['role']);
        }

        // Map full_name to name field and handle nullable mobile_number
        $validated['name'] = $validated['full_name'];
        $validated['mobile_number'] = !empty(trim($validated['mobile_number'] ?? '')) ? trim($validated['mobile_number']) : null;
        unset($validated['full_name']);

        $targetUser->update($validated);

        // PERFORMANCE FIX: DISABLE request updates completely to prevent timeout
        // Historical requests keep their original data for audit trail
        // Only new requests will use updated user profile data
        // This prevents 504 timeout on users with many historical requests

        // PERFORMANCE FIX: DISABLE email notifications to prevent timeout
        // Email notifications can cause 504 timeout due to SMTP connection issues
        // Notifications disabled for immediate user update response

        // Check if request expects JSON (AJAX request)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!'
            ]);
        }

        return redirect()->route('users.show', $id)
                         ->with('success', 'User updated successfully!');
    }

    public function deleteUser($id)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('users.index');
        }

        $targetUser = User::findOrFail($id);
        
        // Don't allow user to delete themselves
        if ($targetUser->id === $user->id) {
            return redirect()->route('users.index')
                           ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // PERFORMANCE FIX: Manual CASCADE DELETE using raw SQL for maximum performance
        // This avoids Laravel ORM overhead and ensures instant deletion

        DB::beginTransaction();
        try {
            // Delete requests first using raw SQL for speed
            DB::delete('DELETE FROM requests WHERE user_id = ?', [$targetUser->id]);

            // Delete user
            DB::delete('DELETE FROM users WHERE id = ?', [$targetUser->id]);

            DB::commit();
            $message = "User deleted successfully! Associated requests also removed.";
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('users.index')
                           ->withErrors(['error' => 'Delete failed: ' . $e->getMessage()]);
        }

        return redirect()->route('users.index')
                         ->with('success', $message);
    }

    public function verifyUser($id)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('users.index');
        }

        $targetUser = User::findOrFail($id);
        
        // Check if user is already verified
        if ($targetUser->email_verified_at) {
            return redirect()->route('users.index')
                           ->with('error', 'User is already verified.');
        }

        // Verify the user
        $targetUser->update([
            'email_verified_at' => now()
        ]);

        return redirect()->route('users.index')
                         ->with('success', "User {$targetUser->name} has been verified successfully!");
    }

    /**
     * Shared logic for getting requests data (used by index and stocks methods)
     */
    private function getRequestsData(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('home');
        }

        $query = \App\Models\Request::query();

        // Role-based filtering
        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        // Search functionality - optimized with proper indexing
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $user) {
                if (in_array($user->role, ['admin', 'super_admin'])) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('stock_code', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                } else {
                    // For user role, only search in stock code and company name
                    $q->where('stock_code', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                }
            });
        }

        // Filter by timeframe
        if ($request->filled('timeframe')) {
            $query->where('timeframe', $request->timeframe);
        }

        // Sorting - optimized with proper indexing
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        // For user role, prevent sorting by full_name since it's hidden
        if ($user->role === 'user' && $sortBy === 'full_name') {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        // Per page - with user selectable options
        $perPage = $request->get('per_page', 10);
        $requests = $query->paginate($perPage);

        return compact('requests', 'user');
    }

    /**
     * Send email notification for role changes (promote/demote)
     */
    private function sendRoleChangeNotification($actionBy, $targetUser, $action, $oldRole, $newRole)
    {
        try {
            $superAdmin = \App\Models\User::where('role', 'super_admin')->first();
            
            $data = [
                'actionBy' => $actionBy,
                'targetUser' => $targetUser,
                'action' => $action,
                'oldRole' => ucfirst($oldRole),
                'newRole' => ucfirst($newRole),
            ];

            // Recipients: target user, super admin (cc)
            $recipients = [$targetUser->email];
            if ($superAdmin && $superAdmin->email !== $targetUser->email) {
                $recipients[] = $superAdmin->email;
            }

            \Illuminate\Support\Facades\Mail::send('emails.role-change', $data, function ($message) use ($targetUser, $action, $newRole, $actionBy, $superAdmin) {
                $message->to($targetUser->email)
                        ->subject("Your role has been {$action} to {$newRole}")
                        ->replyTo($actionBy->email, $actionBy->name);
                
                if ($superAdmin && $superAdmin->email !== $targetUser->email) {
                    $message->cc($superAdmin->email);
                }
            });

        } catch (\Exception $e) {
            \Log::error('Role change notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification for user actions (create, update, delete)
     */
    private function sendUserActionNotification($actionBy, $targetUser, $action, $newRole = null)
    {
        try {
            $superAdmin = \App\Models\User::where('role', 'super_admin')->first();
            
            $data = [
                'actionBy' => $actionBy,
                'targetUser' => $targetUser,
                'action' => $action,
                'newRole' => $newRole ? ucfirst($newRole) : ucfirst($targetUser->role),
            ];

            // Recipients based on action type
            $recipients = [];
            if ($action === 'deleted') {
                // For deletion, only notify super admin (user no longer exists)
                if ($superAdmin) {
                    $recipients[] = $superAdmin->email;
                }
            } else {
                // For create/update, notify target user
                $recipients[] = $targetUser->email;
                
                // CC super admin and action performer
                if ($superAdmin && $superAdmin->email !== $targetUser->email) {
                    $recipients[] = $superAdmin->email;
                }
                if ($actionBy->email !== $targetUser->email && (!$superAdmin || $actionBy->email !== $superAdmin->email)) {
                    $recipients[] = $actionBy->email;
                }
            }

            if (!empty($recipients)) {
                \Illuminate\Support\Facades\Mail::send('emails.user-action', $data, function ($message) use ($targetUser, $action, $actionBy, $recipients) {
                    $message->to($recipients[0]);
                    if (count($recipients) > 1) {
                        $message->cc(array_slice($recipients, 1));
                    }
                    $message->subject("User account {$action}: {$targetUser->name}")
                            ->replyTo($actionBy->email, $actionBy->name);
                });
            }

        } catch (\Exception $e) {
            \Log::error('User action notification failed: ' . $e->getMessage());
        }
    }

}
