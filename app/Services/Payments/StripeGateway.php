<?php
namespace App\Services\Payments;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
class StripeGateway implements PaymentGateway {
    protected string $secret;
    protected ?string $webhookSecret;
    protected string $publicKey;
    protected string $mode;
    public function __construct() {
        $cfg = (array) (\App\Models\Setting::get('payments.stripe', []) ?? []);
        $this->secret = $cfg['secret'] ?? env('STRIPE_SECRET','');
        $this->publicKey = $cfg['key'] ?? env('STRIPE_KEY','');
        $this->webhookSecret = $cfg['webhook_secret'] ?? env('STRIPE_WEBHOOK_SECRET', null);
        $this->mode = $cfg['mode'] ?? env('PAYMENTS_MODE','test');
        if ($this->secret) \Stripe\Stripe::setApiKey($this->secret);
    }
    public function createCheckout(Payment $payment, array $opts=[]): string {
        if (!$this->secret || !$this->publicKey) {
            throw new \RuntimeException('Stripe keys not configured');
        }
        $success = url('/payment/success?pid='.$payment->id);
        $cancel  = url('/payment/cancel?pid='.$payment->id);
        $currency = strtolower($payment->currency ?: 'AED');
        $desc = $opts['description'] ?? 'Payment #'.$payment->id;
        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'success_url' => $success . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancel,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => (int) $payment->amount,
                    'product_data' => ['name' => $desc],
                ],
                'quantity' => 1,
            ]],
            'metadata' => ['payment_id' => (string)$payment->id],
        ]);
        $payment->provider_id = $session->id;
        $payment->save();
        return $session->url;
    }
    public function handleWebhook(array $payload, string $sigHeader=null): ?Payment {
        $event = $payload;
        if ($this->webhookSecret && isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            try {
                $event = \Stripe\Webhook::constructEvent(
                    file_get_contents('php://input') ?: '',
                    $_SERVER['HTTP_STRIPE_SIGNATURE'],
                    $this->webhookSecret
                );
                $event = $event->jsonSerialize();
            } catch (\Throwable $e) {
                Log::warning('Stripe webhook signature failed: '.$e->getMessage());
                return null;
            }
        }
        $type = $event['type'] ?? '';
        $data = $event['data']['object'] ?? [];
        $sessionId = $data['id'] ?? ($data['object'] === 'charge' ? ($data['metadata']['checkout_session_id'] ?? null) : null);
        $pid = $data['metadata']['payment_id'] ?? null;
        $payment = null;
        if ($pid) $payment = Payment::find($pid);
        if (!$payment && $sessionId) $payment = Payment::where('provider_id',$sessionId)->first();
        if (!$payment) return null;
        if (in_array($type, ['checkout.session.completed','payment_intent.succeeded'])) {
            $payment->status = 'paid';
        } elseif (in_array($type, ['checkout.session.expired','payment_intent.payment_failed'])) {
            $payment->status = 'failed';
        }
        $meta = $payment->metadata ?? [];
        $meta['stripe_event'] = $type;
        $payment->metadata = $meta;
        $payment->save();
        return $payment;
    }
}

