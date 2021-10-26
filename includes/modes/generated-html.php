<?php

    echo page("
        <h1>Generated HTML</h1>
        <p>Gordian generates a variety of HTML structures to output your PDF, which you can style. Note that some generated tags and attributes (e.g. pagebreak, or cellSpacing) are MPDF specific markup.</p>

        <h2>Passages</h2>

        <h3>Default front cover (gb-front-cover)</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <div class='cover_back front'>
            <div class='cover_top'><h1>[STORY_NAME]</h1></div>
        </div>
        <pagebreak type='next-odd' resetpagenum='1'></pagebreak>
        </script></code></pre>
        
        <h3>Front and backmatter</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <div class='paragraph frontmatter long'>[TEXT]</div>
        <div class='paragraph backmatter long'>[TEXT]</div>
        </script></code></pre>
        
        <h3>Introduction (gb-introduction)</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <div class='paragraph introduction long'>[TEXT]</div>
        <div class='body_headers'></div>
        <!-- if breakafter -->
        <pagebreak type='next-odd' suppress='off'></pagebreak>
        <!-- else -->
        <div class='game_divider'></div>
        </script></code></pre>
        <pre><code class='language-css'>
        .introduction {
            font-size: 1.1em;
            page: introduction;
            page-break-inside: auto;
        }</code></pre>

        <h3>Passage</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <!-- if breakbefore -->
        <pagebreak suppress='off'/>
        <!-- optionally contents of <before> -->
        <div class='paragraph' id='para_[NUMBER]'>
            <bookmark content='[NUMBER]'></bookmark>
            <h2 id='[NUMBER]'><a name='[NUMBER]'>[NUMBER].</a></h2>
            [TEXT]
            <!-- optionally, for passages tagged end/death -->
            <div class='end'>[END_TEXT]</div>
            <div class='end death'>[DEATH_TEXT]</div>
        </div>
        <!-- optionally contents of <after> -->
        <!-- optionally -->
        <div class='game_divider'></div>
        <!-- if breakafter -->
        <pagebreak type='next-odd' suppress='off'></pagebreak>
        </script></code></pre>
        <pre><code class='language-css'>
        .paragraph, .rules {
            page-break-inside: avoid;
        }
        .paragraph.long {
            page-break-inside: auto;
        }</code></pre>

        <h3>Rear page (gb-rear)</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <pagebreak suppress='off'></pagebreak>
        <div class='paragraph rear'>[TEXT]</div>
        </script></code></pre>

        <h3>Default back cover (gb-rear-cover)</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <pagebreak resetpagenum='1' odd-header-name='firstpageheader' odd-footer-name='firstpagefooter' suppress='on'></pagebreak>
        <div class='cover_back rear'></div>
        [TEXT]
        </script></code></pre>
        
        <hr>
        <h2>Passage Text</h2>

        <h3>Special tags</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <check>     => <span class='check'>&nbsp;[TEXT]&nbsp;</span>
        <rules>     => <div class='rules'>[TEXT]</div>
        <stats>     => <div class='stats'>[TEXT]</div>
        <special>   => <div class='special'>[TEXT]</div>
        </script></code></pre>
        <p><i>We pad the sides of 'check' with spaces because MPDF doesn't support <code class='language-js'>display: inline-block;</code></i></p>
        <pre><code class='language-css'>
        .check {
            display: inline;
            border-radius: 0.5mm;
            padding: 1px 2px;
            border: none;
            background: rgb(205, 250, 255);
            color: black;
        }
        .rules {
            display: block;
            border-radius: 1mm;
            margin: 1em 0px;
            padding: 0.5em;
            border: 1px solid #999;
            background: #f4f4f4;
            color: black;
        }
        .stats {
            display: block;
            margin: 1em 0px;
            padding: 0px;
        }
        .stats .special {
            font-style: italic;
            margin-top: 1em;
        }
        </code></pre>
        
        <h3>Checkboxes</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <table class='checkboxes' cellSpacing='1mm' align='center'><tr>
            <td class='box' width='6mm'>&nbsp;</td>
        </tr></table>
        </script></code></pre>
        <p><i>CellSpacing and mm mesaurements are MPDF features</i></p>
        <pre><code class='language-css'>
        .checkboxes {
            text-align: center;
            height: 4mm;
            margin: 1em auto;
        }
        .checkboxes .box {
            display: inline-block;
            width: 6mm;
            height: 4mm;
            border: 0.2mm solid black;
            background: white;
        }
        </code></pre>

        <h3>Links</h3>
        <pre><code class='language-markup'><script type='prism-html-markup'>
        <a href='#[NUMBER]' class='passage-link'>[LINK TEXT]</a>
        </script></code></pre>
        ",
        [
        'js'    => [
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/prism.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/plugins/autoloader/prism-autoloader.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/plugins/normalize-whitespace/prism-normalize-whitespace.min.js",
            // "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/plugins/line-numbers/prism-line-numbers.min.js"
        ],
        'css'   => [
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/themes/prism.min.css",
            "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/themes/prism-coy.min.css",
            // "https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/plugins/line-numbers/prism-line-numbers.min.css"
        ]
    ]);

?>