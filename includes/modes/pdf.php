<?php

    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_USER_WARNING & ~ E_NOTICE);

    $config = config_mpdf($root,$settings);
    $mpdf = new \Mpdf\Mpdf($config);
    $mpdf->SetCompression(true);
    $mpdf->WriteHTML(htmldoc(true,$settings));
    $mpdf->Output();

?>