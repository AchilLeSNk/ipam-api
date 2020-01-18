# IPAM API

A simple Object Oriented API for 
[IPAM](https://phpipam.net/api/api_documentation/)

## Installation
You can simply Download the Release, and to include the autoloader
```php
require_once '/path/to/your-project/vendor/autoload.php';
```

## Configuration
API utilizes the DotEnv PHP library by Vance Lucas. In the root directory of your application will contain a .env file. 
   
## Basic usage
We need to create application so that we can run it and send the responses back.
```php
$api = new \core\IPAM();
``` 

We can to send request
```php
$api->getSubnetByCidr("192.168.0.0/28");
```