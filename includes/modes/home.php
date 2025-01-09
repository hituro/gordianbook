<?php

    $lists = ['Import' => [], 'Edit' => [], 'Info' => [], 'Play' => [], 'Print' => [], 'Export' => []];
            
    $lists['Import'][] = "<a href='gordian.php?mode=load-json'>Import Game</a>";
    $lists['Import'][] = "<a href='gordian.php?mode=load-game-json'>Import Gordian JSON</a>";
    if ($_SESSION['gb']['raw']) {
        $lists['Import'][] = "<a href='gordian.php?mode=convert'>Convert to Gamebook</a>";
    }
    if ($_SESSION['gb']['numbering']) {
        $lists['Edit'][]  = "<a href='gordian.php?mode=intro-edit'>Edit Introduction/Cover</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=settings'>Edit Settings</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=css-edit'>Edit CSS</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=passage-edit'>Edit Passage</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=placeholders-edit'>Edit Placeholders</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=templates-edit'>Edit Templates</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=renumber'>Renumber</a>";
        $lists['Edit'][]  = "<a href='gordian.php?mode=load-settings'>Import Settings</a>";

        $lists['Info'][]  = "<a href='gordian.php?mode=keywords'>Keyword Report</a>";
        $lists['Info'][]  = "<a href='gordian.php?mode=items'>Item Report</a>";

        $lists['Play'][]  = "<a href='gordian.php?mode=show'>Preview Gamebook</a>";
        $lists['Play'][]  = "<a href='gordian.php?mode=show&playable=1#introduction'>Play Gamebook</a>";

        $lists['Print'][] = "<a href='gordian.php?mode=pdf' target='_new'>Export Gamebook PDF</a>";
        $lists['Print'][] = "PDF: <a href='gordian.php?mode=pdf&print=1' target='_new'>print</a> - 
                                  <a href='gordian.php?mode=pdf&print=1&covers=1' target='_new'>print + cover</a> - 
                                  <a href='gordian.php?mode=pdf&covers=1&simplex=1&cover-only=1' target='_new'>cover</a> - 
                                  <a href='gordian.php?mode=pdf&covers=1&cover-only=1' target='_new'>cover (duplex)</a> - 
                                  <a href='gordian.php?mode=pdf&skip_content=1' target='_new'>cover/matter</a>";
        $lists['Print'][] = "PDF: <a href='gordian.php?mode=pdf-range' target='_new'>page range</a>";

        $lists['Export'][] = "<a href='gordian.php?mode=export-twine'>Export Twine Archive</a> - (<a href='gordian.php?mode=export-twine&skip_numbers=1'>without numbers</a>)";
        $lists['Export'][] = "<a href='gordian.php?mode=export-twee'>Export Twee</a> - (<a href='gordian.php?mode=export-twee&skip_numbers=1'>without numbers</a>)";
        $lists['Export'][] = "<a href='gordian.php?mode=export-json'>Export Gordian JSON</a> - (<a href='gordian.php?mode=show-json'>view</a>)";
        $lists['Export'][] = "<a href='gordian.php?mode=export'>Export HTML (printable)</a>";
        $lists['Export'][] = "<a href='gordian.php?mode=export&proof=1'>Export HTML (proofing)</a>";
        $lists['Export'][] = "<a href='gordian.php?mode=export-playable'>Export HTML (playable)</a>";
        $lists['Export'][] = "<a href='gordian.php?mode=export-settings'>Export Settings</a>";
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

?>