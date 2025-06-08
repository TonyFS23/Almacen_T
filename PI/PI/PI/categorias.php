<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min"></script>
</head>
<body>
<div class="modal" tabindex="-1" id="modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo_modal">Alta de Produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modal-body">
        
        
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-primary invisible" id="Guardar_Edita">Editar Producto</button>
        <button type="button" class="btn btn-primary" id="Guardar_Nuevo">Agregar Producto</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
    <div class="container-fluid">
    <?
      include "menu.php";
      ?>
        <div class="row">
            <div class="col-10 text-center">
                <h1 class="">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-box2-heart-fill" viewBox="0 0 16 16">
                <path d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4zM8.5 4h6l.5.667V5H1v-.333L1.5 4h6V1h1zM8 7.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132"/>
                </svg> Categorias</h1>
                
            </div>
            <div class="col-2 text-center">
                <button class="btn btn-success" id="nuevo" data-bs-toggle="modal" data-bs-target="#modal"> Nuevo <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                </svg></button>
            </div>
            <div class="col-12 text-center">
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Codigo</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Precio U.</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultados_productos">
                       
                    </tbody>
                    </table>
            </div>
        
    </div>
    <script>
        $(document).ready(function(){
            Tabla();
            $(document).on("click", "#Guardar_Nuevo", function(){
              if($("#codigo").val()==""){
                alert("El campo Codigo es obligatorio");
                $("#codigo").focus();
                return false;
              }
              if($("#nombre").val()==""){
                alert("El campo Nombre es obligatorio");
                $("#nombre").focus();
                return false;
              }
              if($("#precio").val()==""){
                alert("El campo Precio es obligatorio");
                $("#precio").focus();
                return false;
              }
              if($("#stock").val()==""){
                alert("El campo Stock es obligatorio");
                $("#stock").focus();
                return false;
              }
              if($("#categoria").val()==""){
                alert("El campo Categoria es obligatorio");
                $("#categoria").focus();
                return false;
              }
                $.ajax({
                  url: 'funciones/productos.php',
                  type: 'POST',
                  data: {funcion: 'Guardar',
                     codigo: $("#codigo").val(),
                     nombre: $("#nombre").val(),
                     precio: $("#precio").val(),
                     stock: $("#stock").val(),
                    categoria: $("#categoria option:selected").val()},
                  success: function(response){
                    if(response=="El codigo ya existe"){
                      alert(response);
                      return false;
                    }else{
                      Tabla();
                      $('#modal').modal('hide');
                    }
                    
                  }
                });
            });
            $(document).on("click", "#Guardar_Edita", function(){
              var idregistros = $(this).attr('idregistros');
              if($("#codigo").val()==""){
                alert("El campo Codigo es obligatorio");
                $("#codigo").focus();
                return false;
              }
              if($("#nombre").val()==""){
                alert("El campo Nombre es obligatorio");
                $("#nombre").focus();
                return false;
              }
              if($("#precio").val()==""){
                alert("El campo Precio es obligatorio");
                $("#precio").focus();
                return false;
              }
              if($("#stock").val()==""){
                alert("El campo Stock es obligatorio");
                $("#stock").focus();
                return false;
              }
              if($("#categoria").val()==""){
                alert("El campo Categoria es obligatorio");
                $("#categoria").focus();
                return false;
              }
                $.ajax({
                  url: 'funciones/productos.php',
                  type: 'POST',
                  data: {funcion: 'Editar',
                     codigo: $("#codigo").val(),
                     nombre: $("#nombre").val(),
                     precio: $("#precio").val(),
                     stock: $("#stock").val(),
                    categoria: $("#categoria option:selected").val(),
                    idregistros: idregistros},
                  success: function(response){
                    Tabla();
                    $('#modal').modal('hide');
                  }
                });
            });
            $(document).on("click", "#nuevo", function(){
              Modal("Nuevo", 0);
              $("#Guardar_Nuevo").show();
              $("#Guardar_Edita").hide();
              $("#titulo_modal").text("Alta de Producto");
            });
            $(document).on("click", ".editar", function(){
              var idregistros = $(this).attr('idregistros');
              Modal("Editar", idregistros);
              $("#Guardar_Nuevo").hide();
              $("#Guardar_Edita").show().removeClass('invisible').attr('idregistros', idregistros);
              $("#titulo_modal").text("Editar Producto");
              $('#modal').modal('show');
            });
            $(document).on("click", ".eliminar", function(){
              var idregistros = $(this).attr('idregistros');
              if(confirm("¿Desea eliminar este registro?")){
                $.ajax({
                  url: 'funciones/productos.php',
                  type: 'POST',
                  data: {funcion: 'Eliminar', idregistros: idregistros},
                  success: function(response){
                    Tabla();
                  }
                });
              }
            });
            function Modal(tipo, id){
                $.ajax({
                    url: 'funciones/productos.php',
                    type: 'POST',
                    data: {funcion: 'Modal', tipo: tipo, id: id},
                    success: function(response){
                        console.log(response);
                        $('#modal-body').html(response);
                    }
                });
            }
          function Tabla(){
            $.ajax({
              url: 'funciones/productos.php',
              type: 'POST',
              data: {funcion: 'Tabla'},
              success: function(response){
                console.log(response);
                $('#resultados_productos').html(response);
              }
            });
          }
          $('#resultados_productos').on('click', '.btn-danger', function(){
            $.ajax({
              url: 'funciones/productos.php',
              type: 'POST',
              data: {funcion: 'Eliminar', id: $(this).attr('id')},
              success: function(response){
                Tabla();
              }
            });
          });
        });
        </script>
</body>
</html>