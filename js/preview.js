// Listen to all click events on the document
document.addEventListener('click', function (event) {

	// If the clicked element does not have the .click-me class, ignore it
	if (!event.target.matches('.box')) return;

	// Otherwise, do something...

    event.target.dataset.checked = !(event.target.dataset.checked === 'true');
    console.log(event.target.dataset);
    console.log(event.target.dataset.checked);

});
