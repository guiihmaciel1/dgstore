<?php

declare(strict_types=1);

namespace App\Domain\B2B\Models;

use Illuminate\Database\Eloquent\Model;

class B2BSetting extends Model
{
    protected $table = 'b2b_settings';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    public static function getMinimumOrderAmount(): float
    {
        return (float) static::get('minimum_order_amount', 5000);
    }
}
