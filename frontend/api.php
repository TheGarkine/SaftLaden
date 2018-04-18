<?php
    require_once('core.php');

    if (!isset($_GET['model'])) {
        die('{"type": "error", "text": "No model given."}');
    }

    if ($_GET['model'] == 'glass') {
        if (!isset($_GET['id'])) {
            die('{"type": "error", "text": "No id given."}');
        }

        require_once('model/glass.php');
        
        $glass = new Glass();
        $glass->rfid = $_GET['id'];
        
        if (!$glass->load()) {
            die('{"type": "error", "text": "Invalid id."}');
        }

        die('{"type": "ok", "data": ' . $glass->serialize() . '}');

    } else if ($_GET['model'] == 'mixture') {
        if (!isset($_GET['id'])) {
            die('{"type": "error", "text": "No id given."}');
        }

        if (!isset($_GET['glass'])) {
            die('{"type": "error", "text": "No glass given."}');
        }

        require_once('model/glass.php');
        require_once('model/mixture.php');

        $mixture = new Mixture();
        $mixture->id = $_GET['id'];

        if (!$mixture->load()) {
            die('{"type": "error", "Invalid id."}');
        }
        
        die('{"type": "ok", "data": ' . $mixture->serialize() . '}');
        
    } else {
        die('{"type": "error", "text": "Unknown model."}');
    }
