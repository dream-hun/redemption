<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AuthCodeDelivery;
use App\Models\Domain;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

final class AuthCodeController extends Controller
{
    public function __construct() {}

    /**
     * Show the auth code generation form
     */
    public function showGenerateForm(Domain $domain)
    {
        if ($domain->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized: You do not own this domain.');
        }

        return view('admin.domains.auth_code_generate', ['domain' => $domain]);
    }

    /**
     * Generate and send auth code to the new owner's email
     */
    public function generateAndSend(Request $request, Domain $domain): RedirectResponse
    {
        if ($domain->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized: You do not own this domain.');
        }

        $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
        ]);

        // Rate limit to prevent abuse (e.g., 3 requests per hour per user)
        $rateLimitKey = 'auth-code:'.Auth::id();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return redirect()->back()
                ->with('error', 'Too many auth code requests. Please try again again in 5 min.');
        }

        try {
            // Generate new or retrieve existing auth code
            $authCode = $domain->generateAuthCode();

            $domain->authCodeRequests()->create([
                'user_id' => Auth::id(),
                'auth_code' => $authCode,
                'recipient_email' => $request->recipient_email,
                'sent_at' => now(),
            ]);

            // Send email to the new owner
            try {
                Mail::to($request->recipient_email)->send(
                    new AuthCodeDelivery($domain, $authCode, $request->recipient_email, Auth::user())
                );

                // Increment rate limiter
                RateLimiter::hit($rateLimitKey, (5 * 60)); // again in 5 min

                Log::info('Auth code sent', [
                    'domain' => $domain->name,
                    'user_id' => Auth::id(),
                    'recipient_email' => $request->recipient_email,
                ]);

                return redirect()->route('admin.domains.index')
                    ->with('success', 'Auth code sent to '.$request->recipient_email);
            } catch (Exception $ee) {
                Log::error('Auth code NOT sent', [
                    'Error: ' => $ee->getMessage(),
                    'domain' => $domain->name,
                    'user_id' => Auth::id(),
                    'recipient_email' => $request->recipient_email,
                ]);

                return redirect()->back()
                    ->with('error', 'Failed to send email with auth code: '.$ee->getMessage());
            }
        } catch (Exception $e) {
            Log::error('Failed to send auth code: '.$e->getMessage(), [
                'domain' => $domain->name,
                'user_id' => Auth::id(),
                'recipient_email' => $request->recipient_email,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send auth code: '.$e->getMessage());
        }
    }
}
