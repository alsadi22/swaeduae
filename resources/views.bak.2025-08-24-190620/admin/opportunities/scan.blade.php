@extends('admin.layout')

@section('title', __('Scanner'))

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-3" style="font-weight:700;">{{ __('Scanner') }} — {{ $op->title ?? ('#'.$op->id) }}</h1>

    <div class="alert alert-info">
        {{ __('Tip: If your browser supports BarcodeDetector, you can scan QR codes. Otherwise, use the email check-in/out on the Attendance page.') }}
    </div>

    <div class="card p-3 mb-3">
        <video id="cam" playsinline style="width:100%;max-width:560px;border-radius:8px;"></video>
        <div class="mt-2">
            <button id="startBtn" class="btn btn-primary" style="background:#9CAFAA;border-color:#9CAFAA;">{{ __('Start Camera') }}</button>
            <span id="scanMsg" class="ms-2 text-muted"></span>
        </div>
    </div>

    <form id="scanForm" method="POST" action="{{ url('/admin/opportunities/'.$op->id.'/attendance/check-in') }}" class="d-flex gap-2">
        @csrf
        <input class="form-control" type="email" id="emailField" name="email" placeholder="user@example.com" required>
        <button class="btn btn-success">{{ __('Check-in') }}</button>
        <a href="{{ url('/admin/opportunities/'.$op->id.'/attendance') }}" class="btn btn-outline-secondary">{{ __('Back to Attendance') }}</a>
    </form>
</div>

<script>
(async function(){
    const startBtn = document.getElementById('startBtn');
    const video = document.getElementById('cam');
    const msg = document.getElementById('scanMsg');
    const emailField = document.getElementById('emailField');

    if (!('BarcodeDetector' in window)) {
        msg.textContent = 'BarcodeDetector not supported in this browser.';
        return;
    }

    let stream = null;
    startBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            await video.play();
            msg.textContent = 'Camera started. Scan a QR code containing an email address.';
            const detector = new BarcodeDetector({ formats: ['qr_code'] });

            const loop = async () => {
                if (!video.srcObject) return;
                try {
                    const barcodes = await detector.detect(video);
                    if (barcodes.length) {
                        const raw = barcodes[0].rawValue || '';
                        let email = raw;
                        const m = raw.match(/email=([^&\s]+)/i);
                        if (m) email = decodeURIComponent(m[1]);
                        if (email && email.includes('@')) {
                            emailField.value = email;
                            msg.textContent = 'Scanned: ' + email;
                        } else {
                            msg.textContent = 'QR scanned but no email found.';
                        }
                    }
                } catch(e){}
                requestAnimationFrame(loop);
            };
            loop();
        } catch (e) {
            msg.textContent = 'Camera error: ' + (e.message || e);
        }
    });
})();
</script>
@endsection
