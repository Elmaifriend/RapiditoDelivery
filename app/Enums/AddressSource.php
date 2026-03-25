<?php

namespace App\Enums;

enum AddressSource: string
{
    case GPS = 'GPS';
    case WEB = 'WEB';
    case APP = 'APP';
    case WHATSAPP = 'WhatsApp';
}