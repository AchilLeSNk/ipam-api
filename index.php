<?php

require_once "vendor/autoload.php";

$api = new \core\IPAM();

$api->getSubnetByCidr("192.168.0.0/28");

