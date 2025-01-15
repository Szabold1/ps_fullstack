<?php

declare(strict_types=1);

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
        $useDatabase = rand(0, 1) === 1;
        return new UserController($db, $userFileModel, $useDatabase);
    }
}
