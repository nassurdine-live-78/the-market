<?php

    namespace Joshua\Core;

    class ErrorManager
    {
        public static function errorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
        {
            if(error_reporting() !== 0)
            {
                throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
                return FALSE;
            }

            return TRUE;
        }

        public static function exceptionHandler(\Throwable $exception): void
        {
    
            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 4));
            $dotenv->load();
            $dotenv->required('DEBUG')->isBoolean();
    
            $code = $exception->getCode();
            
            if(gettype($code) === "int") http_response_code($code);

            if(getenv('DEBUG'))
            {
                
                echo "<h1>Fatal error</h1>";
                echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
                echo "<p>Message: '" . $exception->getMessage() . "'</p>";
                echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
                echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";

                View::render("$code.terminal.phtml");
            }
            else
            {
                $logfile = dirname(dirname(dirname(dirname(__DIR__)))) . "/logs/" . date("Y-m-d") . ".txt";

                ini_set("error_log", $logfile);

                $message  = "--Uncaught exception: '" . get_class($exception) . "'\n";
                $message .= "Message: '" . $exception->getMessage() . "'\n";
                $message .= "Stack trace:" . $exception->getTraceAsString() . "\n";
                $message .= "Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "\n\n";

                error_log($message);

                View::render("$code.terminal.phtml");

            }
        }
    }

    