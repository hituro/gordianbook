<?php

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name']."_playable.html");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: text/html');
    echo playable_doc(['playable' => true, 'para_links' => true]);
    exit;

?>