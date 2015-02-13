function addUser() {
	    $.ajax({
    	type: "POST",
        url: "http://192.168.83.130/zf2app/public/app/prod",
        cache:false,
        data: $("#addForm").serialize()}).
        done(function (msg){
        	
        	window.location="http://192.168.83.130/zf2app/public/app/app#nav/grid";
        	
        }); 
}
