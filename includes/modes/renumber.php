<?php

    if ($_REQUEST['confirm']) {
        $count = 0;
        foreach ($_SESSION['gb']['number_order'] AS $idx => $pid) {
            $count ++;
            if ($idx != $count) {
                $moves[] = [$pid,$idx,$count,gb_get_passage($pid)['name']];
            }
        }
        echo "<pre>"; print_r($moves);
        foreach ($moves AS $move) {
            gb_set_passage_number($move[0],$move[1],$move[2]);
        }
        ksort($_SESSION['gb']['number_order']);
    } else {
        echo page("
            <h2>Renumber</h2>
            <form action='gordian.php' method='get'>
                <input type='hidden' name='mode' value='renumber'>
                <input type='hidden' name='confirm' value='1'>
                <p>Renumber all paragraphs (to remove gaps)?
                <div class='form-row'>
                    <input type='submit' value='Confirm'>
                </div>
            </form>
            ",['title' => "Renumber Passages"]);
    }
?>