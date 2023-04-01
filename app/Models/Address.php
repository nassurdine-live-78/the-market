<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class Address extends Model
    {
        public static function getAll()
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM `address`");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllByUser(object $user)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM `address` WHERE userid = :userid");

            $statement->bindParam(':userid', $user->id, \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getByUser(object $user)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM `address` WHERE userid = :userid");

            $statement->bindParam(':userid', $user->id, \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getByUserId(int $userId)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM `address` WHERE userid = :userid");

            $statement->bindParam(':userid', $userId, \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function updateByUser(object $user, string $sFullname,string $sCountrycode,  string $sPhone, string $sLineone, string $sLinetwo, string $sZipcode, string $sCity, string $bFullname,string $bCountrycode, string $bLineone, string $bLinetwo, string $bZipcode, string $bCity)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO address (userid, shippingfullname, shippingcountrycode, shippingphone, shippinglineone, shippinglinetwo, shippingzipcode, shippingcity, billingfullname, billingcountrycode, billinglineone, billinglinetwo, billingzipcode, billingcity) VALUES(:userid, :sfullname, :scountrycode, :sphone, :slineone, :slinetwo, :szipcode, :scity, :bfullname, :bcountrycode, :blineone, :blinetwo, :bzipcode, :bcity) ON DUPLICATE KEY UPDATE userid = :userid, shippingfullname = :sfullname, shippingcountrycode = :scountrycode, shippingphone = :sphone, shippinglineone = :slineone, shippinglinetwo = :slinetwo, shippingzipcode = :szipcode, shippingcity = :scity, billingfullname = :bfullname, billingcountrycode = :bcountrycode, billinglineone = :blineone, billinglinetwo = :blinetwo, billingzipcode = :bzipcode, billingcity = :bcity");

            $statement->bindParam(':userid',        $user->id,      \PDO::PARAM_STR);
            
            $statement->bindParam(':sfullname',     $sFullname,     \PDO::PARAM_STR);
            
            $statement->bindParam(':scountrycode',  $sCountrycode,  \PDO::PARAM_STR);
            
            $statement->bindParam(':sphone',        $sPhone,        \PDO::PARAM_STR);
            
            $statement->bindParam(':slineone',      $sLineone,      \PDO::PARAM_STR);
            
            $statement->bindParam(':slinetwo',      $sLinetwo,      \PDO::PARAM_STR);
            
            $statement->bindParam(':szipcode',      $sZipcode,      \PDO::PARAM_STR);
            
            $statement->bindParam(':scity',         $sCity,         \PDO::PARAM_STR);
            
            $statement->bindParam(':bfullname',     $bFullname,     \PDO::PARAM_STR);
            
            $statement->bindParam(':bcountrycode',  $bCountrycode,  \PDO::PARAM_STR);
            
            $statement->bindParam(':blineone',      $bLineone,      \PDO::PARAM_STR);
            
            $statement->bindParam(':blinetwo',      $bLinetwo,      \PDO::PARAM_STR);
            
            $statement->bindParam(':bzipcode',      $bZipcode,      \PDO::PARAM_STR);
            
            $statement->bindParam(':bcity',         $bCity,         \PDO::PARAM_STR);

            $result = $statement->execute();

            return $result;
        }
    }