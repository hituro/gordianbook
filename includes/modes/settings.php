<?php

    $mt = $_SESSION['gb']['settings']['margin_top'] ?? 15;
    $mb = $_SESSION['gb']['settings']['margin_bottom'] ?? 15;
    $ml = $_SESSION['gb']['settings']['margin_left'] ?? 10;
    $mr = $_SESSION['gb']['settings']['margin_right'] ?? 10;
    $mpl = $_SESSION['gb']['settings']['margin_print_left'] ?? 20;
    $mpr = $_SESSION['gb']['settings']['margin_print_right'] ?? 10;

    echo page("
        <form action='gordian.php' method='post'>
            <input type='hidden' name='mode' value='settings-save'>
            <div class='form-row'>
                <input type='submit' value='Save'>
            </div>
            <div class='fieldset'>
                <div class='legend'>Game Settings</div>
                <div class='form-row'>
                    <label for='end_text'>Edit End Text</label>
                    <p><i>Default 'THE END', used when a passage is tagged 'end'</i></p>
                    <textarea name='end_text' rows='2'>{$_SESSION['gb']['settings']['end_text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='death_text'>Edit Death Text</label>
                    <p><i>Default 'YOU DIED', used when a passage is tagged 'death'</i></p>
                    <textarea name='death_text' rows='2'>{$_SESSION['gb']['settings']['death_text']}</textarea>
                </div>
                <div class='form-row'>
                    <label for='mdtype'>Interpret Markdown as</label>
                    ".gb_menu('mdtype',['harlowe' => 'Harlowe 3', 'sugarcube' => 'Sugarcube 2'],$_SESSION['gb']['settings']['mdtype'])."
                </div>
            </div>

            <div class='fieldset'>
                <div class='legend'>Print Settings</div>
                <div class='form-row'>
                    <label for='page_size'>Page Size</label>
                    <p><i>Default A4-P</i></p>
                    <input name='page_size' type='text' value='{$_SESSION['gb']['settings']['page_size']}'>
                </div>
                <div class='form-row'>
                    <label for='separator'>Use paragraph separator?</label>
                    <p><i>Show a separator between each paragraph?</i></p>
                    <input name='separator' type='radio' value='1' ".($_SESSION['gb']['settings']['separator']?'CHECKED':'')."> Show
                    <input name='separator' type='radio' value='0' ".($_SESSION['gb']['settings']['separator']?'':'CHECKED')."> Don't Show
                </div>
                <div class='form-row'>
                    <label for='break'>Pagebreak after each section?</label>
                    <p><i>Put each paragraph on its own page?</i></p>
                    <input name='break' type='radio' value='1' ".($_SESSION['gb']['settings']['break']?'CHECKED':'')."> Break
                    <input name='break' type='radio' value='0' ".($_SESSION['gb']['settings']['break']?'':'CHECKED')."> Don't Break
                </div>
                <div class='form-row'>
                    <label for='cover'>Show Cover?</label>
                    <p><i>Show a cover page on the PDF?</i></p>
                    <input name='cover' type='radio' value='1' ".($_SESSION['gb']['settings']['cover']?'CHECKED':'')."> Show
                    <input name='cover' type='radio' value='0' ".($_SESSION['gb']['settings']['cover']?'':'CHECKED')."> Don't Show
                </div>
                <div class='form-row'>
                    <label for='resolution'>Resolution</label>
                    <input name='resolution' type='text' value='{$_SESSION['gb']['settings']['resolution']}' class='auto'> dpi
                </div>
                <div class='form-row'>
                    <label for='image_resolution'>Image Resolution</label>
                    <p><i>Set a different resolution for images, images will be treated as if they are at this resolution, not resized</i></p>
                    <input name='image_resolution' type='text' value='{$_SESSION['gb']['settings']['image_resolution']}' class='auto'> dpi
                </div>
                <div class='form-row'>
                    <label for='cover'>Enable low-res mode for screen PDF?</label>
                    <p><i>Use low-res 72dpi images for the screen mode?</i></p>
                    <input name='low_res' type='radio' value='1' ".($_SESSION['gb']['settings']['low_res']?'CHECKED':'')."> Enable
                    <input name='low_res' type='radio' value='0' ".($_SESSION['gb']['settings']['low_res']?'':'CHECKED')."> Disable
                </div>
                <div class='form-row'>
                    <label>Page Margins</label>
                    <p><i>Set the page margins, in mm</i></p>
                    Top <input name='margin_top'    type='text' value='{$mt}' class='auto' style='width:2em'>
                    Left <input name='margin_left'   type='text' value='{$ml}' class='auto' style='width:2em'>
                    Right <input name='margin_right'  type='text' value='{$mr}' class='auto' style='width:2em'>
                    Bottom <input name='margin_bottom' type='text' value='{$mb}' class='auto' style='width:2em'>
                    <p><i>Print margins, in mm (mirrored on facing pages)</i></p>
                    Left <input name='margin_print_left' type='text' value='{$mpl}' class='auto' style='width:2em'>
                    Right <input name='margin_print_right'  type='text' value='{$mpr}' class='auto' style='width:2em'>
                </div>
            </div>
            <div class='form-row'>
                <input type='submit' value='Save'>
            </div>
        </form>
        ",['title' => 'Settings']);

?>