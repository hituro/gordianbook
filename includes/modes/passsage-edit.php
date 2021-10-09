<?php

        if ($_REQUEST['passage_name']) {
            $pid     = $_SESSION['gb']['passage_names'][$_REQUEST['passage_name']]['pid'];
            $idx     = $_SESSION['gb']['passage_names'][$_REQUEST['passage_name']]['idx'];
        } else if ($_REQUEST['pid']) {
            $pid     = $_REQUEST['pid'];
        } else if ($_REQUEST['number']) {
            $pid     = $_SESSION['gb']['number_order'][$_REQUEST['number']];
        }
        $form = "
            <h2>Enter a Passage Name or ID to edit</h2>
            <form action='gordian.php' method='post'>
                <input type='hidden' name='mode' value='passage-edit'>
                <div class='form-row'>
                    <label for='number'>Passage Number</label>
                    <input type='text' name='number' value=''>
                </div>
                <div class='form-row'>
                    <label for='passage_name'>Passage Name</label>
                    <input type='text' name='passage_name' value=''>
                </div>
                <div class='form-row'>
                    <label for='pid'>Passage ID</label>
                    <input type='text' name='pid' value=''>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Find'>
                </div>
            </form>
        ";
        if (is_numeric($pid)) {
            $idx     = $idx ? $idx : $_SESSION['gb']['pids'][$pid];
            $passage = $_SESSION['gb']['story']['passages'][$idx];
            $tags    = $passage['tags'] ? implode(' ',$passage['tags']) : '';
            echo page("
                <h2>Editing: #{$_SESSION['gb']['numbering'][$pid]['number']} — {$passage['name']} (pid {$passage['pid']})</h2>
                <form action='gordian.php' method='post'>
                    <input type='hidden' name='mode' value='passage-save'>
                    <input type='hidden' name='pid' value='{$pid}'>
                    <div class='form-row'>
                        <label for='number'>Change Number</label>
                        <input type='text' name='number' value='{$_SESSION['gb']['numbering'][$pid]['number']}'>
                    </div>
                    <div class='form-row'>
                        <label for='tags'>Edit Tags</label>
                        <input type='text' name='tags' value='{$tags}'>
                    </div>
                    <div class='form-row'>
                        <label for='text'>Edit Passage Text</label>
                        <textarea name='text' rows='20'>{$passage['text']}</textarea>
                    </div>
                    <div class='form-row'>
                        <input type='submit' value='Save'>
                    </div>
                </form>
                $form
            ",['title' => "Edit Passage : {$passage['name']}", 'sidebar' => gb_passage_edit_list()]);
        } else {
            echo page($form,['title' => "Edit Passage", 'sidebar' => gb_passage_edit_list()]);
        }

?>