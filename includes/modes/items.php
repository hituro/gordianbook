<?php

    $items = [];
    foreach ($_SESSION['gb']['story']['passages'] AS $idx => $passage) {
        foreach ($passage['items'] AS $item) {
            $items[$item][] = $idx;
        }
    }

    ksort($items);
    $out = "";
    foreach ($items AS $item => $data) {
        $out .= "<div style='break-inside:avoid'><h3 class='title'>$item</h3><ul>";
        foreach ($data AS $idx) {
            $passage = $_SESSION['gb']['story']['passages'][$idx];
            $number  = $_SESSION['gb']['numbering'][$passage['pid']]['number'];
            $number  = $number ? $number : 'skip';
            $out .= "<li><a href='gordian.php?mode=passage-edit&passage_name={$passage['name']}' target='_new'>{$passage['name']} ($number)</a></li>";
        }
        $out .= "</ul></div>";
    }

    echo page("
        <h2>Item Report</h2>
        <p>If you have marked keywords with the <code>&lt;item></code> or <code>&lt;it></code> tags, they are listed here. (See <a href='gordian.php?mode=items-about'>Items</a>)</p>
        <div class='fieldset cols'>$out</div>",['title' => "Items"]);

?>