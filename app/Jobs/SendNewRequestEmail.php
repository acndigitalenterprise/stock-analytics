<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Request;

class SendNewRequestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $request;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('Starting email job for user: ' . $this->user->email);
        
        try {
            $data = [
                'user' => $this->user,
                'request' => $this->request,
            ];

            \Log::info('Sending email to: ' . $this->user->email);
            
            Mail::send('emails.new-request', $data, function ($message) {
                $message->to($this->user->email)
                        ->subject('New Stock Analytics Request');
            });
            
            \Log::info('Email sent successfully to: ' . $this->user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
            throw $e;
        }
    }
}
