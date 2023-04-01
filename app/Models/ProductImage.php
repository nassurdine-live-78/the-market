<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class ProductImage extends Model
    {

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