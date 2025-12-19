<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;

class SerializeTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:serialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification serialization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get a user and book
        $user = User::first();
        $book = Book::first();

        if (!$user || !$book) {
            $this->error('Need at least one user and one book to test');
            return;
        }

        $this->info("Creating notification...");
        
        // Create the notification
        $notification = new BookSubmitted($book);
        
        $this->info("Notification class: " . get_class($notification));
        
        try {
            $serialized = serialize($notification);
            $this->info("Serialized successfully. Length: " . strlen($serialized) . " bytes");
            
            $unserialized = unserialize($serialized);
            $this->info("Unserialized successfully. Class: " . get_class($unserialized));
        } catch (\Exception $e) {
            $this->error("Serialization failed: " . $e->getMessage());
        }
    }
}