<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Config;

/**
 * Cores padronizadas para modelos específicos de iPhone.
 * 
 * Define as cores oficiais permitidas para cada linha de produto,
 * garantindo consistência nos registros de estoque.
 */
class StandardColors
{
    /**
     * Mapeamento de modelo -> cores padronizadas
     * 
     * @var array<string, array<string>>
     */
    private const COLOR_MAP = [
        // iPhone 17 Pro e Pro Max
        'iPhone 17 Pro' => ['Deep Blue', 'Silver', 'Orange'],
        'iPhone 17 Pro Max' => ['Deep Blue', 'Silver', 'Orange'],
        
        // iPhone 17
        'iPhone 17' => ['Preto', 'Branco', 'Verde', 'Azul', 'Lavanda'],
    ];

    /**
     * Retorna as cores padronizadas para um modelo específico.
     * 
     * @param string $modelName Nome do modelo (ex: "iPhone 17 Pro Max")
     * @return array<string>|null Array de cores ou null se não houver padrão definido
     */
    public static function getColorsForModel(string $modelName): ?array
    {
        // Tenta match exato primeiro
        if (isset(self::COLOR_MAP[$modelName])) {
            return self::COLOR_MAP[$modelName];
        }

        // Tenta match parcial (case-insensitive)
        $normalizedName = mb_strtolower(trim($modelName));
        
        foreach (self::COLOR_MAP as $pattern => $colors) {
            $normalizedPattern = mb_strtolower($pattern);
            
            if (str_contains($normalizedName, $normalizedPattern)) {
                return $colors;
            }
        }

        return null;
    }

    /**
     * Verifica se um modelo tem cores padronizadas definidas.
     * 
     * @param string $modelName
     * @return bool
     */
    public static function hasStandardColors(string $modelName): bool
    {
        return self::getColorsForModel($modelName) !== null;
    }

    /**
     * Retorna todos os modelos com cores padronizadas.
     * 
     * @return array<string, array<string>>
     */
    public static function getAllStandardColors(): array
    {
        return self::COLOR_MAP;
    }
}
