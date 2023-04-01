<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class Product extends Model
    {

        public static function create(string $upc, string $name, string $filename, float $price, string $description, string $category)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO product(name, imageuri, upc, price, description, instock, categoryid) VALUES (:name, :filename, :upc, :price, :description, 0, :category)");

            $statement->bindParam(':upc',           $upc,           \PDO::PARAM_STR);
            $statement->bindParam(':name',          $name,          \PDO::PARAM_STR);
            $statement->bindParam(':filename',      $filename,      \PDO::PARAM_STR);
            $statement->bindParam(':price',         $price,         \PDO::PARAM_STR);
            $statement->bindParam(':description',   $description,   \PDO::PARAM_STR);
            $statement->bindParam(':category',      $category,      \PDO::PARAM_INT);

            return $statement->execute();
        }

        public static function getAll(): array
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM product");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function countAll(): int
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT COUNT(*) AS total FROM product");

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result->total;
        }

        public static function getAllPaged(int $offset, int $count): array
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM product LIMIT :offset, :count");

            $statement->bindParam(':offset',    $offset,    \PDO::PARAM_INT);
            $statement->bindParam(':count',     $count,     \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAllByCategoryId(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM product WHERE categoryid = :id");

            $statement->bindParam(':id',     $id,     \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getByUPC(string $upc)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT * FROM product WHERE upc = :upc");

            $statement->bindParam(':upc',     $upc,     \PDO::PARAM_STR);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getStockById(int $id)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT instock FROM product WHERE id = :id");

            $statement->bindParam(':id',    $id,    \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result->instock;
        }

        public static function updateByUPC(string $upc, $name, $price, $description)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE product SET name = :name, price = :price, description = :description WHERE upc = :upc");

            $statement->bindParam(':name',          $name,          \PDO::PARAM_STR);
            $statement->bindParam(':price',         $price,         \PDO::PARAM_STR);
            $statement->bindParam(':description',   $description,   \PDO::PARAM_STR);
            $statement->bindParam(':upc',           $upc,           \PDO::PARAM_STR);

            $statement->execute();
        }

        public static function updateById(int $id, $name, $price, $description)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE product SET name = :name, price = :price, description = :description WHERE id = :id");

            $statement->bindParam(':name',          $name,          \PDO::PARAM_STR);
            $statement->bindParam(':price',         $price,         \PDO::PARAM_STR);
            $statement->bindParam(':description',   $description,   \PDO::PARAM_STR);
            $statement->bindParam(':id',            $id,            \PDO::PARAM_INT);

            $statement->execute();
        }

        public static function subtractStockById(int $id, int $count)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE product SET instock = instock - :count WHERE id = :id AND instock > 0");

            $statement->bindParam(':count', $count, \PDO::PARAM_INT);
            $statement->bindParam(':id',    $id,    \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function addStockById(int $id, int $count)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE product SET instock = instock + :count WHERE id = :id");

            $statement->bindParam(':count', $count, \PDO::PARAM_INT);
            $statement->bindParam(':id',    $id,    \PDO::PARAM_INT);

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getById(int $id)
        {
            $pdo = self::getDatabase();

            $statement = null;

            // if(gettype($id) == "array")
            // {
            //     $ids = explode(",", $id);
            //     $statement = $pdo->prepare("SELECT * FROM product WHERE id IN ($ids)");
                
            //     $statement->execute();

            //     $result = $statement->fetchAll(\PDO::FETCH_OBJ);
            // }
            // else if(gettype($id) == "int")
            // {
                $statement = $pdo->prepare("SELECT id, name, imageuri, upc, price, description, categoryid FROM product WHERE id = :id");

                $statement->bindParam(':id',     $id,     \PDO::PARAM_INT);
                
                $statement->execute();

                $result = $statement->fetch(\PDO::FETCH_OBJ);
            // }
            // else
            // {
            //     throw new \Exception("Invalid Argument Type in function Product::getById(). ".gettype($id)." given", 500);
            // }

            return $result;
        }
    }