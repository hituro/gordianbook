setInterval(function() {
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', '/keepalive.php');
    httpRequest.send(null);
}, 30000);

$( document ).ready(function() {
    $(".messages").delay(5000).fadeOut();
    $('.codemirror.css').each(function(i, block) {
        var editor = CodeMirror.fromTextArea(block, {
            lineNumbers: true,
            lineWrapping: true,
            flattenSpans: false,
            mode: "css"
        });
    });
    $('.codemirror.html').each(function(i, block) {
        var editor = CodeMirror.fromTextArea(block, {
            lineNumbers: false,
            lineWrapping: true,
            flattenSpans: false,
            htmlMode: true,
            mode: "markdown"
        });
    });
});