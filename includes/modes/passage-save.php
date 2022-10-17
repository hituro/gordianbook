<?php

        if (is_numeric($_REQUEST['pid'])) {
            $idx     = $_SESSION['gb']['pids'][$_REQUEST['pid']];
            $_SESSION['gb']['story']['passages'][$idx]['text'] = $_REQUEST['text'];
            // tags
            if ($_REQUEST['tags']) {
                $_SESSION['gb']['story']['passages'][$idx]['tags'] = explode(' ',$_REQUEST['tags']);
            }
            // keywords
            $k = find_keywords($_REQUEST['text']);
            if ($k != $_SESSION['gb']['story']['passages'][$idx]['keywords']) {
                // some keywords have been removed or added
                $removed = array_diff($_SESSION['gb']['story']['passages'][$idx]['keywords'],$k);
                $added   = array_diff($k,$_SESSION['gb']['story']['passages'][$idx]['keywords']);
                foreach($removed AS $r) {
                    unset($_SESSION['gb']['keywords'][$r][$idx]); 
                }
                foreach($added AS $r) {
                    $_SESSION['gb']['keywords'][$r][$idx] ++;
                }
                if (!$_SESSION['gb']['keywords'][$r]) {
                    unset($_SESSION['gb']['keywords'][$r]);
                }
                //$_SESSION['gb']['keywords'] = array_diff($_SESSION['gb']['keywords'],$_SESSION['gb']['story']['passages'][$idx]['keywords']);
                $_SESSION['gb']['story']['passages'][$idx]['keywords'] = $k;
            }
            // items 
            $_SESSION['gb']['story']['passages'][$idx]['items'] = find_items($_REQUEST['text']);

            msg("Passage Saved");

            if (is_numeric($_REQUEST['swap_number']) && $_REQUEST['swap_number'] != $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number']) {
                // we are swapping numbers with another paragraph
                // need to change "number_order" (number => pid)
                $currnum    = $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number'];
                $newnum     = $_REQUEST['swap_number'];
                $target_pid = $_SESSION['gb']['number_order'][$newnum];
                
                gb_set_passage_number($_REQUEST['pid'],$currnum,$newnum);
                gb_set_passage_number($target_pid,$newnum,$currnum);
                msg("Passage number swapped from $currnum to $newnum");
            }
            else if (is_numeric($_REQUEST['move_number']) && $_REQUEST['move_number'] != $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number']) {
                // we are moving this passage to a given position
                // if curr > new 
                // we need to increment the number on every subsequent passage (including whatever currently has that number) until we reach curr
                // if curr < new
                // we need to decrement the number of every subsequent passage until we reach new
                //echo "<pre>";
                $currnum = $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number'];
                $newnum  = $_REQUEST['move_number'];
                //echo "Move from $currnum to $newnum\n";
                if ($currnum > $newnum) {
                    //echo "Moving backward\n";
                    $moves = [[$_REQUEST['pid'],$currnum,$newnum,gb_get_passage($_REQUEST['pid'])['name']]];
                    for ($i = $newnum;$i < $currnum;$i ++) {
                        $target_pid = $_SESSION['gb']['number_order'][$i];
                        $moves[] = [$target_pid,$i,$i+1,gb_get_passage($target_pid)['name']];
                    }
                } else {
                    //echo "Moving forward\n";
                    $moves = [];
                    for ($i = $currnum+1;$i <= $newnum;$i ++) {
                        //echo "Will change number on $i\n";
                        $target_pid = $_SESSION['gb']['number_order'][$i];
                        $moves[] = [$target_pid,$i,$i-1,gb_get_passage($target_pid)['name']];
                    }
                    $moves[] = [$_REQUEST['pid'],$currnum,$newnum,gb_get_passage($_REQUEST['pid'])['name']];
                }
                //print_r($moves);
                foreach ($moves AS $move) {
                    gb_set_passage_number($move[0],$move[1],$move[2]);
                }
                //exit;
            }
            go('passage-edit',"pid={$_REQUEST['pid']}&saved=1");
        }

?>