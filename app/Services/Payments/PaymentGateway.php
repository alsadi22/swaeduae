<?php
namespace App\Services\Payments;
use App\Models\Payment;
interface PaymentGateway {
    public function createCheckout(Payment $payment, array $opts=[]): string;
    public function handleWebhook(array $payload, string $sigHeader=null): ?Payment;
}

