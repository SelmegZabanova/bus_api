<?php

namespace App\Enums;

enum DirectionType: string
{
    case FORWARD = 'ПРЯМОЕ';
    case BACKWARD = 'ОБРАТНОЕ';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


    public function label(): string
    {
        return match($this) {
            self::FORWARD => 'Прямое направление',
            self::BACKWARD => 'Обратное направление',
        };
    }
}
