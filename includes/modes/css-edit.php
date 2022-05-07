<?php

    echo page("
        <form action='gordian.php' method='post'>
            <input type='hidden' name='mode' value='css-save'>
            <div class='form-row'>
                <input type='submit' value='Save'>
            </div>

            <div class='form-row'>
                <label for='story_css'>Story Stylesheet</label>
                <textarea name='story_css' rows='20'>{$_SESSION['gb']['story_css']}</textarea>
            </div>

            <div class='form-row'>
                <label for='settings_css'>Settings CSS</label>
                <textarea name='settings_css' rows='10'>{$_SESSION['gb']['settings']['css']}</textarea>
            </div>

            <div class='form-row'>
                <input type='submit' value='Save'>
            </div>

            <ul>
                <li>Style <code>.paragraph</code> to change each entry</li>
                <li>Style <code>.game-divider</code> to change the inter-paragraph rules</li>
                <li>Style <code>.stats</code> to change stat rows  (made with <code>&lt;stats></code>)</li>
                <li>Style <code>.check</code> to change rules text (made with <code>&lt;check></code>)</li>
                <li>Style <code>.rules</code> to change rules text (made with <code>&lt;rules></code>)</li>
                <li>Style <code>.cover</code> to change the cover page</li>
                <li>Style <code>.cover_title</code> to change the cover page text container</li>
            </ul>
        </form>
        ",['title' => 'Settings']);

?>