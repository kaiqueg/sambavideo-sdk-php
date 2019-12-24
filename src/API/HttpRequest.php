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
use Sambavideo\Utils\Curl;
use Sambavideo\Utils\CurlContentType;
use Sambavideo\Utils\CurlMethod;

abstract class HttpRequest
{

    private function injectAccessToken(array $postFields): array
    {
        return array_merge(
            ["access_token" => Settings::getToken()],
            $postFields
        );
    }

    /**
     * @param string $url
     * @param array $postFields
     * @param string $contentType
     * @return Curl
     * @throws UnexpectedValueException
     * @throws WorthlessVariableException
     */
    private function curlInit(string $url, array $postFields, string $contentType): Curl
    {
        $curl = new Curl();
        $curl->setContentType($contentType);
        $curl->setPostFields($this->injectAccessToken($postFields));
        $curl->setUrl($url);
        return $curl;
    }

    /**
     * @param string $url
     * @param array $postFields
     * @param string $contentType
     * @return string
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
    protected function curlGET(string $url, array $postFields = [], string $contentType = CurlContentType::JSON): string
    {
        $curl = $this->curlInit($url, $postFields, $contentType);
        $curl->setMethod(CurlMethod::GET);
        return $curl->send();
    }

    /**
     * @param string $url
     * @param array $postFields
     * @param string $contentType
     * @return string
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
    protected function curlPOST(string $url, array $postFields = [], string $contentType = CurlContentType::JSON): string
    {
        $curl = $this->curlInit($url, $postFields, $contentType);
        $curl->setMethod(CurlMethod::GET);
        return $curl->send();
    }

    /**
     * @param string $url
     * @param array $postFields
     * @param string $contentType
     * @return string
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
    protected function curlPUT(string $url, array $postFields = [], string $contentType = CurlContentType::JSON): string
    {
        $curl = $this->curlInit($url, $postFields, $contentType);
        $curl->setMethod(CurlMethod::GET);
        return $curl->send();
    }

    /**
     * @param string $url
     * @param array $postFields
     * @param string $contentType
     * @return string
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
    protected function curlDELETE(string $url, array $postFields = [], string $contentType = CurlContentType::JSON): string
    {
        $curl = $this->curlInit($url, $postFields, $contentType);
        $curl->setMethod(CurlMethod::GET);
        return $curl->send();
    }
}