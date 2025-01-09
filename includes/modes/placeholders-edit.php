<?php

        echo page("
            <form action='gordian.php' method='post' class=' cm-padded'>
                <input type='hidden' name='mode' value='placeholders-save'>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
                <div class='form-row cm-auto'>
                    <label for='gb-placeholders'>Placeholders</label>
                    <textarea name='gb-placeholders' class='codemirror javascript' rows='50'>{$_SESSION['gb']['gb-placeholders']['passage']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
            </form>
        ",['title' => "Edit Placeholders"]);

?>