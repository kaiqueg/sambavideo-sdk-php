<?php

namespace Sambavideo\API;

use SdkBase\API\Entity as SdkEntity;

abstract class Entity extends SdkEntity
{
    protected function getAuthorizationHeader(): array
    {
        // we don't use AuthorizationHeader on this project
        return [];
    }

    protected function getEndpointUrlExtension(array $postFields = []): string
    {
        $accessToken = Settings::getToken();
        $pid = isset($postFields['pid']) ? "&pid={$postFields['pid']}" : null;
        return "?access_token={$accessToken}$pid";
    }

    protected function injectSettingsData(array $postFields): array
    {
        return array_merge(
            [
                "access_token" => Settings::getToken(),
            ],
            $postFields
        );
    }
}