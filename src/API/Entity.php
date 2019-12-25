<?php

namespace Sambavideo\API;

use Sambavideo\Exceptions\Http\BadRequestException;
use Sambavideo\Exceptions\Http\ConflictException;
use Sambavideo\Exceptions\Http\ForbiddenException;
use Sambavideo\Exceptions\Http\InternalServerErrorException;
use Sambavideo\Exceptions\Http\MethodNotAllowedException;
use Sambavideo\Exceptions\Http\NotFoundException;
use Sambavideo\Exceptions\Http\UnauthorizedException;
use Sambavideo\Exceptions\Validation\UnexpectedResultException;
use Sambavideo\Exceptions\Validation\UnexpectedValueException;
use Sambavideo\Exceptions\Validation\UnidentifiedEntityException;
use Sambavideo\Exceptions\Validation\WorthlessVariableException;

abstract class Entity extends HttpRequest
{
    protected $properties = [];
    private $shadowCopy = [];

    abstract protected function getEndpointUrl(): string;

    /**
     * @param string $name
     * @return mixed|null
     */
    protected function getProperty(string $name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    /**
     * @param string $name
     * @param $value
     */
    protected function setProperty(string $name, $value): void
    {
        $this->properties[$name] = $value;
    }

    /**
     * @param mixed $result
     * @return array
     * @throws UnexpectedResultException
     */
    protected function decodeResult($result): array
    {
        if (is_array($result)) {
            return $result;
        } elseif (!is_string($result) || $result === "" || $result === "[]") {
            return [];
        }
        $result = json_decode($result, true);
        if (!is_array($result)) {
            throw new UnexpectedResultException("Unable to decode result");
        }
        return $result;
    }

    protected function fetchArray(array $result): void
    {
        $this->properties = $this->shadowCopy = $result;
    }

    /**
     * @param mixed $result
     * @param array $postedFields: usage on class Sambavideo\API\Entities\Media
     * @throws UnexpectedResultException
     */
    protected function fetchResult($result, array $postedFields = []): void
    {
        $this->fetchArray(
            $this->decodeResult($result)
        );
    }

    /**
     * @param array $postFields
     * @return array
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    public function search(array $postFields = []): array
    {
        $result = $this->decodeResult($this->curlGET($this->getEndpointUrl(), $postFields));
        if (empty($result)) {
            return [];
        }
        $output = [];
        $class = get_called_class();
        foreach ($result as $item) {
            /** @var Entity $object */
            $object = new $class();
            $object->fetchResult($item, $postFields);
            $output[] = $object;
        }
        return $output;
    }

    /**
     * @param $id
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    public function fetch($id, array $postFields = []): void
    {
        $result = $this->curlGET("{$this->getEndpointUrl()}/$id", $postFields);
        $this->fetchResult($result, $postFields);
    }

    /**
     * @return bool
     */
    protected function existsOnVendor(): bool
    {
        return !empty($this->properties['id']);
    }

    /**
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    public function save(array $postFields = []): void
    {
        if ($this->existsOnVendor()) {
            $this->update($postFields);
        } else {
            $this->create($postFields);
        }
    }

    /**
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    protected function create(array $postFields): void
    {
        $result = $this->curlPOST(
            "{$this->getEndpointUrl()}",
            array_merge($postFields, $this->properties)
        );
        $this->fetchResult($result, $postFields);
    }

    /**
     * @param array $properties
     * @return array
     */
    private function getDirty(array $properties): array
    {
        $dirty = [];
        foreach ($properties as $name => $value) {
            if (!isset($this->shadowCopy['name']) || $this->shadowCopy['name'] !== $value) {
                $dirty[$name] = $value;
            }
        }
        return $dirty;
    }

    /**
     * @param array $postFields
     * @return array
     */
    protected function splitIdFromProperties(array $postFields): array
    {
        $properties = $this->properties;
        $id = $this->properties['id'];
        unset($properties['id']);
        return [$id, array_merge($postFields, $properties)];
    }

    /**
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    protected function update(array $postFields): void
    {
        list($id, $properties) = $this->splitIdFromProperties($postFields);
        $dirty = $this->getDirty($properties);
        if (empty($dirty)) {
            // if we don't have changes, we don't need to execute anything
            return;
        }
        $result = $this->curlPUT("{$this->getEndpointUrl()}/$id", $dirty);
        $this->fetchResult($result, $postFields);
    }

    /**
     * @param array $postFields
     * @throws BadRequestException
     * @throws ConflictException
     * @throws ForbiddenException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws UnexpectedResultException
     * @throws UnexpectedValueException
     * @throws UnidentifiedEntityException
     * @throws WorthlessVariableException
     */
    public function delete(array $postFields = []): void
    {
        if (!$this->existsOnVendor()) {
            throw new UnidentifiedEntityException("You can't delete an entity without id.");
        }
        list($id,) = $this->splitIdFromProperties();
        $this->curlDELETE("{$this->getEndpointUrl()}/$id", $postFields);
    }
}