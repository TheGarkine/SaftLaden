<?php
    class Glass implements Storable
    {
        public $rfid = 0;
        public $volume = 0;

        private $_ratio = [];

        function serialize() {
            return json_encode([
                'rfid' => $this->rfid,
                'volume' => $this->volume,
                'mixtures' => $this->_ratio ? [['juice_ratio' => $this->_ratio]] : [],
            ]);
        }

        function deserialize($obj) {
            if (isset($obj['rfid']))
                $this->rfid = (int)$obj['rfid'];
            
            if (isset($obj['volume']))
                $this->volume = (int)($obj['volume']);
            
            if (isset($obj['juice_ratio'])) {
                if (is_array($obj['juice_ratio'])) {
                    $this->_ratio = $obj['juice_ratio'];
                } else {
                    $this->_ratio = json_decode($obj['juice_ratio']);
                }
            }
        }

        function load() {
            global $pdo;

            $this->loadStmt->bindParam(':rfid', $this->rfid);

            $this->loadStmt->execute();

            if ($obj = $this->loadStmt->fetch()) {
                $this->deserialize($obj);
                return true;
            }
            
            return false;
        }

        function store() {
            global $pdo;

            $this->storeStmt->bindParam(':rfid', $this->rfid);
            $this->storeStmt->bindParam(':volume', $this->volume);

            $this->storeStmt->execute();
        }


        private $storeStmt;
        private $loadStmt;

        function __construct() {
            global $pdo;

            $this->storeStmt = $pdo->prepare("
                INSERT INTO `glasses` (`rfid`, `volume`) VALUES(:rfid, :volume)
                ON DUPLICATE KEY UPDATE `rfid`=:rfid, `volume`=:volume
            ");

            $this->loadStmt = $pdo->prepare("
                SELECT * FROM `glasses`
                    LEFT JOIN `mixtures` ON `mixtures`.`glass`=`glasses`.`rfid`
                WHERE `rfid`=:rfid;
            ");
        }


        static function all() {
            global $pdo;

            $stmt = $pdo->prepare("
                SELECT * FROM `glasses`
            ");

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }