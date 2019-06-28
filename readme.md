# HLEB
### ![HLEB LOGO](https://phphleb.ru/logo.gif)
# PHP Micro-Framework

Requires PHP version 7.0 or higher.

[Link to instructions](https://phphleb.ru/ru/v1/) (RU)

Routing > Controllers > Models > Page Builder > Debug Panel

A distinctive feature of the micro-framework HLEB is the minimalism of the code and the speed of work. The choice of this framework allows you to launch a full-fledged product with minimal time costs and appeals to documentation; it is easy, simple and fast.
At the same time, it solves typical tasks, such as routing, shifting actions to controllers, model support, so, the basic MVC implementation. This is the very minimum you need to quickly launch an application. 

Installation
-----------------------------------
To start the mini-framework HLEB 
1. Download the folder with the project from its original location.

Using Composer:
```html
$ composer create-project phphleb/hleb
```
2. Assign the address of the resource to the "public" subdirectory.
3. Establish the rights to allow changes for all users for the "storage" folder and all folders and files within it.

Upon completion of these steps, you can verify installation by typing the resource address assigned earlier (locally or on a remote server) in the address bar of the browser. If installation is successful, a parked page with the framework logo will be displayed.


Customization
-----------------------------------
Command character constants in the micro-framework HLEB are set in the **start.hleb.php** file. Initially, a file with this name does not exist and must be copied from the **default.start.hleb.php** file in the same project root directory.

Attention! Constant HLEB_PROJECT_DEBUG enables / disables debug mode. Do not use debug mode on a public server.


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
Route::get('/ru/{version}/{page?}/', view('/map/new', ['x' => 59.9, 'y' => 30.3]))->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+'])->name('RouteName'); // /ru/.../.../ or /ru/.../

```


Groups of routes
-----------------------------------

Methods located before a route or group:

**type()->**, **prefix()->**, **protect()->**, **before()->**

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
use Hleb\Constructor\Handlers\Request;
class TestController extends \MainController
{
    function index($value) // $value = 'friends'
    {
     $data = UserModel::getData(['id' => Request::get('id'), 'join' => $value]);
     return view('/user/profile', ['data' => $data]);
    }
}
```
You can use it in the route map:

```php
Route::get('/profile/{id}/')->controller('TestController',['friends'])->where(['id' => '[0-9]+']);
```  
or

```php
Route::get('/profile/{id}/')->controller('TestController@index',['friends'])->where(['id' => '[0-9]+']);
``` 


Models
-----------------------------------
 ```php
// File /app/Models/UserModel.php
namespace App\Models;
class UserModel extends \MainModel
{
   static function getData($params)
   {
     $data = /* ... */ // A query to the database, returning an array of user data.
     return $data;
   }
}
```

ORM
-----------------------------------
Recommended [phphleb/xdorm](https://github.com/phphleb/xdorm)

Templates
-----------------------------------
```php
// File /resources/views/content.php
includeTemplate('templates/name', ['p1'=>'data1', 'p2'=>'data2']);
```
```php
// File /resources/views/templates/name.php
echo $p1; // data1
echo $p2; // data2
```


Page Builder
-----------------------------------
```php
Route::renderMap('index page', ['/parts/header', 'index', '/parts/footer']);
Route::get('/', render('index page'));
```

Debug Panel
-----------------------------------
```php
\Hleb\Main\WorkDebug::add($debug_data, 'description');
```

License
-----------------------------------
Free ([MIT](https://github.com/phphleb/hleb/blob/master/LICENSE) License) 


