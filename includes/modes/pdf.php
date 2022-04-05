<?php

    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_USER_WARNING & ~ E_NOTICE);

    require_once $root . '/vendor/autoload.php';

    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $settings = [
        'print'     => $_REQUEST['print'] ? true : false,
        'covers'    => $_REQUEST['covers'] ? true : false,
        'simplex'   => $_REQUEST['simplex'] ? true : false,
    ];

    $format = (strstr($_SESSION['gb']['settings']['page_size'],',')) ? explode(',',$_SESSION['gb']['settings']['page_size']) : $_SESSION['gb']['settings']['page_size'];

    $config = [
        'format' => $format,
        'fontDir' => array_merge($fontDirs, [
            $root . '/fonts',
        ]),
        'fontdata' => $fontData + [
            'fell' => [
                'R' => 'IMFellDWPica-Regular.ttf',
                'I' => 'IMFellDWPica-Italic.ttf',
            ],
            'eater' => [
                'R' => 'Eater-Regular.ttf',
            ]
        ],
        'dpi'     => $_SESSION['gb']['settings']['low_res'] ? 72 : $_SESSION['gb']['settings']['resolution'],
        'img_dpi' => $_SESSION['gb']['settings']['low_res'] ? 72 : $_SESSION['gb']['settings']['image_resolution'],
        'list_auto_mode' => 'mpdf',
        'list_marker_offset' => '1em',
        'list_symbol_size' =>'0.31em',
        'margin_top'    => $_SESSION['gb']['settings']['margin_top'] ?? 15,
        'margin_bottom' => $_SESSION['gb']['settings']['margin_bottom'] ?? 15,
        'margin_left'   => $_SESSION['gb']['settings']['margin_left'] ?? 10,
        'margin_right'  => $_SESSION['gb']['settings']['margin_right'] ?? 10
    ];
    if ($_REQUEST['print'] && !$settings['covers']) {
        $config = array_merge($config,[
            'margin_left'   => $_SESSION['gb']['settings']['margin_print_left'] ?? 20,
            'margin_right'  => $_SESSION['gb']['settings']['margin_print_right'] ?? 10,
            'mirrorMargins' => true
        ]);
    }
    $mpdf = new \Mpdf\Mpdf($config);
    $mpdf->SetCompression(true);
    $mpdf->WriteHTML(htmldoc(true,$settings));
    $mpdf->Output();

?>