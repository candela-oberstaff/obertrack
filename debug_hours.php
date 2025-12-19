<?php

use App\Models\User;
use App\Models\WorkHours;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Find the user
$user = User::where('name', 'like', '%Cande Villaverde%')->first();

if (!$user) {
    echo "User not found.\n";
    exit;
}

echo "User ID: {$user->id}\n";
echo "Name: {$user->name}\n";

// Define the week
$weekStart = Carbon::create(2025, 12, 1); // Dec 1st 2025 is Monday
$weekEnd = Carbon::create(2025, 12, 5)->endOfDay(); // Dec 5th 2025

echo "Checking range: " . $weekStart->toDateTimeString() . " to " . $weekEnd->toDateTimeString() . "\n";

// Fetch raw DB records
$rawHours = DB::table('work_hours')
    ->where('user_id', $user->id)
    ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
    ->get();

echo "\nRaw DB Records:\n";
foreach ($rawHours as $h) {
    echo "ID: {$h->id} | Date: {$h->work_date} | Hours: {$h->hours_worked} | Approved: " . var_export($h->approved, true) . "\n";
}

// Fetch via Eloquent
$eloquentHours = WorkHours::where('user_id', $user->id)
    ->whereBetween('work_date', [$weekStart, $weekEnd])
    ->get();

echo "\nEloquent Records (Casted):\n";
foreach ($eloquentHours as $h) {
    echo "ID: {$h->id} | Date: {$h->work_date->format('Y-m-d')} | Hours: {$h->hours_worked} | Approved: " . var_export($h->approved, true) . "\n";
}

// Test Filtering Logic
echo "\nLogic Test:\n";
$pending = $eloquentHours->where('approved', false);
echo "Count Pending: " . $pending->count() . "\n";
echo "Sum Pending: " . $pending->sum('hours_worked') . "\n";

$approved = $eloquentHours->where('approved', true);
echo "Count Approved: " . $approved->count() . "\n";

// Check loose comparison
$pendingLoose = $eloquentHours->filter(function($h) {
    return $h->approved == false;
});
echo "Count Pending (Closure == false): " . $pendingLoose->count() . "\n";

