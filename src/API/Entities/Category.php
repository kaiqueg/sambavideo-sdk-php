<?php

namespace Sambavideo\API\Entities;

use Sambavideo\API\Entity;
use Sambavideo\API\Settings;
use SdkBase\Exceptions\Http\BadRequestException;
use SdkBase\Exceptions\Http\ConflictException;
use SdkBase\Exceptions\Http\ForbiddenException;
use SdkBase\Exceptions\Http\InternalServerErrorException;
use SdkBase\Exceptions\Http\MethodNotAllowedException;
use SdkBase\Exceptions\Http\NotFoundException;
use SdkBase\Exceptions\Http\UnauthorizedException;
use SdkBase\Exceptions\Validation\MissingFieldException;
use SdkBase\Exceptions\Validation\UnexpectedResultException;
use SdkBase\Exceptions\Validation\UnexpectedValueException;
use SdkBase\Exceptions\Validation\UnidentifiedEntityException;
use SdkBase\Exceptions\Validation\WorthlessVariableException;

class Category extends Entity
{

    protected function getEndpointUrl(): string
    {
        return Settings::BASE_URL . "categories";
    }

    public function isHidden(): bool
    {
        return (bool)$this->getProperty("hidden");
    }

    public function isDeleted(): bool
    {
        return (bool)$this->getProperty("deleted");
    }

    public function hasLead(): bool
    {
        return (bool)$this->getProperty("leadEnabled");
    }

    public function getName(): ?string
    {
        return $this->getProperty("name");
    }

    public function getMediaCount(): int
    {
        return (int)$this->getProperty("mediasCount");
    }

    public function getParentId(): ?int
    {
        $parent = $this->getProperty("parent");
        if(!$parent) {
            return null;
        }
        return (int)$parent;
    }

    public function getChildren(): array
    {
        $children = $this->getProperty("children");
        if(!is_array($children) || empty($children)) {
            return [];
        }
        $output = [];
        foreach($children as $child) {
            $instance = new self();
            $instance->fetchArray($child);
            $output[] = $instance;
        }
        return $output;
    }

    public function setName(string $name): void
    {
        $this->setProperty("name", $name);
    }

    public function setParentId(int $categoryId): void
    {
        $this->setProperty("parent", $categoryId);
    }

    public function setHiddenState(bool $state): void
    {
        $this->setProperty("hidden", $state);
    }

    /**
     * @param array $postFields
     * @throws MissingFieldException
     * @throws UnexpectedValueException
     */
    private function checkProjectId(array $postFields): void
    {
        if (!isset($postFields['pid'])) {
            throw new MissingFieldException("Please inform the category's pid .");
        } elseif (intval($postFields['pid']) <= 0) {
            throw new UnexpectedValueException("The project's id must be higher than 0.");
        }
    }

    /**
     * @param array $postFields
     * @return array
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws MissingFieldException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    public function search(array $postFields = []): array
    {
        $this->checkProjectId($postFields);
        return parent::search($postFields);
    }

    /**
     * @param $id
     * @param array $postFields
     * @throws MissingFieldException
     * @throws UnexpectedValueException
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws WorthlessVariableException
     */
    public function fetch($id, array $postFields = []): void
    {
        $this->checkProjectId($postFields);
        parent::fetch($id, $postFields);
    }

    /**
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws MissingFieldException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    public function save(array $postFields = []): void
    {
        $this->checkProjectId($postFields);
        parent::save($postFields);
    }

    /**
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws MissingFieldException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws UnidentifiedEntityException
     * @throws WorthlessVariableException
     */
    public function delete(array $postFields = []): void
    {
        $this->checkProjectId($postFields);
        parent::delete($postFields);
    }

    public function toArray(): array
    {
        $children = [];
        foreach($this->getChildren() as $child) {
            /** @var Category $child */
            $children[] = $child->toArray();
        }
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "parentId" => $this->getParentId(),
            "children" => $children,
            "hidden" => $this->isHidden(),
            "deleted" => $this->isDeleted(),
            "leadEnabled" => $this->hasLead(),
        ];
    }
}