<?php

namespace Sambavideo\API;

class Settings
{
    const BASE_URL = "http://api.sambavideos.sambatech.com/v1/";
    private static $TOKEN = "";

    public static function setToken(string $token): void
    {
        self::$TOKEN = $token;
    }

    public static function getToken(): string
    {
        return self::$TOKEN;
    }
}