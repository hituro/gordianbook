<?php

    if ($_REQUEST['only']) {
        go('pdf',"only={$_REQUEST['only']}");
    } else if ($_REQUEST['from'] && $_REQUEST['to'] && is_numeric($_REQUEST['from']) && is_numeric($_REQUEST['to'])) {
        $from = min($_REQUEST['from'],$_REQUEST['to']);
        $to   = max($_REQUEST['from'],$_REQUEST['to']);
        go('pdf',"only={$from}-{$to}");
    } else {
        echo page("
            <h2>View a specific range of passages</h2>
            <form action='gordian.php' method='post'>
                <input type='hidden' name='mode' value='pdf-range'>
                <div class='form-row flex'>
                    <label for='from'>From</label>
                    <input type='text' name='from' value=''>
                    <label for='to'>To</label>
                    <input type='text' name='to' value=''>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Show'>
                </div>
            </form>
        ");
    }

?>