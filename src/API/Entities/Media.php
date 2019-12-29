<?php

namespace Sambavideo\API\Entities;

use Sambavideo\API\Entity;
use Sambavideo\API\Settings;
use SdkBase\Exceptions\Flow\UnimplementedMethodException;
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
use SdkBase\Utils\Date;
use SdkBase\Utils\Time;

class Media extends Entity
{
    /**
     * Few words about this entity
     * - About update: I couldn't find on Sambatech's docs which columns could be modified,
     *   so I don't know what 'set' mmethods to do and I'm throwing 'UnimplementedMethodException'
     *   if anyone tries to update this entity until someone discover what are our choices
     * - About create: there's no endpoint to media creation on docs...
     * - So, please use this Entity only for fetch and search
     */

    private $pid = 0;
    public static $PLAYER_HASHES = [];
    const PLAYER_URL = 'https://fast.player.liquidplatform.com/pApiv2/embed/';

    protected function getEndpointUrl(): string
    {
        return Settings::BASE_URL . "medias";
    }

    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "projectId" => $this->getProjectId(),
            "url" => $this->getEmbedUrl(),
            "iframe" => $this->getIframe(),
            "title" => $this->getTitle(),
            "description" => $this->getDescription(),
            "shortDescription" => $this->getShortDescription(),
            "categoryName" => $this->getCategoryName(),
            "categoryId" => $this->getCategoryId(),
            "tags" => $this->getTags(),
            "files" => $this->getFiles(),
            "thumbs" => $this->getThumbs(),
            "durationMilliseconds" => $this->getDurationMilliseconds(),
            "durationTime" => $this->getDurationTime(),
            "captions" => $this->getCaptions(),
            "hasLibras" => $this->hasLibras(),
            "published" => $this->isPublished(),
            "highlighted" => $this->isHighlighted(),
            "restricted" => $this->isRestricted(),
            "postDate" => $this->getPostDate(),
            "publishDate" => $this->getPublishDate(),
            "unpublishDate" => $this->getUnpublishDate(),
            "lastModificationDate" => $this->getLastModificationDate(),
            "viewCount" => $this->getViewCount(),
            "commentCount" => $this->getCommentCount(),
            "ratingCount" => $this->getRatingCount(),
        ];
    }

    public function hasLibras(): bool
    {
        return (bool)$this->getProperty("libras")['enabled'];
    }

    public function isPublished(): bool
    {
        return (bool)$this->getProperty("published");
    }

    public function isHighlighted(): bool
    {
        return (bool)$this->getProperty("highlighted");
    }

    public function isRestricted(): bool
    {
        return (bool)$this->getProperty("restricted");
    }

    public function getTitle(): ?string
    {
        return $this->getProperty("title");
    }

    public function getDescription(): ?string
    {
        return $this->getProperty("description");
    }

    public function getShortDescription(): ?string
    {
        return $this->getProperty("shortDescription");
    }

    public function getCategoryName(): ?string
    {
        return $this->getProperty("categoryName");
    }

    public function getCategoryId(): int
    {
        return (int)$this->getProperty("categoryId");
    }

    public function getPostDate(string $dateFormat = "Y-m-d H:i:s"): ?string
    {
        return Date::fromMilliseconds(
            (int)$this->getProperty("postdate"),
            $dateFormat
        );
    }

    public function getLastModificationDate(string $dateFormat = "Y-m-d H:i:s"): ?string
    {
        return Date::fromMilliseconds(
            (int)$this->getProperty("lastModified"),
            $dateFormat
        );
    }

    public function getPublishDate(string $dateFormat = "Y-m-d H:i:s"): ?string
    {
        return Date::fromMilliseconds(
            (int)$this->getProperty("publishDate"),
            $dateFormat
        );
    }

    public function getUnpublishDate(string $dateFormat = "Y-m-d H:i:s"): ?string
    {
        return Date::fromMilliseconds(
            (int)$this->getProperty("unpublishDate"),
            $dateFormat
        );
    }

    public function getViewCount(): int
    {
        return (int)$this->getProperty("numberOfViews");
    }

    public function getCommentCount(): int
    {
        return (int)$this->getProperty("numberOfComments");
    }

    public function getRatingCount(): int
    {
        return (int)$this->getProperty("numberOfRatings");
    }

    public function getTags(): array
    {
        $tags = $this->getProperty("tags");
        return is_array($tags) ? $tags : [];
    }

    public function getFiles(): array
    {
        $files = $this->getProperty("files");
        return is_array($files) ? $files : [];
    }

    public function getFile(string $outputName = "1080p"): array
    {
        $files = $this->getFiles();
        foreach ($files as $file) {
            if ($file['outputName'] === $outputName) {
                return $file;
            }
        }
        return [];
    }

    public function getDurationMilliseconds(): int
    {
        $files = $this->getFiles();
        return empty($files) ? 0 : (int)$files[0]['fileInfo']['duration'];
    }

    public function getDurationTime(): string
    {
        return Time::fromMilliseconds($this->getDurationMilliseconds());
    }

    public function getThumbs(): array
    {
        $thumbs = $this->getProperty("thumbs");
        return is_array($thumbs) ? $thumbs : [];
    }

    public function getThumbFor(int $minWidth): ?string
    {
        $thumbs = $this->getThumbs();
        foreach ($thumbs as $thumb) {
            if ($minWidth <= $thumb['width']) {
                return $thumb['url'];
            }
        }
        return null;
    }

    public function getCaptions(): array
    {
        $captions = $this->getProperty("captions");
        return is_array($captions) ? $captions : [];
    }

    public function getPlayerHash(): ?string
    {
        if ($this->pid <= 0) {
            return null;
        } elseif (!isset(self::$PLAYER_HASHES[$this->pid])) {
            $project = new Project();
            try {
                $project->fetch($this->pid);
                self::$PLAYER_HASHES[$this->pid] = $project->getPlayerHash();
            } catch (NotFoundException $e) {
                self::$PLAYER_HASHES[$this->pid] = null;
            } catch (\Exception $e) {
                return null;
            }
        }
        return self::$PLAYER_HASHES[$this->pid];
    }

    public function getEmbedUrl(): ?string
    {
        $playerHash = $this->getPlayerHash();
        if (!$playerHash) {
            return null;
        }
        return self::PLAYER_URL . "$playerHash/{$this->getId()}";
    }

    public function getIframe(): ?string
    {
        $embedUrl = $this->getEmbedUrl();
        if (!$embedUrl) {
            return null;
        }
        return "<iframe 
                allowfullscreen 
                webkitallowfullscreen 
                mozallowfullscreen 
                width=\"640\" 
                height=\"360\" 
                src=\"$embedUrl\" 
                scrolling=\"no\" 
                frameborder=\"0\" 
                allow=\"geolocation; microphone; camera; encrypted-media; midi\"
            ></iframe>";
    }

    private function getProjectId(): int
    {
        return $this->pid;
    }

    private function setProjectId(int $pid): void
    {
        if ($pid <= 0) {
            return;
        }
        $this->pid = $pid;
    }

    /**
     * @param array $postFields
     * @throws MissingFieldException
     * @throws UnexpectedValueException
     */
    private function checkProjectId(array $postFields): void
    {
        if (!isset($postFields['pid'])) {
            throw new MissingFieldException("Please inform the media's pid .");
        } elseif (intval($postFields['pid']) <= 0) {
            throw new UnexpectedValueException("The project's id must be higher than 0.");
        }
    }

    /**
     * @param mixed $result
     * @param array $postedFields
     * @throws UnexpectedResultException
     */
    protected function fetchResult($result, array $postedFields = []): void
    {
        parent::fetchResult($result, $postedFields);
        $this->setProjectId((int)$postedFields['pid']);
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
     * @throws UnimplementedMethodException
     */
    protected function create(array $postFields): void
    {
        throw new UnimplementedMethodException("Create isn't available on Sambavideo's doc.");
    }

    /**
     * @param array $postFields
     * @throws UnimplementedMethodException
     */
    protected function update(array $postFields): void
    {
        throw new UnimplementedMethodException("Update isn't available for now.");
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
    public function deleteRaw(array $postFields = []): void
    {
        if (!$this->existsOnVendor()) {
            throw new UnidentifiedEntityException("You can't delete a media's RAW without it's id.");
        }
        $id = $this->getId();
        $this->curlDELETE(
            "{$this->getEndpointUrl()}/$id/raw{$this->getEndpointUrlExtension($postFields)}",
            $postFields
        );
    }
}
