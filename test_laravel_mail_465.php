<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Override config for testing Port 465 / SSL
Config::set('mail.mailers.smtp.port', 465);
Config::set('mail.mailers.smtp.encryption', 'ssl');

try {
    Mail::raw('Test email sent successfully via Laravel Facade (SSL 465).', function ($message) {
        $message->to('aboajahemmanuel@gmail.com')
                ->subject('Test Email (Laravel SSL 465)');
    });
    echo "Message successfully sent via Laravel (SSL 465)!\n";
} catch (\Exception $e) {
    echo "Error sending email (SSL 465): " . $e->getMessage() . "\n";
}
