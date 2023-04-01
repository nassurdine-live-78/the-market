<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class OrderAddress extends Model
    {

        private static $lastInsertedId;

        public static function getIdOfMatching(string $shippingfullname, string $shippingcountrycode, string $shippingphone, string $shippinglineone, string $shippinglinetwo, string $shippingzipcode, string $shippingcity, string $billingfullname, string $billingcountrycode, string $billinglineone, string $billinglinetwo, string $billingzipcode, string $billingcity)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT `id`FROM `orderaddress` WHERE shippingfullname = :sfullname AND shippingcountrycode = :scountrycode AND shippingphone = :sphone AND shippinglineone = :slineone AND shippinglinetwo = :slinetwo AND shippingzipcode = :szipcode AND shippingcity = :scity AND billingfullname = :bfullname AND billingcountrycode = :bcountrycode AND billinglineone = :blineone AND billinglinetwo = :blinetwo AND billingzipcode = :bzipcode AND billingcity = :bcity");

            $statement->bindParam(':sfullname',     $shippingfullname,      \PDO::PARAM_STR);
            $statement->bindParam(':scountrycode',  $shippingcountrycode,   \PDO::PARAM_STR);
            $statement->bindParam(':sphone',        $shippingphone,         \PDO::PARAM_STR);
            $statement->bindParam(':slineone',      $shippinglineone,       \PDO::PARAM_STR);
            $statement->bindParam(':slinetwo',      $shippinglinetwo,       \PDO::PARAM_STR);
            $statement->bindParam(':szipcode',      $shippingzipcode,       \PDO::PARAM_STR);
            $statement->bindParam(':scity',         $shippingcity,          \PDO::PARAM_STR);
            $statement->bindParam(':bfullname',     $billingfullname,       \PDO::PARAM_STR);
            $statement->bindParam(':bcountrycode',  $billingcountrycode,    \PDO::PARAM_STR);
            $statement->bindParam(':blineone',      $billinglineone,        \PDO::PARAM_STR);
            $statement->bindParam(':blinetwo',      $billinglinetwo,        \PDO::PARAM_STR);
            $statement->bindParam(':bzipcode',      $billingzipcode,        \PDO::PARAM_STR);
            $statement->bindParam(':bcity',         $billingcity,           \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return ($result !== false) ? $result->id : -1;
        }

        public static function save(string $shippingfullname, string $shippingcountrycode, string $shippingphone, string $shippinglineone, string $shippinglinetwo, string $shippingzipcode, string $shippingcity, string $billingfullname, string $billingcountrycode, string $billinglineone, string $billinglinetwo, string $billingzipcode, string $billingcity)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO orderaddress(shippingfullname, shippingcountrycode, shippingphone, shippinglineone, shippinglinetwo, shippingzipcode, shippingcity, billingfullname, billingcountrycode, billinglineone, billinglinetwo, billingzipcode, billingcity) VALUES (:sfullname, :scountrycode, :sphone, :slineone, :slinetwo, :szipcode, :scity, :bfullname, :bcountrycode, :blineone, :blinetwo, :bzipcode, :bcity)");

            $statement->bindParam(':sfullname',     $shippingfullname,      \PDO::PARAM_STR);
            $statement->bindParam(':scountrycode',  $shippingcountrycode,   \PDO::PARAM_STR);
            $statement->bindParam(':sphone',        $shippingphone,         \PDO::PARAM_STR);
            $statement->bindParam(':slineone',      $shippinglineone,       \PDO::PARAM_STR);
            $statement->bindParam(':slinetwo',      $shippinglinetwo,       \PDO::PARAM_STR);
            $statement->bindParam(':szipcode',      $shippingzipcode,       \PDO::PARAM_STR);
            $statement->bindParam(':scity',         $shippingcity,          \PDO::PARAM_STR);
            $statement->bindParam(':bfullname',     $billingfullname,       \PDO::PARAM_STR);
            $statement->bindParam(':bcountrycode',  $billingcountrycode,    \PDO::PARAM_STR);
            $statement->bindParam(':blineone',      $billinglineone,        \PDO::PARAM_STR);
            $statement->bindParam(':blinetwo',      $billinglinetwo,        \PDO::PARAM_STR);
            $statement->bindParam(':bzipcode',      $billingzipcode,        \PDO::PARAM_STR);
            $statement->bindParam(':bcity',         $billingcity,           \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function getLastId()
        {
            return self::$lastInsertedId;
        }
    }