<?php

    /* =================================================================================== */
    /* Gordian FUNCTIONS                                                                   */
    /* =================================================================================== */


    function page($content,$options=[]) {
        return gb_header($options) . gb_links($content) . gb_footer();
    }

    function error($error_text='Unknown Error') {
        $_SESSION['gb_errors'][] = $error_text;
    }

    function msg($msg_text='') {
        $_SESSION['gb_msgs'][] = $msg_text;
    }

    function go($page,$qs='') {
        $qs = $qs ? '&'.$qs : '';
        header("Location: gordian.php?mode=$page{$qs}");
        exit;
    }

    function gb_header($options=[]) {
        $errors = '';
        if ($_SESSION['gb_errors']) {
            $errors = "<div class='errors'><h3>Error</h3><ul><li>".implode('</li><li>',$_SESSION['gb_errors'])."</li></ul></div>";
            $_SESSION['gb_errors'] = [];
        }
        $messages = '';
        if ($_SESSION['gb_msgs']) {
            $errors = "<div class='messages'><ul><li>".implode('</li><li>',$_SESSION['gb_msgs'])."</li></ul></div>";
            $_SESSION['gb_msgs'] = [];
        }
        $page_title = $options['title'] ? "Gordian: {$options['title']}" : "Gordian";
        $css = '';
        if ($options['css']) {
            foreach ($options['css'] AS $link) {
                if ($link == 'custom') {
                    $css .= "<style>{$_SESSION['gb']['story_css']}</style>\n";
                    $css .= "<style>{$_SESSION['gb']['settings']['css']}</style>\n";
                } else {
                    $css .= "<link rel='stylesheet' type='text/css' href='$link'>\n";
                }
            }
        }
        $js = '';
        if ($options['js']) {
            foreach ($options['js'] AS $link) {
                if (substr($link,0,8) == '<script>') {
                    $js .= $link;
                }
                else if ($link == 'vue') {
                    $js  .= "<script src='https://cdn.jsdelivr.net/npm/vue/dist/vue.js'></script>\n";
                } else {
                    $js  .= "<script type='text/javascript' src='$link'></script>\n";
                }
            }
        }
        $sidebar = '';
        if ($options['sidebar']) {
            $sidebar = "<div class='sidebar'>{$options['sidebar']}</div>";
        }
        return "<!DOCTYPE html>
              <head>
              <title>$page_title</title>
              <meta name='viewport' content='width=device-width, initial-scale=1'>
              <meta http-equiv='Content-Type' content='text/html; charset=utf8'>
              <meta property='og:type' content='website'>
              <meta property='og:url' content='http://ddonachie.virga.invertech.co.uk/gordian.php'>
              <meta property='og:title' content='Gordian Book'>
              <meta property='og:image' content='/images/app/SeekPng.com_knot-png_2307422.png'>
              <meta property='og:description' content='A tool to create traditional gamebooks from Twine stories.'>
              <meta name='twitter:card' content='summary_large_image'>
              <link rel='preconnect' href='https://fonts.gstatic.com'>
              <link href='https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&family=IM+Fell+DW+Pica&display=swap' rel='stylesheet'> 
              <link rel='stylesheet' type='text/css' href='/js/codemirror/lib/codemirror.css'>
              <link rel='stylesheet' type='text/css' href='/css/gamebook.css'>
              <script
			    src='https://code.jquery.com/jquery-3.6.0.min.js'
			    integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4='
			    crossorigin='anonymous'></script>
              $css
              $js
              <script type='text/javascript' src='/js/codemirror/lib/codemirror.js'></script>
              <script type='text/javascript' src='/js/codemirror/mode/css/css.js'></script>
              <script type='text/javascript' src='/js/codemirror/mode/xml/xml.js'></script>
              <script type='text/javascript' src='/js/codemirror/mode/javascript/javascript.js'></script>
              <script type='text/javascript' src='/js/codemirror/mode/markdown/markdown.js'></script>
              <script type='text/javascript' src='/js/codemirror/mode/htmlmixed/htmlmixed.js'></script>
              <script type='text/javascript' src='/js/main.js'></script>
              </head>
              <body>
              $errors
              $messages
              $sidebar
              <div class='content {$options['content_class']}'>
              <header>
                <a href='/'>&laquo; Home</a>
              </header>";
    }

    function gb_footer() {
        return "</div>
              </body>
              </html>";
    }

    function gb_menu($name,$options,$selected=null) {
        $out = "<select name='$name'>";
        foreach ($options AS $opt => $title) {
            $s = ($opt == $selected) ? 'SELECTED' : '';
            $out .= "<option value='$opt' $s>$title</option>";
        }
        $out .= "</select>";
        return $out;
    }

    function gb_passage_edit_list() {
        $out = "<div id='tabform'>
            <ul class='tabnav'>
                <li v-on:click='tab=1' v-bind:class=\"[tab==1 ? 'active' : '']\">Defined</li>
                <li v-on:click='tab=2' v-bind:class=\"[tab==2 ? 'active' : '']\">Number</li>
                <li v-on:click='tab=3' v-bind:class=\"[tab==3 ? 'active' : '']\">Alpha</li>
            </ul>
            <div class='tabs'>
                <div class='tab' v-show='tab==1'><ul>";
        foreach ($_SESSION['gb']['story']['passages'] AS $idx => $passage) {
            $number = $_SESSION['gb']['numbering'][$passage['pid']]['number'];
            $number = $number ? $number : 'skip';
            $out .= "<li><a href=\"gordian.php?mode=passage-edit&passage_name={$passage['name']}\">{$passage['name']} ($number)</a></li>";
        }
        $out .= "</ul></div><div class='tab' v-show='tab==2'><ul>";
        foreach ($_SESSION['gb']['number_order'] AS $number => $pid) {
            $idx  = $_SESSION['gb']['numbering'][$pid]['index'];
            $name = $_SESSION['gb']['story']['passages'][$idx]['name'];
            $out .= "<li><a href=\"gordian.php?mode=passage-edit&passage_name={$name}\">{$name} ($number)</a></li>";
        }
        $out .= "</ul></div><div class='tab' v-show='tab==3'><ul>";
        $names = $_SESSION['gb']['passage_names'];
        ksort($names, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($names AS $name => $data) {
            $pid    = $data['pid'];
            $number = $_SESSION['gb']['numbering'][$pid]['number'];
            $number = $number ? $number : 'skip';
            $out .= "<li><a href=\"gordian.php?mode=passage-edit&passage_name={$name}\">{$name} ($number)</a></li>";
        }
        $out .= '</ul></div></div></div>';
        return $out;
    }

    function gb_set_passage_number($pid,$currnum,$newnum) {
        //echo "CALLED gb_set_passage_number($pid,$currnum,$newnum)\n";
        $idx = $_SESSION['gb']['pids'][$pid];
        //echo "UPDATING Passgage $pid, from $currnum -> $newnum, index = $idx\n";

        // change "number_order"
        $_SESSION['gb']['number_order'][$newnum]  = $pid;
        //echo "Set ['number_order'][$newnum]  = $pid\n";

        // change "numbering" (pid => [index, number])
        $_SESSION['gb']['numbering'][$pid] = ['index' => $idx, 'number' => $newnum];
        //echo "Ordering $currord, set ['numbering'][$pid] = ['index' => $idx, 'number' => $newnum]\n";

        // change tag
        if ($_SESSION['gb']['story']['passages'][$idx]['tags'] && in_array($currnum,$_SESSION['gb']['story']['passages'][$idx]['tags'])) {
            $aidx = array_search($currnum,$_SESSION['gb']['story']['passages'][$idx]['tags']);
            $_SESSION['gb']['story']['passages'][$idx]['tags'][$aidx] = $newnum;
            //echo "Updated tags: set ['gb']['story']['passages'][$idx]['tags'][$aidx] = $newnum\n";
        }
        //echo "\n\n";
    }

    function gb_get_passage($pid) {
        return $_SESSION['gb']['story']['passages'][$_SESSION['gb']['pids'][$pid]];
    }

    function gb_links($content) {
        return str_replace("gordian.php?mode=",'',$content);
    }

    function n(...$args) {
        if (!$args) { return true; }
        echo "<pre class='pre_n'>";
        foreach ($args AS $m) {
            if (is_array($m)) { echo htmlspecialchars(print_r($m,1)); } else { echo $m; }
            echo ' ';
        }
        echo "</pre>";
    }

?>