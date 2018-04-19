<?php
    class Juice implements Storable
    {
        public $id = null;
        public $name = '';
        public $color = '#A00';

        function serialize() {
            return json_encode([
                'id' => $this->id,
                'name' => $this->name,
                'color'=> $this->color,
            ]);
        }

        function deserialize($obj) {
            if (isset($obj['id']))
                $this->id = $obj['id'];
            
            if (isset($obj['name']))
                $this->name = $obj['name'];
            
            if (isset($obj['color']))
                $this->color = $obj['color'];
        }

        function load() {
            global $pdo;

            $this->loadStmt->bindParam(':id', $this->id);

            $this->loadStmt->execute();

            if ($obj = $this->loadStmt->fetch()) {
                $this->deserialize($obj);
                return true;
            }
            
            return false;
        }

        function store() {
            global $pdo;

            $this->storeStmt->bindParam(':id', $this->id);
            $this->storeStmt->bindParam(':name', $this->name);
            $this->storeStmt->bindParam(':color', $this->color);

            $this->storeStmt->execute();
        }


        private $loadStmt;
        private $storeStmt;

        function __construct() {
            global $pdo;

            $this->storeStmt = $pdo->prepare("
                INSERT INTO `juices` (`id`, `name`, `color`) VALUES (:id, :name, :color)
                ON DUPLICATE KEY UPDATE `id`=:id, `name`=:name, `color`=:color
            ");

            $this->loadStmt = $pdo->prepare("
                SELECT * FROM `juices` WHERE `id`=:id
            ");
        }


        static function all() {
            global $pdo;

            $stmt = $pdo->prepare("
                SELECT * FROM `juices`
            ");

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }