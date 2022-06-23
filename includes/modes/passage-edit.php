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
            <form action='gordian.php' method='post'>
                <div class='fieldset'>
                    <h2 class='title'>Find Passage</h2>
                    <input type='hidden' name='mode' value='passage-edit'>
                    <div class='form-row flex'>
                        <label for='number'>Number</label>
                        <input type='text' name='number' value='' style='flex-shrink:1'>
                        <label for='passage_name'>Name</label>
                        <input type='text' name='passage_name' value='' style='flex-shrink:1'>
                        <label for='pid'>ID</label>
                        <input type='text' name='pid' value='' style='flex-shrink:1'>
                        <input type='submit' value='Find'>
                    </div>
                </div>
            </form>
        ";
        if (is_numeric($pid)) {
            $idx     = $idx ? $idx : $_SESSION['gb']['pids'][$pid];
            $passage = $_SESSION['gb']['story']['passages'][$idx];
            $tags    = $passage['tags'] ? implode(' ',$passage['tags']) : '';
            $saved   = $_REQUEST['saved'] ? '<p><i>Saved: ' . date('Y-m-d G:i:s') . '</i></p>' : '';

            // render the passage
            $number  = $_SESSION['gb']['numbering'][$pid]['number'];
            $render  = render_one($passage,$number);

            // pdf calculations
            $config = config_mpdf($root);
            $mpdf = new \Mpdf\Mpdf($config);
            $mpdf->WriteHTML(htmldoc(false,[],[$number]));
            $height = $mpdf->y - $mpdf->tMargin;

            //echo "<pre>"; print_r($mpdf); echo "</pre>";

            echo page("
                $form
                <h2>Editing: #{$_SESSION['gb']['numbering'][$pid]['number']} — {$passage['name']} (pid {$passage['pid']})</h2>
                <form action='gordian.php' method='post'>
                    <input type='hidden' name='mode' value='passage-save'>
                    <input type='hidden' name='pid' value='{$pid}'>
                    <div class='form-row flex'>
                        <label>Swap with <input type='text' name='swap_number' value='{$_SESSION['gb']['numbering'][$pid]['number']}'></label>
                        <label>Move to   <input type='text' name='move_number' value='{$_SESSION['gb']['numbering'][$pid]['number']}'></label>
                    </div>
                    <div class='form-row'>
                        <label for='tags'>Edit Tags</label>
                        <input type='text' name='tags' value='{$tags}'>
                    </div>
                    <div class='form-row cm-padded cm-auto'>
                        <label for='text'>Edit Passage Text</label>
                        <textarea name='text' class='codemirror html' rows='20'>{$passage['text']}</textarea>
                        $saved
                    </div>
                    <div class='form-row'>
                        <input type='submit' value='Save'>
                    </div>
                </form>
                </div><div class='content'>
                <h2>Preview</h2>
                <div class='passage_example'>
                $render
                </div>

                <p><a href='gordian.php?mode=pdf&only=$number'>Preview as PDF</a></p>

                <p>{$mpdf->page} page; {$height} mm</p>

            ",['title' => "Edit Passage : {$passage['name']}", 'sidebar' => gb_passage_edit_list()]);
        } else {
            echo page($form,['title' => "Edit Passage", 'sidebar' => gb_passage_edit_list()]);
        }

?>