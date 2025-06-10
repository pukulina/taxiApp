<?php

namespace AnnaBozzi\TaxiApp\controllers;

use AnnaBozzi\TaxiApp\Repositories\RideRepository;
use AnnaBozzi\TaxiApp\Services\FareCalculatorService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RideController {
    private RideRepository $rideRepository;
    private FareCalculatorService $fareCalculator;
    
    public function __construct(RideRepository $rideRepository, FareCalculatorService $fareCalculator) {
        $this->rideRepository = $rideRepository;
        $this->fareCalculator = $fareCalculator;
    }
    
    public function requestRide(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);
        $user = $request->getAttribute('user');
        
        if (!isset($data['from_lat'], $data['from_lng'], $data['to_lat'], $data['to_lng'])) {
            $response->getBody()->write(json_encode(['error' => 'Origin and destination coordinates required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $activeRide = $this->rideRepository->getActiveRideByPassenger($user['account_id']);
        if ($activeRide) {
            $response->getBody()->write(json_encode(['error' => 'You already have an active ride']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $ride = $this->rideRepository->createRide(
            $user['account_id'],
            $data['from_lat'],
            $data['from_lng'],
            $data['to_lat'],
            $data['to_lng']
        );
        
        $response->getBody()->write(json_encode($ride));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function acceptRide(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);
        $user = $request->getAttribute('user');
        
        if (!isset($data['ride_id'])) {
            $response->getBody()->write(json_encode(['error' => 'Ride ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $success = $this->rideRepository->acceptRide($data['ride_id'], $user['account_id']);
        
        if (!$success) {
            $response->getBody()->write(json_encode(['error' => 'Unable to accept ride']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $ride = $this->rideRepository->getRideById($data['ride_id']);
        $response->getBody()->write(json_encode($ride));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function startRide(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['ride_id'])) {
            $response->getBody()->write(json_encode(['error' => 'Ride ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $success = $this->rideRepository->startRide($data['ride_id']);
        
        if (!$success) {
            $response->getBody()->write(json_encode(['error' => 'Unable to start ride']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $ride = $this->rideRepository->getRideById($data['ride_id']);
        $response->getBody()->write(json_encode($ride));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function updatePosition(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['ride_id'], $data['lat'], $data['lng'])) {
            $response->getBody()->write(json_encode(['error' => 'Ride ID, latitude and longitude required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $this->rideRepository->addPosition($data['ride_id'], $data['lat'], $data['lng']);
        
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function finishRide(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['ride_id'])) {
            $response->getBody()->write(json_encode(['error' => 'Ride ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $positions = $this->rideRepository->getRidePositions($data['ride_id']);
        $distance = $this->fareCalculator->calculateDistance($positions);
        $fare = $this->fareCalculator->calculateFare($distance);
        
        $success = $this->rideRepository->finishRide($data['ride_id'], $distance, $fare);
        
        if (!$success) {
            $response->getBody()->write(json_encode(['error' => 'Unable to finish ride']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $ride = $this->rideRepository->getRideById($data['ride_id']);
        $response->getBody()->write(json_encode($ride));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function getRide(Request $request, Response $response, array $args): Response {
        $rideId = $args['id'] ?? null;
        
        if (!$rideId) {
            $response->getBody()->write(json_encode(['error' => 'Ride ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $ride = $this->rideRepository->getRideById($rideId);
        
        if (!$ride) {
            $response->getBody()->write(json_encode(['error' => 'Ride not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $response->getBody()->write(json_encode($ride));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getRides(Request $request, Response $response): Response {
        $userId = $request->getQueryParams()['user_id'] ?? null;
        $user = $request->getAttribute('user');
        
        if (!$userId) {
            $userId = $user['account_id'];
        }
        
        $rides = $this->rideRepository->getRidesByUser($userId);
        
        $response->getBody()->write(json_encode($rides));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
