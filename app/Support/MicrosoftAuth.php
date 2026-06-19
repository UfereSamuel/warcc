<?php

namespace App\Support;

class MicrosoftAuth
{
    public static function isConfigured(): bool
    {
        return filled(config('services.microsoft.client_id'))
            && filled(config('services.microsoft.client_secret'))
            && filled(config('services.microsoft.tenant'));
    }
}
