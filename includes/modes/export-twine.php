<?php

    $export_numbers = $_REQUEST['skip_numbers'] ? false : true;

    $filename = str_replace(' ','_',$_SESSION['gb']['story']['name'].".html");
    header('Content-disposition: attachment; filename='.$filename);
    header('Content-type: text/html');

    // storydata
    $attrs = [];
    $skip = ['tag-colors','passages'];
    foreach ($_SESSION['gb']['story'] AS $attr => $v) {
        if (!in_array($attr,$skip)) {
            $attrs[] = "$attr=\"$v\"";
        }
    }
    $attrs = implode(' ',$attrs);
    echo "<tw-storydata $attrs hidden>";
    echo "<style role=\"stylesheet\" id=\"twine-user-stylesheet\" type=\"text/twine-css\">{$_SESSION['gb']['story_css']}</style>";
    echo '<script role="script" id="twine-user-script" type="text/twine-javascript"></script>';
    if ($_SESSION['gb']['story']['tag-colors']) {
        foreach ($_SESSION['gb']['story']['tag-colors'] AS $tag => $col) { echo "<tw-tag name=\"$tag\" color=\"{$col}\"></tw-tag>"; }
    }

    // passages
    foreach ($_SESSION['gb']['story']['passages'] AS $p) {
        echo twine_passage($p,$export_numbers);
    }

    // special passages
    echo twine_passage([
        'name'      => 'gb-settings', 
        'text'      => json_encode($_SESSION['gb']['settings'],JSON_PRETTY_PRINT),
        'pid'       => $_SESSION['gb']['settings']['passage']['pid'],
        'position'  => $_SESSION['gb']['settings']['passage']['position'],
        'size'      => $_SESSION['gb']['settings']['passage']['size']
    ]);
    if ($_SESSION['gb']['gb-front-cover']) {
        echo twine_passage($_SESSION['gb']['gb-front-cover']);
    }
    if ($_SESSION['gb']['gb-introduction']) {
        echo twine_passage($_SESSION['gb']['gb-introduction']);
    }
    if ($_SESSION['gb']['gb-rear']) {
        echo twine_passage($_SESSION['gb']['gb-rear']);
    }
    if ($_SESSION['gb']['gb-rear-cover']) {
        echo twine_passage($_SESSION['gb']['gb-rear-cover']);
    }
    if ($_SESSION['gb']['gb-templates']) {
        echo twine_passage($_SESSION['gb']['gb-templates']['passage']);
    }
    echo "</tw-storydata>";

?>