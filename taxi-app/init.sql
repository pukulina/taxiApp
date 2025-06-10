-- Criar a tabela
CREATE TABLE account (
                                 account_id UUID PRIMARY KEY,
                                 name TEXT NOT NULL,
                                 email TEXT NOT NULL UNIQUE,
                                 cpf TEXT NOT NULL UNIQUE,
                                 car_plate TEXT NULL,
                                 is_passenger BOOLEAN NOT NULL DEFAULT FALSE,
                                 is_driver BOOLEAN NOT NULL DEFAULT FALSE,
                                 password TEXT NOT NULL
);

CREATE TABLE ride (
    ride_id UUID PRIMARY KEY,
    passenger_id UUID NOT NULL REFERENCES account(account_id),
    driver_id UUID NULL REFERENCES account(account_id),
    status VARCHAR(20) NOT NULL DEFAULT 'SOLICITADA',
    from_lat DECIMAL(10,8) NOT NULL,
    from_lng DECIMAL(11,8) NOT NULL,
    to_lat DECIMAL(10,8) NOT NULL,
    to_lng DECIMAL(11,8) NOT NULL,
    distance DECIMAL(10,2) NULL,
    fare DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE position (
    position_id UUID PRIMARY KEY,
    ride_id UUID NOT NULL REFERENCES ride(ride_id),
    lat DECIMAL(10,8) NOT NULL,
    lng DECIMAL(11,8) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Popular o banco com alguns registros iniciais
INSERT INTO account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, password) VALUES
                                                                                                   ('550e8400-e29b-41d4-a716-446655440000', 'João Silva', 'joao@email.com', '12345678900', 'ABC1A23', TRUE, FALSE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
                                                                                                   ('660e8400-e29b-41d4-a716-446655440001', 'Maria Souza', 'maria@email.com', '98765432100', NULL, TRUE, FALSE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
                                                                                                   ('770e8400-e29b-41d4-a716-446655440002', 'Carlos Mendes', 'carlos@email.com', '19283746500', 'XYZ9B87', FALSE, TRUE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
                                                                                                   ('880e8400-e29b-41d4-a716-446655440003', 'Ana Lima', 'ana@email.com', '56473829100', NULL, TRUE, FALSE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
                                                                                                   ('990e8400-e29b-41d4-a716-446655440004', 'Pedro Oliveira', 'pedro@email.com', '34567891200', 'DEF4C56', FALSE, TRUE, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
