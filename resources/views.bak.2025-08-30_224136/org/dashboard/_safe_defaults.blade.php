@extends("org.layout")
@php
  $volunteersHosted   = (int)($volunteersHosted   ?? 0);
  $totalHours         = (float)($totalHours       ?? 0);
  $upcomingOpps       = (int)($upcomingOpps       ?? 0);
  $certificatesIssued = (int)($certificatesIssued ?? 0);

  $appsPending   = (int)($appsPending   ?? 0);
  $appsApproved  = (int)($appsApproved  ?? 0);
  $appsTotal     = (int)($appsTotal     ?? 0);
  $checkinsToday = (int)($checkinsToday ?? 0);

  $monthLabels = is_array($monthLabels ?? null) ? $monthLabels : [];
  $hoursSeries = is_array($hoursSeries ?? null) ? $hoursSeries : [];
  $appAttend   = is_array($appAttend   ?? null)
                ? array_merge(['labels'=>[], 'apps'=>[], 'attend'=>[]], $appAttend)
                : ['labels'=>[], 'apps'=>[], 'attend'=>[]];

  $recentActivity = collect($recentActivity ?? []);
  $upcoming       = collect($upcoming ?? []);
  $rows           = collect($rows ?? []);
@endphp
