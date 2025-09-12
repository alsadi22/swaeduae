@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>{{ __('QR Code Scanner') }}</h1>
    <p>{{ __('Use your device camera to scan event or certificate QR codes.') }}</p>
    <video id="preview" width="100%" height="auto"></video>
</div>
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
    const html5QrCode = new Html5Qrcode("preview");
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            html5QrCode.start(
                cameras[0].id,
                { fps: 10, qrbox: 250 },
                qrCodeMessage => { alert("QR Code detected: " + qrCodeMessage); },
                errorMessage => {}
            );
        }
    });
</script>
@endsection
