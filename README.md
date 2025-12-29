# Wallet Challenge

Este projeto implementa um sistema de carteira digital para transferências entre usuários e lojistas. Desenvolvimento backend em PHP incluindo design de API REST, princípios SOLID, testes e padrões de arquitetura limpa. Para rodar teste local, navegue até o item de [Instruções de Setup](#instruções-de-setup)

## Funcionalidades

### Funcionalidades Principais
- Autenticação de usuários com tokens JWT
- Transferências de dinheiro entre usuários e para lojistas
- Autorização de transações via serviço externo
- Sistema de notificação para recibos de pagamentos
- Rollback de transações em caso de falhas
- Cobertura de testes unitários

### Regras de Negócio
- Usuários podem transferir dinheiro para outros usuários e lojistas
- Lojistas apenas recebem transferências (não enviam dinheiro)
- Validação de saldo antes da execução da transferência
- Todas as operações são transacionais

## Arquitetura

### Design Patterns Aplicados
- **Dependency Injection**: Gerenciamento de dependências via container
- **Dependency Inversion**: Inversão de dependências nas camadas de domínio 
- **Repository Pattern**: Abstração de acesso a dados
- **Domain-Driven Design**: Separação da lógica de domínio da infraestrutura
- **Event-Driven Architecture**: Sistema de notificação desacoplado
- **Value Objects**: Estruturas de dados imutáveis para conceitos de domínio
- **Strategy Pattern**: Tratamento de erros e formatação de respostas

## Modelagem de Dados

### Design de Banco de Dados
- **Chaves Primárias UUID**: Todas as entidades usam UUID para identificação. Assim não há dependência de persistência no banco para gerar identificadores para as entidades do negócio
- **Timestamps de Precisão**: Precisão de microssegundos para timing de transações a fim de melhorar a rastreabilidade
- **Valores Inteiros**: Valores de amount armazenados como centavos para evitar problemas de ponto flutuante
- **Campos Indexados**: Otimização de performance em colunas frequentemente consultadas

### Entidades Principais
- **Users**: Entidade para rastreabilidade e autorização de usuários entre as requisições
- **Wallets**: Contas financeiras vinculadas aos usuários
- **Transactions**: Registros das transferências realizadas

## Decisões Técnicas

### Gerenciamento de Timezone
- Todos os timestamps armazenados em UTC
- Timestamps de precisão (6 casas decimais) para ordenação de transações
- Tratamento consistente de timezone em toda aplicação

### Tratamento de Erros
- Mapeamento dos códigos de status HTTP via configuração a fim de desacoplar detalhes de implementação das exceptions
- Formato de resposta de erro padronizado por meio de traits e middlewares
- Hierarquia de exceções específicas de domínio

### Eventos
- Mapeamento centralizado de eventos via configuração também a fim de desacoplar detalhes de implementação amqp na comunicação

### Estratégia de Testes
- Cobertura de testes unitários com foco maior nas camadas de Domain e Application
- Na camada de Infrastructure, testes focados em adaptadores e handlers comuns

## Instruções de Setup

### Pré-requisitos
- Docker e Docker Compose

### Quick Start
1. Clone o repositório:  
```sh
git clone git@github.com:luizottavioc/wallet-challenge.git
```
2. Gere o .env:  
```sh
cp .env.example .env
```
3. Inicie os serviços com Docker Compose:  
```sh
docker-compose up -d
```
4. Acesse o container da aplicação:  
```sh
docker compose exec wallet-challenge bash
```
5. Migrações do banco de dados:  
```sh
php bin/hyperf.php migrate
```
6. Popule o banco com os usuários (os dados do usuário criado serão devolvidos no terminal para que posteriormente possam ser usados nos testes dos [endpoints](#documentação-da-api)):  
```sh
php bin/hyperf.php db:seed --path=seeders/user_default_seeder.php &&
php bin/hyperf.php db:seed --path=seeders/user_shopkeeper_seeder.php
```
7. Testes:  
```sh
composer test
```
8. Testes com coverage:  
```sh
composer test-coverage
```

## Documentação da API

A api conta com 2 rotas: 

### Autenticação
```sh
curl --location 'localhost:9501/auth/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "email@example.com",
    "password": "password"
}'
```
- Autenticação credenciadas por e-mail e senha
- Retorna um token JWT que deverá ser utilizado nas requisições subsequentes

### Transferências
```sh
curl --location 'localhost:9501/transfer' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer {{token}}' \
--data '{
    "payeeId": "uuid",
    "amount": 100
}'
```
- Requer autenticação com token disponibilizado na primeira requisição (necessário insirí-lo em "{{token}}")
- Valida saldo do usuário
- Verificação de autorização externa
- Execução transacional

### Postman
Caso se sinta mais confortável, acesse a collection que está configurada com os endpoints da aplicação via Postman no seguinte link: [Wallet Challenge Postman Collection](https://www.postman.com/luizottavioc-workspace/workspace/public-workspace/collection/39605447-91b8de82-e43b-4810-934e-67d66c257088?action=share&source=copy-link&creator=39605447)

## Limitações e Melhorias

### Itens não implementados e Limitações Atuais
- Como a autenticação não é o foco do projeto, a criação de usuários é feita somente através de seeds do banco e não há sistema de permissões baseado em roles (somente no que diz respeito ao tipo de usuário)
- Sem implementação de observabilidade/métricas e logs limitados
- Configuração CORS não implementada
- Não existe um padrão implementado para testes de feature
- Não existe sistema de tradução de mensagens de erros de validação

### Possíveis melhorias implementáveis com tempo disponível
- Suite de testes de feature
- Endpoint para registro de usuários
- Value objects para validação de e-mail/CPF/CNPJ
- Observabilidade com métricas e logging
- Middleware de rate limiting e segurança
- Blacklist de tokens para funcionalidade logout
- Event sourcing para histórico de transações