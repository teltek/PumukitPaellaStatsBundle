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
     * @var \Date
     *
     * @MongoDB\Date
     */
    private $date;
    
	/**
     * @var string
     *
     * @MongoDB\String
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
     * @MongoDB\String
     */
    private $session;
    
	/**
     * @var string
     *
     * @MongoDB\ObjectId
     */
    private $multimediaObject;
    
	/**
     * @var int
     *
     * @MongoDB\Int
     */
    private $inPoint;
    
	/**
     * @var int
     *
     * @MongoDB\Int
     */
    private $outPoint;

    /**
     * @var bool
     *
     * @MongoDB\Boolean
     */
    private $isLive;
	
    public function __construct($ip, $session, $multimediaObject, $inPoint, $outPoint, $isLive, $user = null)
    {
        $this->date = new \DateTime('now');
        $this->ip = $ip;
        $this->session = $session;
        $this->multimediaObject = $multimediaObject;
        $this->inPoint = $inPoint;
        $this->outPoint = $outPoint;
        $this->isLive = $isLive;
        $this->user = $user;
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
     * Set date.
     *
     * @param date $date
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
     * @return date $date
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
}