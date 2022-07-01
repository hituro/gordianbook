<?php
    $tmp = $_SESSION['gb']; unset($tmp['raw']);
    $tmp = json_encode($tmp,JSON_PRETTY_PRINT);

    echo page("
    <h1>Gordian JSON</h1>
    <p>Displays the internal data of your story in Gordian's JSON format. You can export this data using <a href='gordian.php?mode=export-json'>Export Gordian JSON</a> and import it using <a href='gordian.php?mode=load-game-json'>Import Gordian JSON</a>.</p>
    <p>You will not generally need to use this data.</p>
    <pre class='tall'><code class='language-json'><script type='prism-json-markup'>$tmp</script></code></pre>
    ",[
        'title' => 'JSON',
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
        ],
        'content_class' => 'wide'
      ],
    );

?>