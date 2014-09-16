tlassets-bundle
===============

Dump your assets using Gulp on Symfony2 project

## Installation

First you have to install this software on your environnement

    * NPM (Ex: sudo apt-get install npm)
    * Node JS (Ex: sudo apt-get install nodejs)

## Tags Twig

```` Twig
    {% style "@MyCustomBundle/Resources/public/less/" filter="less" %}
        <link rel="stylesheet" href="{{ asset(asset_url) }}" type="text/css" />
    {% endstyle %}

    {% js "@MyCustomBundle/Resources/public/js/" %}
        <script type="application/javascript" src="{{ asset(asset_url) }}" />
    {% endjs %}

````