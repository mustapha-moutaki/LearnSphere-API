<?php
namespace App\Services;

use Srmklive\PayPal\Services\PayPal;
use Illuminate\Support\Facades\Log;
use App\Models\Enrollment;

class PayPalPaymentService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPal();
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }

    public function createPaymentOrder($course)
    {
        try {
            // Create PayPal order
            $order = $this->provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $course->price
                        ],
                        'description' => 'Course: ' . $course->name
                    ]
                ]
            ]);

            // Create enrollment
            $enrollment = Enrollment::create([
                'course_id' => $course->id,
                'status' => 'pending',
                'paypal_order_id' => $order['id']
            ]);

            return [
                'order_id' => $order['id'],
                'enrollment_id' => $enrollment->id
            ];
        } catch (\Exception $e) {
            Log::error('PayPal Order Creation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function verifyPayment($orderId, $enrollmentId)
    {
        try {
            // Capture the order to verify and complete the payment
            $order = $this->provider->capturePaymentOrder($orderId);

            // Check if the order was successfully captured
            if (isset($order['status']) && $order['status'] === 'COMPLETED') {
                // Additional validation can be added here
                return true;
            }

            Log::warning('PayPal Payment Verification Failed', [
                'order_id' => $orderId,
                'enrollment_id' => $enrollmentId,
                'order_status' => $order['status'] ?? 'Unknown'
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('PayPal Payment Verification Error', [
                'order_id' => $orderId,
                'enrollment_id' => $enrollmentId,
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}