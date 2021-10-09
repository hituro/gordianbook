<?php

        if (is_numeric($_REQUEST['pid'])) {
            $idx     = $_SESSION['gb']['pids'][$_REQUEST['pid']];
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

?>