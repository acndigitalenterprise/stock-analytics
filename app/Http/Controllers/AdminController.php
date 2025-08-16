<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Jobs\SendNewRequestEmail;
use App\Jobs\GenerateStockAdvice;
use Illuminate\Support\Facades\Log;
use App\Services\StockService;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('stock-analytics.index');
        }

        // ALL users (including regular users) should go to dashboard first
        // This provides consistent experience across all roles
        return redirect()->route('stock-analytics.admin.dashboard');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store method called', ['data' => $request->all()]);
            
            // Check trading hours (09:00-16:00 WIB)
            $currentHour = now()->setTimezone('Asia/Jakarta')->format('H');
            $isTradingHours = $currentHour >= 9 && $currentHour < 16;
            
            if (!$isTradingHours) {
                $currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i');
                Log::info('Trading hours check failed', ['current_time' => $currentTime]);
                return response()->json([
                    'error' => "Market is closed. Trading hours: 09:00-16:00 WIB (Current: {$currentTime} WIB)"
                ], 403);
            }

            $validated = $request->validate([
                'stock_code' => 'required|string|max:10',
                'timeframe' => 'required|in:1h,1d',
            ]);
            Log::info('Validation passed', ['validated' => $validated]);

            $user = session('user');
            if (!$user) {
                Log::error('No user in session');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            Log::info('User found in session', ['user_id' => $user->id]);

        $stockRequest = \App\Models\Request::create([
            'full_name' => $user->name,
            'mobile_number' => $user->mobile_number ?? '000-000-0000', // Handle null mobile_number for admin
            'email' => $user->email,
            'stock_code' => StockService::ensureJKFormat($validated['stock_code']),
            'company_name' => StockService::getCompanyName($validated['stock_code']),
            'timeframe' => $validated['timeframe'],
            'user_id' => $user->id,
        ]);

        // Send email notification (will be processed in background)
        try {
            Log::info('Dispatching email job for user: ' . $user->email);
            dispatch(new SendNewRequestEmail($user, $stockRequest));
            Log::info('Email job dispatched successfully');
        } catch (\Exception $e) {
            // Log error but don't block the request
            Log::error('Failed to queue email notification: ' . $e->getMessage());
        }

        // Dispatch job to generate AI advice
        try {
            Log::info('Dispatching GenerateStockAdvice job', [
                'request_id' => $stockRequest->id,
                'stock_code' => $stockRequest->stock_code,
                'timeframe' => $stockRequest->timeframe
            ]);
            
            GenerateStockAdvice::dispatch($stockRequest);
            
            Log::info('GenerateStockAdvice job dispatched successfully', [
                'request_id' => $stockRequest->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch GenerateStockAdvice job', [
                'request_id' => $stockRequest->id,
                'error' => $e->getMessage()
            ]);
        }

            Log::info('Request created successfully', ['request_id' => $stockRequest->id]);
            return response()->json([
                'success' => true,
                'message' => 'New request created successfully! Email will be sent shortly and AI advice will be generated in the background.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in store method', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('stock-analytics.index');
        }

        $stockRequest = \App\Models\Request::findOrFail($id);

        // Check if user can edit this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return redirect()->route('stock-analytics.admin.requests')
                ->withErrors(['error' => 'You can only edit your own requests.']);
        }

        return view('admin.edit', compact('stockRequest'));
    }

    public function update(Request $request, $id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('stock-analytics.index');
        }

        $stockRequest = \App\Models\Request::findOrFail($id);

        // Check if user can edit this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return redirect()->route('stock-analytics.admin.requests')
                ->withErrors(['error' => 'You can only edit your own requests.']);
        }

        $validated = $request->validate([
            'advice' => 'nullable|string|max:1000',
        ]);

        $stockRequest->update($validated);

        return redirect()->route('stock-analytics.admin.requests')
            ->with('success', 'Request updated successfully!');
    }

    public function delete($id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('stock-analytics.index');
        }

        $stockRequest = \App\Models\Request::findOrFail($id);

        // Check if user can delete this request
        if ($user->role === 'user' && $stockRequest->user_id !== $user->id) {
            return redirect()->route('stock-analytics.admin.requests')
                ->withErrors(['error' => 'You can only delete your own requests.']);
        }

        $stockRequest->delete();

        return redirect()->route('stock-analytics.admin.requests')
            ->with('success', 'Request deleted successfully!');
    }

    public function detail($id)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('stock-analytics.index');
        }
        $stockRequest = \App\Models\Request::findOrFail($id);
        // Hanya admin, super_admin atau owner yang boleh lihat
        if (!in_array($user->role, ['admin', 'super_admin']) && $stockRequest->user_id !== $user->id) {
            return redirect()->route('stock-analytics.admin.requests')->withErrors(['error' => 'Unauthorized.']);
        }
        return view('admin.detail', compact('stockRequest', 'user'));
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

        return response()->json([
            'has_advice' => !empty($stockRequest->advice),
            'advice' => $stockRequest->advice ? str_replace('```markdown', '', $stockRequest->advice) : null,
            'updated_at' => $stockRequest->updated_at
        ]);
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
            return redirect()->route('stock-analytics.index');
        }

        // Calculate role-based metrics
        $isAdminOrSuperAdmin = in_array($user->role, ['admin', 'super_admin']);
        
        if ($isAdminOrSuperAdmin) {
            // Admin metrics - all data
            $totalRequests = \App\Models\Request::count();
            $totalStocks = \App\Models\Request::distinct('stock_code')->count('stock_code');
            $totalWins = \App\Models\Request::where('result', 'WIN')->count();
            $totalLoss = \App\Models\Request::where('result', 'LOSS')->count();
            $totalTimeout = \App\Models\Request::where('result', 'TIMEOUT')->count();
            $totalUsers = User::count();
            
            // Active users (users who made requests in last 30 days)
            $activeUsers = User::whereHas('requests', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count();
            $inactiveUsers = $totalUsers - $activeUsers;
        } else {
            // User metrics - only their own data
            $totalRequests = \App\Models\Request::where('user_id', $user->id)->count();
            $totalStocks = \App\Models\Request::where('user_id', $user->id)->distinct('stock_code')->count('stock_code');
            $totalWins = \App\Models\Request::where('user_id', $user->id)->where('result', 'WIN')->count();
            $totalLoss = \App\Models\Request::where('user_id', $user->id)->where('result', 'LOSS')->count();
            $totalTimeout = \App\Models\Request::where('user_id', $user->id)->where('result', 'TIMEOUT')->count();
            
            // User role doesn't see user management metrics
            $totalUsers = null;
            $activeUsers = null;
            $inactiveUsers = null;
        }

        // Get market insights (cached or fresh if requested)
        $marketInsights = null;
        try {
            $cacheService = app(\App\Services\CacheService::class);
            
            // Check if user requested fresh data
            if (request()->has('refresh_market')) {
                $marketInsights = $cacheService->refreshMarketInsights();
            } else {
                $marketInsights = $cacheService->getMarketInsights();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to load market insights', ['error' => $e->getMessage()]);
            $marketInsights = [
                'success' => false,
                'error' => 'Market data temporarily unavailable',
                'top_active' => [],
                'top_promising' => []
            ];
        }

        return view('admin.dashboard', compact(
            'totalRequests', 'totalStocks', 'totalWins', 'totalLoss', 'totalTimeout',
            'totalUsers', 'activeUsers', 'inactiveUsers', 'marketInsights', 'user'
        ));
    }

    public function stocks(Request $request)
    {
        $data = $this->getRequestsData($request);
        return view('admin.requests', $data);
    }

    public function users(Request $request)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('stock-analytics.admin');
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
                $q->where('full_name', 'LIKE', "%{$search}%")
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

        return view('admin.users', compact('users', 'user'));
    }

    public function userDetail($id)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('stock-analytics.admin');
        }

        $targetUser = User::findOrFail($id);
        
        return view('admin.user-detail', compact('targetUser', 'user'));
    }

    public function createUser(Request $request)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'required|string|max:20',
            'password' => 'required|string|min:6',
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
            'mobile_number' => $validated['mobile_number'],
            'password' => bcrypt($validated['password']),
            'role' => $newUserRole,
        ]);

        // Send email notification about user creation
        $this->sendUserActionNotification($user, $newUser, 'created', $newUserRole);

        return response()->json(['success' => true]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('stock-analytics.admin.users');
        }

        $targetUser = User::findOrFail($id);
        $oldRole = $targetUser->role;

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile_number' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|in:user,admin',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
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

        // Map full_name to name field
        $validated['name'] = $validated['full_name'];
        unset($validated['full_name']);

        $targetUser->update($validated);

        // Send notification if role changed
        if ($oldRole !== $newRole) {
            $action = ($newRole === 'admin') ? 'promoted' : 'demoted';
            $this->sendRoleChangeNotification($user, $targetUser, $action, $oldRole, $newRole);
        } else {
            // Send regular update notification
            $this->sendUserActionNotification($user, $targetUser, 'updated');
        }

        return redirect()->route('stock-analytics.admin.users.detail', $id)
                         ->with('success', 'User updated successfully!');
    }

    public function deleteUser($id)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('stock-analytics.admin.users');
        }

        $targetUser = User::findOrFail($id);
        
        // Don't allow user to delete themselves
        if ($targetUser->id === $user->id) {
            return redirect()->route('stock-analytics.admin.users')
                           ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // Send deletion notification before deleting
        $this->sendUserActionNotification($user, $targetUser, 'deleted');

        // Delete all requests associated with this user first
        $requestCount = $targetUser->requests()->count();
        $targetUser->requests()->delete();
        
        // Then delete the user
        $targetUser->delete();

        $message = "User deleted successfully!";
        if ($requestCount > 0) {
            $message .= " Also deleted {$requestCount} request(s) associated with this user.";
        }

        return redirect()->route('stock-analytics.admin.users')
                         ->with('success', $message);
    }

    /**
     * Shared logic for getting requests data (used by index and stocks methods)
     */
    private function getRequestsData(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->route('stock-analytics.index');
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
