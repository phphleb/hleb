# HLEB2

[![HLEB LOGO](https://raw.githubusercontent.com/phphleb/hleb/master/public/images/logo.png)](https://github.com/phphleb/hleb)

## PHP Framework

[![version](https://poser.pugx.org/phphleb/hleb/v)](https://packagist.org/packages/phphleb/hleb)
[![Total Downloads](https://poser.pugx.org/phphleb/hleb/downloads)](https://packagist.org/packages/phphleb/hleb)
[![License: MIT](https://img.shields.io/badge/License-MIT%20(Free)-brightgreen.svg)](https://github.com/phphleb/hleb/blob/master/LICENSE)
![PHP](https://img.shields.io/badge/PHP-^8.2-blue)
![build](https://github.com/phphleb/framework/actions/workflows/build.yml/badge.svg?event=push)

Supports PHP 8.2+

A distinctive feature of the framework **HLEB2** is the minimalism of the code and the speed of work.
The choice of this framework allows you to launch a full-fledged product with minimal time costs and appeals to [documentation](https://hleb2framework.ru); it is easy, simple and fast.
At the same time, it solves typical tasks, such as routing, shifting actions to controllers, model support, so, the basic MVC implementation.
This is the very minimum you need to quickly launch an application.

Basic features of the framework:

+ Standard use or asynchronous (RoadRunner, Swoole)
+ MVC(ADR) or modular development
+ PSR support
+ Original router
+ Service container
+ Events
+ Logging
+ Dependency injection
+ Caching
+ Console commands
+ Class autoloader (optional)
+ Twig template engine (optional)
+ Debug panel
+ Creating an [API](https://github.com/phphleb/api-multitool)
+ [Registration module](https://github.com/phphleb/hlogin)
+ [Mutexes](https://github.com/phphleb/conductor)
+ [Admin panel](https://github.com/phphleb/adminpan)


The framework code has been thoroughly [tested](https://github.com/phphleb/tests).

Installation
-----------------------------------
To start the framework HLEB2
1. Download the folder with the project from its original location.

Using Composer:
```bash
$ composer create-project phphleb/hleb
```
2. Assign the address of the resource to the "public" subdirectory.
3. Establish the rights to allow changes for web server for the "storage" folder and all folders and files within it.


Customization
-----------------------------------

Files with project settings are located in the `config` folder of the installed project.

Attention! Initially in the file `/config/common.php`
(in the absence of `/config/common-local.php`)
the **debug** setting is set to _true_.
This means that debug mode is active, which needs to be disabled for a public project.

Greetings
-----------------------------------
Project routes are compiled by the developer in the "/routes/map.php" file.

```php
Route::get('/', 'Hello, world!');
```

Instructions for use
-----------------------------------

[Link to documentation](https://hleb2framework.ru) 

The documentation site was created using the HLEB2 framework.

----------------------

[![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Use%20a%20fast%20and%20simple%20PHP%207-8%20microframework&url=https://github.com/phphleb/hleb&via=phphleb&hashtags=php8.2,framework,developers)  [![Telegram](https://img.shields.io/badge/-Telegram-black?color=white&logo=telegram&style=social)](https://t.me/phphleb)
