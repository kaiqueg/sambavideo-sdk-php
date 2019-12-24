<?php

namespace Sambavideo\API\Entities;

use Sambavideo\API\Entity;
use Sambavideo\API\Settings;

class Media extends Entity
{

    protected function getEndpointUrl(): string
    {
        return Settings::BASE_URL . "medias";
    }

    /**
     * @inheritDoc
     */
    protected function fetchInput(string $result): void
    {
        $this->fetchArray(
            $this->decodeResult($result)
        );
    }

    public function getId(): string
    {
        return $this->getProperty("id");
    }

    public function getTitle(): string
    {
        return $this->getProperty("name");
    }

    public function getDescription(): string
    {
        return $this->getProperty("description");
    }

    public function setTitle(string $title): void
    {
        $this->setProperty("title", $title);
    }

    public function setDescription(string $description): void
    {
        $this->setProperty("description", $description);
    }
}