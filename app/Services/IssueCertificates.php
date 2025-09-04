<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Attendance;
use App\Models\Certificate;

class IssueCertificates
{
    public function issueForAttendance(Attendance $attendance): ?Certificate
    {
        try {
            // sanity checks
            $minutes = (int)($attendance->minutes ?? 0);
            if ($minutes <= 0) return null;
            if (!$attendance->user_id) return null;

            $attEventId = $attendance->event_id ?? null;
            $attOppId   = $attendance->opportunity_id ?? null;

            if (property_exists($attendance, 'no_show') && $attendance->no_show) return null;

            // Avoid duplicates: existing cert for same user + same parent (event or opportunity)
            $q = Certificate::query()->where('user_id', $attendance->user_id);
            $q->where(function($qq) use ($attEventId, $attOppId) {
                if ($attEventId && Schema::hasColumn('certificates','event_id')) {
                    $qq->orWhere('event_id', $attEventId);
                }
                if ($attOppId && Schema::hasColumn('certificates','opportunity_id')) {
                    $qq->orWhere('opportunity_id', $attOppId);
                }
            });
            $existing = $q->latest('id')->first();
            if ($existing) return $existing;

            // Prefer existing CertificateService if present
            if (class_exists(\App\Services\CertificateService::class)) {
                $svc = app(\App\Services\CertificateService::class);
                if (method_exists($svc, 'issueForAttendance')) {
                    return $svc->issueForAttendance($attendance);
                }
                if (method_exists($svc, 'issueForUserEvent') && $attEventId) {
                    return $svc->issueForUserEvent($attendance->user_id, $attEventId, $minutes);
                }
                if (method_exists($svc, 'issueForUserOpportunity') && $attOppId) {
                    return $svc->issueForUserOpportunity($attendance->user_id, $attOppId, $minutes);
                }
            }

            // Minimal fallback: create a stub cert record (file can be generated later)
            $cert = new Certificate();
            if ($attEventId && Schema::hasColumn('certificates','event_id')) $cert->event_id = $attEventId;
            if ($attOppId && Schema::hasColumn('certificates','opportunity_id')) $cert->opportunity_id = $attOppId;
            $cert->user_id = $attendance->user_id;
            if (Schema::hasColumn('certificates','hours')) $cert->hours = round($minutes/60, 2);
            if (Schema::hasColumn('certificates','issued_at')) $cert->issued_at = now();
            if (Schema::hasColumn('certificates','code')) {
                $cert->code = 'SU'.now()->format('ymd').'-'.substr(strtoupper(bin2hex(random_bytes(4))),0,6);
            }
            if (Schema::hasColumn('certificates','issued_date') && empty($cert->issued_date)) {
                $cert->issued_date = now()->toDateString();
            }
            if (Schema::hasColumn('certificates','title') && empty($cert->title)) {
                $cert->title = 'Volunteer Certificate';
            }
            if (Schema::hasColumn('certificates','verification_code') && empty($cert->verification_code)) {
                $cert->verification_code = $cert->code ?? ('SU'.now()->format('ymd').'-'.substr(strtoupper(bin2hex(random_bytes(4))),0,6));
            }
            $cert->save();
            return $cert;
        } catch (\Throwable $e) {
            Log::warning('IssueCertificates fallback failed: '.$e->getMessage());
            return null;
        }
    }

    public function resendLink(?Certificate $cert): bool
    {
        if (!$cert) return false;
        try {
            if (class_exists(\App\Services\CertificateService::class)) {
                $svc = app(\App\Services\CertificateService::class);
                if (method_exists($svc, 'resend')) {
                    $svc->resend($cert);
                    return true;
                }
            }
            \Log::info("Resend certificate link to user_id={$cert->user_id}, code=".($cert->code ?? 'N/A'));
            return true;
        } catch (\Throwable $e) {
            \Log::warning('Resend certificate failed: '.$e->getMessage());
            return false;
        }
    }
}
