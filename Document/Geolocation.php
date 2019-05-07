<?php

namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

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
     * @MongoDB\Field(type="string")
     */
    private $continent;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $continentCode;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $country;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $countryCode;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $subCountry;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $subCountryCode;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $city;

    /**
     * @var string
     *
     * @MongoDB\Field(type="raw")
     */
    private $location = array('latitude' => '', 'longitude' => '', 'timeZone' => '');

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $postal;

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
     * Set continentCode.
     *
     * @param string $continentCode
     *
     * @return self
     */
    public function setContinentCode($continentCode)
    {
        $this->continentCode = $continentCode;

        return $this;
    }

    /**
     * Get continentCode.
     *
     * @return string $continentCode
     */
    public function getContinentCode()
    {
        return $this->continentCode;
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
     * Set countryCode.
     *
     * @param string $countryCode
     *
     * @return self
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode.
     *
     * @return string $countryCode
     */
    public function getCountryCode()
    {
        return $this->countryCode;
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
     * Set subCountryCode.
     *
     * @param string $subCountryCode
     *
     * @return self
     */
    public function setSubCountryCode($subCountryCode)
    {
        $this->subCountryCode = $subCountryCode;

        return $this;
    }

    /**
     * Get subCountryCode.
     *
     * @return string $subCountryCode
     */
    public function getSubCountryCode()
    {
        return $this->subCountryCode;
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
