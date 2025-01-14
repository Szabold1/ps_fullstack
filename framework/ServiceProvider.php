<?php

namespace Framework;

use Controllers\UserController;
use Models\UserFileModel;

class ServiceProvider
{
    // instantiate the user controller and return it
    public static function getUserController(): UserController
    {
        $config = require Helper::basePath('app/config/db.php');
        $db = new Database($config);
        $userFileModel = new UserFileModel(Helper::basePath('data/users.json'));
        return new UserController($db, $userFileModel);
    }
}
