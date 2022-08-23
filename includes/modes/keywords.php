<?php

    $out = "";
    foreach ($_SESSION['gb']['keywords'] AS $keyword => $data) {
        $out .= "<h3 class='title'>$keyword</h3><ul>";
        $uses = 0;
        foreach ($data AS $idx => $c) {
            $passage = $_SESSION['gb']['story']['passages'][$idx];
            $number  = $_SESSION['gb']['numbering'][$passage['pid']]['number'];
            $number  = $number ? $number : 'skip';
            $out .= "<li><a href='gordian.php?mode=passage-edit&passage_name={$passage['name']}' target='_new'>{$passage['name']} ($number)</a> $c uses</li>";
            $uses += $c;
        }
        $out .= "</ul>";
        if ($uses == 1) {
            $out .= "<p class='warning'>Keyword only used once</p>";
        }
    }

    echo page("
        <h2>Keyword Report</h2>
        <p>If you have marked keywords with the <code>&lt;keyword></code> or <code>&lt;k></code> tags, they are listed here. (See <a href='gordian.php?mode=keywords-about'>Keywords</a>)</p>
        <div class='fieldset' style='padding:1em'>$out</div>",['title' => "Keywords"]);

?>