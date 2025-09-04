<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
class PaymentAdminController extends Controller {
    public function index(Request $r) {
        $q = Payment::query()->latest();
        if ($s = $r->get('status')) $q->where('status',$s);
        $payments = $q->paginate(20);
        return view()->first(['admin.payments.index','admin.payments_plain.index'], compact('payments'));
    }
}

