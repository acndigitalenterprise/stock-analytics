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

class SendStockAdviceEmail implements ShouldQueue
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
        \Log::info('Starting stock advice email job for user: ' . $this->user->email);
        
        try {
            $data = [
                'user' => $this->user,
                'request' => $this->request,
            ];

            \Log::info('Sending stock advice email to: ' . $this->user->email);
            
            Mail::send('emails.stock-advice', $data, function ($message) {
                $message->to($this->user->email)
                        ->subject('📊 Stock Analysis Report - ' . $this->request->stock_code . ' (' . ucfirst($this->request->timeframe) . ')');
            });
            
            \Log::info('Stock advice email sent successfully to: ' . $this->user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send stock advice email: ' . $e->getMessage());
            throw $e;
        }
    }
} 