PuMuKIT Paella Stats Bundle
==========================

Bundle based on [Symfony](http://symfony.com/) to work with the [PuMuKIT Video Platform](https://github.com/pumukit/PuMuKIT/blob/master/README.md) and [Paella Player](https://github.com/polimediaupv/paella).


Installation
------------

Steps 1 and 2 requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require teltek/pumukit-paella-stats-bundle 2.0.x-dev
```

### Step 2: Install the Bundle

Add the following line on AppKernel.php

```
public function registerBundles()
{
    return array(
        // ...
        new Pumukit\PaellaStatsBundle\PumukitPaellaStatsBundle()
        new GpsLab\Bundle\GeoIP2Bundle\GpsLabGeoIP2Bundle(),
        // ...
    );
}
```

Add the following lines on routing.yml

```bash
pumukit_paella_stats:
    resource: "@PumukitPaellaStatsBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 3: Configure the bundle

PaellaStats have a hard dependency of [bundle for the Maxmind GeoIP2 API](https://github.com/gpslab/geoip2). It's a composer requiement, and you need
configure it to use.
```
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

### Step 4: Update assets

```bash
$ php app/console cache:clear
$ php app/console cache:clear --env=prod
$ php app/console assets:install
```

### OPTIONAL: Load example statistics data

```bash
    php app/console pumukit:init:paellastatsexample
```
