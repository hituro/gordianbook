<?php

        if ($_REQUEST['game-settings-json']) {
            $json = json_decode($_REQUEST['game-settings-json'],true);
            if (!is_array($json)) {
                error("Sorry, we could not parse those settings, make sure it was exported from the Formatter");
                go('load-settings');
            } else {
                $_SESSION['gb']['settings'] = $json;
                msg("Game settings JSON loaded");
                go('home');
            }
        } else {
            error("Sorry, you must supply the saved game settings JSON to import");
            go('load-game-json');
        }

?>