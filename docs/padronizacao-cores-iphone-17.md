# Padronização de Cores - iPhone 17, 17 Pro e 17 Pro Max

## Resumo

Sistema de padronização de cores implementado para garantir consistência nos registros de estoque dos modelos iPhone 17, 17 Pro e 17 Pro Max.

## Cores Padronizadas

### iPhone 17 Pro e Pro Max
- **Deep Blue**
- **Silver**
- **Orange**

### iPhone 17
- **Preto**
- **Branco**
- **Verde**
- **Azul**
- **Lavanda**

## Arquitetura

### 1. Configuração Centralizada

**Arquivo:** `app/Domain/ConsignmentStock/Config/StandardColors.php`

Classe responsável por centralizar as definições de cores padronizadas:

```php
StandardColors::getColorsForModel('iPhone 17 Pro'); 
// Retorna: ['Deep Blue', 'Silver', 'Orange']

StandardColors::hasStandardColors('iPhone 17');
// Retorna: true
```

### 2. Catálogo de Produtos

**Arquivo:** `app/Domain/ConsignmentStock/Services/ProductCatalogService.php`

O serviço foi modificado para aplicar automaticamente as cores padronizadas ao retornar resultados da busca de produtos. O método `applyStandardColors()` sobrescreve as cores provenientes do banco de dados ou histórico com as cores oficiais definidas em `StandardColors`.

**Fluxo:**
1. Busca produtos de múltiplas fontes (iphone_models, products, consignment_stock_items)
2. Aplica cores padronizadas quando disponíveis
3. Retorna catálogo unificado via API JSON

### 3. Interface do Usuário

Os formulários de entrada de lote já estavam preparados para exibir cores em dropdowns quando disponíveis:

- **Portal do Fornecedor:** `resources/views/supplier/stock/batch-create.blade.php`
- **Sistema Interno:** `resources/views/stock/consignment/batch-create.blade.php`

Ambos utilizam Alpine.js para popular dinamicamente o campo de cor com base no produto selecionado.

### 4. Validação no Backend

**Arquivos Modificados:**
- `app/Presentation/Http/Controllers/Supplier/SupplierStockController.php`
- `app/Presentation/Http/Controllers/ConsignmentStockController.php`

Ambos os controllers implementam o método `validateStandardColors()` que:
- Verifica se o modelo tem cores padronizadas
- Valida se a cor informada está na lista permitida (case-insensitive)
- Lança exceção com mensagem clara caso a cor seja inválida

**Exemplo de erro:**
```
Cor 'Roxo' não é válida para iPhone 17. 
Cores permitidas: Preto, Branco, Verde, Azul, Lavanda
```

## Banco de Dados

### Seeder

**Arquivo:** `database/seeders/StandardizeIphone17ColorsSeeder.php`

Responsável por criar/atualizar os modelos iPhone 17, 17 Pro e 17 Pro Max na tabela `iphone_models` com as cores corretas.

**Execução:**
```bash
php artisan db:seed --class=StandardizeIphone17ColorsSeeder
```

## Pontos Importantes

### Custo Individual

Conforme solicitado, o custo (`supplier_cost`) continua sendo individual de cada aparelho, não havendo mudanças nesta regra de negócio.

### Retrocompatibilidade

- Produtos sem cores padronizadas definidas continuam funcionando normalmente
- A validação só é aplicada para modelos com cores definidas em `StandardColors`
- Cores existentes no histórico continuam válidas (não há migração de dados)

### Manutenção Futura

Para adicionar novos modelos com cores padronizadas:

1. Edite `app/Domain/ConsignmentStock/Config/StandardColors.php`
2. Adicione o modelo e cores no array `COLOR_MAP`
3. Execute o seeder correspondente ou crie um novo

Exemplo:
```php
private const COLOR_MAP = [
    // ... existentes ...
    'iPhone 18 Pro' => ['Titanium Black', 'Titanium White', 'Titanium Blue'],
];
```

## Testes

### Teste Manual

1. Acesse o portal do fornecedor ou sistema interno
2. Busque por "iPhone 17 Pro Max"
3. Selecione o produto
4. Verifique que o campo "Cor" exibe apenas: Deep Blue, Silver, Orange
5. Tente cadastrar uma cor fora da lista → erro de validação

### API

```bash
# Testar catálogo de produtos
curl http://localhost/api/consignment/catalog?q=iPhone%2017 | jq

# Resposta esperada inclui array "colors" com cores padronizadas
```

## Deployment

**Comandos necessários no servidor:**

```bash
# 1. Pull das mudanças
git pull origin main

# 2. Atualizar composer (se necessário)
composer install --no-dev --optimize-autoloader

# 3. Executar seeder para padronizar cores
php artisan db:seed --class=StandardizeIphone17ColorsSeeder

# 4. Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Arquivos Criados/Modificados

### Novos Arquivos
- `app/Domain/ConsignmentStock/Config/StandardColors.php`
- `database/seeders/StandardizeIphone17ColorsSeeder.php`
- `docs/padronizacao-cores-iphone-17.md`

### Arquivos Modificados
- `app/Domain/ConsignmentStock/Services/ProductCatalogService.php`
- `app/Presentation/Http/Controllers/Supplier/SupplierStockController.php`
- `app/Presentation/Http/Controllers/ConsignmentStockController.php`
