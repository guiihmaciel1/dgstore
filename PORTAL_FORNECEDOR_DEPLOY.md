# Portal do Fornecedor - Guia de Deploy

## Comandos para Executar no Servidor

### 1. Rodar Migrations

```bash
cd /home/user/htdocs/srv1360734.hstgr.cloud
php artisan migrate
```

Isso criará a tabela `supplier_users` e aplicará todas as alterações necessárias.

### 2. Criar Usuário do Fornecedor

```bash
php artisan db:seed --class=SupplierUserSeeder
```

**Credenciais criadas:**
- **Email:** `andre@solerbastos.com`
- **Senha:** `DGStore2026!`

**IMPORTANTE:** Solicite ao Andre alterar a senha no primeiro acesso!

### 3. Limpar Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 4. Verificar Instalação

```bash
# Verificar se as rotas foram criadas
php artisan route:list --name=supplier

# Deve listar rotas como:
# - fornecedor/login
# - fornecedor/dashboard
# - fornecedor/estoque
# - etc.
```

## URLs de Acesso

- **Portal Fornecedor:** `https://srv1360734.hstgr.cloud/fornecedor/login`
- **Sistema Principal:** `https://srv1360734.hstgr.cloud/login` (sem alteração)

## O que foi Implementado

### ✅ Fase 1: Autenticação Separada
- Guard de autenticação `supplier` completamente isolado
- Model `SupplierUser` com tabela dedicada
- Middleware de segurança (`supplier.auth` e `supplier.active`)
- Password reset configurado
- Rate limiting (5 tentativas de login)

### ✅ Fase 2: Controllers e Rotas
- `SupplierAuthController` (login/logout)
- `SupplierDashboardController` (estatísticas)
- `SupplierStockController` (gestão completa de estoque)
- `SupplierReportController` (relatórios)
- `SupplierApiController` (autocomplete e validações)
- Rotas em `/fornecedor/*` com middleware de segurança

### ✅ Fase 3: Views com Design Apple/Clean
- Layout minimalista e moderno
- Login centralizado com gradiente
- Dashboard com cards de estatísticas
- Listagem de estoque com tabs (Disponível/Vendido/Devolvido)
- Entrada em lote com autocomplete e validação em tempo real
- Detalhes do item com timeline de movimentações
- Edição de preços (supplier_cost, suggested_price)
- Relatórios com filtro de período e export para WhatsApp

### ✅ Fase 4: Segurança e Testes
- Todos os queries filtram por `supplier_id`
- Validação 403 para acesso não autorizado
- CSRF protection em todos os formulários
- SQL injection protection (Eloquent)
- XSS protection (Blade escaping)
- Testes de segurança implementados
- Documentação completa

## Funcionalidades do Portal

### Para o Fornecedor (Andre Soler Bastos)

1. **Dashboard**
   - Visualizar quantidade e valor de itens disponíveis
   - Ver vendas do mês e repasse a receber
   - Últimas entradas cadastradas

2. **Meu Estoque**
   - Listar todos os itens (disponíveis, vendidos, devolvidos)
   - Buscar por IMEI, serial ou nome
   - Ver detalhes completos de cada item
   - Editar preços (custo/repasse e sugerido)

3. **Nova Entrada**
   - Cadastrar lote de produtos
   - Autocomplete de produtos Apple
   - Adicionar múltiplas unidades com IMEI/Serial individual
   - Validação em tempo real de duplicidade
   - Campos específicos para seminovos

4. **Relatórios**
   - Filtrar por período
   - Ver estoque disponível e vendas
   - Gerar relatório formatado para WhatsApp
   - Copiar com um clique

## Segurança Garantida

- ✅ O fornecedor NÃO tem acesso ao sistema principal
- ✅ O fornecedor só vê seus próprios itens (filtro por supplier_id)
- ✅ Autenticação separada (guard `supplier`)
- ✅ Middleware de segurança em todas as rotas
- ✅ Rate limiting no login
- ✅ CSRF protection
- ✅ Validação de usuário e fornecedor ativos

## Manutenção Futura

### Adicionar Novo Fornecedor

```bash
php artisan tinker

# Criar fornecedor
$supplier = App\Domain\Supplier\Models\Supplier::create([
    'name' => 'Nome do Fornecedor',
    'origin' => App\Domain\Supplier\Enums\SupplierOrigin::Br,
    'email' => 'email@fornecedor.com',
    'phone' => '(11) 99999-9999',
    'active' => true,
]);

# Criar usuário de login
App\Domain\Supplier\Models\SupplierUser::create([
    'supplier_id' => $supplier->id,
    'email' => 'email@fornecedor.com',
    'password' => 'senha_temporaria',
    'active' => true,
]);
```

### Desativar Fornecedor

```bash
php artisan tinker

$user = App\Domain\Supplier\Models\SupplierUser::where('email', 'andre@solerbastos.com')->first();
$user->active = false;
$user->save();
```

## Documentação Completa

Consulte `/root/dgstore/docs/supplier-portal.md` para:
- Arquitetura detalhada
- Fluxos de autenticação
- Troubleshooting
- Checklist de segurança completo

## Suporte

Para dúvidas ou problemas, verifique:
1. Logs do Laravel: `storage/logs/laravel.log`
2. Logs do servidor: `/var/log/apache2/error.log` ou `/var/log/nginx/error.log`
3. Documentação técnica em `docs/supplier-portal.md`

---

**Status:** ✅ Implementação Completa  
**Data:** 24/05/2026  
**Versão:** 1.0.0
