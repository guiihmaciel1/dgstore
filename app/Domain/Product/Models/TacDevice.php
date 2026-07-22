<?php

declare(strict_types=1);

namespace App\Domain\Product\Models;

use Illuminate\Database\Eloquent\Model;

class TacDevice extends Model
{
    protected $fillable = [
        'tac',
        'brand',
        'model',
        'device_type',
    ];

    /**
     * Busca um dispositivo pelo IMEI (usa os 8 primeiros dígitos = TAC).
     */
    public static function findByImei(string $imei): ?self
    {
        $tac = substr(preg_replace('/\D/', '', $imei), 0, 8);

        if (strlen($tac) < 8) {
            return null;
        }

        return static::where('tac', $tac)->first();
    }

    /**
     * Sugere a categoria do produto com base no tipo do dispositivo.
     */
    public function suggestedCategory(): string
    {
        return match (strtolower($this->device_type ?? '')) {
            'phone', 'smartphone' => 'smartphone',
            'tablet' => 'tablet',
            'watch', 'smartwatch' => 'smartwatch',
            'notebook', 'laptop' => 'notebook',
            'headphone', 'earphone', 'earbuds' => 'headphone',
            default => 'smartphone',
        };
    }

    /**
     * Retorna o nome completo sugerido (Marca + Modelo).
     */
    public function suggestedName(): string
    {
        return trim("{$this->brand} {$this->model}");
    }
}
