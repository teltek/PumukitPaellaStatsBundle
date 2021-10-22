<?php

declare(strict_types=1);

namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="Pumukit\PaellaStatsBundle\Repository\MultimediaObjectAudienceRepository")
 */
class MultimediaObjectAudience
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
    private $audience = [];

    public function __construct($multimediaObject, $audience = [])
    {
        $this->lastUpdate = new \DateTime('now');
        $this->multimediaObject = $multimediaObject;
        $this->audience = $audience;
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

    public function setAudience(?int $second = null, int $times = 0): void
    {
        $this->audience[$second] = $times;
    }

    public function getAudience(?int $second = null): int
    {
        if (null === $second) {
            return 0;
        }

        return $this->audience[$second];
    }
}
