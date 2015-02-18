function addUser() {
	if ($("#username").val() === "") {
		document.getElementById("isAddValid").innerHTML = "Please Add Username";
	} else if ($("#add_pwd").val() === "") {
		document.getElementById("isAddValid").innerHTML = "Please Add Password";
	} else {
		$.ajax({
					type : "POST",
					url : "http://192.168.83.130/demoz2/public/app/prod",
					cache : false,
					data : $("#addForm").serialize()
				})
				.done(
						function(msg) {
							window.location = "http://192.168.83.130/demoz2/public/app/app#nav/grid";
						});
	}
}
function setEditUser(click) {
	var id = click.id;

	$.ajax({
		type : "POST",
		url : "http://192.168.83.130/demoz2/public/app/setedit",
		cache : false,
		data : {
			userId : id
		}
	}).done(function(msg) {
		var data = JSON.parse(msg);
		var name = data.name;
		var pdwd = data.id;
		$("#editdisp").val(name);
		$("#stateEdit").val(id);
		window.location.href = "#nav/edit";
	});

}
function editUser() {
	var id = $("#stateEdit").val();
	var newName = $("#editdisp").val();
	$.ajax({
		type : "POST",
		url : "http://192.168.83.130/demoz2/public/app/edit",
		cache : false,
		data : {
			editId : id,
			newName : newName
		}
	}).done(function(msg) {
		window.location.href = "#nav/grid";
	});

}
function deleteUser(click) {
	var id = click.id;
	e = "#" + id;
	$(e).hide();
	$.ajax({
		type : "POST",
		url : "http://192.168.83.130/demoz2/public/app/delete",
		cache : false,
		data : {
			delId : id
		}
	}).done(function(msg) {
		// var data=JSON.parse(msg);
		// var name = data.name;
		// $("#editdisp").val(name);
		// window.location.href = "#nav/grid";

	});

}
function loginUser() {

	$
			.ajax({
				type : "POST",
				url : "http://192.168.83.130/demoz2/public/app/login",
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