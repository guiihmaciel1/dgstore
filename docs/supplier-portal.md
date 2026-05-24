# Portal do Fornecedor - Documentação Técnica

## Visão Geral

O Portal do Fornecedor é uma aplicação web separada e segura que permite aos fornecedores de estoque consignado (especificamente Andre Soler Bastos) gerenciar seu estoque de forma autônoma, sem acesso ao sistema principal da DG Store.

## Arquitetura de Segurança

### Isolamento Completo

O portal utiliza um **guard de autenticação separado** (`supplier`) que é completamente isolado do sistema principal (`web`):

- **Guard:** `supplier`
- **Model:** `SupplierUser` (tabela `supplier_users`)
- **Middleware:** `supplier.auth` + `supplier.active`
- **Rotas:** `/fornecedor/*` (prefix separado)
- **Views:** Layout próprio em `layouts/supplier.blade.php`

### Fluxo de Autenticação

```
1. Usuário acessa /fornecedor/login
2. Credenciais validadas contra supplier_users
3. Guard 'supplier' inicia sessão isolada
4. Middleware valida active=true do user e supplier
5. Acesso permitido apenas às rotas /fornecedor/*
```

### Proteções Implementadas

1. **Autorização por Supplier ID:** Todos os queries filtram `where('supplier_id', auth('supplier')->user()->supplier_id)`
2. **CSRF Protection:** Todos os formulários incluem `@csrf`
3. **Rate Limiting:** 5 tentativas de login por email+IP
4. **Input Validation:** Laravel validation em todos os controllers
5. **XSS Protection:** Blade escaping automático `{{ }}`
6. **SQL Injection Protection:** Eloquent/Query Builder (sem SQL raw)
7. **403 Forbidden:** Controllers validam supplier_id antes de qualquer operação

## Estrutura de Arquivos

### Fase 1: Autenticação

- **Migration:** `database/migrations/2026_05_24_XXXXXX_create_supplier_users_table.php`
- **Models:**
  - `app/Domain/Supplier/Models/SupplierUser.php`
  - `app/Domain/Supplier/Models/Supplier.php` (relacionamentos adicionados)
- **Config:** `config/auth.php` (guard, provider, password broker)
- **Middleware:**
  - `app/Http/Middleware/SupplierAuthenticate.php`
  - `app/Http/Middleware/SupplierActive.php`
- **Seeder:** `database/seeders/SupplierUserSeeder.php`

### Fase 2: Controllers e Rotas

- **Controllers:**
  - `app/Presentation/Http/Controllers/Supplier/SupplierAuthController.php`
  - `app/Presentation/Http/Controllers/Supplier/SupplierDashboardController.php`
  - `app/Presentation/Http/Controllers/Supplier/SupplierStockController.php`
  - `app/Presentation/Http/Controllers/Supplier/SupplierReportController.php`
  - `app/Presentation/Http/Controllers/Supplier/SupplierApiController.php`
- **Rotas:** `routes/supplier.php`
- **Bootstrap:** `bootstrap/app.php` (registro de middlewares e rotas)

### Fase 3: Views

- **Layout:** `resources/views/layouts/supplier.blade.php`
- **Auth:** `resources/views/supplier/auth/login.blade.php`
- **Dashboard:** `resources/views/supplier/dashboard.blade.php`
- **Estoque:**
  - `resources/views/supplier/stock/index.blade.php`
  - `resources/views/supplier/stock/batch-create.blade.php`
  - `resources/views/supplier/stock/show.blade.php`
  - `resources/views/supplier/stock/edit.blade.php`
- **Relatórios:** `resources/views/supplier/reports/index.blade.php`

### Fase 4: Testes e Documentação

- **Testes:** `tests/Feature/SupplierPortalSecurityTest.php`
- **Docs:** `docs/supplier-portal.md` (este arquivo)

## Funcionalidades

### 1. Dashboard

- **URL:** `/fornecedor/dashboard`
- **Funcionalidades:**
  - Cards com estatísticas: disponível (qtd/valor), vendido mês (qtd/valor)
  - Tabela com últimas 10 entradas
  - Navegação rápida para Nova Entrada e Ver Estoque

### 2. Gestão de Estoque

#### 2.1 Listagem

- **URL:** `/fornecedor/estoque`
- **Filtros:** Disponível, Vendido, Devolvido
- **Busca:** Por IMEI, serial, nome do produto
- **Paginação:** 20 itens por página

#### 2.2 Entrada em Lote

- **URL:** `/fornecedor/estoque/entrada`
- **Funcionalidades:**
  - Autocomplete de produtos (API: `/fornecedor/api/produtos`)
  - Adicionar múltiplas unidades
  - IMEI/Serial individual por unidade
  - Validação em tempo real (API: `/fornecedor/api/validar-imei`)
  - Campos condicionais para seminovos (bateria, caixa, cabo)

#### 2.3 Detalhes do Item

- **URL:** `/fornecedor/estoque/{item}`
- **Funcionalidades:**
  - Informações completas do produto
  - Status atual e dados financeiros
  - Timeline de movimentações
  - Botão para editar (se disponível)

#### 2.4 Edição de Item

- **URL:** `/fornecedor/estoque/{item}/editar`
- **Campos Editáveis:**
  - `supplier_cost` (Custo/Repasse)
  - `suggested_price` (Preço Sugerido)
  - `notes` (Observações)
- **Campos Read-only:** IMEI, Serial, Produto, Status

### 3. Relatórios

- **URL:** `/fornecedor/relatorios`
- **Funcionalidades:**
  - Filtro de período (data inicial/final)
  - Cards: Disponível (qtd/valor), Vendidos (qtd/repasse)
  - Tabelas: Estoque Disponível, Vendidos no Período
  - Relatório formatado para WhatsApp (botão copiar)

## Instruções de Deploy

### 1. Preparação do Banco de Dados

```bash
# Rodar migrations
php artisan migrate

# Rodar seeder do fornecedor
php artisan db:seed --class=SupplierUserSeeder
```

### 2. Credenciais Iniciais

**Email:** `andre@solerbastos.com`  
**Senha:** `DGStore2026!`

**IMPORTANTE:** Solicite ao fornecedor alterar a senha no primeiro acesso.

### 3. Verificação

```bash
# Verificar rotas do portal
php artisan route:list --name=supplier

# Testar autenticação
php artisan tinker
>>> Auth::guard('supplier')->attempt(['email' => 'andre@solerbastos.com', 'password' => 'DGStore2026!'])
```

### 4. Testes de Segurança

```bash
# Rodar testes do portal
php artisan test --filter=SupplierPortalSecurityTest
```

## Checklist de Segurança

- [x] Guard de autenticação separado
- [x] Middleware em todas as rotas autenticadas
- [x] Validação de supplier_id em todos os controllers
- [x] CSRF protection em todos os formulários
- [x] Rate limiting no login (5 tentativas)
- [x] Password hashing automático
- [x] XSS protection (Blade escaping)
- [x] SQL injection protection (Eloquent)
- [x] Validação de usuário ativo (user + supplier)
- [x] 403 Forbidden para acessos não autorizados
- [x] Session security (regenerate on login)
- [x] Logout completo (invalidate + regenerate token)

## Manutenção

### Adicionar Novo Fornecedor

```php
// Via Seeder ou Tinker
$supplier = Supplier::create([
    'name' => 'Nome do Fornecedor',
    'origin' => SupplierOrigin::Br,
    'email' => 'email@fornecedor.com',
    'phone' => '(11) 99999-9999',
    'active' => true,
]);

SupplierUser::create([
    'supplier_id' => $supplier->id,
    'email' => 'email@fornecedor.com',
    'password' => 'senha_temporaria',
    'active' => true,
]);
```

### Desativar Fornecedor

```php
// Via Tinker
$supplierUser = SupplierUser::where('email', 'email@fornecedor.com')->first();
$supplierUser->active = false;
$supplierUser->save();

// Ou desativar o supplier inteiro
$supplier = Supplier::where('email', 'email@fornecedor.com')->first();
$supplier->active = false;
$supplier->save();
```

### Logs e Auditoria

O sistema registra automaticamente:
- **Login:** Laravel log em `storage/logs/laravel.log`
- **Movimentações:** Tabela `consignment_stock_movements`
- **Alterações de Preço:** Tabela `consignment_price_histories`

## Troubleshooting

### Problema: Fornecedor não consegue logar

1. Verificar se o usuário está ativo:
```php
SupplierUser::where('email', 'email@fornecedor.com')->first()->active
```

2. Verificar se o supplier está ativo:
```php
Supplier::where('email', 'email@fornecedor.com')->first()->active
```

3. Verificar rate limiting:
```php
RateLimiter::clear('email@fornecedor.com|IP_ADDRESS')
```

### Problema: Erro 403 ao acessar item

Verificar se o item pertence ao fornecedor:
```php
$item = ConsignmentStockItem::find($itemId);
$item->supplier_id === auth('supplier')->user()->supplier_id
```

### Problema: Autocomplete não funciona

1. Verificar rota da API:
```bash
php artisan route:list | grep supplier.api.products
```

2. Testar endpoint manualmente:
```bash
curl -H "Cookie: laravel_session=..." http://localhost/fornecedor/api/produtos?q=iPhone
```

## Contato Técnico

Para dúvidas ou suporte, contate a equipe de desenvolvimento da DG Store.

---

**Última atualização:** 24/05/2026  
**Versão:** 1.0.0
