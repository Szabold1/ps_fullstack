<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/utils.php';

use Framework\Session;
use Framework\Router;

Session::start();

Router::route();
