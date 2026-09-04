
fetch("/assets/componets/header.php")
	.then(response => response.text())
	.then(data => {
		document.getElementById('header').innerHTML = data;
	});

fetch("/assets/componets/footer.php")
	.then(response => response.text())
	.then(data => {
		document.getElementById('footer').innerHTML = data;
	});