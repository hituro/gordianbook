<?php

    $_SESSION['gb']['settings']['end_text']            = $_REQUEST['end_text'];
    $_SESSION['gb']['settings']['death_text']          = $_REQUEST['death_text'];
    $_SESSION['gb']['settings']['separator']           = $_REQUEST['separator'];
    //$_SESSION['gb']['settings']['css']                 = $_REQUEST['css'];
    $_SESSION['gb']['settings']['page_size']           = strstr($_REQUEST['page_size'],',') ? explode(',',$_REQUEST['page_size']) : $_REQUEST['page_size'];
    $_SESSION['gb']['settings']['cover']               = $_REQUEST['cover'];
    $_SESSION['gb']['settings']['break']               = $_REQUEST['break'];
    $_SESSION['gb']['settings']['mdtype']              = $_REQUEST['mdtype'];
    $_SESSION['gb']['settings']['resolution']          = $_REQUEST['resolution'] ? $_REQUEST['resolution'] : 300;
    $_SESSION['gb']['settings']['image_resolution']    = $_REQUEST['image_resolution'] ? $_REQUEST['image_resolution'] : $_SESSION['gb']['settings']['resolution'];
    $_SESSION['gb']['settings']['low_res']             = $_REQUEST['low_res'] ? true : false;
    $_SESSION['gb']['settings']['margin_top']          = $_REQUEST['margin_top'];
    $_SESSION['gb']['settings']['margin_left']         = $_REQUEST['margin_left'];
    $_SESSION['gb']['settings']['margin_right']        = $_REQUEST['margin_right'];
    $_SESSION['gb']['settings']['margin_bottom']       = $_REQUEST['margin_bottom'];
    $_SESSION['gb']['settings']['margin_print_left']   = $_REQUEST['margin_print_left'];
    $_SESSION['gb']['settings']['margin_print_right']  = $_REQUEST['margin_print_right'];
    $_SESSION['gb']['settings']['footers']             = $_REQUEST['footers'] ? $_REQUEST['footers'] : 'numbers';
    msg("Settings saved");
    go('settings');

?>