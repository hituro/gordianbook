<?php

        // introduction
        if (!$_SESSION['gb']['gb-introduction']) {
            $_SESSION['gb']['gb-introduction'] = ['name' => 'gb-introduction', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-introduction']['text'] = $_REQUEST['intro'];

        // rear
        if (!$_SESSION['gb']['gb-rear']) {
            $_SESSION['gb']['gb-rear'] = ['name' => 'gb-rear', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-rear']['text'] = $_REQUEST['rear'];

        // front cover
        if (!$_SESSION['gb']['gb-front-cover']) {
            $_SESSION['gb']['gb-front-cover'] = ['name' => 'gb-front-cover', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-front-cover']['text'] = $_REQUEST['gb-front-cover'];

        // rear cover
        if (!$_SESSION['gb']['gb-rear-cover']) {
            $_SESSION['gb']['gb-rear-cover'] = ['name' => 'gb-rear-cover', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-rear-cover']['text'] = $_REQUEST['gb-rear-cover'];
        msg("Front and back matter saved");
        go('intro-edit');

?>