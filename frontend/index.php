<?php
    require_once('core.php');
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>SaftLaden</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="css/bootstrap-colorpicker.min.css" />
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
</head>
<body>
<?php
    if ($page != 'cover') {
    ?>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Navigation umschalten</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="?page=cover">
                        SaftLaden
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="?page=mixtures">Mischung kreieren</a></li>
                        <li><a href="?page=glasses">Glas konfigurieren</a></li>
                        <li><a href="?page=juices">Säfte konfigurieren</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <?php include 'pages/' . $page . '.php'; ?>
    </div>
<?php
    } else {
        include 'pages/cover.php';
    }
    ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-colorpicker.min.js"></script>
</body>
</html>