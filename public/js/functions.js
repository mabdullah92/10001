function addUser() {
	$
			.ajax({
				type : "POST",
				url : "http://192.168.83.130/demoapp/public/app/prod",
				cache : false,
				data : $("#addForm").serialize()
			})
			.done(
					function(msg) {

						window.location = "http://192.168.83.130/demoapp/public/app/app#nav/grid";

					});
}
function editUser(click) {
	var id = click.id;

	$.ajax({
		type : "POST",
		url : "http://192.168.83.130/demoapp/public/app/edit",
		cache : false,
		data : {
			userId : id
		}
	}).done(function(msg) {
		console.log(msg);
		window.location.href = "#nav/edit";

	});

}
function loginUser() {

	$
			.ajax({
				type : "POST",
				url : "http://192.168.83.130/demoapp/public/app/login",
				cache : false,
				data : $("#loginForm").serialize()
			})
			.done(
					function(msg) {
						if (msg == "success") {
							// console.log(msg);
							window.location.href = "#nav/grid";
						} else {
							// console.log(msg);
							document.getElementById("validuser").innerHTML = "Incorrect Credentials";
						}
					});
}