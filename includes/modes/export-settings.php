<?php

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name']."-settings.json");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: application/json');
    echo json_encode($_SESSION['gb']['settings'],JSON_PRETTY_PRINT);
    exit;

?>