<?php
// Test script to verify quantity implementation
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteCollection;
use Illuminate\Config\Repository as Config;

// Bootstrap Laravel container
$app = new Container();
$app->instance('app', $app);

// Set up basic Laravel services needed for testing
$app->singleton('config', function () {
    return new Config([
        'services' => [
            'erprev' => [
                'account_url' => 'test.erprev.com',
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'enabled' => false, // Disabled for testing
            ]
        ]
    ]);
});

// Test the RevService with quantity
$revService = new App\Services\RevService();

// Create a mock book object
$book = new stdClass();
$book->id = 1;
$book->title = 'Test Book';
$book->description = 'Test Description';
$book->price = 29.99;
$book->isbn = '1234567890';
$book->genre = 'Fiction';
$book->genre_id = '1';

// Test registerProduct with quantity
echo "Testing registerProduct with quantity...\n";
$result = $revService->registerProduct($book, 10);

echo "Result: ";
print_r($result);

echo "\nImplementation test completed.\n";
?>