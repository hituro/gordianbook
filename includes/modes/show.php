<?php

        // in order from gb_numbering, show each paragraph
        $config = [
            'title' => $_SESSION['gb']['story']['name'], 
            'css'   => ['css/game.css','css/preview.css','custom'],
            'js'    => ['js/preview.js']
        ];
        if ($_REQUEST['playable']) {
            $config['css'][] = 'css/hidden.css';
        }
        echo gb_header($config);
        echo htmlise(false,['covers' => false, 'print' => false, 'simplex' => true, 'para_links' => true]);
        echo gb_footer();

?>