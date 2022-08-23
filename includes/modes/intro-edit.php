<?php

        echo page("
            <form action='gordian.php' method='post' class=' cm-padded'>
                <input type='hidden' name='mode' value='intro-save'>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
                <div class='form-row'>
                    <label for='gb-front-cover'>Edit Front Cover (gb-front-cover)</label>
                    <textarea name='gb-front-cover' class='codemirror html' rows='10'>{$_SESSION['gb']['gb-front-cover']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='intro'>Edit Introduction (gb-introduction)</label>
                    <textarea name='intro' class='codemirror html' rows='10'>{$_SESSION['gb']['gb-introduction']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='rear'>Edit Conclusion (gb-rear)</label>
                    <textarea name='rear' class='codemirror html' rows='10'>{$_SESSION['gb']['gb-rear']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='gb-rear-cover'>Edit Rear Cover (gb-rear-cover)</label>
                    <textarea name='gb-rear-cover' class='codemirror html' rows='10'>{$_SESSION['gb']['gb-rear-cover']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
            </form>
        ",['title' => "Edit Introduction"]);

?>