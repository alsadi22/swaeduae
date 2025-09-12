<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use App\Services\Payments\StripeGateway;
use Illuminate\Http\Request;
class PaymentController extends Controller {
    public function donateForm() { return view()->first(['public.donate','public_plain.donate']); }
    public function donate(Request $r, StripeGateway $stripe) {
        $data = $r->validate([
            'amount' => 'required|numeric|min:5',
            'email'  => 'nullable|email',
        ]);
        // Ensure keys configured; otherwise, graceful error
        if (! $this->stripeEnabled()) {
            return back()->withErrors(['amount' => __('Payments are temporarily unavailable.')])->withInput();
        }
        try {
            $p = Payment::create([
                'user_id' => auth()->id(),
                'provider'=> 'stripe',
                'currency'=> 'AED',
                'amount'  => (int) round($data['amount'] * 100),
                'status'  => 'pending',
                'metadata'=> ['purpose'=>'donation','email'=>$data['email'] ?? null],
            ]);
            $url = $stripe->createCheckout($p, ['description'=>'Donation']);
            return redirect()->away($url);
        } catch (\Throwable $e) {
            \Log::warning('donate_failed', ['err' => $e->getMessage()]);
            return back()->withErrors(['amount' => __('Payments are temporarily unavailable.')])->withInput();
        }
    }
    private function stripeEnabled(): bool {
        $cfg = (array) (\App\Models\Setting::get('payments.stripe', []) ?? []);
        $k = $cfg['key'] ?? env('STRIPE_KEY');
        $s = $cfg['secret'] ?? env('STRIPE_SECRET');
        return filled($k) && filled($s);
    }
    public function success(Request $r) {
        $pid = $r->integer('pid'); $p = Payment::find($pid);
        return view()->exists('public.payment.success')
            ? view('public.payment.success', compact('p'))
            : view('public_plain.payment_success', compact('p'));
    }
    public function cancel(Request $r)  {
        $pid = $r->integer('pid'); $p = Payment::find($pid);
        return view()->exists('public.payment.cancel')
            ? view('public.payment.cancel', compact('p'))
            : view('public_plain.payment_cancel', compact('p'));
    }
    public function webhook(Request $r, StripeGateway $stripe) { $stripe->handleWebhook($r->all(), $r->header('Stripe-Signature')); return response()->json(['ok'=>true]); }
}
