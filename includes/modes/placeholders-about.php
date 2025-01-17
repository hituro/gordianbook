<?php

    echo page(file_get_contents("includes/placeholders.inc.html"),[
        'title' => 'Placeholders',
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
        ]]
    );

?>