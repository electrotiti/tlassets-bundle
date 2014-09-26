tlassets-bundle
===============

[![Build Status](https://travis-ci.org/electrotiti/tlassets-bundle.svg?branch=master)](https://travis-ci.org/electrotiti/tlassets-bundle)

TlAssetsBundle is an alternative to Assetic, it build your assets using NodeJS, GULP and Twig on a Symfony2 project.

__WARNING: For this moment this bundle is under development, do not use on production.__

## How it works ?
__1:/__ In your Twig template, use the tags "style" and "js" to give assets source path and optional filters

__2:/__ The command "tlassets:dump", search all Twig template with these tags and create a Json buffer file in cache that describe the location of source path, the destination filename and some others options

__3:/__ The command "tlassets:compile" read the buffer files previously created and compile the final assets with GULP

The goal of these two steps (and not one step like assetic does), is to compile only your assets without parsing all your Twig template when you just modify your assets file.


## Prerequisites
First you have to install in your environment this tools: 
   * Node JS 
   * NPM 

## Installation

### Gulp and dependencies
In order to install Gulp and his dependencies, execute command this command :
````
php app/console tlassets:gulp:install
````
## Tags Twig

Below an example of tags that you can use in your Twig

```` Twig
{% style "@MyCustomBundle/Resources/public/less/" filter="less" %}
  <link rel="stylesheet" href="{{ asset(asset_url) }}" type="text/css" />
{% endstyle %}

{% js "@MyCustomBundle/Resources/public/js/" %}
  <script type="application/javascript" src="{{ asset(asset_url) }}" />
{% endjs %}

````

## Install your assets:

````
php app/console assets:install
````
_This command (from Symfony), copy your assets from the folder : "src/" to the folder : "web/bundles/"_

## Quick assets compilation

To compile quickly your assets you just have to do this:
````
php app/console assets:dump
````
_This command do a tlassets:flush, a tlassets:parse and a tlassets:compile"_

## Generate your assets step by step

Alternatively (and it's why this bundle is different of Assetic), each task on assets construction can be do separately

### Flush previous compilation
````
php app/console tlassets:flush
````
_This command remove cache and assets previously generated in order to have a clean environment_

### Parse Twig template
````
php app/console tlassets:parse
````
_This command parse your Twig template and create a JSON file on the cache directory that will be used by GULP_

### Compile assets
````
php app/console tlassets:compile
````
_This command retrieves all file buffer previously created with the parsing command and compile the final assets files_

