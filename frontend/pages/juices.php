<?php
    require_once('model/juice.php');

    if (isset($_POST['name']) && isset($_POST['color'])) {
        $juice = new Juice();
        $juice->deserialize($_POST);
        $juice->store();
    }

    $juices = Juice::all();
?>


<!-- Modal -->
<div id="createJuiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Neuen Saft anlegen</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="new-name-input">Name</label>
                        <input type="text" id="new-name-input" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                    <div id="new-color-input" class="input-group colorpicker-component">
                        <input type="text" name="color" class="form-control" value="#DD0F20">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Hinzufügen</button>
        </div>
        </form>
    </div>

  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h4>
                Säfte konfigurieren

                <button class="btn btn-success pull-right" data-toggle="modal" data-target="#createJuiceModal">
                    <i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;Neuen Saft eintragen
                </button>
            </h4>
            <div style="margin: -2px 0 -8px 2px; font-size: 95%;">Verfügbare Säfte: <?php echo count($juices); ?></div>
        </div>
    </div>
    
    <hr style="width: 100%;">

    <div class="row">
    <?php
        foreach ($juices as $juice)
        {
        ?>
        <div class="col-sm-3 col-xs-4">
            <div style="overflow: hidden; background: <?php echo $juice['color']; ?>; color: <?php echo getForeColor($juice['color']); ?>; border-radius: 5px; padding: 15px 20px;" class="form-group">
                <?php echo $juice['name']; ?>
            </div>
        </div>
    <?php
        }
        ?>
    </div>
</div>

<script>
window.addEventListener('load', () => {
    $('#new-color-input').colorpicker();
});
</script>
