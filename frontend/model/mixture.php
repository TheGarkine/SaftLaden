<?php
    class Mixture implements Storable
    {
        public $id = 1;
        public $glass = null;

        public $ratio = [
            0.5,
            0.5,
        ];

        function serialize() {
            return '{"glass": ' . $this->glass->serialize() . ', ' .
                    '"ratio": ' . json_encode($this->ratio) . '}';
        }

        function deserialize($obj) {
            if (isset($obj['glass'])) {
                if ($obj['glass'] instanceof Glass) {
                    $this->glass = $obj['glass'];
                } else {
                    $this->glass = new Glass();
                    $this->glass->rfid = $obj['glass'];
                    $this->glass->load();
                }
            }

            if (isset($obj['juice_ratio'])) {
                if (is_array($obj['juice_ratio']))
                    $this->ratio = $obj['juice_ratio'];
                else
                    $this->ratio = json_decode($obj['juice_ratio']);
            }
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

            $juice_ratio = json_encode($this->ratio);

            $this->storeStmt->bindParam(':id', $this->id);
            $this->storeStmt->bindParam(':glass', $this->glass->rfid);
            $this->storeStmt->bindParam(':juice_ratio', $juice_ratio);

            $this->storeStmt->execute();
        }


        private $storeStmt;
        private $loadStmt;

        function __construct() {
            global $pdo;

            $this->storeStmt = $pdo->prepare("
                INSERT INTO `mixtures` (`id`, `glass`, `juice_ratio`) VALUES(:id, :glass, :juice_ratio)
                ON DUPLICATE KEY UPDATE `glass`=:glass, `juice_ratio`=:juice_ratio
            ");

            $this->loadStmt = $pdo->prepare("
                SELECT * FROM `mixtures` WHERE `id`=:id
            ");
        }


        static function all($glass = null) {
            global $pdo;

            $stmt = $pdo->prepare("SELECT * FROM `glasses`"
                . ($glass ? " WHERE `glass`=:glass" : "")
            );

            $stmt->bindParam(':glass', $glass);

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }