<?php

declare(strict_types=1);

namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="Pumukit\PaellaStatsBundle\Repository\UserActionRepository")
 */
class UserAction
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="date")
     */
    private $date;

    /**
     * @MongoDB\Field(type="string")
     */
    private $ip;

    /**
     * @MongoDB\Field(type="object_id")
     */
    private $user;

    /**
     * @MongoDB\Field(type="string")
     */
    private $session;

    /**
     * @MongoDB\Field(type="string")
     */
    private $userAgent;

    /**
     * @MongoDB\Field(type="object_id")
     */
    private $multimediaObject;

    /**
     * @MongoDB\Field(type="object_id")
     */
    private $series;

    /**
     * @MongoDB\Field(type="int")
     */
    private $inPoint;

    /**
     * @MongoDB\Field(type="int")
     */
    private $outPoint;

    /**
     * @MongoDB\Field(type="bool")
     */
    private $isLive;

    /**
     * @MongoDB\EmbedOne(targetDocument="Geolocation")
     */
    private $geolocation;

    /**
     * @MongoDB\Field(type="bool")
     */
    private $isProcessed;

    public function __construct($ip, $session, $userAgent, $multimediaObject, $series, $inPoint, $outPoint, $isLive, $user = null)
    {
        $this->date = new \DateTime('now');
        $this->ip = $ip;
        $this->session = $session;
        $this->userAgent = $userAgent;
        $this->multimediaObject = $multimediaObject;
        $this->series = $series;
        $this->inPoint = $inPoint;
        $this->outPoint = $outPoint;
        $this->isLive = $isLive;
        $this->user = $user;
        $this->isProcessed = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getSession(): string
    {
        return $this->session;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setMultimediaObject($multimediaObject): self
    {
        $this->multimediaObject = $multimediaObject;

        return $this;
    }

    public function getMultimediaObject()
    {
        return $this->multimediaObject;
    }

    public function setSerie($series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getSerie()
    {
        return $this->series;
    }

    public function setInPoint(int $inPoint): self
    {
        $this->inPoint = $inPoint;

        return $this;
    }

    public function getInPoint(): int
    {
        return $this->inPoint;
    }

    public function setOutPoint(int $outPoint): self
    {
        $this->outPoint = $outPoint;

        return $this;
    }

    public function getOutPoint(): int
    {
        return $this->outPoint;
    }

    public function setIsLive(bool $isLive): self
    {
        $this->isLive = $isLive;

        return $this;
    }

    public function getIsLive(): bool
    {
        return $this->isLive;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setGeolocation(Geolocation $geolocation): self
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    public function getGeolocation(): Geolocation
    {
        return $this->geolocation;
    }

    public function setIsProcessed(bool $isProcessed): self
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    public function getIsProcessed(): bool
    {
        return $this->isProcessed;
    }
}
