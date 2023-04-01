<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class Category extends Model
    {
        public static function getAll()
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM category");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getByURI(string $uri)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM category WHERE uri = :uri");

            $statement->bindParam(":uri",   $uri,   \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getByGUID(string $guid)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM category WHERE guid = :guid");

            $statement->bindParam(":guid",   $guid,   \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function create(string $name, string $uri, string $guid)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO category(name, uri, guid) VALUES (:name, :uri, :guid)");

            $statement->bindParam(":name",  $name,  \PDO::PARAM_STR);
            $statement->bindParam(":uri",   $uri,   \PDO::PARAM_STR);
            $statement->bindParam(":guid",  $guid,  \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function updateByGUID(string $guid, string $name, string $uri)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE category SET name = :name, uri = :uri WHERE guid = :guid");

            $statement->bindParam(":name",  $name,  \PDO::PARAM_STR);
            $statement->bindParam(":uri",   $uri,   \PDO::PARAM_STR);
            $statement->bindParam(":guid",  $guid,  \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function deleteByGUID(string $guid)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("DELETE FROM category WHERE guid = :guid");

            $statement->bindParam(":guid",  $guid,  \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }
    }