<?php
    ini_set('display_errors',0);
    //error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_USER_WARNING & ~E_USER_NOTICE);
    
    $config = config_mpdf($root,$settings);
    $config['debug'] = false;
    $mpdf = new \Mpdf\Mpdf($config);
    $mpdf->SetCompression(true);
    try {
        $mpdf->WriteHTML(htmldoc(true,$settings));
        $mpdf->Output();
    } catch (Exception $e) { echo "<pre>"; print_r($e); }
?>