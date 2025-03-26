<?php
namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PayPalPaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PayPalPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function checkout(Request $request)
    {
        try {
            // Validate course_id
            $request->validate([
                'course_id' => 'required|exists:courses,id'
            ]);

            // Find the course
            $course = Course::findOrFail($request->course_id);

            // Create payment order
            $paymentDetails = $this->paymentService->createPaymentOrder($course);
            
            return response()->json([
                'order_id' => $paymentDetails['order_id'],
                'enrollment_id' => $paymentDetails['enrollment_id'],
                'amount' => $course->price ?? 0
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Checkout Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to create payment order',
                'details' => $e->getMessage()
            ], 400);
        }
    }

    public function confirmPayment(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'order_id' => 'required|string',
                'enrollment_id' => 'required|exists:enrollments,id'
            ]);
    
            // Verify the payment with PayPal
            $paymentVerified = $this->paymentService->verifyPayment(
                $request->order_id, 
                $request->enrollment_id
            );
    
            if ($paymentVerified) {
                // Update enrollment status
                $enrollment = Enrollment::findOrFail($request->enrollment_id);
                $enrollment->status = 'paid';
                $enrollment->save();
    
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
            // Log the error
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

    private function verifyPayPalPayment($orderId)
    {
        try {
            // Initialize PayPal provider
            $provider = new \Srmklive\PayPal\Services\PayPal;
            
            // Configure the provider (make sure this matches your .env configuration)
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Capture the order to verify and complete the payment
            $order = $provider->capturePaymentOrder($orderId);

            // Check if the order was successfully captured
            return isset($order['status']) && $order['status'] === 'COMPLETED';
        } catch (\Exception $e) {
            Log::error('PayPal Payment Verification Error', [
                'order_id' => $orderId,
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}