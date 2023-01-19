<?php

    $export_numbers = $_REQUEST['skip_numbers'] ? false : true;

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name'].".twee");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: text/twee');
    echo ":: StoryTitle\n{$_SESSION['gb']['story']['name']}\n\n";
    echo ":: UserScript[script]\n\n:: UserStylesheet[stylesheet]\n\n{$_SESSION['gb']['story_css']}\n\n";
    echo ":: StoryData\n";
        $sd = $_SESSION['gb']['story'];
        unset($sd['name']); unset($sd['passages']);
        if (!array_key_exists('start',$sd)) { $sd['start'] = $_SESSION['gb']['story']['passages'][$_SESSION['gb']['pids'][$sd['startnode']]]['name']; }
        if (!array_key_exists('ifid',$sd))  { $sd['ifid'] = strtoupper(guidv4()); }
        echo json_encode($sd,JSON_PRETTY_PRINT) . "\n\n";
    foreach ($_SESSION['gb']['story']['passages'] AS $p) {
        echo twee_passage($p,$export_numbers);
    }
    echo twee_passage([
        'name'      => 'gb-settings', 
        'text'      => json_encode($_SESSION['gb']['settings'],JSON_PRETTY_PRINT),
        'pid'       => $_SESSION['gb']['settings']['passage']['pid'],
        'position'  => $_SESSION['gb']['settings']['passage']['position'],
        'size'      => $_SESSION['gb']['settings']['passage']['size']
    ]);
    if ($_SESSION['gb']['gb-front-cover']) {
        echo twee_passage($_SESSION['gb']['gb-front-cover']);
    }
    if ($_SESSION['gb']['gb-introduction']) {
        echo twee_passage($_SESSION['gb']['gb-introduction']);
    }
    if ($_SESSION['gb']['gb-rear']) {
        echo twee_passage($_SESSION['gb']['gb-rear']);
    }
    if ($_SESSION['gb']['gb-rear-cover']) {
        echo twee_passage($_SESSION['gb']['gb-rear-cover']);
    }
    if ($_SESSION['gb']['gb-templates']) {
        echo twee_passage($_SESSION['gb']['gb-templates']['passage']);
    }
    

?>