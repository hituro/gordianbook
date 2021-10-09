<?php

        if ($_REQUEST['game-json'] || $_FILES['game-json-upload']) {
            if ($_REQUEST['game-json']) {
                $src  = $_REQUEST['game-json'];
            } else {
                $src  = file_get_contents($_FILES['game-json-upload']['tmp_name']);
            }
            $json = json_decode($src,true);
            if (!is_array($json) || !$json['settings']) {
                error("Sorry, we could not parse that saved game, make sure it was exported from the Formatter");
                go('load-game-json');
            } else {
                $_SESSION['gb']                 = $json;
                msg("Game JSON export loaded");
                go('home');
            }
        } else {
            error("Sorry, you must supply the saved game JSON to import");
            go('load-game-json');
        }

?>