<?php

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name']."_proofing.html");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: text/html');
    echo htmldoc(false,['proof' => $_REQUEST['proof'], 'export' => true]);
    exit;

?>