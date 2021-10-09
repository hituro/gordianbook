<?php

        echo page("
            <form action='gordian.php' method='post'>
                <input type='hidden' name='mode' value='intro-save'>
                <div class='form-row'>
                    <label for='gb-front-cover'>Edit Front Cover</label>
                    <textarea name='gb-front-cover' rows='10'>{$_SESSION['gb']['gb-front-cover']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='intro'>Edit Introduction</label>
                    <textarea name='intro' rows='10'>{$_SESSION['gb']['gb-introduction']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='rear'>Edit Conclusion</label>
                    <textarea name='rear' rows='10'>{$_SESSION['gb']['gb-rear']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='gb-rear-cover'>Edit Rear Cover</label>
                    <textarea name='gb-rear-cover' rows='10'>{$_SESSION['gb']['gb-rear-cover']['text']}</textarea>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
            </form>
        ",['title' => "Edit Introduction"]);

?>