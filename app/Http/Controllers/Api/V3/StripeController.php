<?php
namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;

class StripeController extends Controller
{
    protected $paymentService;

    public function __construct(StripePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


       /**
    * @OA\Post(
    *     path="/api/checkout",
    *     summary="Create Stripe checkout session",
    *     tags={"Payments"},
    *     @OA\Parameter(
    *         name="course_id",
    *         in="query",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(response=200, description="Checkout session created"),
    *     @OA\Response(response=400, description="Checkout failed")
    * )
    */

    public function checkout(Request $request)
    {
        try {
            // Validate course_id
            $request->validate([
                'course_id' => 'required|exists:courses,id'
            ]);

            // Find the course
            $course = Course::findOrFail($request->course_id);

            // Create Stripe Checkout Session
            $paymentDetails = $this->paymentService->createCheckoutSession(
                $course, 
                $request->user()
            );
            
            return response()->json([
                'session_id' => $paymentDetails['session_id'],
                'enrollment_id' => $paymentDetails['enrollment_id'],
                'checkout_url' => $paymentDetails['checkout_url'],
                'amount' => $course->price ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to create payment session',
                'details' => $e->getMessage()
            ], 400);
        }
    }

     /**
    * @OA\Post(
    *     path="/api/confirm-payment",
    *     summary="Confirm Stripe payment",
    *     tags={"Payments"},
    *     @OA\Parameter(
    *         name="session_id",
    *         in="query",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="enrollment_id",
    *         in="query",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(response=200, description="Payment confirmed"),
    *     @OA\Response(response=400, description="Payment verification failed")
    * )
    */

    public function confirmPayment(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'session_id' => 'required|string',
                'enrollment_id' => 'required|exists:enrollments,id'
            ]);
    
            // Verify the payment with Stripe
            $paymentVerified = $this->paymentService->verifyPayment(
                $request->session_id
            );
    
            if ($paymentVerified) {
                $enrollment = Enrollment::findOrFail($request->enrollment_id);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully',
                    'enrollment_id' => $enrollment->id
                ]);
            } else {
                // Payment verification failed
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment Confirmation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'error' => 'Failed to confirm payment',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/payment-history",
    *     summary="Get user payment history",
    *     tags={"Payments"},
    *     @OA\Response(response=200, description="Payment history retrieved")
    * )
    */

    public function paymentHistory()
    {
        // Implement payment history logic
        $user = auth()->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($enrollments);
    }

    public function paymentStatus($id)
    {
        // Get specific enrollment/payment status
        $enrollment = Enrollment::findOrFail($id);

        return response()->json([
            'status' => $enrollment->status,
            'course' => $enrollment->course
        ]);
    }

       /**
    * @OA\Get(
    *     path="/api/payment-status/{id}",
    *     summary="Get payment status for specific enrollment",
    *     tags={"Payments"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(response=200, description="Payment status retrieved"),
    *     @OA\Response(response=404, description="Enrollment not found")
    * )
    */
}