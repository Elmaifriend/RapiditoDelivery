<?php

namespace App\Enums;

enum CountryCode: string
{
    case MX = '+52';
    case US = '+1';
    case CA = '+1_CA';
    case CO = '+57';
    case AR = '+54';
    case CL = '+56';
    case PE = '+51';
    case EC = '+593';
    case VE = '+58';
    case ES = '+34';
    case GT = '+502';
    case CR = '+506';

    /**
     * Busca un caso del Enum por su nombre de caso (ej: "MX")
     */
    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    public function dialCode(): string
    {
        return match($this) {
            self::CA => '+1',
            default => $this->value,
        };
    }

    public function flag(): string
    {
        return match($this) {
            self::MX => '🇲🇽',
            self::US => '🇺🇸',
            self::CA => '🇨🇦',
            self::CO => '🇨🇴',
            self::AR => '🇦🇷',
            self::CL => '🇨🇱',
            self::PE => '🇵🇪',
            self::EC => '🇪🇨',
            self::VE => '🇻🇪',
            self::ES => '🇪🇸',
            self::GT => '🇬🇹',
            self::CR => '🇨🇷',
        };
    }

    public function label(): string
    {
        return match($this) {
            self::MX => 'México',
            self::US => 'Estados Unidos',
            self::CA => 'Canadá',
            self::CO => 'Colombia',
            self::AR => 'Argentina',
            self::CL => 'Chile',
            self::PE => 'Perú',
            self::EC => 'Ecuador',
            self::VE => 'Venezuela',
            self::ES => 'España',
            self::GT => 'Guatemala',
            self::CR => 'Costa Rica',
        };
    }

    public function fullLabel(): string
    {
        return "{$this->flag()} {$this->label()} ({$this->dialCode()})";
    }
}