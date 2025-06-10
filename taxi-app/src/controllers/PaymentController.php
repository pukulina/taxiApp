<?php

namespace AnnaBozzi\TaxiApp\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentController {
    
    public function processPayment(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['ride_id'], $data['amount'])) {
            $response->getBody()->write(json_encode(['error' => 'Ride ID and amount required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $paymentResult = [
            'payment_id' => uniqid('pay_'),
            'ride_id' => $data['ride_id'],
            'amount' => $data['amount'],
            'status' => 'completed',
            'processed_at' => date('Y-m-d H:i:s'),
            'method' => 'automatic_debit'
        ];
        
        $response->getBody()->write(json_encode($paymentResult));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
