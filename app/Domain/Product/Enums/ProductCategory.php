<?php

declare(strict_types=1);

namespace App\Domain\Product\Enums;

enum ProductCategory: string
{
    // Celulares
    case Smartphone = 'smartphone';
    
    // Eletrônicos
    case Tablet = 'tablet';
    case Notebook = 'notebook';
    case Smartwatch = 'smartwatch';
    case Headphone = 'headphone';
    case Speaker = 'speaker';
    case Console = 'console';
    case Camera = 'camera';
    
    // Perfumes
    case Perfume = 'perfume';
    
    // Acessórios e Serviços
    case Accessory = 'accessory';
    case Service = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Smartphone => 'Smartphone',
            self::Tablet => 'Tablet',
            self::Notebook => 'Notebook',
            self::Smartwatch => 'Smartwatch',
            self::Headphone => 'Fone de Ouvido',
            self::Speaker => 'Caixa de Som',
            self::Console => 'Console/Videogame',
            self::Camera => 'Câmera',
            self::Perfume => 'Perfume',
            self::Accessory => 'Acessório',
            self::Service => 'Serviço',
        };
    }

    /**
     * Retorna o grupo/tipo da categoria
     */
    public function group(): string
    {
        return match ($this) {
            self::Smartphone => 'Celulares',
            self::Tablet, self::Notebook, self::Smartwatch, 
            self::Headphone, self::Speaker, self::Console, self::Camera => 'Eletrônicos',
            self::Perfume => 'Perfumes',
            self::Accessory => 'Acessórios',
            self::Service => 'Serviços',
        };
    }

    /**
     * Retorna o ícone da categoria
     */
    public function icon(): string
    {
        return match ($this) {
            self::Smartphone => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
            self::Tablet => 'M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            self::Notebook => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            self::Smartwatch => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            self::Headphone => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3',
            self::Speaker => 'M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z',
            self::Console => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            self::Camera => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
            self::Perfume => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            self::Accessory => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
            self::Service => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        };
    }

    /**
     * Retorna categorias agrupadas para seleção
     */
    public static function grouped(): array
    {
        return [
            'Celulares' => [
                self::Smartphone,
            ],
            'Eletrônicos' => [
                self::Tablet,
                self::Notebook,
                self::Smartwatch,
                self::Headphone,
                self::Speaker,
                self::Console,
                self::Camera,
            ],
            'Perfumes' => [
                self::Perfume,
            ],
            'Outros' => [
                self::Accessory,
                self::Service,
            ],
        ];
    }
}
