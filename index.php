<?php

require __DIR__ . '/vendor/autoload.php';

use Framework\Session;
use Framework\Router;

Session::start();

Router::route();
