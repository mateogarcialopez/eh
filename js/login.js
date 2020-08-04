$(document).ready(function () {
    localStorage.clear();
    $('#login').click(login);
})


function login(e) {
    e.preventDefault();
    $('.loader').show();

    let data = {
        user: $('#username').val(),
        pwd: $('#password').val()
    };
    
    if(validateLogin(data)){
        $.ajax({
            type: "post",
            url: "server/login.php",
            data: data,
            dataType: "json",
            success: function (response) {
                $('#username').val("");
                $('#password').val("");
                $('.loader').hide();
                if (!response) {
                    window.location.href = "monitoreoChatbot.html"
                } else {
                    if(response.tipo_usuario == "monitoreo"){

                        let nombre = response.nombre
                        localStorage.setItem('nombre', nombre)
                        let idusuario = response.idusuario
                        localStorage.setItem('idusuario', idusuario)
                        window.location.href = "monitoreoChatbot.html"
                        
                    }else{

                        let nombre = response.nombre
                        localStorage.setItem('nombre', nombre)
                        let idusuario = response.idusuario
                        localStorage.setItem('idusuario', idusuario)
                        window.location.href = "bot.html"
                    }
                    
                }
            },
            error: function(response) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error en el ingreso al sistema',
                  })
            }
        });

    }


}

function validateLogin(data){
    if(!data.user || data.user == "" || data.user==" " || !data.pwd || data.pwd == ""){
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Ingresa todos los campos para continuar',
          })
          $('.loader').hide();
        return false;
    }else return true;
}