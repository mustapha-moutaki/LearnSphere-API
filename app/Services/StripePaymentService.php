<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Course $course, $user)
{
    try {
        // Create an enrollment record first
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending'
        ]);

        // Create Stripe Checkout Session
        $session = Session::create([
            'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'unit_amount' => $course->price * 100,
            'product_data' => [
                'name' => $course->title,
            ],
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => url(route('payment.success', ['enrollment_id' => $enrollment->id])),
    'cancel_url' => url(route('payment.cancel', ['enrollment_id' => $enrollment->id])),
    'metadata' => [
        'enrollment_id' => $enrollment->id,
        'course_id' => $course->id,
        'user_id' => $user->id
    ]
        ]);

        return [
            'session_id' => $session->id,
            'enrollment_id' => $enrollment->id,
            'checkout_url' => $session->url
        ];
    } catch (\Exception $e) {
        Log::error('Stripe Checkout Session Error', [
            'message' => $e->getMessage(),  // Corrected line
            'trace' => $e->getTraceAsString()
        ]);

        throw $e;
    }
}

    public function verifyPayment($sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);

            // Check payment status
            if ($session->payment_status === 'paid') {
                $enrollment = Enrollment::findOrFail($session->metadata['enrollment_id']);
                $enrollment->status = 'paid';
                $enrollment->save();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Stripe Payment Verification Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}