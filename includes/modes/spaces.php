<?php

    set_time_limit(100);

    //ini_set('display_errors',1);

    $config = config_mpdf($root);
    //$config['cacheCleanupInterval'] = 1;

    $s     = time();
    $c     = 0;
    $limit = 400;
    echo "<pre>";

    foreach ($_SESSION['gb']['number_order'] AS $number => $pid) {

        if (!$_SESSION['gb']['passage_sizes'][$pid]) {
            //$idx     = $_SESSION['gb']['pids'][$pid];
            //$passage = $_SESSION['gb']['story']['passages'][$idx];
            //$render  = render_one($passage,$number);
            //echo memory_get_usage() . "\n";
            $mpdf = new \Mpdf\Mpdf($config);
            $mpdf->WriteHTML(htmldoc(false,[],[$number]));
            $height = $mpdf->y - $mpdf->tMargin;
            //$tmp = $mpdf->Output('','S');
            //$height = $mpdf->_getHtmlHeight($render);

           // echo "$number -> p{$mpdf->page} {$height}mm\n";
            $_SESSION['gb']['passage_sizes'][$pid] = ['pages' => $mpdf->page, 'mm' => $height];
            //$mpdf->DeletePages($mpdf->page);
            unset($mpdf);
           // unset($tmp);
            gc_collect_cycles();
            //echo memory_get_usage() . "\n";

            $c++;
        }

        if ($c >= $limit) { break; }
    }
    $e = time() - $s;
    echo "$e seconds for $c passages = " . ($e/$c) . " secs/passage\n";
    echo count($_SESSION['gb']['passage_sizes']) . " passages measured out of " . count($_SESSION['gb']['number_order']) . "\n";
    print_r($_SESSION['gb']['passage_sizes']);
    echo "</pre>";
	
?>