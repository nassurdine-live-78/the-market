<?php
    declare(strict_types=1);

    namespace Joshua\Core;

    class View
    {
        public static function render(string $view, array $params = [])
        {
            $file = "../app/Views/$view";

            if(is_readable($file))
            {
                extract($params, EXTR_SKIP);
                require($file);
            }
            else
            {
                throw new \Exception("$file not found", 500);
            }
        }
    }