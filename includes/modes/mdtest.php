<?php

        if ($_REQUEST['mdtest']) {
            $t    = $_REQUEST['mdtest'];
            if (preg_match_all("/(?:<template name=\"(.*)\"[^>]*>(.*)<\/template>|<t:(.*)>(.*)<\/t>)/sU",$t,$templatematch,PREG_SET_ORDER)) {
                $t   = templates($t,$templatematch);
            }
            $md   = markdown($t,$_REQUEST['mdmode']);
            $esc  = htmlspecialchars($md);
            $p    = autop(process_links(['text' => $md]),0);
            $para = htmlspecialchars($p);
        }
        $h = ($_REQUEST['mdmode'] == 'harlowe')   ? 'selected' : '';
        $s = ($_REQUEST['mdmode'] == 'sugarcube') ? 'selected' : '';
        echo page("
            <form action='gordian.php' method='post'>
                <input type='hidden' name='mode' value='mdtest'>
                <div class='form-row'>
                    <label for='mdmode'>Mode</label>
                    <select name='mdmode'>
                        <option $h>harlowe</option>
                        <option $s>sugarcube</option>
                    </select>
                </div>
                <div class='form-row'>
                    <label for='mdtest'>Text</label>
                    <textarea name='mdtest' rows='10'>{$_REQUEST['mdtest']}</textarea>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Test'>
                </div>
            </form>
            <pre style='overflow:scroll'>$esc</pre>
            $md
            <pre style='overflow:scroll'>$para</pre>
            $p",['title' => "Markdown Test", 'css' => ['css/game.css']]);

?>