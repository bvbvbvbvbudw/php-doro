<?php

class Db {
    private static $pdo;

    public static function getConnection() {
        if (self::$pdo === null) {
            $dotenv = parse_ini_file(__DIR__ . '/.env');
            $host = $dotenv['DB_HOST'];
            $db = $dotenv['DB_DATABASE'];
            $user = $dotenv['DB_USERNAME'];
            $pass = $dotenv['DB_PASSWORD'];
            $port = $dotenv['DB_PORT'];

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }

        return self::$pdo;
    }
}
