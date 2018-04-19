<?php
    require_once('model/juice.php');
    require_once('model/glass.php');
    require_once('model/mixture.php');
    require_once('model/mixture_juice.php');
    
    $mixture = new Mixture();
    if (count($_POST)) {
        if (isset($_POST['juice1']) && isset($_POST['juice2'])) {
            $r = 0.5;

            if (isset($_POST['juice_ratio'])) {
                $r = $_POST['juice_ratio'] / 100;
            }
    
            $_POST['juices'] = [[
                'juice' => $_POST['juice1'],
                'ratio' => $r,
            ], [
                'juice' => $_POST['juice2'],
                'ratio' => 1 - $r,
             ]];
        }

        $mixture->deserialize($_POST);
        $mixture->store();
    }

    $juices = Juice::all();
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
                        ((isset($_POST['glass']) && $_POST['glass'] == $glass['rfid']) ? ' selected' : '') . 
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
            <?php
                $ratioVal = 50;

                if (count($mixture->juices) > 0) {
                    $ratioVal = $mixture->juices[0]->ratio * 100;
                }
                ?>

                <input type="range" id="juice_ratio-input" name="juice_ratio" min="0" max="100" value="<?php echo $ratioVal; ?>" />
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-4 col-xs-6 col-sm-offset-3">
        <center>
            <object id="glass1" data="img/glass.svg" type="image/svg+xml" style="transform: scale(0.8);">
            </object>
        </center>
        </div>

        <div class="col-sm-4 col-xs-6 col-sm-offset-1">
        <center>
            <object id="glass2" data="img/glass.svg" type="image/svg+xml" style="transform: scale(0.8);">
            </object>
        </center>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 col-sm-offset-3">
            <div class="form-group">
                <select id="juice1-input" name="juice1" class="form-control">
                <?php
                    foreach ($juices as $juice)
                    {
                        echo '<option value="' . $juice['id'] . '"'
                             . ($_POST['juice1'] == $juice['id'] ? ' selected' : '')
                             . ' data-color="' . $juice['color'] . '">' . $juice['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-sm-4 col-sm-offset-1">
            <div class="form-group">
                <select id="juice2-input" name="juice2" class="form-control">
                <?php
                    foreach ($juices as $juice)
                    {
                        echo '<option value="' . $juice['id'] . '"'
                        . ($_POST['juice2'] == $juice['id'] ? ' selected' : '')
                        . ' data-color="' . $juice['color'] . '">' . $juice['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
        <?php
            if (count($_POST)) {
            ?>
            <div class="alert alert-success">
                Mischung gespeichert.
            </div>
        <?php
            }
            ?>

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

    function updateGlassColors() {
        var colorId1 = $('#juice1-input').val();
        var colorId2 = $('#juice2-input').val();

        var color1 = $('#juice1-input > [value=' + colorId1 + ']').attr('data-color');
        var color2 = $('#juice2-input > [value=' + colorId2 + ']').attr('data-color');

        setGlassColor(glassById('glass1'), color1);
        setGlassColor(glassById('glass2'), color2);
    }

    window.addEventListener('load', function() {
        function updateForm() {
            $.getJSON('api.php?model=glass&id=' + $("#glass-input").val(), (result) => {
                if (result.type == 'ok') {
                    if (result.data.mixtures.length > 0) {
                        $('#juice_ratio-input').val(result.data.juices[0].ratio * 100);
                    } else {
                        $('#juice_ratio-input').val(50);
                    }

                    $('#juice1-input').val(result.data.juices[0].id);
                    $('#juice2-input').val(result.data.juices[1].id);
                    
                    updateGlasses();
                }
            });
        }

        function updateGlasses() {
            setGlassValue(glassById('glass1'), $('#juice_ratio-input').val());
            setGlassValue(glassById('glass2'), 100 - $('#juice_ratio-input').val());
            updateGlassColors();
        }

        $('#glass-input').on('change', () => {
            updateForm();
        });

        updateForm();

        $('#juice_ratio-input').on('input', () => {
            updateGlasses();
        });

        $('#juice1-input').on('input', () => {
            updateGlasses();
        });

        $('#juice2-input').on('input', () => {
            updateGlasses();
        });
    });

</script>
