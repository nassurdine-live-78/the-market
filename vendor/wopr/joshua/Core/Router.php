<?php

    namespace Joshua\Core;

    class Router
    {

        private static array $routes            = [];

        private static array $args              = [];

        private static ?string $matchedRoute    = NULL;

        public static function dispatch(string $route): void
        {
            if(self::match($route))
            {
                if(class_exists(self::$routes[self::$matchedRoute]["controller"]))
                {
                    $controller_name    = self::$routes[self::$matchedRoute]["controller"];
                    $activity_name        = self::$routes[self::$matchedRoute]["activity"];
                    $controller = new $controller_name();

                    if(method_exists($controller, $activity_name."Activity"))
                    {
                        $method_name    = $activity_name."Activity";
                        $reflection     = new \ReflectionMethod(get_class($controller), $method_name);
                        $parameters     = $reflection->getParameters();

                        if(self::methodDefinitionIsWellFormed($reflection))
                        {
                            $params     = [];
                            $success    = TRUE;
                            $firstrun   = TRUE;

                            foreach($parameters as $parameter)
                            {
                                if(isset(self::$args[$parameter->getName()]))
                                {
                                    array_push($params, self::$args[$parameter->getName()]);
                                }
                                else if($parameter->isDefaultValueAvailable())
                                {
                                    array_push($params, $parameter->getDefaultValue());
                                }
                                else if($firstrun)
                                {
                                    $firstrun           = FALSE;
                                    $missingArgument    = FALSE;

                                    if($parameter->hasType())
                                    {
                                        if($parameter->getType()->getName() !== \Joshua\HTTP\Request::class)
                                        {
                                            $missingArgument = TRUE;
                                        }
                                    }
                                    else
                                    {
                                        $missingArgument = TRUE;
                                    }

                                    if($missingArgument)
                                    {
                                        $success = FALSE;
                                        throw new \Exception("Missing parameter for argument ".(($parameter->hasType()) ? $parameter->getType()->getName()." " : "")."$".$parameter->getName()." to method $controller_name->$method_name()", 500);
                                        break;
                                    }
                                }
                                else
                                {
                                    $success = FALSE;
                                    throw new \Exception("Missing parameter for argument ".(($parameter->hasType()) ? $parameter->getType()->getName()." " : "")."$".$parameter->getName()." to method $controller_name->$method_name()", 500);
                                    break;
                                }
                            }

                            if($success)
                            {
                                call_user_func_array([$controller, $activity_name], $params);
                            }
                        }
                        else
                        {
                            throw new \Exception("Malformed method definition for $controller_name->$method_name()", 500);
                        }
                    }
                    else
                    {
                        throw new \Exception("Method {$activity_name}Activity() not found in class $controller_name", 500);
                    }
                }
                else
                {
                    throw new \Exception("Controller ".self::$routes[self::$matchedRoute]["controller"]." not found or not properly defined", 500);
                }
            }
            else
            {
                throw new \Exception("Route $route not found", 404);
            }
        }

        public static function methodDefinitionIsWellFormed(\ReflectionMethod $reflection): bool
        {
            $parameters         = $reflection->getParameters();
            $hasDefaultValue    = FALSE;

            foreach($parameters as $parameter)
            {
                if($parameter->isDefaultValueAvailable() && !$hasDefaultValue)
                {
                    $hasDefaultValue = TRUE;
                }
                else if($hasDefaultValue)
                {
                    return FALSE;
                }
            }

            return TRUE;
        }

        public static function match(string $url): bool
        {
            foreach(self::$routes as $route => $params)
            {
                if(preg_match("/".$route."/", $url, $matches))
                {
                    self::$matchedRoute = $route;

                    foreach($matches as $key => $value)
                    {
                        if(is_string($key)) self::$args[$key] = $value;
                    }

                    return true;
                }
            }
            return false;
        }

        public static function add(string $route, array $params = array("controller" => "App\\Controllers\\DefaultController", "activity" => "index")): void
        {

            $new_route = preg_replace("/\//", "\/", $route);
            #$new_route = preg_replace("/\{([a-zA-Z0-9]+)\}/", "(?P<\\1>[a-zA-Z0-9\-_]+)", $new_route);
            $new_route = preg_replace("/\{([a-zA-Z0-9_]+):([a-zA-Z0-9_\\\+\-\[\]\^\$\.\(\)\?\|\*\{\}\,]+)\}/", "(?P<\\1>\\2)", $new_route);
            $new_route = "^".$new_route."$";

            self::$routes[$new_route] = $params;

            /*
            $controller     = new $params["controller"]();
            
            $method_name    = $params["activity"] . "Activity";
            $reflection     = new \ReflectionMethod(get_class($controller), $method_name);
            $parameters     = $reflection->getParameters();
            */

        }
    }