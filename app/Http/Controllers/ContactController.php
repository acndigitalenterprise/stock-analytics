<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            // Validate the form data
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:20',
                'email_address' => 'required|email|max:255',
                'message' => 'required|string|min:10|max:2000',
            ], [
                'full_name.required' => 'Full name is required',
                'mobile_number.required' => 'Mobile number is required',
                'email_address.required' => 'Email address is required',
                'email_address.email' => 'Please enter a valid email address',
                'message.required' => 'Message is required',
                'message.min' => 'Message must be at least 10 characters',
                'message.max' => 'Message cannot exceed 2000 characters',
            ]);

            Log::info('Contact form submitted', [
                'name' => $validated['full_name'],
                'email' => $validated['email_address'],
                'mobile' => $validated['mobile_number']
            ]);

            // Prepare email data
            $emailData = [
                'full_name' => $validated['full_name'],
                'mobile_number' => $validated['mobile_number'],
                'email_address' => $validated['email_address'],
                'user_message' => $validated['message'],
                'submitted_at' => now()->setTimezone('Asia/Jakarta')->format('d M Y H:i T')
            ];

            // Send email to contact@tickerapp.ai
            try {
                Mail::send('emails.contact-message', $emailData, function ($message) use ($validated) {
                    $message->to('contact@tickerapp.ai')
                            ->subject('Contacts | Tickerai.app')
                            ->replyTo($validated['email_address'], $validated['full_name']);
                });

                Log::info('Contact email sent successfully', [
                    'to' => 'contact@tickerapp.ai',
                    'from' => $validated['email_address']
                ]);

                return redirect()->route('contacts')->with('success', 
                    'Thank you for contacting us! Your message has been sent successfully. We will get back to you soon.');

            } catch (\Exception $emailException) {
                Log::error('Failed to send contact email', [
                    'error' => $emailException->getMessage(),
                    'email' => $validated['email_address']
                ]);

                return redirect()->route('contacts')->with('error', 
                    'Sorry, there was an issue sending your message. Please try again later or contact us directly at contact@tickerapp.ai');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Contact form validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['_token'])
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('Contact form error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('contacts')->with('error', 
                'An unexpected error occurred. Please try again later.');
        }
    }
}