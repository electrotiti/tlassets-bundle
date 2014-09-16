tlassets-bundle
===============

Dump assets using Node JS and Gulp on Symfony2 project

__WARNING: This bundle is on development, do not use on production.__

## Intro
This bundle is developed to manage your assets (JS and CSS for the moment), using Gulp on a Symfony project. 

### Prerequisites (Unix only):
   * Node JS 
   * NPM 

## Installation

### Gulp and dependencies
In order to install Gulp and his dependencies, execute command this command :
````
php app/console tlassets:gulp:install
````
## Tags Twig

Below an example of Tags :

```` Twig
{% style "@MyCustomBundle/Resources/public/less/" filter="less" %}
  <link rel="stylesheet" href="{{ asset(asset_url) }}" type="text/css" />
{% endstyle %}

{% js "@MyCustomBundle/Resources/public/js/" %}
  <script type="application/javascript" src="{{ asset(asset_url) }}" />
{% endjs %}

````

## Dump your assets

Install your assets:

````
php app/console assets:install
````

Dump tlassets buffer on cache/ for GULP
````
php app/console tlassets:dump
````

Compile assets based on Gulp buffer
````
php app/console tlassets:dump
````

