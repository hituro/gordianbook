<?php

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name'].".html");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: text/html');
    echo htmldoc(false,['proof' => true]);
    exit;

?>