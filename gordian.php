<?php

    ini_set('session.gc_maxlifetime','5000');
    ini_set('max_execution_time',50);
    session_start();
    require "includes/gordian_interface.functions.php";
    require "includes/gordian_output.functions.php";
    $mode = $_REQUEST['mode'] ?? 'home';

    $defaults = [
        'story'         => [],
        'numbering'     => [],
        'number_order'  => [],
        'passage_names' => [],
        'format'        => '',
        'frontmatter'   => [],
        'backmatter'    => [],
        'settings'      => [
            'end_text'      => 'THE END',
            'death_text'    => 'YOU DIED',
            'separator'     => false,
            'break'         => false,
            'css'           => '',
            'page_size'     => 'A4-P',
            'cover'         => false,
            'mdtype'        => 'harlowe'
        ],
        'stats'         => [
            'passages'  => 0,
            'links'     => 0,
        ]
    ];
    if (!$_SESSION['gb']) {
        $_SESSION['gb'] = $defaults;
    }

    if (file_exists("includes/modes/$mode.php")) {
        include_once "includes/modes/$mode.php";
    } else {
        error("Sorry, that mode was not recognised");
        echo page('',[
            'title' => 'Error',
        ]);
    }

?>