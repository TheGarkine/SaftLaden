<?php
    require_once('model/glass.php');
    
    $glass = new Glass();
    if (count($_POST)) {
        $glass->deserialize($_POST);
    }
?>

<div class="container">
    <form method="POST">
    <div class="row">
        <div class="col-sm-3">
            <h4>
                Glas auswählen
            </h4>
        </div>
        <div class="col-sm-9">
            <div class="form-group">
                <label for="rfid">RFID</label>
                <input type="number" value="<?php echo $glass->rfid; ?>" min="0" name="rfid" id="rfid" class="form-control" />
            </div>
        </div>

        <div class="col-sm-3">
            <h4>
                Glas konfigurieren
            </h4>
        </div>
        <div class="col-sm-9">
            <div class="form-group">
                <label for="volume">Füllmenge (in ml)</label>
                <input type="number" value="<?php echo $glass->volume; ?>" min="0" max="500" name="volume" id="volume" class="form-control" />
            </div>
            <div class="form-group">
            <?php
                if (count($_POST)) {
                    $glass->store();
                    echo '<div class="alert alert-success">Konfiguration gespeichert</div>';
                }
                ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Konfiguration speichern</button>
            </div>
        </div>
    </div>
    </form>
</div>