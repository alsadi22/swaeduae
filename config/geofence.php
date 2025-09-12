<?php

return [
    'radius_meters' => env('GEOFENCE_RADIUS_METERS', 150),
    'absence_minutes' => env('GEOFENCE_ABSENCE_MINUTES', 30),
    'email_throttle_minutes' => env('GEOFENCE_EMAIL_THROTTLE_MINUTES', 120),
];
