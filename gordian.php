<?php

    //ini_set('display_errors',1);
    ini_set('session.gc_maxlifetime','5000');
    ini_set('max_execution_time',50);
    session_start();
    require "includes/gordian_interface.functions.php";
    require "includes/gordian_output.functions.php";
    $mode = $_REQUEST['mode'];
    $mode = in_array($mode,[
        'home','workflow','markdown', 'changelog','wf',
        'load-json','import-json','load-game-json','import-game-json','convert','show', 'pdf-source',
        'intro-edit','intro-save','passage-edit','passage-save','settings','settings-save','load-settings','import-settings',
        'export','pdf','export-json','export-settings','export-twee','export-twine','fix',
        'mdtest','test','pdftest'
       ]) ? $mode : 'home';
/*
    if (!$_SESSION['gb']['numbering'] && !in_array($mode,['home','workflow','markdown',
        'load-json','import-json','load-game-json','import-game-json','mdtest'])) {
        error("Sorry, you must import a game first");
        $mode = 'home';
    } */
    $defaults = [
        'story'         => [],
        'numbering'     => [],
        'number_order'  => [],
        'passage_names' => [],
        'format'        => '',
        'frontmatter'   => [],
        'backmatter'    => [],
        'settings'      => [
            'end_text'      => 'THE END',
            'death_text'    => 'YOU DIED',
            'separator'     => false,
            'break'         => false,
            'css'           => '',
            'page_size'     => 'A4-P',
            'cover'         => false,
            'mdtype'        => 'harlowe'
        ],
        'stats'         => [
            'passages'  => 0,
            'links'     => 0,
        ]
    ];
    if (!$_SESSION['gb']) {
        $_SESSION['gb'] = $defaults;
    }

    /* =================================================================================== */
    /* HOME                                                                                */
    /* =================================================================================== */

    if ($mode == 'home') {
        $lists = ['Import' => [], 'Edit' => [], 'Play' => [], 'Print' => [], 'Export' => []];
        
        $lists['Import'][] = "<a href='gordian.php?mode=load-json'>Import Game</a>";
        $lists['Import'][] = "<a href='gordian.php?mode=load-game-json'>Import Gordian JSON</a>";
        if ($_SESSION['gb']['raw']) {
            $lists['Import'][] = "<a href='gordian.php?mode=convert'>Convert to Gamebook</a>";
        }
        if ($_SESSION['gb']['numbering']) {
            $lists['Edit'][]  = "<a href='gordian.php?mode=intro-edit'>Edit Introduction/Cover</a>";
            $lists['Edit'][]  = "<a href='gordian.php?mode=settings'>Edit Settings</a>";
            $lists['Edit'][]  = "<a href='gordian.php?mode=passage-edit'>Edit Passage</a>";
            $lists['Edit'][]  = "<a href='gordian.php?mode=load-settings'>Import Settings</a>";

            $lists['Play'][]  = "<a href='gordian.php?mode=show'>Preview Gamebook</a>";

            $lists['Print'][] = "<a href='gordian.php?mode=pdf' target='_new'>Export Gamebook PDF</a>";
            $lists['Print'][] = "PDF: <a href='gordian.php?mode=pdf&print=1' target='_new'>print</a> - 
                                      <a href='gordian.php?mode=pdf&covers=1&simplex=1' target='_new'>cover</a> - 
                                      <a href='gordian.php?mode=pdf&covers=1' target='_new'>cover (duplex)</a>";

            $lists['Export'][] = "<a href='gordian.php?mode=export-settings'>Export Settings</a>";
            $lists['Export'][] = "<a href='gordian.php?mode=export-json'>Export Gordian JSON</a>";
            $lists['Export'][] = "<a href='gordian.php?mode=export-twine'>Export Twine Archive</a>";
            $lists['Export'][] = "<a href='gordian.php?mode=export'>Export HTML (proofing)</a>";
            $lists['Export'][] = "<a href='gordian.php?mode=export-twee'>Export Twee</a>";
        }

        $menus = "";
        foreach ($lists AS $list => $v) {
            if (!$v) { continue; }
            $n = ($list == 'Play') ? " ({$_SESSION['gb']['story']['name']})" : '';
            $p = ($list == 'Play') ? "<p><i>{$_SESSION['gb']['stats']['passages']} passages</i></p>" : '';
            $menus .= "<div class='home_$list'><h4>$list{$n}</h4>{$p}
                       <ul><li>" . implode('</li><li>',$v) . '</li></ul></div>';
        }

        echo page("
            <h1>Gordian Book</h1>
            <p>Formats a traditonal choose-your-own-adventure style gamebook from Twine / Twee</p>
            <div class='menus'>$menus</div>
            ".file_get_contents("includes/about.inc.html"),['title' => "Home"]);
    }

    if ($mode == 'workflow') {
        echo page(file_get_contents("includes/workflows.inc.html"),['title' => 'Workflows']);
    }

    if ($mode == 'changelog') {
        echo page(file_get_contents("includes/changelog.inc.html"),['title' => 'Changelog']);
    }

    if ($mode == 'markdown') {
        echo page(file_get_contents("includes/markdown.inc.html"),['title' => 'Markdown']);
    }

    if ($mode == 'wf') {
        echo page(file_get_contents("includes/wf.inc.html"),['title' => 'WritingFantasy']);
    }

    /* =================================================================================== */
    /* LOAD DATA                                                                           */
    /* =================================================================================== */

    if ($mode == 'load-json') {
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
    }

    if ($mode == 'import-json') {
        //echo "<pre>REQUEST\n"; echo htmlspecialchars(print_r($_REQUEST,1)); echo "FILES\n"; print_r($_FILES); echo "</pre>"; exit;
        if ($_REQUEST['story-json'] || $_FILES['story-json-upload']) {
            if ($_REQUEST['story-json']) {
                $src  = $_REQUEST['story-json'];
            } else {
                $src  = file_get_contents($_FILES['story-json-upload']['tmp_name']);
            }
            $json = json_decode($src,true);
            $src  = preg_replace("/(\r\n|\n|\r)/", "\n", $src); // cross-platform newlines
            if (is_array($json)) {
                $format = 'twison';
            } else if (preg_match('/^:: StoryTitle$/m',$src,$matches)) {
                $format = 'twee';
            } else if (preg_match('/tw-storydata/',$src,$matches)) {
                $format = 'twine';
            } 
            //echo "<pre>$format -> "; print_r($matches); echo htmlspecialchars($src); exit;
            if ($format) {
                $_SESSION['gb']                 = $defaults;
                $_SESSION['gb']['format']       = $format;
                $_SESSION['gb']['raw']          = $src;
                $_SESSION['gb']['story']        = $json;
                $_SESSION['gb']['numbering']    = [];
                $_SESSION['gb']['number_order'] = [];
                msg("Game data loaded. You should now Convert to Gamebook");
                go('home');
            } else {
                error("Sorry, we could not parse that story. Make sure it is in Twison, Twee3, or Twine Archive format");
                go('load-json');
            }
        } else {
            error("Sorry, you must supply the story data to import");
            go('load-json');
        }
    }

    if ($mode == 'load-game-json') {
        echo gb_header([
            'title' => "Load Game Formatter JSON",
            'js'    => ['vue','js/tabs.js']
        ]);
        echo "<form action='gordian.php' method='POST' id='tabform' enctype='multipart/form-data'>
                <input type='hidden' name='mode' value='import-game-json'>
                <ul class='tabnav'>
                    <li v-on:click='tab=1' v-bind:class=\"[tab==1 ? 'active' : '']\">Paste</li>
                    <li v-on:click='tab=2' v-bind:class=\"[tab==2 ? 'active' : '']\">Upload</li>
                </ul>
                <div class='tabs'>
                    <div class='tab' v-show='tab==1'>
                        <div class='form-row'>
                            <label for='game-json'>Enter game JSON</label>
                            <textarea name='game-json' rows='40'></textarea>
                        </div>
                    </div>
                    <div class='tab' v-show='tab==2'>
                        <div class='form-row'>
                            <label for='game-json-upload'>Upload game JSON</label>
                            <input type='file' name='game-json-upload' />
                        </div>
                    </div>
                </div>
                <div class='form-row'>
                    <input type='submit' value='import'>
                </div>
              </form>";
        echo gb_footer();
    }

    if ($mode == 'import-game-json') {
        if ($_REQUEST['game-json'] || $_FILES['game-json-upload']) {
            if ($_REQUEST['game-json']) {
                $src  = $_REQUEST['game-json'];
            } else {
                $src  = file_get_contents($_FILES['game-json-upload']['tmp_name']);
            }
            $json = json_decode($src,true);
            if (!is_array($json) || !$json['settings']) {
                error("Sorry, we could not parse that saved game, make sure it was exported from the Formatter");
                go('load-game-json');
            } else {
                $_SESSION['gb']                 = $json;
                msg("Game JSON export loaded");
                go('home');
            }
        } else {
            error("Sorry, you must supply the saved game JSON to import");
            go('load-game-json');
        }
    }

    if ($mode == 'load-settings') {
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
    }

    if ($mode == 'import-settings') {
        if ($_REQUEST['game-settings-json']) {
            $json = json_decode($_REQUEST['game-settings-json'],true);
            if (!is_array($json)) {
                error("Sorry, we could not parse those settings, make sure it was exported from the Formatter");
                go('load-settings');
            } else {
                $_SESSION['gb']['settings'] = $json;
                msg("Game settings JSON loaded");
                go('home');
            }
        } else {
            error("Sorry, you must supply the saved game settings JSON to import");
            go('load-game-json');
        }
    }

    /* =================================================================================== */
    /* CONVERT / EDIT / PLAY                                                               */
    /* =================================================================================== */

    if ($mode == 'convert') {
        // build an index of passages, then assign random numbers which we then put into a converter array. 
        // But passage 0 needs to stay as 1

        if ($_SESSION['gb']['format'] == 'twee') {
            // need to convert from twee
            $_SESSION['gb']['story'] = convert_from_twee($_SESSION['gb']['raw']);
            $_SESSION['gb']['format'] = 'twee-converted';
        }

        if ($_SESSION['gb']['format'] == 'twine') {
            // need to convert from twine archive
            $_SESSION['gb']['story'] = convert_from_twine($_SESSION['gb']['raw']);
            $_SESSION['gb']['format'] = 'twine-converted';
        }

        //unset($_SESSION['gb']['raw']);
        
        //$debug .= "STORY : " . print_r($_SESSION['gb']['story'],1);

        $passage_count = count($_SESSION['gb']['story']['passages']);
        $debug .= "PASSAGE COUNT: $passage_count\n==================================\n";
        if ($passage_count < 1) {
            error("The imported story does not have enough passages, must be at least 2");
        } else {
            $nlist  = [];
            $nindex = [];
            $norder = [];
            $prenum = [];
            $unums  = [];
            $skip   = [];
            $pnames = [];
            $pids   = [];
            $_SESSION['gb']['frontmatter'] = $_SESSION['gb']['backmatter'] = ['numbered' => [], 'unnumbered' => []];
            // first look for introduction and pull it out
            foreach ($_SESSION['gb']['story']['passages'] AS $idx => $passage) {
                $pname = strtolower($passage['name']);
                if ($pname == 'gb-introduction' || $pname == 'gb-rear' || $pname == 'gb-front-cover' || $pname == 'gb-rear-cover') {
                    $_SESSION['gb'][$pname] = $passage;
                    unset($_SESSION['gb']['story']['passages'][$idx]);
                    $passage_count --;
                    $debug .= "REMOVING $pname, PASSAGE COUNT $passage_count\n";
                } else if ($passage['tags'] && ($matter = array_filter($passage['tags'],'gb_matter'))) {
                    $skip[$passage['pid']] = 1;
                    $passage_count --;
                    // front and back matter from WF
                    $matter = array_pop($matter);
                    if ($matter == 'frontmatter') {
                        $debug .= "matter option 1\n";
                        $_SESSION['gb']['frontmatter']['unnumbered'][] = $passage['pid'];
                    } else if (substr($matter,0,12) == 'frontmatter_') {
                        $debug .= "matter option 2\n";
                        $matter_idx = substr($matter,12);
                        $_SESSION['gb']['frontmatter']['numbered'][$matter_idx] = $passage['pid'];
                    } else if ($matter == 'backmatter') {
                        $debug .= "matter option 3\n";
                        $_SESSION['gb']['backmatter']['unnumbered'][] = $passage['pid'];
                    } else if (substr($matter,0,12) == 'backmatter_') {
                        $debug .= "matter option 4\n";
                        $matter_idx = substr($matter,12);
                        $_SESSION['gb']['backmatter']['numbered'][$matter_idx] = $passage['pid'];
                    }
                    $debug .= "REMOVING $pname ({$passage['pid']}) AS FRONT/BACK MATTER\n".$matter." PLACING it in $mater_idx\n".print_r($_SESSION['gb']['frontmatter'],1).print_r($_SESSION['gb']['backmatter'],1);
                } else if ($passage['tags'] && (in_array('skip',$passage['tags']) || in_array('hidden',$passage['tags']))) {
                    $skip[$passage['pid']] = 1;
                    $passage_count --;
                    $debug .= "SKIPPING $pname, PASSAGE COUNT $passage_count\n";
                } else if ($passage['tags'] && in_array('stylesheet',$passage['tags'])) {
                    $_SESSION['gb']['settings']['css'] .= $passage['text'];
                    unset($_SESSION['gb']['story']['passages'][$idx]);
                    $passage_count --;
                } else if ($pname == 'gb-settings') {
                    $content = json_decode($passage['text'],true);
                    unset($_SESSION['gb']['story']['passages'][$idx]);
                    $skip[$passage['pid']] = 1;
                    $passage_count --;
                    if ($content) { 
                        $content['passage'] = [
                            'pid'       => $passage['pid'],
                            'tags'      => $passage['tags'],
                            'position'  => $passage['position'],
                            'size'      => $passage['size']
                        ];
                        $_SESSION['gb']['settings'] = array_merge($defaults['settings'],$content); 
                    }
                } else if ($pname == 'gb-templates') {
                    preg_match_all("|<template name=\"(.*)\">(.*)</template>|sU",$passage['text'],$matches,PREG_SET_ORDER);
                    $debug .= "MATCHES : " . print_r($matches,1);
                    foreach ($matches AS $match) {
                        $templates[$match[1]] = $match[2];
                    }
                    $debug .= "TEMPLATES : " . print_r($templates,1);
                    $_SESSION['gb'][$pname] = $templates;
                    unset($_SESSION['gb']['story']['passages'][$idx]);
                    $passage_count --;
                    $debug .= "REMOVING $pname, PASSAGE COUNT $passage_count\n";
                }
            } 

            // now merge front and back matter
            $debug .= "BEFORE MERGE FRONT/BACKMATTER".print_r($_SESSION['gb']['frontmatter'],1).print_r($_SESSION['gb']['backmatter'],1);
            $debug .= "MERGE: ".print_r(array_merge($_SESSION['gb']['frontmatter']['numbered'],$_SESSION['gb']['frontmatter']['unnumbered']),1);
            $_SESSION['gb']['frontmatter'] = array_merge($_SESSION['gb']['frontmatter']['numbered'],$_SESSION['gb']['frontmatter']['unnumbered']);
            $_SESSION['gb']['backmatter']  = array_merge($_SESSION['gb']['backmatter']['numbered'],$_SESSION['gb']['backmatter']['unnumbered']);
            $debug .= "Final FRONT/BACKMATTER".print_r($_SESSION['gb']['frontmatter'],1).print_r($_SESSION['gb']['backmatter'],1);

            $debug .=  "SETTING SKIP TO ".print_r($skip,1);
            //echo "<pre>";
            // loop again to look for prenumbered paragraphs
            foreach ($_SESSION['gb']['story']['passages'] AS $idx => $passage) {
                $pids[$passage['pid']] = $idx;
                $pnames[$passage['name']] = ['idx' => $idx, 'pid' => $passage['pid']];
                if ($passage['tags'] && ($num = array_filter($passage['tags'],'gb_passage_number'))) {
                    // a numeric tag
                    $num = (int) str_replace('fixednumber_','',array_pop($num));
                    if (in_array($num,$unums)) {
                        error("Cannot compile game, number $num used more than once on prenumbered paragraphs");
                        go('home');
                    }
                    $prenum[$passage['pid']] = ['index' => $idx, 'number' => $num];
                    $debug .=  "prenumbering {$passage['name']} ".print_r($prenum[$passage['pid']],1)."\n";
                } else if ($passage['tags'] && (in_array('last',$passage['tags']) || in_array('ending',$passage['tags']))) {
                    // we want this at the end
                    $prenum[$passage['pid']] = ['index' => $idx, 'number' => 'last'];
                    $debug .=  "prenumbering {$passage['name']} as last".print_r($prenum[$passage['pid']],1)."\n";
                }
            }
            for ($i=2;$i<=$passage_count;$i++) { $nlist[] = $i; }
            //$debug .=  "NUMBER LIST (nlist) ".print_r($nlist,1)."\n";
            foreach ($prenum AS $pid => $num) {
                if ($num['number'] == 'last') {
                    $last = array_pop($nlist);
                    $num['number'] = $last;
                    $debug .=  "unsetting \$nlist[last]\n";
                } else {
                    unset($nlist[$num['number'] - 2]);
                    $debug .=  "unsetting \$nlist[{$num['number']} - 2]\n";
                }
                $norder[$num['number']] = $pid;
                $nindex[$pid] = $num;
            }
            //$debug .=  "NUMBER LIST (nlist) ".print_r($nlist,1)."\n";
            shuffle($nlist);
            // $debug .=  "NUMBER LIST SHUFFLED (nlist) ".print_r($nlist,1)."\n==================================\n";
            $start  = $_SESSION['gb']['story']['startnode'];
            //$debug .=  "START NODE: $start\n";
            foreach($_SESSION['gb']['story']['passages'] AS $idx => $passage) {
                if ($passage['pid'] != $start) { 
                    if (!array_key_exists($passage['pid'],$nindex) && !array_key_exists($passage['pid'],$skip)) {
                        $number = array_pop($nlist);
                        $norder[$number] = $passage['pid'];
                        $nindex[$passage['pid']] = ['index' => $idx, 'number' => $number];
                        //$debug .=  "Setting $idx {$passage['name']} (pid {$passage['pid']}) to $number\n";
                        //$debug .=  "NLIST ".print_r($nlist,1);
                    } else {
                        //$debug .=  "<p>Skipping $idx {$passage['name']} (pid {$passage['pid']})</p>";
                    }
                } else {
                    $norder[1] = $passage['pid'];
                    $nindex[$passage['pid']] = ['index' => $idx, 'number' => 1];
                    //$debug .=  "Setting start passage $idx {$passage['name']} (pid {$passage['pid']}) to 1\n";
                }
            }
            ksort($norder);
            $_SESSION['gb']['numbering']     = $nindex;
            $_SESSION['gb']['number_order']  = $norder;
            $_SESSION['gb']['passage_names'] = $pnames;
            $_SESSION['gb']['pids']          = $pids;
            $_SESSION['gb']['stats']['passages'] = $passage_count;
        }
        if ($_REQUEST['debug']) {
            echo "<pre>". $debug; 
            exit;
        }
        msg("Game paragraphs compiled and randomised");
        go('home');
    }

    if ($mode == 'intro-edit') {
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
    }

    if ($mode == 'intro-save') {
        // introduction
        if (!$_SESSION['gb']['gb-introduction']) {
            $_SESSION['gb']['gb-introduction'] = ['name' => 'gb-introduction', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-introduction']['text'] = $_REQUEST['intro'];

        // rear
        if (!$_SESSION['gb']['gb-rear']) {
            $_SESSION['gb']['gb-rear'] = ['name' => 'gb-rear', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-rear']['text'] = $_REQUEST['rear'];

        // front cover
        if (!$_SESSION['gb']['gb-front-cover']) {
            $_SESSION['gb']['gb-front-cover'] = ['name' => 'gb-front-cover', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-front-cover']['text'] = $_REQUEST['gb-front-cover'];

        // rear cover
        if (!$_SESSION['gb']['gb-rear-cover']) {
            $_SESSION['gb']['gb-rear-cover'] = ['name' => 'gb-rear-cover', 'text' => '', 'tags' => []];
        }
        $_SESSION['gb']['gb-rear-cover']['text'] = $_REQUEST['gb-rear-cover'];
        msg("Front and back matter saved");
        go('intro-edit');
    }

    if ($mode == 'mdtest') {
        if ($_REQUEST['mdtest']) {
            $md   = markdown($_REQUEST['mdtest'],$_REQUEST['mdmode']);
            $esc  = htmlspecialchars($md);
            $p    = autop(process_links(['text' => $md]),0);
            $para = htmlspecialchars($p);
        }
        echo page("
            <form action='gordian.php' method='post'>
                <input type='hidden' name='mode' value='mdtest'>
                <div class='form-row'>
                    <label for='mdmode'>Mode</label>
                    <select name='mdmode'>
                        <option>harlowe</option>
                        <option>sugarcube</option>
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
    }

    if ($mode == 'passage-edit') {
        if ($_REQUEST['passage_name']) {
            $pid     = $_SESSION['gb']['passage_names'][$_REQUEST['passage_name']]['pid'];
            $idx     = $_SESSION['gb']['passage_names'][$_REQUEST['passage_name']]['idx'];
        } else if ($_REQUEST['pid']) {
            $pid     = $_REQUEST['pid'];
        } else if ($_REQUEST['number']) {
            $pid     = $_SESSION['gb']['number_order'][$_REQUEST['number']];
        }
        $form = "
            <h2>Enter a Passage Name or ID to edit</h2>
            <form action='gordian.php' method='post'>
                <input type='hidden' name='mode' value='passage-edit'>
                <div class='form-row'>
                    <label for='number'>Passage Number</label>
                    <input type='text' name='number' value=''>
                </div>
                <div class='form-row'>
                    <label for='passage_name'>Passage Name</label>
                    <input type='text' name='passage_name' value=''>
                </div>
                <div class='form-row'>
                    <label for='pid'>Passage ID</label>
                    <input type='text' name='pid' value=''>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Find'>
                </div>
            </form>
        ";
        if (is_numeric($pid)) {
            $idx     = $idx ? $idx : $_SESSION['gb']['pids'][$pid];
            $passage = $_SESSION['gb']['story']['passages'][$idx];
            $tags    = $passage['tags'] ? implode(' ',$passage['tags']) : '';
            echo page("
                <h2>Editing: #{$_SESSION['gb']['numbering'][$pid]['number']} — {$passage['name']} (pid {$passage['pid']})</h2>
                <form action='gordian.php' method='post'>
                    <input type='hidden' name='mode' value='passage-save'>
                    <input type='hidden' name='pid' value='{$pid}'>
                    <div class='form-row'>
                        <label for='number'>Change Number</label>
                        <input type='text' name='number' value='{$_SESSION['gb']['numbering'][$pid]['number']}'>
                    </div>
                    <div class='form-row'>
                        <label for='tags'>Edit Tags</label>
                        <input type='text' name='tags' value='{$tags}'>
                    </div>
                    <div class='form-row'>
                        <label for='text'>Edit Passage Text</label>
                        <textarea name='text' rows='20'>{$passage['text']}</textarea>
                    </div>
                    <div class='form-row'>
                        <input type='submit' value='Save'>
                    </div>
                </form>
                $form
            ",['title' => "Edit Passage : {$passage['name']}", 'sidebar' => gb_passage_edit_list()]);
        } else {
            echo page($form,['title' => "Edit Passage", 'sidebar' => gb_passage_edit_list()]);
        }
    }

    if ($mode == 'passage-save') {
        if (is_numeric($_REQUEST['pid'])) {
            //$idx     = $_SESSION['gb']['numbering'][$_REQUEST['pid']]['index'];
            //echo "main $idx ";
            $idx     = $_SESSION['gb']['pids'][$_REQUEST['pid']];
            //print_r($_SESSION['gb']['story']['passages'][$idx]);
            //echo $idx;
            //return;
            $_SESSION['gb']['story']['passages'][$idx]['text'] = $_REQUEST['text'];
            $_SESSION['gb']['story']['passages'][$idx]['tags'] = explode(' ',$_REQUEST['tags']);
            msg("Passage Saved");
            if (is_numeric($_REQUEST['number']) && $_REQUEST['number'] != $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number']) {
                // we are swapping numbers with another paragraph
                // need to change "number_order" (number => pid)
                $currnum = $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number'];
                $newnum  = $_REQUEST['number'];
                $old_pid = $_SESSION['gb']['number_order'][$newnum];
                           $_SESSION['gb']['number_order'][$newnum]  = $_REQUEST['pid'];
                           $_SESSION['gb']['number_order'][$currnum] = $old_pid;
                // need to change "numbering" (pid => [index, number])
                $currord = $_SESSION['gb']['numbering'][$_REQUEST['pid']];
                $old_ord = $_SESSION['gb']['numbering'][$old_pid];
                           $_SESSION['gb']['numbering'][$_REQUEST['pid']] = ['index' => $currord['index'], 'number' => $newnum];
                           $_SESSION['gb']['numbering'][$old_pid] = ['index' => $old_ord['index'], 'number' => $currnum];
                // need to change tag, if it exists
                if ($_SESSION['gb']['story']['passages'][$idx]['tags'] && in_array($currnum,$_SESSION['gb']['story']['passages'][$idx]['tags'])) {
                    msg("Tag changed on current");
                    $aidx = array_search($currnum,$_SESSION['gb']['story']['passages'][$idx]['tags']);
                    $_SESSION['gb']['story']['passages'][$idx]['tags'][$aidx] = $newnum;
                }
                if ($_SESSION['gb']['story']['passages'][$old_ord['index']]['tags'] && in_array($newnum,$_SESSION['gb']['story']['passages'][$old_ord['index']]['tags'])) {
                    msg("Tag changed on other");
                    $aidx = array_search($newnum,$_SESSION['gb']['story']['passages'][$old_ord['index']]['tags']);
                    $_SESSION['gb']['story']['passages'][$old_ord['index']]['tags'][$aidx] = $currnum;
                }
                msg("Passage number swapped from $currnum to $newnum");
            }
            go('passage-edit',"pid={$_REQUEST['pid']}");
        }
    }
 
    if ($mode == 'show') {
        // in order from gb_numbering, show each paragraph
        echo gb_header([
            'title' => $_SESSION['gb']['story']['name'], 
            'css'   => ['css/game.css','css/preview.css','custom'],
            'js'    => ['js/preview.js']
        ]);
        //echo "<pre>" . print_r($_SESSION['gb']['numbering'],1) . print_r($_SESSION['gb']['number_order'],1) . "</pre>";
        echo htmlise(false,['covers' => false, 'print' => false, 'simplex' => true]);
        echo gb_footer();
    }

    /* =================================================================================== */
    /* EXPORT GAME                                                                         */
    /* =================================================================================== */

    if ($mode == 'export') {
        $filename = str_replace(' ','_',$_SESSION['gb']['story']['name'].".html");
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-type: text/html');
        echo htmldoc(false,['proof' => true]);
        exit;
    }

    if ($mode == 'export-json') {
        $filename = str_replace(' ','_',$_SESSION['gb']['story']['name']."-gbf.json");
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-type: application/json');
        $tmp = $_SESSION['gb']; unset($tmp['raw']);
        echo json_encode($tmp,JSON_PRETTY_PRINT);
        exit;
    }

    if ($mode == 'export-settings') {
        $filename = str_replace(' ','_',$_SESSION['gb']['story']['name']."-settings.json");
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-type: application/json');
        echo json_encode($_SESSION['gb']['settings'],JSON_PRETTY_PRINT);
        exit;
    }

    if ($mode == 'export-twee') {
        $filename = str_replace(' ','_',$_SESSION['gb']['story']['name'].".twee");
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-type: text/twee');
        echo ":: StoryTitle\n{$_SESSION['gb']['story']['name']}\n\n";
        echo ":: UserScript[script]\n\n:: UserStylesheet[stylesheet]\n\n";
        echo ":: StoryData\n";
            $sd = $_SESSION['gb']['story'];
            unset($sd['name']); unset($sd['passages']);
            if (!array_key_exists('start',$sd)) { $sd['start'] = $sd['startnode']; }
            echo json_encode($sd,JSON_PRETTY_PRINT) . "\n\n";
        foreach ($_SESSION['gb']['story']['passages'] AS $p) {
            echo twee_passage($p);
        }
        echo twee_passage([
            'name'      => 'gb-settings', 
            'text'      => json_encode($_SESSION['gb']['settings'],JSON_PRETTY_PRINT),
            'pid'       => $_SESSION['gb']['settings']['passage']['pid'],
            'position'  => $_SESSION['gb']['settings']['passage']['position'],
            'size'      => $_SESSION['gb']['settings']['passage']['size']
        ]);
        if ($_SESSION['gb']['gb-front-cover']) {
            echo twee_passage($_SESSION['gb']['gb-front-cover']);
        }
        if ($_SESSION['gb']['gb-introduction']) {
            echo twee_passage($_SESSION['gb']['gb-introduction']);
        }
        if ($_SESSION['gb']['gb-rear']) {
            echo twee_passage($_SESSION['gb']['gb-rear']);
        }
        if ($_SESSION['gb']['gb-rear-cover']) {
            echo twee_passage($_SESSION['gb']['gb-rear-cover']);
        }
    }

    if ($mode == 'export-twine') {
        $filename = str_replace(' ','_',$_SESSION['gb']['story']['name'].".html");
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-type: text/html');

        // storydata
        $attrs = [];
        $skip = ['tag-colors','passages'];
        foreach ($_SESSION['gb']['story'] AS $attr => $v) {
            if (!in_array($attr,$skip)) {
                $attrs[] = "$attr=\"$v\"";
            }
        }
        $attrs = implode(' ',$attrs);
        echo "<tw-storydata $attrs hidden>";
        echo "<style role=\"stylesheet\" id=\"twine-user-stylesheet\" type=\"text/twine-css\">{$_SESSION['gb']['settings']['css']}</style>";
        echo '<script role="script" id="twine-user-script" type="text/twine-javascript"></script>';
        if ($_SESSION['gb']['story']['tag-colors']) {
            foreach ($_SESSION['gb']['story']['tag-colors'] AS $tag => $col) { echo "<tw-tag name=\"$tag\" color=\"{$col}\"></tw-tag>"; }
        }

        // passages
        foreach ($_SESSION['gb']['story']['passages'] AS $p) {
            echo twine_passage($p);
        }

        // special passages
        echo twine_passage([
            'name'      => 'gb-settings', 
            'text'      => json_encode($_SESSION['gb']['settings'],JSON_PRETTY_PRINT),
            'pid'       => $_SESSION['gb']['settings']['passage']['pid'],
            'position'  => $_SESSION['gb']['settings']['passage']['position'],
            'size'      => $_SESSION['gb']['settings']['passage']['size']
        ]);
        if ($_SESSION['gb']['gb-front-cover']) {
            echo twine_passage($_SESSION['gb']['gb-front-cover']);
        }
        if ($_SESSION['gb']['gb-introduction']) {
            echo twine_passage($_SESSION['gb']['gb-introduction']);
        }
        if ($_SESSION['gb']['gb-rear']) {
            echo twine_passage($_SESSION['gb']['gb-rear']);
        }
        if ($_SESSION['gb']['gb-rear-cover']) {
            echo twine_passage($_SESSION['gb']['gb-rear-cover']);
        }
        echo "</tw-storydata>";
    }

    if ($mode == 'pdf-source') {
        $settings = [
            'duplex'    => $_REQUEST['print'] ? true : false,
            'covers'    => $_REQUEST['covers'] ? true : false,
        ];
        echo htmldoc(true,$settings['duplex'],$settings['covers']);
    }

    if ($mode == 'pdftest') {
        error_reporting(E_ALL);
        require_once __DIR__ . '/vendor/autoload.php';
        $config = [];
        $data = "<html><head><title>test</title></head><body>test</body></html>";
        $mpdf = new \Mpdf\Mpdf($config);
        $mpdf->WriteHTML($data);
        $mpdf->Output();
        /*
        require_once __DIR__ . '/vendor/autoload.php';

        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $settings = [
            'print'     => $_REQUEST['print'] ? true : false,
            'covers'    => $_REQUEST['covers'] ? true : false,
            'simplex'   => $_REQUEST['simplex'] ? true : false,
        ];

        $config = [
            'dpi'   => 300,
            'img_dpi' => 300,
            'list_auto_mode' => 'mpdf',
            'list_marker_offset' => '1em',
            'list_symbol_size' =>'0.31em',
        ];
        if ($_REQUEST['print'] && !$settings['covers']) {
            $config += [
                'margin_left'   => 20,
                'margin_right'  => 10,
                'mirrorMargins' => true
            ];
        }
        $mpdf = new \Mpdf\Mpdf($config);
        //$mpdf->SetHTMLFooter("<div class='footer'>{PAGENO}</div>");
        $mpdf->WriteHTML("
        <html>
        <head>
        <title>Test</title>
        <style>".file_get_contents('css/game.css')."</style>
        </head>
        <body>
        <div class='paragraph'>
        <div style='float:right;width:586px;text-align:center;'><img src='/images/iod/boise_circle.png'><b>Boise</b></div>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        </body>
        </html>");
        $mpdf->Output(); */
    }

    if ($mode == 'pdf') {
        require_once __DIR__ . '/vendor/autoload.php';

        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $settings = [
            'print'     => $_REQUEST['print'] ? true : false,
            'covers'    => $_REQUEST['covers'] ? true : false,
            'simplex'   => $_REQUEST['simplex'] ? true : false,
        ];

        $config = [
            'format' => $_SESSION['gb']['settings']['page_size'],
            'fontDir' => array_merge($fontDirs, [
                __DIR__ . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'fell' => [
                    'R' => 'IMFellDWPica-Regular.ttf',
                    'I' => 'IMFellDWPica-Italic.ttf',
                ]
            ],
            'dpi'   => 300,
            'img_dpi' => 300,
            'list_auto_mode' => 'mpdf',
            'list_marker_offset' => '1em',
            'list_symbol_size' =>'0.31em',
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 10,
            'margin_right'  => 10
            //'collapseBlockMargins' => false
        ];
        if ($_REQUEST['print'] && !$settings['covers']) {
            $config += [
                'margin_left'   => 20,
                'margin_right'  => 10,
                'mirrorMargins' => true
            ];
        }
        $mpdf = new \Mpdf\Mpdf($config);
        //$mpdf->SetHTMLFooter("<div class='footer'>{PAGENO}</div>");
        $mpdf->WriteHTML(htmldoc(true,$settings));
        $mpdf->Output();
    }

    if ($mode == 'settings') {
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
                        <label for='death_text'>Custom CSS</label>
                        <p><i>Custom CSS to override the Game view</i></p>
                        <textarea name='css' rows='10'>{$_SESSION['gb']['settings']['css']}</textarea>
                        <ul>
                            <li>Style <code>.paragraph</code> to change each entry</li>
                            <li>Style <code>.game-divider</code> to change the inter-paragraph rules</li>
                            <li>Style <code>.stats</code> to change stat rows</li>
                            <li>Style <code>.check</code> to change rules text</li>
                            <li>Style <code>.rules</code> to change rules text</li>
                            <li>Style <code>.cover</code> to change the cover page</li>
                            <li>Style <code>.cover_title</code> to change the cover page text container</li>
                        </ul>
                    </div>
                </div>
                <div class='form-row'>
                    <input type='submit' value='Save'>
                </div>
            </form>
        ",['title' => 'Settings']);
    }

    if ($mode == 'settings-save') {
        $_SESSION['gb']['settings']['end_text']   = $_REQUEST['end_text'];
        $_SESSION['gb']['settings']['death_text'] = $_REQUEST['death_text'];
        $_SESSION['gb']['settings']['separator']  = $_REQUEST['separator'];
        $_SESSION['gb']['settings']['css']        = $_REQUEST['css'];
        $_SESSION['gb']['settings']['page_size']  = $_REQUEST['page_size'];
        $_SESSION['gb']['settings']['cover']      = $_REQUEST['cover'];
        $_SESSION['gb']['settings']['break']      = $_REQUEST['break'];
        $_SESSION['gb']['settings']['mdtype']     = $_REQUEST['mdtype'];
        msg("Settings saved");
        go('settings');
    }

    if ($mode == 'fix') {

        require_once __DIR__ . '/vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A5-P'
        ]);
        $mpdf->WriteHTML("<!DOCTYPE html>
                <head>
                <title>Test</title>
                </head>
                <body>
                <htmlpageheader name=\"firstpageheader\" style=\"display:none\">
                </htmlpageheader>
                
                <htmlpagefooter name=\"firstpagefooter\" style=\"display:none\">
                </htmlpagefooter>
                
                <htmlpageheader name=\"otherpageheader\" style=\"display:none\">
                </htmlpageheader>
                
                <htmlpagefooter name=\"otherpagefooter\" style=\"display:none\">
                    <div class='footer'>{PAGENO}</div>
                </htmlpagefooter>
                <h3>Hello World</h3></body>
                </html>");
        $mpdf->Output();

        /*
        $raw = file_get_contents('gwcs_backup.html');
        preg_match_all("/\<tw-passagedata pid=\"([0-9]+)\" name=\"([A-Za-z\-'0-9 ]+)\" tags=\"[A-Za-z0-9 ]*\" position=\"([0-9.,]+)\"/m",$raw,$matches);
        echo "<pre>";
        print_r($matches);
        $ids = [];
        foreach ($matches[0] AS $mid => $match) {
            $pid  = $matches[1][$mid];
            $name = $matches[2][$mid];
            $pos  = $matches[3][$mid];
            list($x,$y) = explode(',',$pos);
            $pos  = floor($x).','.floor($y);
            $idx  = $_SESSION['gb']['passage_names'][$name]['idx'];
            $_SESSION['gb']['story']['passages'][$idx]['position'] = $pos;
        }
        */
        // check each number in number

        /*
        foreach ($_SESSION['gb']['story']['passages'] AS $idx => $passage) {
            // need to change "number_order" (number => pid)
            // need to change "numbering" (pid => [index, number])
            echo "<pre>";
            $numbering = $_SESSION['gb']['numbering'][$passage['pid']];
            if (!in_array($numbering['number'],$passage['tags'])) {
                echo "ERROR: tag mismatch in ".print_r($numbering,1).print_r($passage,1);
                $num = array_filter($passage['tags'],'is_numeric');
                $num = array_pop($num);
                echo "NEED TO CHANGE {$num} to {$numbering['number']}\n\n";
                $aidx = array_search($num,$passage['tags']);
                $_SESSION['gb']['story']['passages'][$idx]['tags'][$aidx] = $numbering['number'];
            }
        } */
    }

    if ($mode == 'test') {
        $t = "<checkboxes>3</checkboxes>\n<rules>Each time you are directed to this section, you may be told to check one of the boxes above. \n* If **no boxes are checked**, it is morning.\n* If **one box is checked**, it is afternoon.\n* If **two boxes are checked**, it is evening.\nOnce the third box is checked, your time is up. You can no longer pick any of the options here and must [[move on]]</rules>\n\nYou are in the bustling heart of Crescentium, the Crusader city on the western edge of Outremer. Narrow warrens of hard clay streets wind between buildings of stone and patterned brick, zig-zagging up and down the three hills that bracket the city. The streets are crowded day and night with a throng of Ta&#39;ashim beggars, Selentine knights in high helms, Badawin traders, and pilgrims clamouring to reach Ibrahim, the Holy City.\n\nTo the west, the broad harbour is filled with ships. To the north, the bells ring out from the Sacra Familia \u2014 a holy place of both True Faith and Ta&#39;ashim. To the east, the crowded Caravanserai abuts the fortified iron gates that open onto the coastal plains.\n\nYou have one day to prepare for your departure. You may:\n\n* [[Seek a blessing at the Sacra Familia|city sacra familia]]\n* [[Purchase supplies at the bazaar|city bazaar]]\n* [[Enter the caravanserai to arrange passage with a caravan|city caravanserai]]\n* [[Seek out the Knight Balthazar|city balthazar]]\n* [[Visit the library|city library]]";
        echo "<pre>".HTML_ENTITY_DECODE($t,ENT_QUOTES)."</pre>";
        echo "<pre>".htmlspecialchars(html_entity_decode($t,ENT_QUOTES),ENT_QUOTES)."</pre>";
    }
?>