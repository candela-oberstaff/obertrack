<?php
/**
 * WAHA Connection Diagnostic Script
 * Run this from command line: php test-waha-connection.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WAHA Connection Diagnostic ===\n\n";

// 1. Check configuration
$wahaUrl = env('WAHA_BASE_URL');
$wahaKey = env('WAHA_API_KEY');

echo "1. Configuration Check:\n";
echo "   WAHA_BASE_URL: " . ($wahaUrl ?: "NOT SET") . "\n";
echo "   WAHA_API_KEY: " . ($wahaKey ? "SET (hidden)" : "NOT SET") . "\n\n";

if (!$wahaUrl) {
    echo "❌ ERROR: WAHA_BASE_URL is not configured in .env\n";
    echo "   Please add: WAHA_BASE_URL=https://waha.obertrack.com\n";
    exit(1);
}

// 2. Test basic connectivity
echo "2. Testing connectivity to: {$wahaUrl}\n";

try {
    $headers = ['Accept' => 'application/json'];
    if ($wahaKey) {
        $headers['X-Api-Key'] = $wahaKey;
    }

    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
        ->withHeaders($headers)
        ->timeout(10)
        ->get("{$wahaUrl}/api/sessions");

    echo "   Status Code: " . $response->status() . "\n";
    echo "   Response: " . substr($response->body(), 0, 200) . "...\n\n";

    if ($response->successful()) {
        echo "✅ Connection successful!\n";
        echo "   Sessions found: " . count($response->json()) . "\n\n";
    } else {
        echo "⚠️  Connection established but got error response\n\n";
    }

} catch (\Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 3. Test session creation
echo "3. Testing session creation (test_session_diagnostic):\n";

try {
    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
        ->withHeaders($headers)
        ->post("{$wahaUrl}/api/sessions", [
            'name' => 'test_session_diagnostic',
            'config' => ['proxy' => null, 'webhooks' => []]
        ]);

    echo "   Status Code: " . $response->status() . "\n";
    
    if ($response->successful() || $response->status() === 422) {
        echo "✅ Session endpoint working (422 = already exists, which is OK)\n";
        
        // Try to get status
        $statusResponse = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders($headers)
            ->get("{$wahaUrl}/api/sessions/test_session_diagnostic");
            
        if ($statusResponse->successful()) {
            $status = $statusResponse->json();
            echo "   Session Status: " . ($status['status'] ?? 'unknown') . "\n";
        }
        
        // Cleanup
        \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders($headers)
            ->delete("{$wahaUrl}/api/sessions/test_session_diagnostic");
            
        echo "   Test session cleaned up.\n\n";
    } else {
        echo "❌ Session creation failed: " . $response->body() . "\n\n";
    }

} catch (\Exception $e) {
    echo "❌ Session test failed: " . $e->getMessage() . "\n\n";
}

echo "=== Diagnostic Complete ===\n";
echo "\nIf all tests passed, the WAHA integration should work.\n";
echo "If tests failed, check:\n";
echo "  1. WAHA is running and accessible\n";
echo "  2. URL is correct in .env\n";
echo "  3. Firewall/network allows connection\n";
