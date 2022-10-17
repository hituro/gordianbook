<?php

        if ($_REQUEST['mdtest']) {
            $t    = $_REQUEST['mdtest'];
            if (preg_match_all("/(?:<template name=\"(.*)\"[^>]*>(.*)<\/template>|<t:(.*)>(.*)<\/t>)/sU",$t,$templatematch,PREG_SET_ORDER)) {
                $t   = templates($t,$templatematch);
            }
            $md   = markdown($t,$_REQUEST['mdmode']);
            $esc  = htmlspecialchars($md);
            $p    = autop(process_links(['text' => $md]),0); 
            $p    = md_apply_attributes($p);
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
            </div>
            
            <div class='content'>
            <h2>Preview</h2>
            <div class='passage_example'>$p</div>
            </div>

            <div class='content'>
            <h2><code>markdown()</code></h2>
            <pre style='overflow:scroll'>$esc</pre>
            $md
            </div>
            
            <div class='content'>
            <h2><code>autop()</code> and <code>process_links()</code></h2>
            <pre style='overflow:scroll'>$para</pre>
            ",['title' => "Markdown Test", 'css' => ['css/game.css','css/preview.css']]);

?>