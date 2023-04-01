<?php

    declare(strict_types=1);

    namespace Joshua\Core;

    abstract class Model
    {
        protected static $db = NULL;

        public static function getDatabase(): ?\PDO
        {
            if(self::$db === NULL)
            {

                $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 4));
                $dotenv->load();
                // try
                // {
                    self::$db = new \PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'].";charset=utf8", $_ENV['DB_USER'], $_ENV['DB_PWD']);
                    self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                // }
                // catch(PDOException $ex)
                // {
                //     echo $ex->getMessage();
                // }
            }

            return self::$db;
        }

        public static function createTable() : void 
        {
            
        }
    }