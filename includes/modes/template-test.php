<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
    $t = '
    <repeat foo.a as item>{{item}} {{foo_a_index}} <if last>LAST</if></repeat>
    ';

    $data = ['foo' => [ 'a' => [ 'b' => 1, 'c' => 0], 'b', 'c'], 'title' => 'note'];
    
    echo "<pre>";
    $p = template_parse($t);
    echo htmlspecialchars($p);
    echo "</pre>";

    $o = template_execute($p,$data);
    echo "<pre>";
    echo htmlspecialchars($o);
    echo "</pre>";

?>