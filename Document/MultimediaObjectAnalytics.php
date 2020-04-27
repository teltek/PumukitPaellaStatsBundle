<?php

namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="Pumukit\PaellaStatsBundle\Repository\MultimediaObjectAnalyticsRepository")
 */
class MultimediaObjectAnalytics
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="date")
     */
    private $lastUpdate;

    /**
     * @MongoDB\Field(type="object_id")
     */
    private $multimediaObject;

    /**
     * @MongoDB\Field(type="raw")
     */
    private $analytics = [];

    public function __construct($multimediaObject, $analytics = [])
    {
        $this->lastUpdate = new \DateTime('now');
        $this->multimediaObject = $multimediaObject;
        $this->analytics = $analytics;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLastUpdate(\DateTime $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
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

    public function setAnalytics(?int $second = null, int $times = 0): void
    {
        $this->analytics[$second] = $times;
    }

    public function getAnalytics(?int $second = null): int
    {
        if (null === $second) {
            return 0;
        }

        return $this->analytics[$second];
    }
}
