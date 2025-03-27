<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller; 
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

     /**
     * @OA\Get(
     *     path="/api/payment/success/{enrollment_id}",
     *     summary="Process successful payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="enrollment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Payment successful"),
     *     @OA\Response(response=400, description="Payment processing error")
     * )
     */

     
    
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

            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed'
            ], 400);
        }
    }


     /**
     * @OA\Get(
     *     path="/api/payment/success/{enrollment_id}",
     *     summary="Process successful payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="enrollment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Payment successful"),
     *     @OA\Response(response=400, description="Payment processing error")
     * )
     */

    public function paymentCancel($enrollment_id)
    {
        try {
           
            $enrollment = Enrollment::findOrFail($enrollment_id);

            if ($enrollment->status == 'pending') {
                $enrollment->status = 'active';
                $enrollment->save();
            }

           
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