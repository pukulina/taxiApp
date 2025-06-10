<?php

namespace AnnaBozzi\TaxiApp\Repositories;

use PDO;
use Ramsey\Uuid\Uuid;

class RideRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function createRide(string $passengerId, float $fromLat, float $fromLng, float $toLat, float $toLng): array {
        $rideId = Uuid::uuid4()->toString();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO ride (ride_id, passenger_id, status, from_lat, from_lng, to_lat, to_lng)
            VALUES (:ride_id, :passenger_id, 'SOLICITADA', :from_lat, :from_lng, :to_lat, :to_lng)
        ");
        
        $stmt->execute([
            'ride_id' => $rideId,
            'passenger_id' => $passengerId,
            'from_lat' => $fromLat,
            'from_lng' => $fromLng,
            'to_lat' => $toLat,
            'to_lng' => $toLng
        ]);
        
        return $this->getRideById($rideId);
    }
    
    public function getRideById(string $rideId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM ride WHERE ride_id = :ride_id");
        $stmt->execute(['ride_id' => $rideId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function getActiveRideByPassenger(string $passengerId): ?array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ride 
            WHERE passenger_id = :passenger_id 
            AND status IN ('SOLICITADA', 'ACEITA', 'EM_ANDAMENTO')
        ");
        $stmt->execute(['passenger_id' => $passengerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function acceptRide(string $rideId, string $driverId): bool {
        $stmt = $this->pdo->prepare("
            UPDATE ride 
            SET driver_id = :driver_id, status = 'ACEITA', updated_at = CURRENT_TIMESTAMP
            WHERE ride_id = :ride_id AND status = 'SOLICITADA'
        ");
        
        $stmt->execute(['ride_id' => $rideId, 'driver_id' => $driverId]);
        return $stmt->rowCount() > 0;
    }
    
    public function startRide(string $rideId): bool {
        $stmt = $this->pdo->prepare("
            UPDATE ride 
            SET status = 'EM_ANDAMENTO', updated_at = CURRENT_TIMESTAMP
            WHERE ride_id = :ride_id AND status = 'ACEITA'
        ");
        
        $stmt->execute(['ride_id' => $rideId]);
        return $stmt->rowCount() > 0;
    }
    
    public function finishRide(string $rideId, float $distance, float $fare): bool {
        $stmt = $this->pdo->prepare("
            UPDATE ride 
            SET status = 'CONCLUIDA', distance = :distance, fare = :fare, updated_at = CURRENT_TIMESTAMP
            WHERE ride_id = :ride_id AND status = 'EM_ANDAMENTO'
        ");
        
        $stmt->execute(['ride_id' => $rideId, 'distance' => $distance, 'fare' => $fare]);
        return $stmt->rowCount() > 0;
    }
    
    public function addPosition(string $rideId, float $lat, float $lng): void {
        $positionId = Uuid::uuid4()->toString();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO position (position_id, ride_id, lat, lng)
            VALUES (:position_id, :ride_id, :lat, :lng)
        ");
        
        $stmt->execute([
            'position_id' => $positionId,
            'ride_id' => $rideId,
            'lat' => $lat,
            'lng' => $lng
        ]);
    }
    
    public function getRidePositions(string $rideId): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM position 
            WHERE ride_id = :ride_id 
            ORDER BY recorded_at ASC
        ");
        $stmt->execute(['ride_id' => $rideId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRidesByUser(string $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ride 
            WHERE passenger_id = :user_id OR driver_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
