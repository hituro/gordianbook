# Gordian Book

Gordian Book imports a Twine game (either a Twine Archive, or a Twison/Twee version), and converts it to a gamebook output by numbering and shuffling the paragraphs, and converting links to the appropriate "turn to page x" form. Once converted, you can view the game in the browser, download as a standalone HTML file (e.g. for further editing), or export it as a simple PDF.

# Installation

Gordian Book requires a PHP environment with Composer to install dependencies. Install this repo, and then run composer install to get your dependencies.

It also uses codemirror to provide syntax highlighting while editing. Install a copy of codemirror in js/codemirror