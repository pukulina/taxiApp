-- Remover e recriar o schema
DROP SCHEMA IF EXISTS cccat14 CASCADE;
CREATE SCHEMA cccat14;

-- Criar a tabela
CREATE TABLE cccat14.account (
                                 account_id UUID PRIMARY KEY,
                                 name TEXT NOT NULL,
                                 email TEXT NOT NULL UNIQUE,
                                 cpf TEXT NOT NULL UNIQUE,
                                 car_plate TEXT NULL,
                                 is_passenger BOOLEAN NOT NULL DEFAULT FALSE,
                                 is_driver BOOLEAN NOT NULL DEFAULT FALSE
);

-- Popular o banco com alguns registros iniciais
INSERT INTO cccat14.account (account_id, name, email, cpf, car_plate, is_passenger, is_driver) VALUES
                                                                                                   ('550e8400-e29b-41d4-a716-446655440000', 'João Silva', 'joao@email.com', '12345678900', 'ABC1A23', TRUE, FALSE),
                                                                                                   ('660e8400-e29b-41d4-a716-446655440001', 'Maria Souza', 'maria@email.com', '98765432100', NULL, TRUE, FALSE),
                                                                                                   ('770e8400-e29b-41d4-a716-446655440002', 'Carlos Mendes', 'carlos@email.com', '19283746500', 'XYZ9B87', FALSE, TRUE),
                                                                                                   ('880e8400-e29b-41d4-a716-446655440003', 'Ana Lima', 'ana@email.com', '56473829100', NULL, TRUE, FALSE),
                                                                                                   ('990e8400-e29b-41d4-a716-446655440004', 'Pedro Oliveira', 'pedro@email.com', '34567891200', 'DEF4C56', FALSE, TRUE);
