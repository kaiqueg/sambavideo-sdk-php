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

    public function getName(): ?string
    {
        return $this->getProperty("name");
    }

    public function getDescription(): ?string
    {
        return $this->getProperty("description");
    }

    public function getPlayerHash(): ?string
    {
        return $this->getProperty("playerKey");
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