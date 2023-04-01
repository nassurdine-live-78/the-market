<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class Cart extends Model
    {

        private static $lastInsertedId;

        public static function getAll()
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM cart");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllByUserId(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM cart WHERE userid = :id");

            $statement->bindParam(':id',  $id,   \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllByUserIdSorted(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM cart WHERE userid = :id ORDER BY addeddate DESC");

            $statement->bindParam(':id',  $id,   \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function emptyByUserId(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("DELETE FROM cart WHERE userid = :id");

            $statement->bindParam(':id',  $id,   \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllByUserEmail(string $email)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM `cart` INNER JOIN `user` ON `cart`.`userid` = `user`.`id` WHERE `user`.`email` = :email");

            $statement->bindParam(':email', $email,  \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function save(array $product)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO cart(`userid`, `productid`, `quantity`, `addeddate`) VALUES(:userid, :productid, :quantity, :addeddate) ON DUPLICATE KEY UPDATE `quantity`= :quantity");

            $statement->bindParam(':userid',    $product["userid"] ,    \PDO::PARAM_STR);

            $statement->bindParam(':productid', $product["productid"],  \PDO::PARAM_STR);

            $statement->bindParam(':quantity',  $product["quantity"],   \PDO::PARAM_STR);

            $statement->bindParam(':addeddate', $product["addeddate"],  \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function remove(array $product)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("DELETE FROM cart WHERE `userid` = :userid AND `productid` = :productid;");

            $statement->bindParam(':userid',    $product["userid"] ,    \PDO::PARAM_STR);

            $statement->bindParam(':productid', $product["productid"],  \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }
    }