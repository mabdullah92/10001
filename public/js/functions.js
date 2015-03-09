function addUser() {
    if ($("#username").val() === "") {
        document.getElementById("isAddValid").innerHTML = "Please Add Username";
    } else if ($("#add_pwd").val() === "") {
        document.getElementById("isAddValid").innerHTML = "Please Add Password";
    } else {
        var tableName= $("#datatable").attr("name");
        $.ajax({
            type : "POST",
            url : myUrl+"/strom/public/app/submit",
            cache : false,
            data : $("#addForm").serialize()+'&tableName='+tableName+'&operation='+'insertC'
        })
            .done(
            function(msg) {
                window.location = myUrl+"/strom/public/app#nav/grid";
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
    var tableName= $("#datatable").attr("name");
    $(e).css('background-color','#C9302C' );
    $(e).hide('slow');
    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/submit",
        cache : false,
        data : {
            delId : id,
            tableName : tableName,
            operation : "deleteC"
        }
    }).done(function(msg) {

    });
}
function loginUser() {
    $.ajax({
        type : "POST",
        url : myUrl+"/strom/public/app/login",
        cache : false,
        data : $("#loginForm").serialize()+'&tableName='+'user'+'&operation='+'findC'
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

