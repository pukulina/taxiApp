**Tema**

Ao longo de cada uma das 8 aulas nós vamos desenvolver passo a passo um projeto sobre um serviço de taxi online.

**Escopo**

OK -> Tudo começa com a CRIAÇÃO DA CONTA, que pode ser feita pelo PASSAGEIRO ou pelo MOTORISTA ou ambos. É necessário informar o NOME, EMAIL, CPF, SENHA e a PLACA DO CARRO, caso seja o motorista. Não será permitido que o mesmo email tenha mais de uma conta, não existe restrição em relação ao CPF.

 -> Após a CRIAÇÃO DA CONTA e o PASSAGEIRO pode SOLICITAR UMA CORRIDA informando a origem e o destino, que são coordenadas com latitude e longitude. Ao solicitar o passageiro não é cobrado e nem o valor da corrida é exibido já que ele será calculado apenas depois que a corrida for finalizada.

 -> A CORRIDA ficará com o status de SOLICITADA e deve ser retornado o IDENTIFICADOR da corrida. Caso o PASSAGEIRO já tenha uma corrida não CONCLUÍDA, não deve ser possível SOLICITAR UMA CORRIDA.

Um MOTORISTA pode ACEITAR A CORRIDA que terá seu status modificado para ACEITA e o PASSAGEIRO fica no aguardo do MOTORISTA que aceitou a corrida. Após ACEITAR A CORRIDA o MOTORISTA não poderá aceitar qualquer outra corrida. Não deve ser possível ACEITAR UMA CORRIDA que não estiver com o status SOLICITADA.

Após o PASSAGEIRO entrar no carro o MOTORISTA deve INICIAR A CORRIDA, que terá seu status modificado para EM ANDAMENTO.

Durante a CORRIDA, a cada minuto, é feita uma ATUALIZAÇÃO DA POSIÇÃO, registrando cada latitude e longitude do trajeto da CORRIDA.

Chegando ao destino o MOTORISTA deve ENCERRAR A CORRIDA, que agora terá o status de CONCLUÍDA. Com a lista de POSIÇÕES o valor da corrida deve ser calculado por meio da soma da distância entre cada uma delas, seguindo a seguinte tabela de preço:

Dia normal entre 8 e 22 horas = R$2,10 por km
Dia normal entre 22 e 8 horas = R$3,90 por km
Domingo entre 8 e 22 horas = R$2,90 por km
Domingo entre 22 e 8 horas = R$5,00 por km

Dessa forma a tarifa da CORRIDA é mais justa tanto para o PASSAGEIRO quanto para o MOTORISTA em caso de trânsito intenso, desvios ou corridas mais longas que tenham mudança da tarifa de preço.
O ciclo de vida da CORRIDA é SOLICITADA -> ACEITA -> EM ANDAMENTO -> CONCLUÍDA, podendo o PASSAGEIRO ou o MOTORISTA CANCELAR A CORRIDA, desde que ela não esteja CONCLUÍDA ou CANCELADA.
Após o cálculo da tarifa o PAGAMENTO DEVE SER PROCESSADO.
Algumas ações serão necessárias como OBTER A CONTA, OBTER A CORRIDA, OBTER AS CORRIDAS. Outras também podem ser realizadas, mas são opcionais, como ESTIMAR O VALOR DA CORRIDA, AUTORIZAR O PAGAMENTO, CAPTURAR O PAGAMENTO, VERIFICAR CONTA, ATUALIZAR DADOS DA CONTA, AVALIAR A CORRIDA e ENVIAR COMPROVANTE DA CORRIDA.

**Use Cases**

**Obrigatórios**

1 - Signup (CRIAÇÃO DA CONTA)
2 - GetAccount (OBTER A CONTA)
3 - RequestRide (SOLICITAR UMA CORRIDA)
4 - AcceptRide (ACEITAR A CORRIDA)
5 - StartRide (INICIAR A CORRIDA)
6 - FinishRide (FINALIZAR A CORRIDA)
7 - GetRide (OBTER A CORRIDA)
8 - GetRides (OBTER AS CORRIDAS)
9 - UpdatePosition (ATUALIZAR POSIÇÃO)
10 - ProcessPayment (PROCESSAR PAGAMENTO)
11 - Login (AUTENTICAR)
12 - VerifyToken (VERIFICAR TOKEN)


**Opcionais**

13 - EvaluateRide (AVALIAR CORRIDA)
14 - VerifyAccount (VERIFICAR CONTA)
15 - UpdateAccount (ATUALIZAR DADOS DA CONTA)
16 - SendReceipt (ENVIAR COMPROVANTE)
17 - EstimateFare (ESTIMAR O VALOR DA CORRIDA)
18 - AuthorizePayment (AUTORIZAR O PAGAMENTO)
19 - CapturePayment (CAPTURAR O PAGAMENTO)
20 - ForgetAccount (ESQUECER CONTA)


**Modelo de Dados**

drop schema cccat14 cascade;

create schema cccat14;

create table cccat14.account (
account_id uuid,
name text,
email text,
cpf text,
car_plate text,
is_passenger boolean,
is_driver boolean,
password text,
password_algorithm text,
salt text
);

create table cccat14.ride (
ride_id uuid,
passenger_id uuid,
driver_id uuid,
status text,
fare numeric,
distance numeric,
from_lat numeric,
from_long numeric,
to_lat numeric,
to_long numeric,
date timestamp
);

create table cccat14.position (
position_id uuid primary key,
ride_id uuid,
lat numeric,
long numeric,
date timestamp
);

**Consumir signUp**

Exemplo com Postman:
URL: http://localhost:8080/signup

Método: POST

Cabeçalhos:

Content-Type: application/json

Corpo da requisição (dados do usuário):

```json
{
"name": "Lucas Almeida",
"email": "lucas.silva89@email.com",
"cpf": "473.285.610-39",
"is_passenger": true,
"password": "123456"
}
```

```json
curl -X POST http://localhost:8080/signup \
-H "Content-Type: application/json" \
-d '{"name": "Lucas Almeida", "email": "lucas.silva89@email.com", "cpf": "473.285.610-39", "is_passenger": true, "password": "123456"}'
```