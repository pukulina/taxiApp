<?php

namespace App\Models;

class Account {
    public string $account_id;
    public string $name;
    public string $email;
    public string $cpf;
    public ?string $car_plate;
    public bool $is_passenger;
    public bool $is_driver;

    public function __construct($data) {
        $this->account_id = $data['account_id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->cpf = $data['cpf'];
        $this->car_plate = $data['car_plate'] ?? null;
        $this->is_passenger = $data['is_passenger'];
        $this->is_driver = $data['is_driver'];
    }
}
