<?php

namespace App\Enums;

enum ColorTheme: string
{
    case PINK = 'pink';
    case PURPLE = 'purple';
    case BLUE = 'blue';
    case CYAN = 'cyan';
    case GREEN = 'green';

    public function label(): string
    {
        return match ($this) {
            ColorTheme::PINK => 'Rose',
            ColorTheme::PURPLE => 'Violet',
            ColorTheme::BLUE => 'Bleu',
            ColorTheme::CYAN => 'Canard',
            ColorTheme::GREEN => 'Vert',
        };
    }

    public static function enum(): array
    {
        return collect(ColorTheme::cases())
            ->mapWithKeys(fn ($colorTheme) => [$colorTheme->value => $colorTheme->label()])
            ->toArray();
    }
}
