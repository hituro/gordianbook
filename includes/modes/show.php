<?php

        // in order from gb_numbering, show each paragraph
        echo gb_header([
            'title' => $_SESSION['gb']['story']['name'], 
            'css'   => ['css/game.css','css/preview.css','custom'],
            'js'    => ['js/preview.js']
        ]);
        echo htmlise(false,['covers' => false, 'print' => false, 'simplex' => true]);
        echo gb_footer();

?>