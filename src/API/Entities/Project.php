<?php

namespace Sambavideo\API\Entities;

use Sambavideo\API\Entity;
use Sambavideo\API\Settings;

class Project extends Entity
{

    protected function getEndpointUrl(): string
    {
        return Settings::BASE_URL . "projects";
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

    public function getId(): int
    {
        return $this->getProperty("id");
    }

    public function getName(): string
    {
        return $this->getProperty("name");
    }

    public function getDescription(): string
    {
        return $this->getProperty("description");
    }

    public function setName(string $name): void
    {
        $this->setProperty("name", $name);
    }

    public function setDescription(string $description): void
    {
        $this->setProperty("description", $description);
    }
}