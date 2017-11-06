$(function(){
        var btnTestUser = $("#TestUser");
        btnTestUser.click(manejadorUser);

        var btnTestAdmin = $("#TestAdmin");
        btnTestAdmin.click(manejadorAdmin);
});

function manejadorAdmin() {
    $("#email").val("admin@estacionar.com");
    $("#pwd").val("admin123");  
}

function manejadorUser() { 
    $("#email").val("emp01@estacionar.com");
    $("#pwd").val("emp123"); 
}

