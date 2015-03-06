function addUser() {
    if ($("#username").val() === "") {
        document.getElementById("isAddValid").innerHTML = "Please Add Username";
    } else if ($("#add_pwd").val() === "") {
        document.getElementById("isAddValid").innerHTML = "Please Add Password";
    } else {
        $.ajax({
            type : "POST",
            url : myUrl+"/strom/public/app/prod",
            cache : false,
            data : $("#addForm").serialize()
        })
            .done(
            function(msg) {
                window.location = myUrl+"/strom/public/app/app#nav/grid";
            });
    }
}

function editUser() {
    var id = $("#stateEdit").val();
    var newName = $("#editdisp").val();
    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/edit",
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
    console.log(e);
    $(e).css('background-color','#C9302C' );
    $(e).hide('slow');
    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/delete",
        cache : false,
        data : {
            delId : id
        }
    }).done(function(msg) {

    });
}
function loginUser() {
    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/login",
        cache : false,
        data : $("#loginForm").serialize()
    })
        .done(
        function(msg) {
        });
}
function verifyUser() {

    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/isloggedin",
        cache : false
    })
        .success(
        function(msg) {
            if(msg==="false" || msg===null){
                $(".logout").hide();
                window.location.href = "#nav/login";
            }
            else{
                return msg;
            }
        });
}

function logoutUser() {
    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/logout",
        cache : false
    })
        .done(
        function(msg) {
            window.location.href = "#nav/login";
        });
}

