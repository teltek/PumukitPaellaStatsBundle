<?php
namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints\False;

/**
 * Pumukit\PaellaStatsBundle\Document\Geolocation.
 *
 * @MongoDB\EmbeddedDocument
 */
class Geolocation
{

    /**
     * @var int
     *
     * @MongoDB\Id
     */
    private $id;
   
	/**
     * @var string
     *
     * @MongoDB\String
     */
    private $continent;
    
	/**
     * @var string
     *
     * @MongoDB\String
     */
    private $isoContinent;
    
	/**
     * @var string
     *
     * @MongoDB\String
     */
    private $country;

    /**
     * @var string
     *
     * @MongoDB\String
     */
    private $isoCountry;
    
    /**
     * @var string
     *
     * @MongoDB\String
     */
    private $subCountry;

    /**
     * @var string
     *
     * @MongoDB\String
     */
    private $isoSubCountry;

    /**
     * @var string
     *
     * @MongoDB\String
     */
    private $city;

    /**
     * @var string
     *
     * @MongoDB\Raw
     */
    private $location = array('latitude' => '', 'longitude' => '', 'timeZone' => '');

    /**
     * @var string
     *
     * @MongoDB\String
     */
    private $postal;

	
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
     * Set continent.
     *
     * @param string $continent
     *
     * @return self
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;
        return $this;
    }
    
	/**
     * Get continent.
     *
     * @return string $continent
     */
    public function getContinent()
    {
        return $this->continent;
    }
    
    /**
     * Set isoContinent.
     *
     * @param string $isoContinent
     *
     * @return self
     */
    public function setIsoContinent($isoContinent)
    {
        $this->isoContinent = $isoContinent;
        return $this;
    }
    
	/**
     * Get isoContinent.
     *
     * @return string $isoContinent
     */
    public function getIsoContinent()
    {
        return $this->isoContinent;
    }
    
	/**
     * Set country.
     *
     * @param string $country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
    
	/**
     * Get country.
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set isoCountry.
     *
     * @param string $isoCountry
     *
     * @return self
     */
    public function setIsoCountry($isoCountry)
    {
        $this->isoCountry = $isoCountry;
        return $this;
    }

    /**
     * Get isoCountry.
     *
     * @return string $isoCountry
     */
    public function getIsoCountry()
    {
        return $this->isoCountry;
    }

	/**
     * Set subCountry.
     *
     * @param string $subCountry
     *
     * @return self
     */
    public function setSubCountry($subCountry)
    {
        $this->subCountry = $subCountry;
        return $this;
    }
    
	/**
     * Get subCountry.
     *
     * @return int $subCountry
     */
    public function getSubCountry()
    {
        return $this->subCountry;
    }
	
	/**
     * Set isoSubCountry.
     *
     * @param string $isoSubCountry
     *
     * @return self
     */
    public function setIsoSubCountry($isoSubCountry)
    {
        $this->isoSubCountry = $isoSubCountry;
        return $this;
    }
	
	/**
     * Get isoSubCountry.
     *
     * @return string $isoSubCountry
     */
    public function getIsoSubCountry()
    {
        return $this->isoSubCountry;
    }
	
	/**
     * Set city.
     *
     * @param string $city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }
	
	/**
     * Get city.
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }
	
    /**
     * Set latitude.
     *
     * @param string|null $latitude
     */
    public function setLatitude($latitude = null)
    {
        $this->location['latitude'] = $latitude;
        return $this;
    }

    /**
     * Set longitude.
     *
     * @param string|null $longitude
     */
    public function setLongitude($longitude = null)
    {
        $this->location['longitude'] = $longitude;
        return $this;
    }

    /**
     * Set timeZone.
     *
     * @param string|null $timeZone
     */
    public function setTimeZone($timeZone = null)
    {
        $this->location['timeZone'] = $timeZone;
        return $this;
    }

    /**
     * Get latitude.
     *
     * @param string|null $latitude
     */
    public function getLatitude($latitude = null)
    {
        return $this->location['latitude'];
    }

    /**
     * Get longitude.
     *
     * @param string|null $longitude
     */
    public function getLongitude($longitude = null)
    {
        return $this->location['longitude'];
    }

    /**
     * Get timeZone.
     *
     * @param string|null $timeZone
     */
    public function getTimeZone($timeZone = null)
    {
        return $this->location['timeZone'];
    }

    /**
     * Set postal.
     *
     * @param string $postal
     *
     * @return self
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
        return $this;
    }
    
    /**
     * Get postal.
     *
     * @return string $postal
     */
    public function getPostal()
    {
        return $this->postal;
    }

}