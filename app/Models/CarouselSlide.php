<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class CarouselSlide extends Model
    {
        public static function getAll()
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM carousel");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function update(array $filenames)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare('INSERT INTO carousel(imageurl, alternativetext, slidetext) VALUES(:url, :alt, :text)');

            $result = FALSE;
        
            for($i = 0; $i < count($filenames); $i++)
            {
                $emptyString = "test";
                $statement->bindParam(":url",     $filenames[$i],   \PDO::PARAM_STR);
                $statement->bindParam(":alt",     $emptyString,     \PDO::PARAM_STR);
                $statement->bindParam(":text",    $emptyString,     \PDO::PARAM_STR);
                if(!$statement->execute())
                {
                    $result = TRUE;
                }
            }

            return $result;
        }
    }