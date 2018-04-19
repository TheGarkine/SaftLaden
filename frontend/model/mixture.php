<?php
    class Mixture implements Storable
    {
        public $glass = null;

        public $juices = [
            null,
            null,
        ];

        function serialize() {
            $json = '{';
            
            foreach ($this->juices as $juice) {
                $json .= $juice->serialize() . ', ';
            }

            $json .= '"glass": ' . $this->glass->serialize() . '}';

            return $json;
        }

        function deserialize($obj) {
            if (isset($obj['id'])) {
                $this->id = $obj['id'];
                $this->juices[0]->mixture = $this->glass->rfid;
                $this->juices[1]->mixture = $this->glass->rfid;
            }

            if (isset($obj['glass'])) {
                if ($obj['glass'] instanceof Glass) {
                    $this->glass = $obj['glass'];
                } else {
                    $this->glass = new Glass();
                    $this->glass->rfid = $obj['glass'];
                    $this->glass->load();
                }
            }

            if (isset($obj['juices'])) {
                foreach ($obj['juices'] as $key => $juice) {
                    $this->juices[$key]->deserialize($juice);
                }
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

            $this->storeStmt->bindParam(':glass', $this->glass->rfid);

            $this->storeStmt->execute();

            foreach ($this->juices as $juice) {
                $juice->mixture = $this->glass->rfid;
                $juice->store();
            }
        }


        private $storeStmt;
        private $loadStmt;

        function __construct() {
            global $pdo;

            $this->storeStmt = $pdo->prepare("
                REPLACE INTO `mixtures` (`id`) VALUES(:glass)
            ");

            $this->loadStmt = $pdo->prepare("
                SELECT * FROM `mixtures` WHERE `id`=:glass
            ");

            $this->glass = new Glass();

            $this->juices[0] = new MixtureJuice();
            $this->juices[0]->mixture = $this->glass->rfid;
            $this->juices[0]->id = 0;
            
            $this->juices[1] = new MixtureJuice();
            $this->juices[1]->mixture = $this->glass->rfid;
            $this->juices[1]->id = 1;
        }


        static function all($glass = null) {
            global $pdo;

            $stmt = $pdo->prepare("SELECT * FROM `glasses`"
                . ($glass ? " WHERE `id`=:glass" : "")
            );

            $stmt->bindParam(':glass', $glass);

            $stmt->execute();

            return $stmt->fetchAll();
        }


        static function allByGlass($glass) {
            global $pdo;

            $stmt = $pdo->prepare("
                SELECT * FROM `mixtures` WHERE `id`=:glass
            ");

            $stmt->bindParam(':glass', $glass);

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }