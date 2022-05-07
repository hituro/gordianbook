<?php

    $_SESSION['gb']['settings']['css']  = $_REQUEST['settings_css'];
    $_SESSION['gb']['story_css']        = $_REQUEST['story_css'];
    msg("CSS saved");
    go('css-edit');

?>