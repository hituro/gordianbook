<?php

    /* =================================================================================== */
    /* GAMEBOOK OUTPUT FUNCTIONS                                                           */
    /* =================================================================================== */

    function htmldoc($print=true,$settings,$only=false) {
        //ini_set('display_errors',1);
        $link_css  = $settings['print']    ? "<style>".file_get_contents('css/print.css')."</style>" : '';
        $proof_css = $settings['proof']    ? "<style>".file_get_contents('css/proof.css')."</style>" : '';
        return "<!DOCTYPE html>
                <head>
                <title>{$_SESSION['gb']['story']['name']}</title>
                <style>".file_get_contents('css/game.css')."</style>
                $link_css
                $proof_css
                <style>{$_SESSION['gb']['story_css']}</style>
                <style>{$_SESSION['gb']['settings']['css']}</style>
                $play_css
                </head>
                <body>
                <htmlpageheader name=\"firstpageheader\" style=\"display:none\"></htmlpageheader>
                
                <htmlpagefooter name=\"firstpagefooter\" style=\"display:none\"></htmlpagefooter>
                
                <htmlpageheader name=\"otherpageheader\" style=\"display:none\"></htmlpageheader>
                
                <htmlpagefooter name=\"otherpagefooter\" style=\"display:none\">
                    <div class='footer'>{PAGENO}</div>
                </htmlpagefooter>"
                .htmlise($print,$settings,$only)."</body>
                </html>";
    }

    function playable_doc($settings) {
        $play_css  = $settings['playable'] ? "<style>".file_get_contents('css/hidden.css')
                                                      .file_get_contents('css/preview.css')
                                                      .file_get_contents('css/gamebook.css')."</style>".
                                              "<script>".file_get_contents('js/preview.js')."</script>" : '';
        return "<!DOCTYPE html>
                <head>
                    <title>{$_SESSION['gb']['story']['name']}</title>
                    <style>".file_get_contents('css/game.css')."</style>
                    <style>{$_SESSION['gb']['story_css']}</style>
                    <style>{$_SESSION['gb']['settings']['css']}</style>
                    <style>".file_get_contents('css/gamebook.css')
                            .file_get_contents('css/hidden.css')
                            .file_get_contents('css/preview.css').
                    "</style>
                    <script>".file_get_contents('js/preview.js')."</script>
                </head>
                <body>
                <div class='content'>
                ".htmlise($print,$settings,$only)."
                </div>
                </body>
                </html>";
    }

    function htmlise($print=false,$settings,$only=false) {
        $out  = '';
        $pf   = "<sethtmlpagefooter name='otherpagefooter' page='ALL' value='on'></sethtmlpagefooter>";
        $rf   = '';
        $only = get_only($only);
        if ($print && $_SESSION['gb']['settings']['cover'] && !$only && ($settings['covers'] || !$settings['print'])) {
            // use a cover page
            $text = $_SESSION['gb']['gb-front-cover'] ? process_para($_SESSION['gb']['gb-front-cover'])['text'] : "<div class='cover_top'><h1>{$_SESSION['gb']['story']['name']}</h1></div>";
            $out .= "<div class='cover_back front'></div>
                     $text";
            if ($settings['covers'] && $settings['simplex']) {
            } else if ($settings['covers'] && $settings['covers-only']) {
                $out .= "<pagebreak type='next-odd' suppress='on' resetpagenum='1'></pagebreak>";
            } else if ($settings['covers']) {
                $out .= "<pagebreak resetpagenum='1'></pagebreak>";
            } else {
                $out .= "<pagebreak type='next-odd' resetpagenum='1'></pagebreak>";
            }
        } else if (!$settings['print'] && !$settings['covers'] && !$only) {
            $out = "<h1 class='story_name'>{$_SESSION['gb']['story']['name']}</h1>";
        }
        if (!$settings['covers'] || ($settings['print'] && $settings['covers'])) {

            /* First output any frontmatter from WF style frontmatter_X tags */
            if ($_SESSION['gb']['frontmatter'] && !$only) {
                foreach ($_SESSION['gb']['frontmatter'] AS $front_pid) {
                    $front = $_SESSION['gb']['story']['passages'][$_SESSION['gb']['pids'][$front_pid]];
                    if ($front['tags'] && in_array('breakbefore',$front['tags'])) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                    $out .= "<div class='paragraph frontmatter long'>".process_para($front,true,$print,$settings)['text']."</div>
                             <div class='body_headers'></div>";
                    if ($front['tags'] && in_array('breakafter',$front['tags'])) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                }
            }

            /* Then is gb-introduction */
            if ($_SESSION['gb']['gb-introduction'] && !$only && !$settings['playable']) {
                $out .= "<div class='paragraph introduction long' id='introduction'>".process_para($_SESSION['gb']['gb-introduction'],true,$print,$settings)['text']."</div>
                         <div class='body_headers'></div>";
                if ($_SESSION['gb']['gb-introduction']['tags'] && in_array('breakafter',$_SESSION['gb']['gb-introduction']['tags'])) {
                    $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                } else {
                    $out .= "<div class='game_divider'></div>";
                }
            }

            /* Then turn on page numbering, and start outputting actual passages */

            if (!$_REQUEST['skip_content']) {
                $out    .= $pf;
                $divider = ($_SESSION['gb']['settings']['separator'] || $settings['proof']) ? "<div class='game_divider'></div>" : '';
                $total   = count($_SESSION['gb']['numbering']);
                $count   = 0;
                foreach ($_SESSION['gb']['number_order'] AS $number => $pid) {
                    if ($only && !in_array($number,$only)) { continue; }
                    if ($number == $total) { $divider = ''; }
                    $para = $_SESSION['gb']['numbering'][$pid];
                    $pass = $_SESSION['gb']['story']['passages'][$para['index']];
                    if ($pass['tags'] && in_array('skip',$pass['tags'])) { continue; }
                    $long = ($pass['tags'] && in_array('long',$pass['tags'])) ? 'long' : '';
                    $edit = ($print || $settings['proof'] || $settings['playable'] || $settings['export']) ? '' : " (<a href='gordian.php?mode=passage-edit&pid=$pid' target='_new'>edit</a>)";
                    $pp   = process_para($pass,true,$print,$settings);
                    
                    if ($pass['tags'] && in_array('breakbefore',$pass['tags'])) {
                        $out .= "<pagebreak suppress='off'/>";
                    }
                    
                    if ($settings['proof']) {
                        $proof_info = "<h3 class='proof_info'>pid #{$pid} — {$pass['name']}</h3>";
                    }
                    $out .= $rf ? $rf : $pf;
                    
                    if ($_REQUEST['dice']) {
                        $r    = rand(1,100);
                        $out .= "
                        <htmlpagefooter name=\"{$number}_footer\" style=\"display:none\">
                            <div class='footer'>
                              <img src='/images/app/dice-".rand(1,6).".png' style='width:55px;'> 
                              <img src='/images/app/dice-".rand(1,6).".png' style='width:55px;'></div>
                        </htmlpagefooter>";
                        $rf   = "<sethtmlpagefooter name='{$number}_footer' page='ALL' value='on'></sethtmlpagefooter>";
                    }
                    
                    $out .= "
                            {$pp['before']}
                            <div class='paragraph $long' id='para_$number'>
                            <bookmark content='$number'></bookmark>
                            <h2 id='$number'><a name='$number'>$number.</a> {$edit}</h2>
                            $proof_info
                            {$pp['text']}
                            $tag
                            </div>
                            $divider
                            {$pp['after']}";
                    $out .= $rf ? $rf : $pf;
                    if ($_SESSION['gb']['settings']['break'] || ($pass['tags'] && in_array('breakafter',$pass['tags']))) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                    $count ++;
                    if ($_REQUEST['limit'] && $count >= $_REQUEST['limit']) { break; }
                }
            }


            /* Then is gb-introduction again, for playable version */
            if ($_SESSION['gb']['gb-introduction'] && !$only && $settings['playable']) {
                $out .= "<div class='paragraph introduction long' id='introduction'>".process_para($_SESSION['gb']['gb-introduction'],true,$print,$settings)['text']."</div>";
            }
            
            /* Then output any backmatter from WF style backmatter_X tags */
            if ($_SESSION['gb']['backmatter'] && !$only) {
                $out .= "<pagebreak suppress='off'></pagebreak>";
                foreach ($_SESSION['gb']['backmatter'] AS $back_pid) {
                    $back = $_SESSION['gb']['story']['passages'][$_SESSION['gb']['pids'][$back_pid]];
                    if ($back['tags'] && in_array('breakbefore',$back['tags'])) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                    $out .= "<div class='paragraph backmatter long'>".process_para($back,true,$print,$settings)['text']."</div>
                             <div class='body_headers'></div>";
                    if ($back['tags'] && in_array('breakafter',$back['tags'])) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                }
            }

            /* Finally, output gb-rear */

            if ($_SESSION['gb']['gb-rear'] && !$only) {
                $out .= "<pagebreak suppress='off'></pagebreak><div class='paragraph rear'>".process_para($_SESSION['gb']['gb-rear'])['text']."</div>";
            }
        }
        if ($print && $_SESSION['gb']['settings']['cover'] && ($_SESSION['gb']['gb-rear-cover'])['text'] && !$only && ($settings['covers'] || !$settings['print'])) {
            // use a cover page
            if ($settings['covers'] && !$settings['simplex']) {
                $out .= "<pagebreak type='next-odd' suppress='on' resetpagenum='1'></pagebreak>";
            }
            $text = process_para($_SESSION['gb']['gb-rear-cover'])['text'];
            $out .= "<pagebreak resetpagenum='1' odd-header-name='firstpageheader' odd-footer-name='firstpagefooter' suppress='on'></pagebreak>
                     <div class='cover_back rear'></div>
                     $text";
        }
        return $out;
    }

    function process_para($passage,$process_markdown=true,$print=false,$settings=[]) {
        // need to turn each link in [[]] into a link to the correct paragraph
        //echo "<pre>".print_r($passage,1)."</pre>";
        if ($passage['tags'] && in_array('death',$passage['tags'])) {
            $tag = "<div class='end death'>{$_SESSION['gb']['settings']['death_text']}</div>";
        } else if ($passage['tags'] && in_array('end',$passage['tags'])) { 
            $tag = "<div class='end'>{$_SESSION['gb']['settings']['end_text']}</div>";
        } else {
            $tag = '';
        }
        $text = $passage['text'];
        $text = process_links($passage,$settings); 
        if (preg_match_all("/(?:<template name=\"(.*)\"[^>]*>(.*)<\/template>|<t:(.*)>(.*)<\/t>)/sU",$text,$templatematch,PREG_SET_ORDER)) {
            $text   = templates($text,$templatematch,$print);
        }
        if (preg_match("|<after>(.*?)</after>|s",$text,$aftermatch)) {
            $after  = $aftermatch[1];
            $text   = str_replace($aftermatch[0],'',$text);
        }
        if (preg_match("|<before>(.*?)</before>|s",$text,$beforematch)) {
            $before = $beforematch[1];
            $text   = str_replace($beforematch[0],'',$text);
        }
        if (preg_match("|<page-before>(.*?)</page-before>|s",$text,$beforematch)) {
            $before = "<pagebreak></pagebreak><sethtmlpagefooter name='otherpagefooter' page='ALL' value='on'></sethtmlpagefooter>" . $beforematch[1];
            $text   = str_replace($beforematch[0],'',$text);
        }
        if ($process_markdown) {
            $text   = markdown($text, $_SESSION['gb']['settings']['mdtype'],$settings);
            $text   = autop($text,0).$tag;
            $after  = $after ? markdown($after, $_SESSION['gb']['settings']['mdtype'],$settings) : '';
            $before = $before ? markdown($before, $_SESSION['gb']['settings']['mdtype'],$settings) : '';
        }
        //echo "<pre>".htmlspecialchars($text)."</pre>";
        return ['text' => $text, 'after' => $after, 'before' => $before];
    }

    /* TEMPLATE FUNCTIONS */

    function templates($text,$templatematches,$print=false) {
        foreach ($templatematches AS $t) {
            //echo "<pre>TEMPLATE MATCH ".htmlspecialchars(print_r($t,1))."</pre>";
            $r    = template($t[3] ? $t[3] : $t[1],$t[4] ? $t[4] : $t[2],null,'',$print);
            $text = str_replace($t[0],$r,$text);
        }
        return $text;
    }

    function template($name,$data,$template=null,$prefix='',$print=false) {
        //echo "<pre>CALLED templates with $name/$template, ".htmlspecialchars(print_r($data,1)).htmlspecialchars(print_r($_SESSION['gb']['gb-templates']['templates'],1))."</pre>";
        $template = $template ? $template : $_SESSION['gb']['gb-templates']['templates'][$name];
        if (!is_array($data)) {
            $data     = trim(preg_replace_callback('/("[^"]*")/',function($matches) {  return str_replace(["\r\n","\n","\r"],'\\n',$matches[1]); },$data));
            //echo "<pre>" . htmlspecialchars(($data)) . "</pre>";
            $data     = substr($data,0,1) == '{' ? json_decode($data,true) : ['default' => $data];
        }
        //echo "<pre>" . htmlspecialchars(($template)) . "</pre>";
        $parsed    = template_parse($template);
        //echo "<pre>" . htmlspecialchars(($parsed)) . "</pre>";
        //echo "<pre>" . htmlspecialchars(print_r($data,1)) . "</pre>";
        $processed = template_execute($parsed,$data,$print);
        //echo "<pre>" . htmlspecialchars(($processed)) . "</pre>";
        return trim($processed);
    }

    /**
     * Turn a template into interpolated PHP code
     */
    function template_parse($t) {
        // take template and replace all tokens with PHP callable versions, then eval the result
        // first clean template of possible code
        $t = str_replace(['<?php','<?=','<?','?>'],'',$t);
        $t = str_replace('$','\$',$t);
        // replace variables with echos
        $t = preg_replace_callback_array(["/{{([a-zA-Z_.0-9]+)}}/" => 'template_var'],$t);
        // replace <repeat> with foreach loops
        $t = preg_replace_callback_array(["/<repeat *(\S+) *as *(\w+), *(\w+)>/i" => 'template_repeat'],$t);
        $t = preg_replace_callback_array(["/<repeat *(\S+) *as *(\w+)>/i" => 'template_repeat'],$t);
        $t = preg_replace_callback_array(["/<repeat *(\S+)>/iU" => 'template_repeat'],$t);
        $t = preg_replace("/<\/repeat.*?>/i",'<?php } ?>',$t);
        // replace <if> with if
        $t = preg_replace_callback_array(["/<if (.+)>/iU" => 'template_if'],$t);
        $t = preg_replace("/<else>/i",'<?php } else { ?>',$t);
        $t = preg_replace("/<\/if>/i",'<?php } ?>',$t);
        return $t;
    }

    /**
     * Process an <if> tag inside a template
     */
    function template_if($var) {
        // split the content of the tag on spaces and word boundaries to tokenise it
        $tokens  = preg_split("/(\b|\s)/",$var[1],-1,PREG_SPLIT_DELIM_CAPTURE + PREG_SPLIT_NO_EMPTY);
        $t       = [];
        $consume = 0;
        $token   = '';
        $opmap   = ['or' => '||', 'and' => '&&', 'not' => '!'];
        foreach ($tokens AS $i => $to) {
            //echo "TOKEN $i |$to|\n";
            // if $consume is non-zero it means we are consuming elements from the token steam
            // to construct a multi-part token, such as a delimited string, or an arrray access
            if ($consume) {
                if ($consume == 'var') {
                    //echo "    + CONSUMING FOR var\n";
                    // consume until we see something that is not a . or valid variable -> used for var.var.var
                    if ($to != '.' && !preg_match("/[a-zA-Z_0-9]/",$to)) {
                        //echo "    + |$to| is not a valid part of our var, so write out token\n";
                        $t[] = template_var(['',$token],false);
                        $consume = 0; $token = '';
                    } else {
                        //echo "    + ADDING |$to| to token\n";
                        $token .= $to;
                        continue;
                    }
                } else if (is_numeric($consume)) {
                    // consuming N tokens -> used for var.var syntax
                    $consume --;
                    $token .= $to;
                    if ($consume > 0) { continue; } else {
                        $t[] = template_var(['',$token],false);
                        $consume = 0; $token = '';
                    }
                    continue;
                } else if ($to == $consume) {
                    // consuming until we hit a specific token -> used for delimiters
                    $t[]     = $token . $to;
                    $consume = 0; $token = '';
                    continue;
                } else {
                    $token  .= $to;
                    continue;
                }
            }
            if ($to == ' ') {
                // if we have whitespace and are not in a string, skip it
                continue;
            }
            if ($tokens[$i +1] == '.' ) {
                // start consuming for a dot-notation variable
                $token   = $to;
                $consume = 'var';
                //echo "    + START CONSUMING FOR var\n";
            } else if ($to == '"' || $to == "'") {
                // start consuming for a delimited string
                $token   = $to;
                $consume = $to;
            } else if (is_numeric($to)) {
                $t[] = $to;
            } else if (array_key_exists($to,$opmap)) {
                // convert nice operators to PHP versions
                $t[] = $opmap[$to];
            } else if (preg_match("/[a-zA-Z_0-9]/",$to)) {
                $t[] = template_var(['',$to],false);
            } else {
                $t[] = $to;
            }
        }
        // a token may be left at the end of the tokenisation pass, which should be added
        if ($token && $consume == 'var') { 
            // echo "APPENDING $token\n";
            $t[] = template_var(['',$token],false);
        }
        $expr = implode(' ',$t);
        return "<?php if($expr) { ?>";
    }

    /**
     * Process a <repeat> tag inside a template
     */
    function template_repeat($var) {
        if ($var[3]) {
            // foreach loops with key
            $v1 = template_var(['',$var[1]],false);
            $v2 = "\$gbt_{$var[3]}";
            $v3 = "\$gbt_{$var[2]}";
            $vn = str_replace('.','_',$var[1]);
            return "<?php \$gbt_{$vn}_length = count($v1); \$gbt_{$vn}_index = 0; foreach($v1 AS $v3 => $v2) { \$gbt_{$vn}_index ++; \$gbt_last = (\$gbt_{$vn}_length == \$gbt_{$vn}_index); ?>";
        } else if ($var[2]) {
            // foreach loops
            $v1 = template_var(['',$var[1]],false);
            $v2 = "\$gbt_{$var[2]}";
            $vn = str_replace('.','_',$var[1]);
            return "<?php \$gbt_{$vn}_length = count($v1); \$gbt_{$vn}_index = 0; foreach($v1 AS \$gbt_idx => $v2) { \$gbt_{$vn}_index ++; \$gbt_last = (\$gbt_{$vn}_length == \$gbt_{$vn}_index); ?>";
        } else {
            // simple for loops
            $v1 = is_numeric($var[1]) ? $var[1] : template_var(['',$var[1]],false);
            return "<?php for(\$gbt_idx = 1;\$gbt_idx <= $v1;\$gbt_idx ++) { \$gbt_last = (\$gbt_idx == $v1); ?>";
        }
    }

    /**
     * Process a {{variable}} inside a template
     */
    function template_var($var,$enclose=true) {
        if (strpos($var[1],'.')) {
            $parts = explode('.',$var[1]);
            $out   = "\$gbt_" . array_shift($parts);
            foreach ($parts AS $part) {
                $out .= "['$part']";
            }
        } else {
            $out   = "\$gbt_{$var[1]}";
        }
        $out = str_replace('$_','$',$out);
        return $enclose ? "<?=$out?>" : $out;
    }

    /**
     * Take a compiled template (from template_parse()) and use eval() to execute it
     */
    function template_execute($t,$gbt_data,$gbt_gb_print=false,$show_defined=false) {
        if ($gbt_data) {
            extract($gbt_data,EXTR_PREFIX_ALL,'gbt');
        }
        if ($show_defined) {
            echo "<pre>" . print_r(get_defined_vars(),1) . "</pre>";
        }
        ob_start();
        eval("?>$t<?php ");
        return ob_get_clean();
    }

    /* MARKDOWN FUNCTIONS */

    function markdown($text,$mode='harlowe',$settings=[]) {
        // very basic markdown parse
        // includes
        $include = "/(\(display: *\"(.*?)\"\)|<<include *\"(.*?)\">>)/";
        $text = preg_replace_callback($include, 
                    function($m) { 
                        $idx = $_SESSION['gb']['passage_names'][$m[2]]['idx'];
                        return process_para($_SESSION['gb']['story']['passages'][$idx],false,$settings)['text']; 
                    },$text);
        $text = preg_replace("/(\r\n|\n|\r)/", "\n", $text); // cross-platform newlines
        // trailing line collapse
        $text = preg_replace("|\\\\\n|",' ',$text);
        $text = preg_replace("|\n\\\\|",' ',$text);
        // WF no-process sections
        list($text,$ncplaceholders) = md_noprocess($text);
        // comments 
        $text = preg_replace("|/[*%](.*?)[*%]/|s","<comment>$1</comment>",$text);
        $text = preg_replace("|<!--(.*?)-->|s","<comment>$1</comment>",$text);
        // lists
        $text = md_list($text,'*','ul');
        $text = ($mode == 'harlowe') ? md_list($text,'0.','ol') : md_list($text,'#','ol');
        $text = ($mode == 'harlowe') ? $text : md_list($text,'>','blockquote','',"<br>");
        // alignment
        $text = ($mode == 'harlowe') ? md_align($text) : $text;
        // hr
        $hr   = ($mode == 'harlowe') ? 3 : 4;
        $text = preg_replace("/^\s*-{{$hr},}\s*$/m","<hr>",$text);
        // bold
        $text = preg_replace("/\*\*(.+?)\*\*/s","<b>$1</b>",$text);
        $text = preg_replace("/''(.+?)''/s","<b>$1</b>",$text);
        // italic
        $text = preg_replace("/\*(.+?)\*/s","<i>$1</i>",$text);
        $text = preg_replace("|//(.+?)//|s","<i>$1</i>",$text);
        $text = preg_replace("/http(s){0,1}:(<\/i>|<i>)/s","http$1://",$text);
        // underline
        $text = preg_replace("/__(.+?)__/s","<u>$1</u>",$text);
        // strikethrough
        $text = preg_replace("/--(.+?)--/s","<del>$1</del>",$text);
        $text = preg_replace("/==(.+?)==/s","<del>$1</del>",$text);
        // superscript
        $text = preg_replace("/\^\^(.+?)\^\^/s","<sup>$1</sup>",$text);
        $text = preg_replace("/~~(.+?)~~/s","<sub>$1</sub>",$text);
        // headings
        $h    = ($mode == 'harlowe') ? '#' : '!';
        $text = preg_replace_callback("/^\s*{$h}{$h}{$h}{$h}(.*)$/m",function($m) { return "\n<h6>".trim($m[1])."</h6>"; },$text);
        $text = preg_replace_callback("/^\s*{$h}{$h}{$h}(.*)$/m",function($m) { return "\n<h5>".trim($m[1])."</h5>"; },$text);
        $text = preg_replace_callback("/^\s*{$h}{$h}(.*)$/m",function($m) { return "\n<h4>".trim($m[1])."</h4>"; },$text);
        $text = preg_replace_callback("/^\s*{$h}(.*)$/m",function($m) { return "\n<h3>".trim($m[1])."</h3>"; },$text);
        // rules
        $text = preg_replace("/<check>(.+?)<\/check>/s","<span class='check'>$1</span>",$text);
        $text = preg_replace("/<rules>(.+?)<\/rules>/s","<div class='rules'>$1</div>",$text);
        $text = preg_replace("/<stats>(.+?)<\/stats>/s","<div class='stats'>$1</div>",$text);
        $text = preg_replace("/<special>(.+?)<\/special>/s","<div class='special'>$1</div>",$text);
        $text = preg_replace("/<item>(.+?)<\/item>/s","<b class='item'>$1</b>",$text);
        $text = preg_replace("/<it>(.+?)<\/it>/s","<b class='item'>$1</b>",$text);
        $text = preg_replace("/<keyword>(.+?)<\/keyword>/s","<i class='keyword'>$1</i>",$text);
        $text = preg_replace("/<k>(.+?)<\/k>/s","<i class='keyword'>$1</i>",$text);
        $text = preg_replace_callback("/<keywords(?: +cols=['\"](?P<cols>[0-9]+)['\"])?>(?P<body>.*?)<\/keywords>/s","md_keywords",$text);
        $text = preg_replace_callback("/<checkbox-list(?: +cols=['\"](?P<cols>[0-9]+)['\"])?>(?P<body>.*?)<\/checkbox-list>/s","md_checkbox_list",$text);
        $text = preg_replace_callback("/<checkboxes>(.+?)<\/checkboxes>/s","md_boxes",$text);
        // comments 
        $text = preg_replace("|<comment>(.*?)</comment>|s","<!-- $1 -->",$text);
        // image resolution
        $text = str_replace("{RES}",$_SESSION['gb']['settings']['low_res'] ? 72 :  $_SESSION['gb']['settings']['image_resolution'],$text);
        // restore no-process sections
        $text = md_restore_placeholders($text,$ncplaceholders);
        return $text;
    }

    function md_noprocess($text) {
        preg_match_all("/(`+)((?:.(?!\\1))*.?)\\1/",$text,$blocks,PREG_OFFSET_CAPTURE);
        $splices = [];
        foreach ($blocks[0] AS $aidx => $aitem) {
            $splices[] = ['rep' => "GB_MD_PLACEHOLDER_$aidx", 'start' => $aitem[1], 'length' => strlen($aitem[0]), 'orig' => str_replace($blocks[1][$aidx][0],'',$aitem[0])];
        }
        $splices = array_reverse($splices);
        foreach ($splices AS $splice) {
            $text = str_splice($text,$splice['rep'],$splice['start'],$splice['length']);
        }
        return [$text,$splices];
    }

    function md_restore_placeholders($text,$placeholders) {
        foreach ($placeholders AS $ph) {
            $text = str_replace($ph['rep'],$ph['orig'],$text);
        }
        return $text;
    }

    function md_list($text,$list_char,$list_type,$start='<li>',$end='</li>') {
        $lists = ['depth' => 0, 'splices' => []];
        $lc    = addcslashes($list_char,'*.');
        $total = preg_match_all("/^\s*((?:{$lc})+) +(.*)$/m",$text,$litems,PREG_OFFSET_CAPTURE) - 1;
        //echo "<pre>TURNING $list_char into $list_type ".print_r($litems,1) . "</pre>";
        foreach ($litems[0] AS $lidx => $litem) {
            $depth = substr_count($litems[1][$lidx][0],$list_char);
            //echo "<pre>INDEX $lidx: {$litems[1][$lidx][0]}, depth $depth running depth {$lists['depth']}</pre>";
            if ($depth > $lists['depth']) {
                $diff = $depth - $lists['depth'];
                //echo "<pre>  + increase depth by $diff </pre>";
                $lists['depth'] = $depth;
                $rep = str_repeat("<{$list_type}>",$diff) . $start.trim($litems[2][$lidx][0]).$end;
            } else if ($depth < $lists['depth']) {
                $diff = $lists['depth'] - $depth;
                //echo "<pre>  - increase depth by $diff </pre>";
                //echo "Closing lists here";
                $lists['depth'] = $depth;
                $rep = str_repeat("</{$list_type}>",$diff) . $start.trim($litems[2][$lidx][0]).$end;
            } else {
                //echo "<pre>    stay at current depth $depth</pre>";
                $rep = $start.trim($litems[2][$lidx][0]).$end;
            }
            $expected = $litem[1] + strlen($litem[0]) + 1;
            //echo "<pre>Check that {$expected} == {$litems[0][$lidx+1][1]}</pre>";
            if ($lidx == $total) {
                $rep .= str_repeat("</{$list_type}>",$depth);
            } else if ($expected != $litems[0][$lidx+1][1]) {
                // if the next list item doesn't start where this one ends, start a new list
                //echo "Closing lists here";
                $lists['depth']  = 0;
                $rep .= str_repeat("</{$list_type}>",$depth);
            }
            $lists['splices'][] = ['rep' => $rep, 'start' => $litems[0][$lidx][1], 'length' => strlen($litems[0][$lidx][0])];
        }
        $lists['splices'] = array_reverse($lists['splices']);
        foreach ($lists['splices'] AS $splice) {
            //echo "<pre>    insert ".htmlspecialchars($splice['rep'])." at {$splice['start']} for {$splice['length']}</pre>";
            //if ($list_type == 'blockquote') { $splice['rep'] = str_replace("\n",'<br>',$splice['rep']); }
            $text = str_splice($text,$splice['rep'],$splice['start'],$splice['length']);
        }
        return $text;
    }

    function md_align($text) {
        preg_match_all("/^ *(==+>|=+><=+|<==+>|<==+) *$/m",$text,$aligns,PREG_OFFSET_CAPTURE) -1;
        $open    = false;
        $splices = [];
        foreach ($aligns[0] AS $aidx => $aitem) {
            $close = $open ? '</div>' : '';
            if (preg_match("/^==+>$/",$aligns[1][$aidx][0])) { // right-aligned
                $splices[] = ['rep' => "{$close}<div class='align' style='text-align:right'>", 'start' => $aitem[1], 'length' => strlen($aitem[0])];
                $open      = true;
            }
            else if (preg_match("/^<==+$/",$aligns[1][$aidx][0])) { // right-aligned
                $splices[] = ['rep' => "{$close}", 'start' => $aitem[1], 'length' => strlen($aitem[0])];
                $open      = false;
            }
            else if (preg_match("/^<==+>$/",$aligns[1][$aidx][0])) { // right-aligned
                $splices[] = ['rep' => "{$close}<div class='align' style='text-align:justify'>", 'start' => $aitem[1], 'length' => strlen($aitem[0])];
                $open      = true;
            }
            else if (preg_match("/^(=+)><(=+)$/",$aligns[1][$aidx][0],$cmatch)) { // right-aligned
                $left      = strlen($cmatch[1]);
                $right     = strlen($cmatch[2]);
                if ($left != $right) {
                    $total     = $left + $right;
                    $mleft     = (($left / $total) * 100) / 2;
                    $mright    = (($right / $total) * 100) / 2;
                    $margins   = "width:50%;margin-left:{$mleft}%;margin-right::$mright}%";
                } else {
                    $margins   = "margin:auto;";
                }
                $splices[] = ['rep' => "{$close}<div class='align' style='text-align:center;$margins'>", 'start' => $aitem[1], 'length' => strlen($aitem[0])];
                $open      = true;
            }
        }
        //echo "<pre>"; echo htmlspecialchars(print_r($splices,1)); echo "</pre>";
        $splices = array_reverse($splices);
        foreach ($splices AS $splice) {
            $text = str_splice($text,$splice['rep'],$splice['start'],$splice['length']);
        }
        if ($open) { $text .= '</div>'; }
        return $text;
    }

    function md_keywords($matches) {
        if (substr(trim($matches['body']),0,1) == '{') {
            $mode      = 'json';
            $info      = json_decode($matches['body'],true);
            $keys      = array_map('trim',$info['keywords']);
                         sort($keys);
            $kcount    = count($keys);
            $colcount  = $info['cols'];
        } else {
            $mode      = 'body';
            $length    = 20;
            $keys      = $matches['body'] ? explode(',',trim($matches['body'])) : array_keys($_SESSION['gb']['keywords']);
            $keys      = array_map('trim',$keys);
                         sort($keys);
            $kcount    = count($keys);
            $colcount  = $matches['cols'] ? $matches['cols'] : ceil($kcount / $length);
        }
        $colheight = ceil($kcount / $colcount);
        $cols      = array_chunk($keys,$colheight);

        //echo "<pre>"; print_r($_SESSION['gb']['keywords']); print_r($keys); print_r($cols); echo "</pre>";

        $out       = '<table class="checklist">';
        for($row = 0;$row < $colheight;$row++) {
            $out .= '<tr>';
            for($col = 0;$col < $colcount;$col++) {
                $key  = $cols[$col][$row];
                $out .= $key ? '<td class="checkbox">' . md_boxes([0,1]) . '</td><td>' . $key . '</td>' : '<td></td>';
            } 
            $out .= '</tr>';
        }
        return $out . '</table>';
    }

    function md_checkbox_list($matches) {
        if (substr(trim($matches['body']),0,1) == '{') {
            $mode      = 'json';
            $info      = json_decode($matches['body'],true);
            $keys      = $info['checkboxes'];
                         ksort($keys);
            $kcount    = count($keys);
            $colcount  = $info['cols'];
        } else {
            $mode      = 'body';
            $length    = 20;
            $keys      = md_get_checkboxes();
            $kcount    = count($keys);
            $colcount  = $matches['cols'] ? $matches['cols'] : ceil($kcount / $length);
        }
        $colheight = ceil($kcount / $colcount);
        $cols      = array_chunk($keys,$colheight);

        $out       = '<table class="checkboxlist" border="0">';
        for($row = 0;$row < $colheight;$row++) {
            $out .= '<tr>';
            for($col = 0;$col < $colcount;$col++) {
                $key  = $cols[$col][$row];
                $out .= $key ? '<tr><td class="number">' . $key[0] . '</td><td class="checkbox">' . md_boxes([0,$key[1]],'left') . '</td>' : '<td></td>';
            } 
            $out .= '</tr>';
        }
        return $out . '</table>';
    }
    function md_get_checkboxes() {
        $checkboxes = [];
        foreach ($_SESSION['gb']['story']['passages'] AS $pidx => $passage) {
            preg_match_all("/<checkboxes>(.+?)<\/checkboxes>/s",$passage['text'],$matches);
            if ($matches[1]) {
                $count   = array_sum($matches[1]);
                $number  = $_SESSION['gb']['numbering'][$passage['pid']]['number'];
                $checkboxes[$number] = [$number,$count];
            }
        }
        ksort($checkboxes);
        return $checkboxes;
    }

    function md_boxes($matches,$align='center') {
        return "<table class='checkboxes' cellSpacing='1mm' align='$align' border='0'><tr>" . str_repeat("<td class='box' width='6mm'>&nbsp;</td> ",$matches[1]) . '</tr></table>';
    }

    function str_splice($string,$replace,$start,$length) {
        $p1 = substr($string,0,$start);
        $p2 = substr($string,$start+$length);
        return $p1 . $replace . $p2;
    }

    function autop($pee, $br=1) {
        $pee = preg_replace("/(\r\n|\n|\r)/", "\n", $pee); // cross-platform newlines
        $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        $arr = explode("\n\n",$pee);
        $out = '';
        foreach ($arr AS $part) {
            if (!preg_match('/^\s*<(.*)>\s*$/',$part)) {
                $out .= '<p>'.$part.'</p>';
            } else {
                $out .= $part;
            }
            $out .= "\n";
        }
        return str_replace(["<p><div","/div></p>","<p><table","/ul></p>"],['<div','/div>','<table','/ul>'],$out);
    }

    function process_links($passage,$settings=[]) {
        $prefix = $settings['para_links'] ? 'para_' : '';
        preg_match_all("/\[\[(.*?)\]\]/",$passage['text'],$matches);
        $debug = "<pre>"; 
        $debug .= print_r($matches,1);
        foreach ($matches[1] AS $lidx => $link) {
            $link  = html_entity_decode($link,ENT_QUOTES | ENT_HTML5);
            $parts = preg_split("/(->|<-|\|)/",$link,-1,PREG_SPLIT_DELIM_CAPTURE);
            $debug .= print_r($parts,1);
            if ($parts[1] == '<-') {
                // reversed style
                $name   = $parts[2];
                $pid    = $_SESSION['gb']['passage_names'][$parts[0]];
                $debug .= " LINK PATTERN 1 ";
            } else if ($parts[1]) {
                $name   = $parts[0];
                $pid    = $_SESSION['gb']['passage_names'][$parts[2]];
                $debug .= " LINK PATTERN 2 $name -> {$parts[2]} ($pid)\n";
            } else {
                $name   = $parts[0];
                $pid    = $_SESSION['gb']['passage_names'][$parts[0]];
                $debug .= " LINK PATTERN 3 ";
            }
            $debug .= print_r($_SESSION['gb']['passage_names'],1);
            $debug .= "\$name = '$name' \$pid = '{$pid['pid']}' ".print_r($_SESSION['gb']['numbering'][$pid['pid']],1);
            $number = $_SESSION['gb']['numbering'][$pid['pid']]['number'];
            $number = $number ?? "X";
            $name   = trim($name);
            if ($name == 'Turnto') {
                $debug .= " TURNTO LINK ";
                $ltext = "Turn to $number";
            } else if ($name == 't_urnto') {
                $ltext = "turn to $number";
            } else if ($name == 'turnto' || $name === '') {
                $debug .= " TURNTO LINK ";
                $pattern = "/[.?!]\s+\[\[".str_replace('|','\|',$link)."/"; 
                if (preg_match($pattern,$passage['text'],$lmatches)) {
                    $ltext = "Turn to $number";
                } else {
                    $ltext = "turn to $number";
                }
            } else if ($name == 'Returnto') {
                $ltext = "Return to $number";
            } else if ($name == 'returnto') {
                $ltext = "return to $number";
            } else if ($name == '#') {
                $ltext = "$number";
            } else {
                $ltext = "$name (turn to $number)";
            }
            $debug .= " $ltext \n\n";
            //$ltext  = ($name == 'turnto') ? "turn to $number" : "$name (turn to $number)";
            $tlink  = "<a href='#{$prefix}{$number}' class='passage-link'>$ltext</a>";
            $passage['text'] = str_replace($matches[0][$lidx],$tlink,$passage['text']);
        }
        $debug .= "</pre>";
        //echo $debug;
        return $passage['text'];
    }

    /* CONVERSION FUNCTIONS */

    function convert_from_twine($twine) {
        //ini_set('display_errors',1);
        require_once __DIR__ . '/../vendor/autoload.php';
        $dom = voku\helper\HtmlDomParser::str_get_html($twine);
        $out      = [
            "passages"  => [],
            "name"      => "",
            "startnode" => 1
        ];
        // first build the document data (title, metadata)
        $story_data       = $dom->findOne('tw-storydata');
        foreach (['name','startnode','format','format-version','zoom','tag-colors','ifid','tags'] AS $e) {
            if ($story_data->getAttribute($e))  { $out[$e] = $story_data->getAttribute($e); }
        }
        // then add styles and js
        $style = $dom->findOne('#twine-user-stylesheet');
        if ($style) {
            $_SESSION['gb']['story_css'] .= $style->innerHTML();
        }
        // then add tags to tag-colours
        $tags  = $dom->find('tw-tag');
        if ($tags) {
            foreach ($tags AS $tag) {
                $out['tag-colors'][$tag->getAttribute('name')] = $tag->getAttribute('color');
            }
        }
        // then loop through passages and parse them
        $pas   = $dom->find('tw-passagedata');
        foreach ($pas AS $passage) {
            $new = [
                "pid"       => $passage->getAttribute('pid'),
                "name"      => $passage->getAttribute('name'),
                "tags"      => $passage->getAttribute('tags') ? explode(' ',$passage->getAttribute('tags')) : [],
                "position"  => $passage->getAttribute('position'),
                "size"      => $passage->getAttribute('size'),
                "text"      => html_entity_decode($passage->innerHTML(),ENT_QUOTES),
            ];
            $out['passages'][] = $new;
        }
        return $out;
    }

    function convert_from_twee($src) {
        // need to split $src into chunks based on :: separatos (1 per passage)
        // then for each passage interpret the header and tags, and set the text and pid (which is just the order)
        // first, trim everything before the first ::

        $out      = [
            "passages"  => [],
            "name"      => "",
            "startnode" => 1
        ];
        $ommit_tags = ['script','haml'];
        $passages   = explode('::',$src);
        $idx        = 1;
        foreach ($passages AS $passage) {
            if (!trim($passage)) { continue; }
            $passage = '::' . $passage;
            //echo "<pre>"; print_r($passage);
            $title   = preg_match("/^:: *(?<title>.*?) *(?:\[(?<tags>.*?)\])? *(?:(?<meta>\{.*?\}))?\n/",$passage,$matches);
            $passage = trim(str_replace($matches[0],'',$passage));
            $tags    = array_filter(explode(' ',$matches['tags']));
            if ($matches['title'] == 'StoryTitle') {
                //echo "title set\n";
                $out['name'] = $passage;
            } else if ($matches['title'] == 'StoryData') {
                //echo "data set\n";
                $json = json_decode($passage,true);
                if ($json['startnode']) { $out['startnode'] = $json['startnode']; } else { $out['startnode'] = $json['start']; }
                foreach (['format-version','zoom','tag-colors','ifid'] AS $e) {
                    if ($json[$e])  { $out[$e] = $json[$e]; }
                }
                $out['format'] = $json['format'] ? $json['format'] : 'Harlowe';
            //} else if (in_array('stylesheet',$tags)) {
            //    $_SESSION['gb']['settings']['css'] .= $passage;
            //    continue;
            } else if (array_intersect($tags,$ommit_tags)) {
                //echo "ommitted\n";
                continue;
            } else {
                $new = [
                    "name"  => $matches['title'],
                    "text"  => $passage,
                    "links" => false,
                    "pid"   => $idx,
                    "tags"  => $tags
                ];
                if ($matches['meta']) {
                    $meta = json_decode($matches['meta'],true);
                    if ($meta['position']) { $new['position'] = $meta['position']; }
                    if ($meta['size']) { $new['size'] = $meta['size']; }
                }
                //echo "create passage ".print_r($new,1)."\n";
                $out['passages'][] = $new;
                $idx ++;
            }
            //print_r($matches); echo "================================\n</pre>";
        }
        //echo "<pre>"; print_r($out); echo "</pre>";
        return $out;
    }

    function twee_passage($p,$export_numbers=true) {
        $pid    = $_SESSION['gb']['passage_names'][$p['name']]['pid'];
        if ($pid && $export_numbers) {
            $number = $_SESSION['gb']['numbering'][$pid]['number'];
            if (!in_array($number,$p['tags'])) { $p['tags'][] = $number; }
        }
        $tags = $p['tags'] ? '['.implode(' ',$p['tags']).']' : '';
        $meta = [];
        if ($p['position']) { 
            if (is_array($p['position'])) {
                // twison
                $meta['position'] = "{$p['position']['x']},{$p['position']['y']}"; 
            } else {
                // twee 
                $meta['position'] = $p['position'];
            }
        }
        if ($p['size'])     { $meta['size']     = $p['size'];     }
        $meta = $meta ? json_encode($meta) : '';
        $p['text'] = preg_replace("/(\r\n|\n|\r)/", "\n", $p['text']); // cross-platform newlines
        $tags = $tags ? ' '.$tags : '';
        $meta = $meta ? ' '.$meta : '';
        return ":: {$p['name']}{$tags}{$meta}\n{$p['text']}\n\n";
    }

    function twine_passage($p,$export_numbers=true) {
        $pid    = $_SESSION['gb']['passage_names'][$p['name']]['pid'];
        if ($pid && $export_numbers) {
            $number = $_SESSION['gb']['numbering'][$pid]['number'];
            if (!in_array($number,$p['tags'])) { $p['tags'][] = $number; }
        }
        $tag = [
            'pid'       => $p['pid'],
            'name'      => htmlspecialchars(html_entity_decode($p['name'],ENT_QUOTES),ENT_QUOTES),
            'tags'      => $p['tags'] ? htmlspecialchars(html_entity_decode(trim(implode(' ',$p['tags'])),ENT_QUOTES),ENT_QUOTES) : '',
            'position'  => $p['position'] ? $p['position'] : 100+(100*$p['pid']).',100',
            'size'      => $p['size'] ? $p['size'] : '100,100'
        ];
        foreach ($tag AS $attr => $v) {
            $attrs[] = "$attr=\"$v\"";
        }
        $attrs = implode(' ',$attrs);
        return "<tw-passagedata $attrs>".htmlspecialchars(html_entity_decode(str_replace("\r\n","\n",$p['text']),ENT_QUOTES),ENT_QUOTES)."</tw-passagedata>";
    }

    function create_templates($source) {
        preg_match_all("|<template name=\"(.*)\">(.*)</template>|sU",$source,$matches,PREG_SET_ORDER);
        $debug .= "MATCHES : " . print_r($matches,1);
        foreach ($matches AS $match) {
            $templates[$match[1]] = $match[2];
        }
        $debug .= "TEMPLATES : " . print_r($templates,1);
        return $templates;
    }

    function find_keywords($text) {
        preg_match_all("/<k(?:eyword)?>(.+?)<\/k(?:eyword)?>/s",$text,$matches);
        return $matches[1];
    }

    function find_items($text) {
        preg_match_all("/<it(?:em)?>(.+?)<\/it(?:em)?>/s",$text,$matches);
        return $matches[1];
    }

    /* UTILITY */

    function gb_passage_number($tag) {
        return (is_numeric($tag) || preg_match('/fixednumber_[0-9]+/',$tag));
    }

    function gb_matter($tag) {
        return (substr($tag,0,11) == 'frontmatter' || substr($tag,0,10) == 'backmatter');
    }

    function get_only($only=false) {
        if (!$only && !$_REQUEST['only']) { return false; }
        $only = $only ? $only : explode(',',$_REQUEST['only']);
        $ret  = [];
        foreach ($only AS $o) {
            if (is_numeric($o)) { $ret[] = $o; }
            else {
                list($start,$end) = explode('-',$o);
                for ($i = $start;$i<=$end;$i++) {
                    $ret[] = $i;
                }
            }
        }
        return $ret;
    }

    function config_mpdf($root,&$settings = []) {

        require_once $root . '/vendor/autoload.php';

        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $settings = $settings ?? [
            'print'       => $_REQUEST['print'] ? true : false,
            'covers'      => $_REQUEST['covers'] ? true : false,
            'simplex'     => $_REQUEST['simplex'] ? true : false,
            'covers-only' => $_REQUEST['cover-only'] ? true : false,
        ];

        $format = (strstr($_SESSION['gb']['settings']['page_size'],',')) ? explode(',',$_SESSION['gb']['settings']['page_size']) : $_SESSION['gb']['settings']['page_size'];

        $config = [
            'format' => $format,
            'fontDir' => array_merge($fontDirs, [
                $root . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'fell' => [
                    'R' => 'IMFellDWPica-Regular.ttf',
                    'I' => 'IMFellDWPica-Italic.ttf',
                ],
                'eater' => [
                    'R' => 'Eater-Regular.ttf',
                ],
                'forum' => [
                    'R' => 'Forum-Regular.ttf',
                ]
            ],
            'dpi'                => $_SESSION['gb']['settings']['low_res'] ? 72 : $_SESSION['gb']['settings']['resolution'],
            'img_dpi'            => $_SESSION['gb']['settings']['low_res'] ? 72 : $_SESSION['gb']['settings']['image_resolution'],
            'list_auto_mode'     => 'mpdf',
            'list_marker_offset' => '1em',
            'list_symbol_size'   =>'0.31em',
            'margin_top'         => $_SESSION['gb']['settings']['margin_top']    ?? 15,
            'margin_bottom'      => $_SESSION['gb']['settings']['margin_bottom'] ?? 15,
            'margin_left'        => $_SESSION['gb']['settings']['margin_left']   ?? 10,
            'margin_right'       => $_SESSION['gb']['settings']['margin_right']  ?? 10
        ];
        if ($_REQUEST['print'] && !$_REQUEST['cover-only']) {
            $config = array_merge($config,[
                'margin_left'   => $_SESSION['gb']['settings']['margin_print_left'] ?? 20,
                'margin_right'  => $_SESSION['gb']['settings']['margin_print_right'] ?? 10,
                'mirrorMargins' => true
            ]);
        }
        return $config;
    }

    function render_one($passage,$number) {
        $pp      = process_para($passage,true,$print);
        return "
        <style>".file_get_contents('css/game.css')."</style>
        <style>".file_get_contents('css/preview.css')."</style>
        <style>{$_SESSION['gb']['story_css']}</style>
        <style>{$_SESSION['gb']['settings']['css']}</style>
        {$pp['before']}
        <div class='paragraph $long' id='para_$number'>
        <bookmark content='{$_SESSION['gb']['numbering'][$pid]['number']}'></bookmark>
        <h2 id=''><a name='$number'>$number.</a>{$edit}</h2>
        {$pp['text']}
        $tag
        </div>
        {$pp['after']}";
    }

?>