<?php

    $templates = create_templates($_REQUEST['gb-templates']);
    if ($_SESSION['gb']['gb-templates']) {
        $_SESSION['gb']['gb-templates']['templates'] = $templates;
        $_SESSION['gb']['gb-templates']['passage']['text'] = $_REQUEST['gb-templates'];
    } else {
        $_SESSION['gb']['gb-templates'] = [
            'passage'   => [
                'text' => $_REQUEST['gb-templates'],
                'title' => 'gb-templates'
            ],
            'templates' => $templates
        ];
    }
    msg("Templates saved");
    go('templates-edit');

?>