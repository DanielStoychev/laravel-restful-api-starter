<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Send Welcome Email Listener
 * 
 * Handles sending welcome email when a user registers.
 * Uses queue for better performance.
 */
class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        try {
            // Dispatch welcome email job
            SendWelcomeEmailJob::dispatch($event->user);

            Log::info('Welcome email queued for user', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue welcome email', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger failure handling
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(UserRegistered $event, \Throwable $exception): void
    {
        Log::error('Welcome email listener failed', [
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
