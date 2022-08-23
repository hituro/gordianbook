<?php

    $content = file_get_contents("includes/tutorial.inc.html");
    $content = str_replace(['<x>','</x>'],
                           ["<pre><code class='language-twine'>",
                            "</code></pre>"],$content);
    echo page($content,[
        'title' => 'Tutorial',
        'js'    => [
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/prism.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/9000.0.1/components/prism-markdown.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/plugins/autoloader/prism-autoloader.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/plugins/normalize-whitespace/prism-normalize-whitespace.min.js",
            "<script>
            Prism.languages.twine = Prism.languages.extend('markdown', {
                'link': {
                    pattern: /\[\[(.*?)\]\]/
                },
                'code': /^```[\s\S]*?^```$/m
            });</script>"
        ],
        'css'   => [
            "/css/game.css",
            "/css/preview.css",
            "/css/tutorial.css",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/themes/prism.min.css",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/themes/prism-coy.min.css"
        ],
        'content_class' => 'tutorial',
        'sidebar' => "
            <ol>
                <li><a href='#install'>Installing Twine</a></li>
                <li><a href='#start'>Starting your Story</a></li>
                <li><a href='#expand'>Expanding your Story</a></li>
                <li><a href='#begin'>Beginings and Endings</a></li>
                <ul>
                    <li><a href='#introduction'>Adding an Introcution</a></li>
                    <li><a href='#ending'>Ending your Game</a></li>
                </ul>
                <li><a href='#pdf'>Making it pretty</a></li>
                <ul>
                    <li><a href='#page_size'>Page Size</a></li>
                    <li><a href='#covers'>Covers</a></li>
                    <li><a href='#styling'>Styling</a></li>
                </ul>
                <li><a href='#export'>Editing in Gordian</a></li>
                <ul>
                    <li><a href='#managing_settings'>Managing Settings</a></li>
                    <li><a href='#exporting'>Exporting</a></li>
                    <li><a href='#numbering'>Numbering</a></li>
                </ul>
                <li><a href='#interaction'>Adding Interactivity</a></li>
                <ul>
                    <li><a href='#items'>Items</a></li>
                    <li><a href='#rules'>Rules</a></li>
                    <li><a href='#keywords'>Keywords</a></li>
                    <li><a href='#checkboxes'>Checkboxes</a></li>
                    <li><a href='#reference'>Reference Sheets</a></li>
                </ul>
                <li><a href='#reuse'>Reusing Code</a></li>
                <ul>
                    <li><a href='#include'>Including Passages</a></li>
                    <li><a href='#templates'>Templates</a></li>
                </ul>
                <li><a href='#publishing'>Publishing</a></li>
                <ul>
                    <li><a href='#proofing'>Proofing</a></li>
                    <li><a href='#ordering'>Final Numbering</a></li>
                    <li><a href='#images'>Images</a></li>
                    <li><a href='#moving_passages'>Moving Passages</a></li>
                </ul>
            </ol>
        "
    ]);

?>
