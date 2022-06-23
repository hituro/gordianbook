<?php
 
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
                $pname = strtolower(html_entity_decode($passage['name'],ENT_QUOTES | ENT_HTML5));
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
                    } else if (substr($matter,0,11) == 'backmatter_') {
                        $debug .= "matter option 4\n";
                        $matter_idx = substr($matter,11);
                        $_SESSION['gb']['backmatter']['numbered'][$matter_idx] = $passage['pid'];
                    }
                    $debug .= "REMOVING $pname ({$passage['pid']}) AS FRONT/BACK MATTER\n".$matter." PLACING it in $mater_idx\n".print_r($_SESSION['gb']['frontmatter'],1).print_r($_SESSION['gb']['backmatter'],1);
                } else if ($passage['tags'] && (in_array('skip',$passage['tags']) || in_array('hidden',$passage['tags']))) {
                    $skip[$passage['pid']] = 1;
                    $passage_count --;
                    $debug .= "SKIPPING $pname, PASSAGE COUNT $passage_count\n";
                } else if ($passage['tags'] && in_array('stylesheet',$passage['tags'])) {
                    $_SESSION['gb']['settings']['story_css'] .= $passage['text'];
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
                    // preg_match_all("|<template name=\"(.*)\">(.*)</template>|sU",$passage['text'],$matches,PREG_SET_ORDER);
                    // $debug .= "MATCHES : " . print_r($matches,1);
                    // foreach ($matches AS $match) {
                    //     $templates[$match[1]] = $match[2];
                    // }
                    // $debug .= "TEMPLATES : " . print_r($templates,1);
                    $templates = convert_templates($passage['text']);
                    $_SESSION['gb'][$pname] = [
                        'passage' => $passage,
                        'templates' => $templates
                    ];
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
                $pname = html_entity_decode($passage['name'],ENT_QUOTES | ENT_HTML5);
                $pids[$passage['pid']] = $idx;
                $pnames[$pname] = ['idx' => $idx, 'pid' => $passage['pid']];
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
        
?>