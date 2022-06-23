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
                <p>Renumber all paragraphs (to remove gaps)?</p>
                <p>Starting from passage 1, gaps will be removed by decreasing passage numbers in sequence. So if your current story has a number sequence of <code>1 2 4 5</code> it will end up with <code>1 2 3 4</code>. Passages will not otherwise change order.</p>
                <div class='form-row'>
                    <input type='submit' value='Confirm'>
                </div>
            </form>
            ",['title' => "Renumber Passages"]);
    }
?>