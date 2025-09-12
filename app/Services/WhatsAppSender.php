<?php

namespace App\Services;

class WhatsAppSender
{
    // Example send method - replace with real WhatsApp API call
    public static function sendCertificate($phone, $certificatePdfPath)
    {
        // Simulate sending by logging
        \Log::info("WhatsApp certificate sent to $phone: $certificatePdfPath");
        return true;
    }
}
