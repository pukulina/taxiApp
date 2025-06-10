<?php

namespace AnnaBozzi\TaxiApp\Services;

class FareCalculatorService {
    private const BASE_FARE = 3.50;
    private const RATE_PER_KM = 2.00;
    private const NIGHT_MULTIPLIER = 1.5;
    private const WEEKEND_MULTIPLIER = 1.2;
    
    public function calculateDistance(array $positions): float {
        if (count($positions) < 2) return 0;
        
        $totalDistance = 0;
        
        for ($i = 1; $i < count($positions); $i++) {
            $totalDistance += $this->haversineDistance(
                $positions[$i-1]['lat'],
                $positions[$i-1]['lng'],
                $positions[$i]['lat'],
                $positions[$i]['lng']
            );
        }
        
        return round($totalDistance, 2);
    }
    
    public function calculateFare(float $distance): float {
        $baseFare = self::BASE_FARE + ($distance * self::RATE_PER_KM);
        
        $now = new \DateTime();
        $hour = (int) $now->format('H');
        $dayOfWeek = (int) $now->format('w');
        
        if ($hour >= 22 || $hour < 6) {
            $baseFare *= self::NIGHT_MULTIPLIER;
        }
        
        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            $baseFare *= self::WEEKEND_MULTIPLIER;
        }
        
        return round($baseFare, 2);
    }
    
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $earthRadius = 6371;
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}
