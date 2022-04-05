<?php

        if (is_numeric($_REQUEST['pid'])) {
            $idx     = $_SESSION['gb']['pids'][$_REQUEST['pid']];
            $_SESSION['gb']['story']['passages'][$idx]['text'] = $_REQUEST['text'];
            $_SESSION['gb']['story']['passages'][$idx]['tags'] = explode(' ',$_REQUEST['tags']);
            msg("Passage Saved");
            if (is_numeric($_REQUEST['swap_number']) && $_REQUEST['swap_number'] != $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number']) {
                // we are swapping numbers with another paragraph
                // need to change "number_order" (number => pid)
                $currnum    = $_SESSION['gb']['numbering'][$_REQUEST['pid']]['number'];
                $newnum     = $_REQUEST['swap_number'];
                $target_pid = $_SESSION['gb']['number_order'][$newnum];
                //echo "<pre>";
                gb_set_passage_number($_REQUEST['pid'],$currnum,$newnum);
                gb_set_passage_number($target_pid,$newnum,$currnum);
                /*
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
                */
                //exit;
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
                        echo "Will change number on $i\n";
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