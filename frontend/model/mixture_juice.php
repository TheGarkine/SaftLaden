<?php
    class MixtureJuice implements Storable
    {
        public $id = null;
        public $mixture = null;
        public $juice = null;
        public $ratio = 0;

        function serialize() {
            return json_encode([
                'id' => $this->id,
                'mixture' => $this->mixture,
                'juice' => $this->juice,
                'ratio' => $this->ratio,
            ]);
        }

        function deserialize($obj) {
            if (isset($obj['id']))
                $this->id = $obj['id'];
                
            if (isset($obj['mixture']))
                $this->mixture = $obj['mixture'];
            
            if (isset($obj['juice']))
                $this->juice = $obj['juice'];
            
            if (isset($obj['ratio']))
                $this->ratio = $obj['ratio'];
        }

        function load() {
            global $pdo;

            $this->loadStmt->bindParam(':id', $this->id);
            $this->loadStmt->bindParam(':mixture', $this->mixture);

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
            $this->storeStmt->bindParam(':mixture', $this->mixture);
            $this->storeStmt->bindParam(':juice', $this->juice);
            $this->storeStmt->bindParam(':ratio', $this->ratio);

            $this->storeStmt->execute();
        }


        private $loadStmt;
        private $storeStmt;

        function __construct() {
            global $pdo;

            $this->storeStmt = $pdo->prepare("
                INSERT INTO `mixture_juices` (`id`, `mixture_id`, `juice_id`, `ratio`) VALUES (:id, :mixture, :juice, :ratio)
                ON DUPLICATE KEY UPDATE `juice_id`=:juice, `ratio`=:ratio
            ");

            $this->loadStmt = $pdo->prepare("
                SELECT * FROM `mixture_juices` WHERE `id`=:id AND `mixture_id`=:mixture
            ");
        }


        static function all() {
            global $pdo;

            $stmt = $pdo->prepare("
                SELECT * FROM `mixture_juices`
            ");

            $stmt->execute();

            return $stmt->fetchAll();
        }

        static function allByGlass($id) {
            global $pdo;

            $stmt = $pdo->prepare("
                SELECT * FROM `mixture_juices` AS m
                    LEFT JOIN `juices` AS j ON m.juice_id = j.id
                WHERE `mixture_id`=:glass
            ");

            $stmt->bindParam(':glass', $id);

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }