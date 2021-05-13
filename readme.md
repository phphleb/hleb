
# HLEB
### [![HLEB LOGO](https://raw.githubusercontent.com/phphleb/hleb/f95d0092692c082c1b2b0d96c75dcaf68600b73b/public/images/logo.png)](https://github.com/phphleb/hleb/tree/master)
# PHP Micro-Framework

Requires PHP version 7.0 or higher (including version 8).

[Link to instructions](https://phphleb.ru/ru/v1/) (RU)

Routing > Controllers > Models > Page Builder > Debug Panel

A distinctive feature of the micro-framework HLEB is the minimalism of the code and the speed of work. The choice of this framework allows you to launch a full-fledged product with minimal time costs and appeals to documentation; it is easy, simple and fast.
At the same time, it solves typical tasks, such as routing, shifting actions to controllers, model support, so, the basic MVC implementation. This is the very minimum you need to quickly launch an application. 

Installation
-----------------------------------
To start the mini-framework HLEB 
1. Download the folder with the project from its original location.

Using Composer:
```bash
$ composer create-project phphleb/hleb
```
2. Assign the address of the resource to the "public" subdirectory.
3. Establish the rights to allow changes for web server for the "storage" folder and all folders and files within it.

Upon completion of these steps, you can verify installation by typing the resource address assigned earlier (locally or on a remote server) in the address bar of the browser. If installation is successful, a parked page with the framework logo will be displayed.

List of standard console commands:
```bash
$ cd hleb
```
```bash
$ php console --help
```

Customization
-----------------------------------
Command character constants in the micro-framework HLEB are set in the **start.hleb.php** file. Initially, a file with this name does not exist and must be copied from the **default.start.hleb.php** file in the same project root directory.

Attention! Constant HLEB_PROJECT_DEBUG enables/disables debug mode. Do not use debug mode on a public server.


Routing
-----------------------------------
Project routes are compiled by the developer in the "/routes/main.php" file, other files with routes from the "routes" folder can be inserted (included) into this file, which together constitute a routing map.

Routes are determined by class **Route** methods, the main of which is **get()**. All methods of this class are available and used only in the routing map.

Attention! Route files are cached and should not contain any code containing external data.

```php
Route::get('/', 'Hello, world!');
```

Display the contents of the "/views/index.php" file using the **view()** function (also available in controllers).
```php
Route::get('/', view('index'));
```

This is an example of a more complex-named route. Here, $x and $y values are transferred to the "/views/map/new.php" file, and conditions for the dynamic address are set ("version" and "page" can take different values). You can call a route up by its name using dedicated functions of the framework.
```php
Route::get('/ru/{version}/{page?}/', view('/map/new', ['x' => 0, 'y' => 0]))->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+'])->name('RouteName'); // /ru/.../.../ or /ru/.../

```

or (with **IDE** hints)

```php
Route::get('/ru/{version}/{page?}/', view('/map/new', ['x' => 0, 'y' => 0]))::where(['version' => '[a-z0-9]+', 'page' => '[a-z]+'])::name('RouteName'); // /ru/.../.../ or /ru/.../

```

Groups of routes
-----------------------------------

Methods located before a route or group:

**type()->**, **prefix()->**, **protect()->**, **before()->**, **domain()->**

```php
Route::prefix('/lang/')->before('AuthClassBefore')->getGroup();
  Route::get('/page/', "<h1>Page</h1>"); // /lang/page/
  Route::protect()->type('post')->get('/ajax/', '{"connect":1}'); // /lang/ajax/
Route::endGroup();
```
Methods located after a route or group:

**->where()**, **->after()**

```php
Route::type(['get','post'])->before('ClassBefore')->get('/path/')->controller('ClassController')->after('ClassAfter');

```



Controllers
-----------------------------------
Creating a simple controller with such content:
```php
// File /app/Controllers/TestController.php
namespace App\Controllers;
use App\Models\UserModel;
class TestController extends \MainController
{
    function index($status) {  // $status = 'friends'
      $data = UserModel::getUsersDataByParentId(\Request::get('id'), $status);
      return view('/user/profile', ['contacts' => $data]);
    }
}
```
You can use it in the route map:

```php
Route::get('/profile/{id}/contacts/')->controller('TestController',['friends'])->where(['id' => '[0-9]+']);
```  
or

```php
Route::get('/profile/{id}/contacts/')->controller('TestController@index',['friends'])->where(['id' => '[0-9]+']);
``` 

Replacing class and method calls from url:
```php
Route::get('/example/{class}/{method}/')->controller('<class>Controller@get<method>'); // Converts `site.com/example/all-users/user/` to `AllUsersController@getUser`

```

Models
-----------------------------------
 ```php
// File /app/Models/UserModel.php
namespace App\Models;
class UserModel extends \MainModel
{
   static function getUsersDataByParentId(int $id, string $status) {
     $data = /* ... */ // A query to the database, returning users data.
     return $data;
   }
}
```

Modules
-----------------------------------
For modular development, you need to create the folder 'modules'.

+ /modules
  + /example    
    + /**DefaultModuleController**.php (or 'Controller.php' without specifying the controller in the route)
    + /**content**.php
    + /templates
       + /**origin**.php
```php
Route::get('/test/module/example/')->module('example', 'DefaultModuleController');
``` 

```php
// File /modules/example/DefaultModuleController.php (similar to standard controller)
namespace Modules\Example;
class DefaultModuleController extends \MainController
{
   function index() {
      return view('content');
   }
}
```
```php
// File /modules/example/content.php
includeTemplate('/example/templates/origin');

```


ORM
-----------------------------------
Recommended [phphleb/xdorm](https://github.com/phphleb/xdorm)

Templates
-----------------------------------
```php
// File /resources/views/content.php
includeTemplate('templates/origin', ['title' => 'Short text', 'content' => 'Long text']);
```
```php
// File /resources/views/templates/origin.php
echo $title; // Short text
echo $content; // Long text
// or
echo $this->title; // Short text
echo $this->content; // Long text
```


Page Builder
-----------------------------------
```php
Route::renderMap('index page', ['/parts/header', 'index', '/parts/footer']);
Route::get('/', render('index page'));
```

Optional use of `Twig` template engine
-----------------------------------
```bash
$ composer require "twig/twig:^3.0"
```

```php
Route::get('/template/', view('templates/map.twig', ['variable' => 'value']));
```

Debug Panel
-----------------------------------
```php
WorkDebug::add($debug_data, 'description');
```


-----------------------------------


 [![version](https://poser.pugx.org/phphleb/hleb/v)](https://packagist.org/packages/phphleb/hleb) [![Total Downloads](https://poser.pugx.org/phphleb/hleb/downloads)](//packagist.org/packages/phphleb/hleb) [![License: MIT](https://img.shields.io/badge/License-MIT%20(Free)-brightgreen.svg)](https://github.com/phphleb/hleb/blob/master/LICENSE) ![PHP](https://img.shields.io/badge/PHP-7-blue) ![PHP](https://img.shields.io/badge/PHP-8-blue)  [![Build Status](https://travis-ci.com/phphleb/hleb.svg?branch=master)](https://travis-ci.com/phphleb/hleb) [![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Use%20a%20fast%20and%20simple%20PHP%207-8%20microframework&url=https://github.com/phphleb/hleb&via=phphleb&hashtags=php7,php8,framework,developers)