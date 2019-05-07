<?php

namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Pumukit\PaellaStatsBundle\Document\UserAction.
 *
 * @MongoDB\Document(repositoryClass="Pumukit\PaellaStatsBundle\Repository\UserActionRepository")
 */
class UserAction
{
    /**
     * @var int
     *
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @MongoDB\Date
     */
    private $date;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $ip;

    /**
     * @var string
     *
     * @MongoDB\ObjectId
     */
    private $user;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $session;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $userAgent;

    /**
     * @var string
     *
     * @MongoDB\ObjectId
     */
    private $multimediaObject;

    /**
     * @var string
     *
     * @MongoDB\ObjectId
     */
    private $series;

    /**
     * @var int
     *
     * @MongoDB\Field(type="int")
     */
    private $inPoint;

    /**
     * @var int
     *
     * @MongoDB\Field(type="int")
     */
    private $outPoint;

    /**
     * @var bool
     *
     * @MongoDB\Boolean
     */
    private $isLive;

    /**
     * @var Geolocation
     *
     * @MongoDB\EmbedOne(targetDocument="Geolocation")
     */
    private $geolocation;

    /**
     * @var bool
     *
     * @MongoDB\Boolean
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

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return self
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string $ip
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set session.
     *
     * @param string $session
     *
     * @return self
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session.
     *
     * @return string $session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set userAgent.
     *
     * @param string $userAgent
     *
     * @return self
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get session.
     *
     * @return string $userAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
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
     * @return string $multimediaObject
     */
    public function getMultimediaObject()
    {
        return $this->multimediaObject;
    }

    /**
     * Set series.
     *
     * @param string $series
     *
     * @return self
     */
    public function setSerie($series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * Get series.
     *
     * @return string $series
     */
    public function getSerie()
    {
        return $this->series;
    }

    /**
     * Set inPoint.
     *
     * @param int $inPoint
     *
     * @return self
     */
    public function setInPoint($inPoint)
    {
        $this->inPoint = $inPoint;

        return $this;
    }

    /**
     * Get inPoint.
     *
     * @return int $inPoint
     */
    public function getInPoint()
    {
        return $this->inPoint;
    }

    /**
     * Set outPoint.
     *
     * @param int $outPoint
     *
     * @return self
     */
    public function setOutPoint($outPoint)
    {
        $this->outPoint = $outPoint;

        return $this;
    }

    /**
     * Get outPoint.
     *
     * @return int $outPoint
     */
    public function getOutPoint()
    {
        return $this->outPoint;
    }

    /**
     * Set isLive.
     *
     * @param bool $isLive
     *
     * @return self
     */
    public function setIsLive($isLive)
    {
        $this->isLive = $isLive;

        return $this;
    }

    /**
     * Get isLive.
     *
     * @return bool $isLive
     */
    public function getIsLive()
    {
        return $this->isLive;
    }

    /**
     * Set user.
     *
     * @param string $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return string $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set geolocation.
     *
     * @param Geolocation $geolocation
     */
    public function setGeolocation(Geolocation $geolocation)
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    /**
     * Get geolocation.
     *
     * @return Geolocation $geolocation
     */
    public function getGeolocation()
    {
        return $this->geolocation;
    }

    /**
     * Set isProcessed.
     *
     * @param bool $isProcessed
     */
    public function setIsProcessed($isProcessed)
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    /**
     * Get isProcessed.
     *
     * @return bool $isProcessed
     */
    public function getIsProcessed()
    {
        return $this->isProcessed;
    }
}
