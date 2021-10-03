setInterval(function() {
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', 'https://ddonachie.virga.invertech.co.uk/keepalive.php');
    httpRequest.send(null);
}, 30000);

$( document ).ready(function() {
    $(".messages").delay(5000).fadeOut();
});