<?php

    echo gb_header([
        'title' => "Load Twison",
        'js'    => ['vue','js/tabs.js']
    ]);
    echo "<form action='gordian.php' method='POST' id='tabform' enctype='multipart/form-data'>
            <input type='hidden' name='mode' value='import-json'>
            <ul class='tabnav'>
                <li v-on:click='tab=1' v-bind:class=\"[tab==1 ? 'active' : '']\">Paste</li>
                <li v-on:click='tab=2' v-bind:class=\"[tab==2 ? 'active' : '']\">Upload</li>
            </ul>
            <div class='tabs'>
                <div class='tab' v-show='tab==1'>
                    <div class='form-row'>
                        <label for='story-json'>Enter story data (Twison/Twee/Twine Archive)</label>
                        <textarea name='story-json' rows='40'></textarea>
                    </div>
                </div>
                <div class='tab' v-show='tab==2'>
                    <div class='form-row'>
                        <label for='story-json-upload'>Upload story (Twison/Twee/Twine Archive)</label>
                        <input type='file' name='story-json-upload' />
                    </div>
                </div>
            </div>
            <div class='form-row'>
                <input type='submit' value='import'>
            </div>
        </form>";
    echo gb_footer();

?>