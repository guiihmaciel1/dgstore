# DG Store - Sistema de Gerenciamento para Loja de iPhones

Sistema completo de gerenciamento para lojas de iPhones desenvolvido com Laravel 11, arquitetura DDD Lite, e interface moderna com Tailwind CSS.

## Funcionalidades

### Módulo de Produtos
- CRUD completo de produtos (iPhones, acessórios, serviços)
- Cadastro de IMEI para iPhones
- Controle de estoque com alerta de estoque mínimo
- Gestão de preço de custo e venda
- Filtros por categoria, modelo, cor, condição

### Módulo de Vendas
- PDV para criação de vendas
- Seleção de cliente e produtos com busca dinâmica
- Aplicação de descontos
- Múltiplas formas de pagamento
- Parcelamento
- Baixa automática no estoque
- Cancelamento com devolução ao estoque
- Impressão de comprovante em PDF

### Módulo de Clientes
- CRUD de clientes
- Histórico de compras
- Busca por nome, telefone, CPF

### Módulo de Estoque
- Visualização de produtos com estoque baixo
- Registro de entrada/ajuste manual
- Histórico de movimentações por produto

### Dashboard
- Vendas do dia/mês
- Produtos mais vendidos
- Alertas de estoque baixo
- Gráfico de vendas (últimos 7 dias)

### Relatórios (Admin)
- Relatório de vendas por período
- Relatório de estoque
- Ranking de produtos mais vendidos
- Exportação em PDF

### Autenticação e Autorização
- Login com Laravel Breeze
- Roles: Admin (acesso total) e Seller (sem acesso a custos e relatórios)

## Requisitos

- PHP 8.3+
- MySQL 8.0+
- Composer
- Node.js 18+ e NPM

## Instalação

1. Clone o repositório:
```bash
git clone <url-do-repositorio>
cd dgstore
```

2. Instale as dependências PHP:
```bash
composer install
```

3. Instale as dependências Node:
```bash
npm install
```

4. Configure o arquivo de ambiente:
```bash
cp .env.example .env
```

5. Edite o arquivo `.env` com suas configurações de banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dgstore
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

6. Gere a chave da aplicação:
```bash
php artisan key:generate
```

7. Execute as migrations:
```bash
php artisan migrate
```

8. (Opcional) Execute os seeders para dados de exemplo:
```bash
php artisan db:seed
```

9. Compile os assets:
```bash
npm run build
```

10. Inicie o servidor:
```bash
php artisan serve
```

Acesse: http://localhost:8000

## Usuários de Teste (após rodar seeders)

| Tipo | Email | Senha |
|------|-------|-------|
| Admin | admin@dgstore.com.br | password |
| Vendedor | vendedor@dgstore.com.br | password |

## Estrutura do Projeto (DDD Lite)

```
app/
├── Domain/                     # Camada de Domínio
│   ├── Product/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── DTOs/
│   │   └── Enums/
│   ├── Sale/
│   ├── Customer/
│   ├── Stock/
│   └── User/
├── Application/                # Camada de Aplicação
│   └── UseCases/
├── Infrastructure/             # Camada de Infraestrutura
│   ├── Repositories/
│   └── Observers/
└── Presentation/               # Camada de Apresentação
    └── Http/
        ├── Controllers/
        └── Requests/
```

## Tecnologias Utilizadas

- **Backend**: Laravel 11, PHP 8.3
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Banco de Dados**: MySQL
- **PDF**: barryvdh/laravel-dompdf
- **Autenticação**: Laravel Breeze
- **Charts**: Chart.js

## Comandos Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rodar testes
php artisan test

# Desenvolvimento com hot reload
npm run dev

# Em outro terminal
php artisan serve
```

## Licença

Este projeto está licenciado sob a licença MIT.

## Autor

Sistema desenvolvido para a DG Store.
