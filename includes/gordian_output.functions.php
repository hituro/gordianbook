<?php



    /* =================================================================================== */
    /* GAMEBOOK OUTPUT FUNCTIONS                                                           */
    /* =================================================================================== */

    function htmldoc($print=true,$settings) {
        $link_css = $settings['print'] ? "<style>".file_get_contents('print.css')."</style>" : '';
        return "<!DOCTYPE html>
                <head>
                <title>{$_SESSION['gb']['story']['name']}</title>
                <style>".file_get_contents('css/game.css')."</style>
                $link_css
                <style>{$_SESSION['gb']['settings']['css']}</style>
                </head>
                <body>
                <htmlpageheader name=\"firstpageheader\" style=\"display:none\"></htmlpageheader>
                
                <htmlpagefooter name=\"firstpagefooter\" style=\"display:none\"></htmlpagefooter>
                
                <htmlpageheader name=\"otherpageheader\" style=\"display:none\"></htmlpageheader>
                
                <htmlpagefooter name=\"otherpagefooter\" style=\"display:none\">
                    <div class='footer'>{PAGENO}</div>
                </htmlpagefooter>"
                .htmlise($print,$settings)."</body>
                </html>";
    }

    function htmlise($print=false,$settings) {
        $out = '';
        if ($print && $_SESSION['gb']['settings']['cover'] && ($settings['covers'] || !$settings['print'])) {
            // use a cover page
            $text = $_SESSION['gb']['gb-front-cover'] ? process_para($_SESSION['gb']['gb-front-cover'])['text'] : "<div class='cover_top'><h1>{$_SESSION['gb']['story']['name']}</h1></div>";
            $out .= "<div class='cover_back front'></div>
                     $text";
            if ($settings['covers'] && $settings['simplex']) {
            } else if ($settings['covers']) {
                $out .= "<pagebreak type='next-odd' suppress='on' resetpagenum='1'></pagebreak>";
            } else {
                $out .= "<pagebreak type='next-odd' resetpagenum='1'></pagebreak>";
            }
        } else if (!$settings['print'] && !$settings['covers']) {
            $out = "<h1>{$_SESSION['gb']['story']['name']}</h1>";
        }
        if (!$settings['covers']) {

            /* First output any frontmatter from WF style frontmatter_X tags */
            if ($_SESSION['gb']['frontmatter']) {
                foreach ($_SESSION['gb']['frontmatter'] AS $front_pid) {
                    $front = $_SESSION['gb']['story']['passages'][$_SESSION['gb']['pids'][$front_pid]];
                    $out .= "<div class='paragraph frontmatter long'>".process_para($front)['text']."</div>
                             <div class='body_headers'></div>";
                    if ($front['tags'] && in_array('breakafter',$front['tags'])) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                }
            }

            /* Then is gb-introduction */
            if ($_SESSION['gb']['gb-introduction']) {
                $out .= "<div class='paragraph introduction long'>".process_para($_SESSION['gb']['gb-introduction'])['text']."</div>
                         <div class='body_headers'></div>";
                if ($_SESSION['gb']['gb-introduction']['tags'] && in_array('breakafter',$_SESSION['gb']['gb-introduction']['tags'])) {
                    $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                } else {
                    $out .= "<div class='game_divider'></div>";
                }
            }

            /* Then turn on page numbering, and start outputting actual passages */

            if (!$_REQUEST['skip_content']) {
                $out    .= "<sethtmlpagefooter name='otherpagefooter' page='ALL' value='on'></sethtmlpagefooter>";
                $divider = ($_SESSION['gb']['settings']['separator'] || $settings['proof']) ? "<div class='game_divider'></div>" : '';
                $total   = count($_SESSION['gb']['numbering']);
                $count   = 0;
                foreach ($_SESSION['gb']['number_order'] AS $number => $pid) {
                    if ($number == $total) { $divider = ''; }
                    $para = $_SESSION['gb']['numbering'][$pid];
                    $pass = $_SESSION['gb']['story']['passages'][$para['index']];
                    if ($pass['tags'] && in_array('skip',$pass['tags'])) { continue; }
                    $long = ($pass['tags'] && in_array('long',$pass['tags'])) ? 'long' : '';
                    $edit = ($print || $settings['proof']) ? '' : " (<a href='gordian.php?mode=passage-edit&pid=$pid' target='_new'>edit</a>)";
                    $pp   = process_para($pass,true,$print);
                    if ($pass['tags'] && in_array('breakbefore',$pass['tags'])) {
                        $out .= "<pagebreak suppress='off'/>";
                    }
                    $out    .= "<sethtmlpagefooter name='otherpagefooter' page='ALL' value='on'></sethtmlpagefooter>";
                    $out .= "<div class='paragraph $long' id='para_$number'>
                            <bookmark content='$number'></bookmark>
                            <h2 id='$number'><a name='$number'>$number.</a>{$edit}</h2>
                            {$pp['text']}
                            $tag
                            </div>
                            {$pp['after']}
                            $divider";
                    if ($_SESSION['gb']['settings']['break'] || ($pass['tags'] && in_array('breakafter',$pass['tags']))) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                    $count ++;
                    if ($_REQUEST['limit'] && $count >= $_REQUEST['limit']) { break; }
                }
            }

            /* Then output any backmatter from WF style backmatter_X tags */
            if ($_SESSION['gb']['backmatter']) {
                $out .= "<pagebreak suppress='off'></pagebreak>";
                foreach ($_SESSION['gb']['backmatter'] AS $back_pid) {
                    $back = $_SESSION['gb']['story']['passages'][$_SESSION['gb']['pids'][$back_pid]];
                    $out .= "<div class='paragraph backmatter long'>".process_para($back)['text']."</div>
                            <div class='body_headers'></div>";
                    if ($front['tags'] && in_array('breakafter',$back['tags'])) {
                        $out .= "<pagebreak type='next-odd' suppress='off'></pagebreak>";
                    }
                }
            }

            /* Finally, output gb-rear */

            if ($_SESSION['gb']['gb-rear']) {
                $out .= "<pagebreak suppress='off'></pagebreak><div class='paragraph rear'>".process_para($_SESSION['gb']['gb-rear'])['text']."</div>";
            }
        }
        if ($print && $_SESSION['gb']['settings']['cover'] && ($_SESSION['gb']['gb-rear-cover'])['text'] && ($settings['covers'] || !$settings['print'])) {
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

    function process_para($passage,$process_markdown=true,$print=false) {
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
        $text = process_links($passage); 
        if (preg_match_all("|<template name=\"(.*)\">(.*)</template>|sU",$text,$templatematch,PREG_SET_ORDER)) {
            //echo "<pre>template match found</pre>";
            $text   = templates($text,$templatematch);
        }
        if (preg_match("|<after>(.*?)</after>|",$text,$aftermatch)) {
            $after  = $aftermatch[1];
            $text   = str_replace($aftermatch[0],'',$text);
        }
        if (preg_match("|<before>(.*?)</before>|",$text,$beforematch)) {
            $before = $beforematch[1];
            $text   = str_replace($beforematch[0],'',$text);
        }
        if ($process_markdown) {
            $text   = markdown($text, $_SESSION['gb']['settings']['mdtype']);
            $text   = autop($text,0).$tag;
            $after  = $after ? markdown($after, $_SESSION['gb']['settings']['mdtype']) : '';
            $before = $before ? markdown($before, $_SESSION['gb']['settings']['mdtype']) : '';
        }
        //echo "<pre>".htmlspecialchars($text)."</pre>";
        return ['text' => $text, 'after' => $after, 'before' => $before];
    }

    function templates($text,$templatematches) {
        //echo "<pre>".print_r($templatematches,1)."</pre>";
        foreach ($templatematches AS $t) {
            $r    = template($t[1],$t[2]);
            $text = str_replace($t[0],$r,$text);
            //echo "<pre>replacing ".htmlspecialchars($t[0])." with " . htmlspecialchars($r) . "</pre>";
        }
        return $text;
    }

    function template($name,$data) {
        //echo "<pre>Processing template $name\n\n</pre>";
        $template = $_SESSION['gb']['gb-templates'][$name];
        $data     = json_decode($data,true);
        //echo "<pre>".print_r($data,1)."</pre>";
        foreach ($data AS $k => $v) {
            $template = str_replace("{{" . $k. "}}",$v,$template);
        }
        //echo "<pre>".htmlspecialchars($template)."</pre>";
        if (preg_match_all("|<repeat (.*) AS (.*)>(.*)</repeat>|sU",$template,$rmatches,PREG_SET_ORDER)) {
            //echo "<pre>RMATCHES: ".print_r($rmatches,1)."</pre>";
            foreach ($rmatches AS $rep) {
                $rname = $rep[1];
                $ritem = $rep[2];
                $rbody = $rep[3];
                $rout  = '';
                foreach ($data[$rname] AS $ditem) {
                    $out = $rbody;
                    foreach ($ditem AS $k => $v) {
                        $out = str_replace("{{" . $ritem . "." . $k ."}}",$v,$out);
                    }
                    $rout .= $out;
                }
                $template = str_replace($rep[0],$rout,$template);
            }
        } 
        return $template;
    }

    function markdown($text,$mode='harlowe') {
        // very basic markdown parse
        // includes
        $include = ($mode=="harlowe") ? "/\(display: *\"(.*?)\"\)/" : "/<<include *\"(.*?)\">>/";
        $text = preg_replace_callback($include, 
                    function($m) { 
                        $idx = $_SESSION['gb']['passage_names'][$m[1]]['idx'];
                        return process_para($_SESSION['gb']['story']['passages'][$idx],false)['text']; 
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
        $text = ($mode == 'harlowe') ? $text : md_list($text,'>','blockquote','','');
        // alignment
        $text = ($mode == 'harlowe') ? md_align($text) : $text;
        // hr
        $hr   = ($mode == 'harlowe') ? 3 : 4;
        $text = preg_replace("/^\s*-{{$hr},}\s*$/m","<hr>",$text);
        // bold
        $text = preg_replace("/\*\*(.+?)\*\*/s","<b>$1</b>",$text);
        $text = preg_replace("/\''(.+?)\''/s","<b>$1</b>",$text);
        // italic
        $text = preg_replace("/\*(.+?)\*/s","<i>$1</i>",$text);
        $text = preg_replace("|//(.+?)//|s","<i>$1</i>",$text);
        $text = preg_replace("|http(s*):(?!//)|s","http$1://",$text);
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
        $text = preg_replace_callback("/^\s*{$h}{$h}{$h}{$h}(.*)$/m",function($m) { return "<h6>".trim($m[1])."</h6>"; },$text);
        $text = preg_replace_callback("/^\s*{$h}{$h}{$h}(.*)$/m",function($m) { return "<h5>".trim($m[1])."</h5>"; },$text);
        $text = preg_replace_callback("/^\s*{$h}{$h}(.*)$/m",function($m) { return "<h4>".trim($m[1])."</h4>"; },$text);
        $text = preg_replace_callback("/^\s*{$h}(.*)$/m",function($m) { return "<h3>".trim($m[1])."</h3>"; },$text);
        // rules
        $text = preg_replace("/<check>(.+?)<\/check>/s","<span class='check'>&nbsp;$1&nbsp;</span>",$text);
        $text = preg_replace("/<rules>(.+?)<\/rules>/s","<div class='rules'>$1</div>",$text);
        $text = preg_replace("/<stats>(.+?)<\/stats>/s","<div class='stats'>$1</div>",$text);
        $text = preg_replace("/<special>(.+?)<\/special>/s","<div class='special'>$1</div>",$text);
        $text = preg_replace_callback("/<checkboxes>(.+?)<\/checkboxes>/s",md_boxes,$text);
        // comments 
        $text = preg_replace("|<comment>(.*?)</comment>|s","<!-- $1 -->",$text);
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
        //echo "<pre style='background:white;'>"; print_r($blocks); print_r($splices);echo "</pre>";
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

    function md_boxes($matches) {
        return "<table class='checkboxes' cellSpacing='1mm' align='center'><tr>" . str_repeat("<td class='box' width='6mm'>&nbsp;</td> ",$matches[1]) . '</tr></table>';
    }

    function str_splice($string,$replace,$start,$length) {
        $p1 = substr($string,0,$start);
        $p2 = substr($string,$start+$length);
        return $p1 . $replace . $p2;
    }

    function autop($pee, $br=1) {
        $pee = preg_replace("/(\r\n|\n|\r)/", "\n", $pee); // cross-platform newlines
        $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        /*
        $pee = preg_replace('/\n?(.+?)(\n\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
        $pee = preg_replace('|<p><ul>(.+)</ul></p>|si', "<ul>$1</ul>", $pee);
        $pee = preg_replace('|<p><div(.*?)>(.+)</div></p>|si', "<div$1>$2</div>", $pee);
        $pee = preg_replace('|<div(.*?)>(.+)</div></p>|si', "<div$1>$2</div>", $pee);
        $pee = preg_replace('|^<p><h|i', "<h", $pee);
        if ($br) $pee = preg_replace('|(?<!</p>)\s*\n|', "<br />\n", $pee); // optionally make line breaks
        if ($br) $pee = preg_replace('|<br />\n<li>|', "\n<li>", $pee); // clean lists
        if ($br) $pee = preg_replace('|</li><br />|', "</li>", $pee); // clean lists
        */
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

    function convert_from_twine($twine) {
        ini_set('display_errors',1);
        require_once __DIR__ . '/../vendor/autoload.php';
        $dom = voku\helper\HtmlDomParser::str_get_html($twine);
        $out      = [
            "passages"  => [],
            "name"      => "",
            "startnode" => 1
        ];
        // first build the document data (title, metadata)
        $story_data       = $dom->findOne('tw-storydata');
        foreach (['name','startnode','format','format-version','zoom','tag-colors','ifid'] AS $e) {
            if ($story_data->getAttribute($e))  { $out[$e] = $story_data->getAttribute($e); }
        }
        // then add styles and js
        $style = $dom->findOne('#twine-user-stylesheet');
        if ($style) {
            //$out['settings']['css'] = $style->innerHTML();
            $_SESSION['gb']['settings']['css'] .= $style->innerHTML();
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
                "tags"      => explode(' ',$passage->getAttribute('tags')),
                "position"  => $passage->getAttribute('position'),
                "size"      => $passage->getAttribute('size'),
                "text"      => html_entity_decode($passage->innerHTML()),
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

    function process_links($passage) {
        preg_match_all("/\[\[(.*?)\]\]/",$passage['text'],$matches);
        $debug = "<pre>"; 
        $debug .= print_r($matches,1);
        foreach ($matches[1] AS $lidx => $link) {
            $parts = preg_split("/(->|<-|\|)/",$link,-1,PREG_SPLIT_DELIM_CAPTURE);
            $debug .= print_r($parts,1);
            if ($parts[1] == '<-') {
                // reversed style
                $name   = $parts[2];
                $pid    = $_SESSION['gb']['passage_names'][$parts[0]];
            } else if ($parts[1]) {
                $name   = $parts[0];
                $pid    = $_SESSION['gb']['passage_names'][$parts[2]];
            } else {
                $name   = $parts[0];
                $pid    = $_SESSION['gb']['passage_names'][$parts[0]];
            }
            $debug .= "\$pid = {$pid['pid']} ".print_r($_SESSION['gb']['numbering'][$pid['pid']],1);
            $number = $_SESSION['gb']['numbering'][$pid['pid']]['number'];
            $number = $number ?? "X";
            $name   = trim($name);
            if ($name == 'turnto' || $name === '') {
                $debug .= " TURNTO LINK ";
                $pattern = "/\.\s+\[\[".str_replace('|','\|',$link)."/"; 
                if (preg_match($pattern,$passage['text'],$lmatches)) {
                    $ltext = "Turn to $number";
                } else {
                    $ltext = "turn to $number";
                }
            } else if ($name == '#') {
                $ltext = "$number";
            } else {
                $ltext = "$name (turn to $number)";
            }
            $debug .= " $ltext \n\n";
            //$ltext  = ($name == 'turnto') ? "turn to $number" : "$name (turn to $number)";
            $tlink  = "<a href='#{$number}'>$ltext</a>";
            $passage['text'] = str_replace($matches[0][$lidx],$tlink,$passage['text']);
        }
        //echo "========================================\n</pre>";
        $debug .= "</pre>";
        return $passage['text'];
    }

    function twee_passage($p) {
        $pid    = $_SESSION['gb']['passage_names'][$p['name']]['pid'];
        if ($pid) {
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

    function twine_passage($p) {
        $pid    = $_SESSION['gb']['passage_names'][$p['name']]['pid'];
        if ($pid) {
            $number = $_SESSION['gb']['numbering'][$pid]['number'];
            if (!in_array($number,$p['tags'])) { $p['tags'][] = $number; }
        }
        $tag = [
            'pid'       => $p['pid'],
            'name'      => $p['name'],
            'tags'      => $p['tags'] ? implode(' ',$p['tags']) : '',
            'position'  => $p['position'] ? $p['position'] : 100+(100*$p['pid']).',100',
            'size'      => $p['size'] ? $p['size'] : '100,100'
        ];
        foreach ($tag AS $attr => $v) {
            $attrs[] = "$attr=\"$v\"";
        }
        $attrs = implode(' ',$attrs);
        return "<tw-passagedata $attrs>".htmlspecialchars(html_entity_decode($p['text'],ENT_QUOTES),ENT_QUOTES)."</tw-passagedata>";
    }

    function gb_passage_number($tag) {
        return (is_numeric($tag) || preg_match('/fixednumber_[0-9]+/',$tag));
    }

    function gb_matter($tag) {
        return (substr($tag,0,11) == 'frontmatter' || substr($tag,0,11) == 'backmatter');
    }

?>