<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function paymentSuccess($enrollment_id)
    {
        try {
            // Find the enrollment
            $enrollment = Enrollment::findOrFail($enrollment_id);

            // Optional: You might want to do additional checks here
            // For example, verify the payment status with Stripe again

            // Update enrollment status if needed
            if ($enrollment->status == 'pending') {
                $enrollment->status = 'complete';
                $enrollment->save();
            }

            // Redirect to a success page or return a response
            return view('payment.success', [
                'enrollment' => $enrollment,
                'course' => $enrollment->course
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Payment Success Error', [
                'enrollment_id' => $enrollment_id,
                'message' => $e->getMessage()
            ]);

            // Redirect to an error page
            return view('payment.error', [
                'message' => 'There was an issue processing your payment.'
            ]);
        }
    }

    public function paymentCancel($enrollment_id)
    {
        try {
            // Find the enrollment
            $enrollment = Enrollment::findOrFail($enrollment_id);

            // Optional: Update enrollment status
            if ($enrollment->status == 'pending') {
                $enrollment->status = 'active';
                $enrollment->save();
            }

            // Redirect to a cancel page or return a response
            return view('payment.cancel', [
                'enrollment' => $enrollment
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Payment Cancel Error', [
                'enrollment_id' => $enrollment_id,
                'message' => $e->getMessage()
            ]);

            // Redirect to an error page
            return view('payment.error', [
                'message' => 'There was an issue cancelling your payment.'
            ]);
        }
    }
}