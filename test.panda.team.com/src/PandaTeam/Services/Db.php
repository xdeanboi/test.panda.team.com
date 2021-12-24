<?php

namespace PandaTeam\Services;

use PandaTeam\Exceptions\DbException;

class Db
{
    private static $instance;
    private $pdo;

    private function __construct()
    {
        $option = (require __DIR__ . '/../../settings.php')['db'];
        try {
            $this->pdo = new \PDO('mysql:host=' . $option['host'] . ';dbname=' . $option['dbname'],
                $option['users'],
                $option['password']);

            $this->pdo->exec('SET NAMES UTF8');
        } catch (\PDOException $e) {
            throw new DbException('Ошибка при подключение к БД: ' . $e->getMessage());
        }
    }

    public function query(string $sql, $params = [], string $className = 'stdClass'): ?array
    {
        $sth = $this->pdo->prepare($sql);
        $result = $sth->execute($params);

        if (false === $result) {
            return null;
        }

        return $sth->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getLastId(): int
    {
        return $this->pdo->lastInsertId();
    }
}