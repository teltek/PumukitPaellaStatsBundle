<?php
namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints\False;

/**
 * Pumukit\PaellaStatsBundle\Document\MultimediaObjectAudience.
 *
 * @MongoDB\Document(repositoryClass="Pumukit\PaellaStatsBundle\Repository\MultimediaObjectAudienceRepository")
 */
class MultimediaObjectAudience
{
    /**
     * @var int
     *
     * @MongoDB\Id
     */
    private $id;
    
	/**
     * @var \Date
     *
     * @MongoDB\Date
     */
    private $lastUpdate;
    
	/**
     * @var string
     *
     * @MongoDB\ObjectId
     */
    private $multimediaObject;

    /**
     * @var int
     *
     * @MongoDB\Raw
     */
    private $audience = array();


    public function __construct($multimediaObject, $audience = array())
    {
        $this->lastUpdate = new \DateTime('now');
        $this->multimediaObject = $multimediaObject;
        $this->audience = $audience;
    }
	
    /**
     * Get id.
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }
	
    /**
     * Set lastUpdate.
     *
     * @param date $lastUpdate
     *
     * @return self
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }
    
	/**
     * Get lastUpdate.
     *
     * @return date $lastUpdate
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set multimediaObject.
     *
     * @param string $multimediaObject
     *
     * @return self
     */
    public function setMultimediaObject($multimediaObject)
    {
        $this->multimediaObject = $multimediaObject;
        return $this;
    }
    
	/**
     * Get multimediaObject.
     *
     * @return int $multimediaObject
     */
    public function getMultimediaObject()
    {
        return $this->multimediaObject;
    }

    /**
     * Set audience.
     *
     * @param int|null $second
     * @param int $times
     */
    public function setAudience($second = null, $times = 0)
    {
        $this->audience[$second] = $times;
    }

    /**
     * Get audience.
     *
     * @param int|null $second
     *
     * @return int
     */
    public function getAudience($second = null)
    {
        if ($second == null) {
            return 0;
        }

        return $this->audience[$second];
    }
}