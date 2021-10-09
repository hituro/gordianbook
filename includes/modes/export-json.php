<?php

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name']."-gbf.json");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: application/json');
    $tmp = $_SESSION['gb']; unset($tmp['raw']);
    echo json_encode($tmp,JSON_PRETTY_PRINT);
    exit;

?>