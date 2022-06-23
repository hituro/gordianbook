<?php

        echo page("
            <form action='gordian.php' method='post' class=' cm-padded'>
                <input type='hidden' name='mode' value='templates-save'>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
                <div class='form-row cm-auto'>
                    <label for='gb-templates'>Templates</label>
                    <textarea name='gb-templates' class='codemirror html' rows='50'>{$_SESSION['gb']['gb-templates']['passage']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
            </form>
        ",['title' => "Edit Templates"]);

?>