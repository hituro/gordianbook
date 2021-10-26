<?php

    $_SESSION['gb']['settings']['end_text']         = $_REQUEST['end_text'];
    $_SESSION['gb']['settings']['death_text']       = $_REQUEST['death_text'];
    $_SESSION['gb']['settings']['separator']        = $_REQUEST['separator'];
    $_SESSION['gb']['settings']['css']              = $_REQUEST['css'];
    $_SESSION['gb']['settings']['page_size']        = $_REQUEST['page_size'];
    $_SESSION['gb']['settings']['cover']            = $_REQUEST['cover'];
    $_SESSION['gb']['settings']['break']            = $_REQUEST['break'];
    $_SESSION['gb']['settings']['mdtype']           = $_REQUEST['mdtype'];
    $_SESSION['gb']['settings']['resolution']       = $_REQUEST['resolution'] ? $_REQUEST['resolution'] : 300;
    $_SESSION['gb']['settings']['image_resolution'] = $_REQUEST['image_resolution'] ? $_REQUEST['image_resolution'] : $_SESSION['gb']['settings']['resolution'];
    msg("Settings saved");
    go('settings');

?>