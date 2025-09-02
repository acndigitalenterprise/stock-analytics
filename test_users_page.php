<?php
// Test script to check users page directly
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create fake session
$user = new App\Models\User();
$user->id = 1;
$user->name = 'Test Admin';
$user->email = 'admin@example.com';
$user->role = 'admin';

// Simulate session
session(['user' => $user]);

// Create request to users page
$request = \Illuminate\Http\Request::create('/users', 'GET');

// Get the controller
$controller = new \App\Http\Controllers\AdminController();

try {
    $response = $controller->users($request);
    
    if ($response instanceof \Illuminate\View\View) {
        echo "SUCCESS: Users page loaded successfully!\n";
        echo "View: " . $response->name() . "\n";
        echo "Data keys: " . implode(', ', array_keys($response->getData())) . "\n";
    } else {
        echo "REDIRECT: " . get_class($response) . "\n";
        if (method_exists($response, 'getTargetUrl')) {
            echo "Target: " . $response->getTargetUrl() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}