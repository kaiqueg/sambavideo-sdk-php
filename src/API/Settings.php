<?php

namespace Sambavideo\API;

class Settings
{
    const BASE_URL = "http://api.sambavideos.sambatech.com/v1/";
    private static $TOKEN = "";
    private static $PLAYER_HASH = "";

    public static function setToken(string $token): void
    {
        self::$TOKEN = $token;
    }

    public static function getToken(): string
    {
        return self::$TOKEN;
    }

    public static function setPlayerHash(string $playerHash): void
    {
        self::$PLAYER_HASH = $playerHash;
    }

    public static function getPlayerHash(): string
    {
        return self::$PLAYER_HASH;
    }
}