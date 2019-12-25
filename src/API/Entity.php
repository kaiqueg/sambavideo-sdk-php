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
use Sambavideo\Exceptions\Validation\WorthlessVariableException;

abstract class Entity extends HttpRequest
{
    protected $properties = [];
    private $shadowCopy = [];

    abstract protected function getEndpointUrl(): string;

    protected function getProperty(string $name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    protected function setProperty(string $name, $value): void
    {
        $this->properties[$name] = $value;
    }

    /**
     * @param string $result
     * @return array
     * @throws UnexpectedResultException
     */
    protected function decodeResult(string $result): array
    {
        if($result === "" || $result === "[]") {
            return [];
        }
        $result = json_decode($result, true);
        if(!is_array($result)) {
            throw new UnexpectedResultException("Unable to decode result");
        }
        return $result;
    }

    protected function fetchArray(array $result): void
    {
        $this->properties = $this->shadowCopy = $result;
    }

    /**
     * @param string $result
     * @throws UnexpectedResultException
     */
    abstract protected function fetchResult(string $result): void;

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
        if(empty($result)) {
            return [];
        }
        $output = [];
        $class = get_called_class();
        foreach($result as $item) {
            /** @var Entity $object */
            $object = new $class();
            $object->fetchArray($item);
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
        $this->fetchResult($result);
    }

    protected function existsOnVendor(): bool
    {
        return !empty($this->properties['id']);
    }

    /**
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
    public function save(): void
    {
        if($this->existsOnVendor()) {
            $this->update();
        } else {
            $this->create();
        }
    }

    /**
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
    private function create(): void
    {
        $result = $this->curlPOST("{$this->getEndpointUrl()}", $this->properties);
        $this->fetchResult($result);
    }

    private function getDirty(array $properties): array
    {
        $dirty = [];
        foreach($properties as $name => $value) {
            if(!isset($this->shadowCopy['name']) || $this->shadowCopy['name'] !== $value) {
                $dirty[$name] = $value;
            }
        }
        return $dirty;
    }

    private function splitIdFromProperties(): array
    {
        $properties = $this->properties;
        $id = $this->properties['id'];
        unset($properties['id']);
        return [$id, $properties];
    }

    /**
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
    private function update(): void
    {
        list($id, $properties) = $this->splitIdFromProperties();
        $dirty = $this->getDirty($properties);
        $result = $this->curlPUT("{$this->getEndpointUrl()}/$id", $dirty);
        $this->fetchResult($result);
    }

    /**
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
    public function delete(): void
    {
        list($id, $properties) = $this->splitIdFromProperties();
        $this->curlDELETE("{$this->getEndpointUrl()}/$id", $properties);
    }
}