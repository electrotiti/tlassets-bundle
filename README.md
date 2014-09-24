tlassets-bundle
===============

Dump assets using Node JS and Gulp on Symfony2 project

__WARNING: This bundle is on development, do not use on production.__

### Prerequisites
   * Node JS 
   * NPM 

## Installation

### Gulp and dependencies
In order to install Gulp and his dependencies, execute command this command :
````
php app/console tlassets:gulp:install
````
## Tags Twig

Below an example of Tags that you can use in your Twig

```` Twig
{% style "@MyCustomBundle/Resources/public/less/" filter="less" %}
  <link rel="stylesheet" href="{{ asset(asset_url) }}" type="text/css" />
{% endstyle %}

{% js "@MyCustomBundle/Resources/public/js/" %}
  <script type="application/javascript" src="{{ asset(asset_url) }}" />
{% endjs %}

````

## Generate your assets

#### Install your assets:

````
php app/console assets:install
````
_This command copy your assets from SRC to on web/bundles/_


#### Dump tlassets buffer on cache/ for GULP
````
php app/console tlassets:dump
````
_This command parse your Twig and create a JSON file on the cache directory that will be used by GULP_

#### Compile assets based on Gulp buffer
````
php app/console tlassets:compile
````
_This command retrieves all file buffer and compile the final assets files_
