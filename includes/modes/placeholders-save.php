<?php

    $placeholders = create_placeholders(json_decode($_REQUEST['gb-placeholders'],true));
    if ($_SESSION['gb']['gb-placeholders']) {
        $_SESSION['gb']['gb-placeholders']['placeholders'] = $placeholders;
        $_SESSION['gb']['gb-placeholders']['passage']['text'] = $_REQUEST['gb-placeholders'];
    } else {
        $_SESSION['gb']['gb-placeholders'] = [
            'passage'   => [
                'text' => $_REQUEST['gb-placeholders'],
                'title' => 'gb-placeholders'
            ],
            'placeholders' => $placeholders
        ];
    }
    msg("placeholders saved");
    go('placeholders-edit');

?>