<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class Order extends Model
    {

        private static $lastInsertedId;

        public static function getAll()
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT id, ordernum, userid, addressid, shippingcost, status, tracknum, placedat FROM orders ORDER BY placedat DESC");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllPendingPaged(int $offset, int $count) :array
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT id, ordernum, userid, addressid, shippingcost, status, tracknum, placedat FROM orders WHERE status = 'PENDING' ORDER BY placedat DESC LIMIT :offset, :count");

            $statement->bindParam(':offset',    $offset,    \PDO::PARAM_INT);
            $statement->bindParam(':count',     $count,     \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllProcessingPaged(int $offset, int $count) :array
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT id, ordernum, userid, addressid, shippingcost, status, tracknum, placedat FROM orders WHERE status = 'PROCESSING' ORDER BY placedat DESC LIMIT :offset, :count");

            $statement->bindParam(':offset',    $offset,    \PDO::PARAM_INT);
            $statement->bindParam(':count',     $count,     \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllShippedPaged(int $offset, int $count) :array
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT id, ordernum, userid, addressid, shippingcost, status, tracknum, placedat FROM orders WHERE status = 'SHIPPED' ORDER BY placedat DESC LIMIT :offset, :count");

            $statement->bindParam(':offset',    $offset,    \PDO::PARAM_INT);
            $statement->bindParam(':count',     $count,     \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllPendingCount() :int
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT COUNT(id) AS count FROM orders WHERE status = 'PENDING' ORDER BY placedat DESC");

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return intval($result->count);
        }

        public static function getAllProcessingCount() :int
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT COUNT(id) AS count FROM orders WHERE status = 'PROCESSING' ORDER BY placedat DESC");

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return intval($result->count);
        }

        public static function getAllShippedCount() :int
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT COUNT(id) AS count FROM orders WHERE status = 'SHIPPED' ORDER BY placedat DESC");

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return intval($result->count);
        }

        public static function getAllByUserId(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT id, ordernum, userid, addressid, status, tracknum FROM orders WHERE userid = :id ORDER BY placedat DESC");

            $statement->bindParam(':id', $id, \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getDetailsByUserAndOrder(int $id, string $ordernum)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT orders.id, ordernum, addressid, shippingcost, status, tracknum, placedat, shippingfullname, shippingcountrycode, shippingphone, shippinglineone, shippinglinetwo, shippingzipcode, shippingcity, billingfullname, billingcountrycode, billinglineone, billinglinetwo, billingzipcode, billingcity FROM orders INNER JOIN orderaddress ON `orders`.`addressid` = `orderaddress`.`id` WHERE userid = :id AND ordernum = :ordernum");

            $statement->bindParam(':id',        $id,        \PDO::PARAM_INT);

            $statement->bindParam(':ordernum',  $ordernum,  \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getDetailsByNum(string $ordernum)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT orders.id, ordernum, addressid, shippingcost, status, tracknum, placedat, shippingfullname, shippingcountrycode, shippingphone, shippinglineone, shippinglinetwo, shippingzipcode, shippingcity, billingfullname, billingcountrycode, billinglineone, billinglinetwo, billingzipcode, billingcity FROM orders INNER JOIN orderaddress ON `orders`.`addressid` = `orderaddress`.`id` WHERE ordernum = :ordernum");

            $statement->bindParam(':ordernum',  $ordernum,  \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllItemsByOrderId(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT `name`, `imageuri`, `upc`, `unitprice`, `quantity`, `description` FROM `product` INNER JOIN `orderitems` ON `product`.`id` = `orderitems`.`itemid` WHERE `orderitems`.`orderid` = :orderid");

            $statement->bindParam(':orderid', $id, \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function create(string $ordernum, int $userid, int $addressid)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO orders(`ordernum`, `userid`, `addressid`) VALUES(:ordernum, :userid, :addressid)");

            $statement->bindParam(':ordernum',  $ordernum,  \PDO::PARAM_STR);

            $statement->bindParam(':userid',    $userid,    \PDO::PARAM_INT);

            $statement->bindParam(':addressid', $addressid, \PDO::PARAM_INT);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function addToOrder(int $orderid, array $item)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO orderitems(`orderid`, `itemid`, `quantity`, `unitprice`) VALUES(:orderid, :itemid, :quantity, :unitprice)");

            $statement->bindParam(':orderid',   $orderid ,    \PDO::PARAM_STR);

            $statement->bindParam(':itemid',    $item["productid"],  \PDO::PARAM_INT);

            $statement->bindParam(':quantity',  $item["quantity"],   \PDO::PARAM_INT);

            $statement->bindParam(':unitprice', $item["price"],  \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function updateStatusByOrderNum(string $status, string $ordernum)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE orders SET status = :status WHERE ordernum = :ordernum");

            $statement->bindParam(':status',    $status ,   \PDO::PARAM_STR);
            $statement->bindParam(':ordernum',  $ordernum , \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function updateTrackingByOrderNum(string $tracknum, string $ordernum)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE orders SET tracknum = :tracknum WHERE ordernum = :ordernum");

            $statement->bindParam(':tracknum',   $tracknum, \PDO::PARAM_STR);
            $statement->bindParam(':ordernum',   $ordernum, \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function getLastId()
        {
            return self::$lastInsertedId;
        }
    }