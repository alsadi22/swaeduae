<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class QrVerifyController extends Controller
{
    public function index(Request $request)
    {
        $code = trim((string) $request->query('code', ''));
        $status = null;
        $certificate = null;

        if ($code !== '') {
            $certificate = Certificate::where('code', $code)->first();
            if ($certificate) {
                $status = $certificate->revoked_at ? 'expired' : 'valid';
            } else {
                $status = 'invalid';
            }
        }

        return view('admin.qr.verify', [
            'code' => $code,
            'status' => $status,
            'certificate' => $certificate,
        ]);
    }
}
