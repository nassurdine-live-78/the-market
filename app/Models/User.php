<?php

    namespace App\Models;

    use Joshua\Core\Model;

    class User extends Model
    {

        private static $lastInsertedId;

        public static function getAll()
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT `id`, `email`, `createdat` FROM user_type");

            $statement->execute();

            $result = $statement->fetchAll(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getByEmail(string $email)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT `id`, `email`, `password`, `usertype`, `createdat` FROM user WHERE email = '$email' AND usertype = 'CUSTOMER'");

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function getAdminByEmail(string $email)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("SELECT `id`, `email`, `password`, `createdat` FROM user WHERE email = '$email' AND usertype = 'ADMINISTRATOR'");

            $statement->execute();

            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result;
        }

        public static function save(string $email, string $passwordHash)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("INSERT INTO user(`email`, `password`, `usertype`) VALUES(:email, :pass, 'CUSTOMER')");

            $statement->bindParam(':email', $email, \PDO::PARAM_STR);

            $statement->bindParam(':pass', $passwordHash, \PDO::PARAM_STR);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function updatePasswordById(int $id, string $hashedPassword)
        {
            $pdo = self::getDatabase();

            $statement = $pdo->prepare("UPDATE user SET `password` = :pwd WHERE `id` = :id");

            $statement->bindParam(':pwd', $hashedPassword, \PDO::PARAM_STR);

            $statement->bindParam(':id', $id, \PDO::PARAM_INT);

            $result = $statement->execute();

            self::$lastInsertedId = $pdo->lastInsertId();

            return $result;
        }

        public static function lastInsertedId()
        {
            return self::$lastInsertedId;
        }
    }