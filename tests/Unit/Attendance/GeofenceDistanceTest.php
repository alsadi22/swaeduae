<?php

namespace Tests\Unit\Attendance;

use App\Services\Attendance\AbsenceDetector;
use PHPUnit\Framework\TestCase;

class GeofenceDistanceTest extends TestCase
{
    public function test_distance_within_radius(): void
    {
        $detector = new AbsenceDetector();
        $dist = $detector->distanceMeters(0,0,0,0.001); // ~111m
        $this->assertLessThan(150, $dist);
    }

    public function test_distance_outside_radius(): void
    {
        $detector = new AbsenceDetector();
        $dist = $detector->distanceMeters(0,0,0,0.003); // ~333m
        $this->assertGreaterThan(150, $dist);
    }
}
