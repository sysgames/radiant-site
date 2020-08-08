<?php
declare(strict_types=1);

namespace App\Repository;
use \DI\Container;
use \PDO;

abstract class BaseRepository{
    protected $container;
    protected $conn;

    public function __construct(Container $container){
        $this->container = $container;
        $this->conn = $container->get('PDO');
    }

    protected function getDb(): PDO{
        return $this->conn;
    }

    protected function getResultsByPage($query, $page, $perPage){ //Method from example class, haven't used it yet but seems useful
        $offset = ($page - 1) * $perPage;
        $query .= " LIMIT ${perPage} OFFSET ${offset}";
        $stmt = $this->database(prepare($query));
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

?>
