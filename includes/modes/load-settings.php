<?php

        echo gb_header(['title' => "Load Game Settings JSON"]);
        echo "<form action='gordian.php' method='POST'>
                <input type='hidden' name='mode' value='import-settings'>
                <div class='form-row'>
                    <label for='game-settings-json'>Enter game settings JSON</label>
                    <textarea name='game-settings-json' rows='40'></textarea>
                </div>
                <div class='form-row'>
                    <input type='submit' value='import'>
                </div>
              </form>";
        echo gb_footer();

?>