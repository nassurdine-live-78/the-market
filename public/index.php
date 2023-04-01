<?php

declare(strict_types=1);

require "../vendor/autoload.php";

error_reporting(E_ALL);
set_error_handler("\\Joshua\\Core\\ErrorManager::errorHandler");
set_exception_handler("\\Joshua\\Core\\ErrorManager::exceptionHandler");

use Joshua\Core\Router;

require "../routes/web.php";

Router::dispatch($_SERVER["QUERY_STRING"]);