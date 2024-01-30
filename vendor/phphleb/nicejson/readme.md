NICEJSON
=====================

[![HLEB1](https://img.shields.io/badge/HLEB-1-olive)](https://github.com/phphleb/hleb) [![HLEB2](https://img.shields.io/badge/HLEB-2-darkcyan)](https://github.com/phphleb/hleb) ![PHP](https://img.shields.io/badge/PHP-^8.2-blue) [![License: MIT](https://img.shields.io/badge/License-MIT%20(Free)-brightgreen.svg)](https://github.com/phphleb/hleb/blob/master/LICENSE)

[Previous code](![PHP](https://phphleb/nicejson)) for PHP version < 8.2

###  Convert json to readable form

 Install using Composer:
 ```bash
composer require phphleb/nicejson
 ```
-----------------------------------------

Convert
 ```json
{"example":["first","second"]}
 ```
to
 ```json
{
    "example": [
        "first",
        "second"
    ]
}
 ```

 ```php
$data = '{"example":["first","second"]}'; // string json
file_put_contents('/path/to/result/json/file/', (new \Phphleb\Nicejson\JsonConverter())->get($data));
 ```
or

 ```php
$data = ["example"=>["first","second"]]; // array
file_put_contents('/path/to/result/json/file/', (new \Phphleb\Nicejson\JsonConverter())->get($data));
 ```
or

 ```php
$data = (object) ["example"=>["first","second"]]; // object
file_put_contents('/path/to/result/json/file/', (new \Phphleb\Nicejson\JsonConverter())->get($data));
 ```

add flag to json_encode(...)

 ```php
use Phphleb\Nicejson\JsonConverter;
$jsonConverterObject = new JsonConverter(JSON_FORCE_OBJECT);
 ```

