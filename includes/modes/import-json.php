<?php

    if ($_REQUEST['story-json'] || $_FILES['story-json-upload']) {
        if ($_REQUEST['story-json']) {
            $src  = $_REQUEST['story-json'];
        } else {
            $src  = file_get_contents($_FILES['story-json-upload']['tmp_name']);
        }
        $json = json_decode($src,true);
        $src  = preg_replace("/(\r\n|\n|\r)/", "\n", $src); // cross-platform newlines
        if (is_array($json)) {
            $format = 'twison';
        } else if (preg_match('/^:: StoryTitle$/m',$src,$matches)) {
            $format = 'twee';
        } else if (preg_match('/tw-storydata/',$src,$matches)) {
            $format = 'twine';
        } 
        if ($format) {
            $_SESSION['gb']                 = $defaults;
            $_SESSION['gb']['format']       = $format;
            $_SESSION['gb']['raw']          = $src;
            $_SESSION['gb']['story']        = $json;
            $_SESSION['gb']['numbering']    = [];
            $_SESSION['gb']['number_order'] = [];
            if (array_key_exists('convert',$_REQUEST) && !$_REQUEST['convert']) {
                msg("Game data loaded. You should now Convert to Gamebook");
                go('home');
            } else {
                include "convert.php";
            }
        } else {
            error("Sorry, we could not parse that story. Make sure it is in Twison, Twee3, or Twine Archive format");
            go('load-json');
        }
    } else {
        error("Sorry, you must supply the story data to import");
        go('load-json');
    }

?>