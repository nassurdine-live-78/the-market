<?php
    declare(strict_types=1);

    namespace Joshua\Core;

    abstract class Controller
    {

        protected function boot(\Joshua\HTTP\Request $request)
        {
            session_start();
            //TODO
        }
    
        protected function mount(\Joshua\HTTP\Request $request)
        {
            //TODO
        }
    
        protected function unmount(\Joshua\HTTP\Request $request)
        {
            //TODO
        }
    
        protected function shutdown(\Joshua\HTTP\Request $request)
        {
            //TODO
        }

        public function __call($name, $args)
        {
            $method_name    = $name . "Activity";
            
            if(method_exists($this, $method_name))
            {
                $reflection     = new \ReflectionMethod(get_class($this), $method_name);
                $parameters     = $reflection->getParameters();
                $arguments      = $args;

                $request        = new \Joshua\HTTP\Request();

                $request->setActivity($method_name);

                $this->boot($request);
                $this->mount($request);

                if(count($parameters) > 0 && $parameters[0]->getType()->getName() === \Joshua\HTTP\Request::class)
                {
                    array_unshift($arguments, $request);
                }

                call_user_func_array(array($this, $method_name), $arguments);

                $this->unmount($request);
                $this->shutdown($request);
            }
            else
            {
                throw new \Exception("Method $method_name not found in controller {get_class($this)}", 500);
            }
        }
    }