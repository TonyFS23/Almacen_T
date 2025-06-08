<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Inicia sesión</title>

    <!--STYLE-->
    <style>
    .gradient-custom-2 {
        background: #fccb90;

        background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);

        background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
    }

    @media (min-width: 768px) {
        .gradient-form {
            height: 100vh !important;
        }
    }

    @media (min-width: 769px) {
        .gradient-custom-2 {
            border-top-right-radius: .3rem;
            border-bottom-right-radius: .3rem;
        }
    }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

</head>

<body>

    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">
                                    <div class="text-center">
                                        <img src="imagenes/Logo.jpeg" style="width: 185;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">Punto de venta</h4>
                                    </div>
                                    <div class="container-form">
                                        <h5>Inicio de sesión</h5>
                                        <div data-mdb-input-init class="form-floating mb-4">
                                            <input type="text" id="nombre" class="form-control" placeholder="Nombre de usuario">
                                            <label for="nombre">Nombre de usuario</label>
                                        </div>
                                        <div data-mdb-input-init class="form-floating mb-4">
                                            <input type="password" id="contra" class="form-control" placeholder="Contraseña">
                                            <label for="contra">Contraseña</label>
                                        </div>
                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button id="iniciar" class="btn btn-primary btn-block gradient-custom-2 fa-lg mb-3" type="button">Iniciar sesión</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <img src="imagenes/tienda.jpg" alt="Ventas" class="img-fluid"
                                    style="border-radius: 3 rem 3 rem 3 rem 3 rem;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

<script src="js/jquery-3.7.1.min"></script>
<script>
$(document).ready(function() {

    $(document).on('click', '#iniciar', function(event) {
        if ($("#user").val() == '') {
            alertify.error("Ingresa un nombre de usuario.");
            $("#user").focus();
            return false;
        }
        if ($("#contra").val() == '') {
            alertify.error("Ingresa una contraseña.");
            $("#contra").focus();
            return false;
        }

        $.ajax({
            type: "POST",
            url: "validarlogin.php",
            data: ({
                funcion: "iniciar",
                nombre: $("#nombre").val(),
                contra: $("#contra").val()
            }),
            dataType: "html",
            async: false,
            success: function(msg) {
               
                if (msg === "error") {
                    alertify.error("Usuario o contraseña incorrectos.");
                    $("#contra").val("");
                    $("#nombre").val("");
                } else {
                    alertify.success("Inicio de sesión exitoso.");
                    window.location.href = "index.php";
                }
            },
            error: function() {
                alertify.error("Error al procesar la solicitud.");
            }
        });
    });

});
</script>

</html>