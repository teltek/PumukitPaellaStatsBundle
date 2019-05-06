PuMuKIT Paella Stats Bundle
==========================

Bundle based on [Symfony](http://symfony.com/) to work with the [PuMuKIT Video Platform](https://github.com/campusdomar/PuMuKIT2/blob/2.1.x/README.md) and [Paella Player](https://github.com/polimediaupv/paella).


Installation
------------

Steps 1 and 2 requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.


### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require teltek/pumukit-paella-stats-bundle dev-master
```


### Step 2: Install the Bundle

Install the bundle by executing the following line command. This command updates the Kernel to enable the bundle (app/AppKernel.php) and loads the routing (app/config/routing.yml) to add the bundle routes\.

```bash
$ php app/console pumukit:install:bundle Pumukit/PaellaStatsBundle/PumukitPaellaStatsBundle
```

### Step 3: Update assets

```bash
$ php app/console cache:clear
$ php app/console cache:clear --env=prod
$ php app/console assets:install
```


### NOTE: You need to install the [bundle for the Maxmind GeoIP2 API](https://github.com/gpslab/geoip2) 
###### 1. Add GpsLabGeoIP2Bundle to your application kernel

```bash
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new GpsLab\Bundle\GeoIP2Bundle\GpsLabGeoIP2Bundle(),
        // ...
    );
}
```

###### 2. Configure the bundle

```bash
gpslab_geoip:
    # Path to download GeoIP database.
    # It's a default value. You can change it.
    cache: '%kernel.cache_dir%/GeoLite2-City.mmdb'

    # URL for download new GeoIP database.
    # It's a default value. You can change it.
    url: 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz'

    # Get model data in this locale
    # It's a default value. You can change it.
    locales: [ '%locale%' ]
```


### OPTIONAL: Load example statistics data

```bash
    php app/console pumukit:init:paellastatsexample
```