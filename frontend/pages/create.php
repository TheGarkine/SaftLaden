<?php
    require_once('model/glass.php');
    require_once('model/mixture.php');
    
    $mixture = new Mixture();
    if (count($_POST)) {
        if (isset($_POST['juice_ratio'])) {
            $r = $_POST['juice_ratio'] / 100;
            $_POST['juice_ratio'] = '[' . $r . ', ' . (1 - $r) . ']';
        }

        $mixture->deserialize($_POST);
        $mixture->store();
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
                <label for="rfid">RFID (Volumen)</label>
                <select class="form-control" id="glass-input" name="glass">
            <?php
                foreach (Glass::all() as $glass) {
                    echo '<option value="' . $glass['rfid'] . '"'.
                        ((isset($_GET['glass']) && $_GET['glass'] == $glass['rfid']) ? ' selected' : '') . 
                        '>' . $glass['rfid'] . ' (' . $glass['volume'] . 'ml)</option>';
                }
                ?>
                </select>
            </div>
        </div>

        <div class="col-sm-3">
            <h4>
                Mischung dosieren
            </h4>
        </div>
        <div class="col-sm-9">
            <div class="form-group">
                <h4 class="pull-left">
                    Saft 1
                    <br />
                </h4>
                <h4 class="pull-right">
                    Saft 2
                </h4>
            </div>
        </div>

        <div class="col-sm-9 col-sm-offset-3">
            <div class="form-group">
            <?php
                $ratioVal = 50;

                if (count($mixture->ratio) > 0) {
                    $ratioVal = $mixture->ratio[0] * 100;
                }
                ?>

                <input type="range" id="juice_ratio-input" name="juice_ratio" min="0" max="100" value="<?php echo $ratioVal; ?>" />
            </div>
        </div>

        <div class="col-sm-4 col-sm-offset-3">
            <object id="glass1" data="img/glass.svg" type="image/svg+xml" style="transform: scale(0.8);">
            </object>
        </div>

        <div class="col-sm-4 col-sm-offset-1">
            <object id="glass2" data="img/glass.svg" type="image/svg+xml" style="transform: scale(0.8);" class="pull-right">
            </object>
        </div>

        <div class="col-sm-9 col-sm-offset-3">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    Mischung speichern
                </button>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
    function glassById(id) {
        return document.getElementById(id).contentDocument;
    }

    function setGlassValue(glass, percentage) {
        var offset = (30 / 100) * (100 - percentage);

        $(glass.getElementById('glass-top')).attr({
            cy: (20 + offset) + 'px',
        });

        $(glass.getElementById('glass-middle')).attr({
            y: (22 + offset) + 'px',
            height: (30 - offset) + 'px',
        });
    }

    function setGlassColor(glass, color) {
        $(glass.getElementsByClassName('fill')).css({
            fill: color,
        });
    }

    window.addEventListener('load', function() {
        function updateForm() {
            $.getJSON('api.php?model=glass&id=' + $("#glass-input").val(), (result) => {
                if (result.type == 'ok') {
                    if (result.data.mixtures.length > 0) {
                        $('#juice_ratio-input').val(result.data.mixtures[0].juice_ratio[0] * 100);
                    } else {
                        $('#juice_ratio-input').val(50);
                    }

                    updateGlasses();
                }
            });
        }

        function updateGlasses() {
            setGlassValue(glassById('glass1'), $('#juice_ratio-input').val());
            setGlassValue(glassById('glass2'), 100 - $('#juice_ratio-input').val());
        }

        $('#glass-input').on('change', () => {
            updateForm();
        });

        updateForm();

        $('#juice_ratio-input').on('input', () => {
            updateGlasses();
        });
    });

</script>
