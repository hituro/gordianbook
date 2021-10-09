<?php

    $settings = [
        'duplex'    => $_REQUEST['print'] ? true : false,
        'covers'    => $_REQUEST['covers'] ? true : false,
    ];
    echo htmldoc(true,$settings['duplex'],$settings['covers']);

?>