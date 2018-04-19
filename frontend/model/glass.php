<?php
    class Glass implements Storable
    {
        public $rfid = 0;
        public $volume = 0;

        public $ratios = [];
        public $juices = [];

        function serialize() {
            return json_encode([
                'rfid' => $this->rfid * 1,
                'volume' => $this->volume * 1,
                'mixtures' => $this->ratios,
                'juices' => $this->juices,
            ]);
        }

        function deserialize($obj) {
            if (isset($obj['rfid']))
                $this->rfid = $obj['rfid'];
            
            if (isset($obj['volume']))
                $this->volume = $obj['volume'];
        }

        function load() {
            global $pdo;

            $this->loadStmt->bindParam(':rfid', $this->rfid);

            $this->loadStmt->execute();

            if ($obj = $this->loadStmt->fetch()) {
                $this->deserialize($obj);

                $this->ratios = [];
                $this->juices = [];

                $mixtures = MixtureJuice::allByGlass($this->rfid);
                
                foreach ($mixtures as $mixture) {
                    $this->ratios[] = $mixture['ratio'] * 1;
                    $this->juices[] = [
                        'id' => $mixture['juice_id'] * 1,
                        'color' => $mixture['color'],
                        'ratio' => $mixture['ratio'] * 1,
                    ];
                }
                
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
                    LEFT JOIN `mixtures` ON `mixtures`.`id`=`glasses`.`rfid`
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