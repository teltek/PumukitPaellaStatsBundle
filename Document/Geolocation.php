<?php

namespace Pumukit\PaellaStatsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class Geolocation
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $continent;

    /**
     * @MongoDB\Field(type="string")
     */
    private $continentCode;

    /**
     * @MongoDB\Field(type="string")
     */
    private $country;

    /**
     * @MongoDB\Field(type="string")
     */
    private $countryCode;

    /**
     * @MongoDB\Field(type="string")
     */
    private $subCountry;

    /**
     * @MongoDB\Field(type="string")
     */
    private $subCountryCode;

    /**
     * @MongoDB\Field(type="string")
     */
    private $city;

    /**
     * @MongoDB\Field(type="raw")
     */
    private $location = ['latitude' => '', 'longitude' => '', 'timeZone' => ''];

    /**
     * @MongoDB\Field(type="string")
     */
    private $postal;

    public function getId()
    {
        return $this->id;
    }

    public function setContinent(string $continent): self
    {
        $this->continent = $continent;

        return $this;
    }

    public function getContinent(): string
    {
        return $this->continent;
    }

    public function setContinentCode(string $continentCode): self
    {
        $this->continentCode = $continentCode;

        return $this;
    }

    public function getContinentCode(): string
    {
        return $this->continentCode;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setSubCountry(string $subCountry): self
    {
        $this->subCountry = $subCountry;

        return $this;
    }

    public function getSubCountry(): string
    {
        return $this->subCountry;
    }

    public function setSubCountryCode(string $subCountryCode): self
    {
        $this->subCountryCode = $subCountryCode;

        return $this;
    }

    public function getSubCountryCode(): string
    {
        return $this->subCountryCode;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): self
    {
        return $this->city;
    }

    public function setLatitude(?string $latitude = null): self
    {
        $this->location['latitude'] = $latitude;

        return $this;
    }

    public function setLongitude(?string $longitude = null): self
    {
        $this->location['longitude'] = $longitude;

        return $this;
    }

    public function setTimeZone(?string $timeZone = null): self
    {
        $this->location['timeZone'] = $timeZone;

        return $this;
    }

    public function getLatitude(): string
    {
        return $this->location['latitude'];
    }

    public function getLongitude(): string
    {
        return $this->location['longitude'];
    }

    public function getTimeZone(): string
    {
        return $this->location['timeZone'];
    }

    public function setPostal(string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getPostal(): string
    {
        return $this->postal;
    }
}
