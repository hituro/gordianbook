<?php

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

    $config = [
        'format' => $_SESSION['gb']['settings']['page_size'],
        'fontDir' => array_merge($fontDirs, [
            $root . '/fonts',
        ]),
        'fontdata' => $fontData + [
            'fell' => [
                'R' => 'IMFellDWPica-Regular.ttf',
                'I' => 'IMFellDWPica-Italic.ttf',
            ]
        ],
        'dpi'   => 300,
        'img_dpi' => 300,
        'list_auto_mode' => 'mpdf',
        'list_marker_offset' => '1em',
        'list_symbol_size' =>'0.31em',
        'margin_top'    => 15,
        'margin_bottom' => 15,
        'margin_left'   => 10,
        'margin_right'  => 10
    ];
    if ($_REQUEST['print'] && !$settings['covers']) {
        $config += [
            'margin_left'   => 20,
            'margin_right'  => 10,
            'mirrorMargins' => true
        ];
    }
    $mpdf = new \Mpdf\Mpdf($config);
    $mpdf->WriteHTML(htmldoc(true,$settings));
    $mpdf->Output();

?>