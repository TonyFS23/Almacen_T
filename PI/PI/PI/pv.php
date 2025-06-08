<?
include("inc/conectar.php");
function FolioVenta(){
    include "inc/conectar.php";
    $Auto = $consulta->query("SELECT MAX(idventas)+1 AS autoincrement  FROM ventas");
    foreach ($Auto as $row);

    if($row['autoincrement']==""){
        $folio = 1;
    }else{   
        $folio = $row['autoincrement'];
    }  
    return $folio;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario: <?=$_SESSION['SISTEMA']['nombre']?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min"></script>
</head>
<body>
    <div class="container-fluid">
    <?
      include "menu.php";      
      ?>
        <div class="row">
            <div class="col-10 text-center">
                <div class="row">
                    <div class="col-4">Clientes
                        <input list="datosClientes" name="" autocomplete="off" value="<?=$CLIENTES?>" class="form-control form-control-sm" id="clientes" placeholder="Buscar clientes">
                        <datalist id="datosClientes" active>
                        <?

                        $Auto = $consulta->query("SELECT * FROM clientes ");
                        foreach ($Auto as $producto){
                            echo "<option value ='$producto[idclientes]-$producto[nombre]'>";
                        }
                        ?>
                    </datalist>
                    </div>
                    <div class="col-2">Cantidad
                        <input type="number" class="form-control form-control-sm" id="cantidad" value="1">
                    </div>
                    <div class="col-2">Productos
                        <input list="datosProductos" name="" autocomplete="off" value="<?=$CLIENTES?>" class="form-control form-control-sm" id="productos" placeholder="Buscar Productos">
                        <datalist id="datosProductos" active>
                            <?

                            $Auto = $consulta->query("SELECT * FROM productos WHERE productos.fechabaja IS NULL");
                            foreach ($Auto as $producto){
                                echo "<option value ='$producto[codigo]-$producto[nombre]'>";
                            }
                            ?>
                        </datalist>
                        <button class="btn btn-success" id="agregar_producto">Agregar Producto</button>
                    </div>
                
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Codigo</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Precio U.</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_detalle">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="col-2">
                <div class="row">
                    <div class="col-12">
                        Folio Venta
                        <input type="text" class="form-control form-control-sm" id="folio" value="<?=str_pad(FolioVenta(), 4, "0", STR_PAD_LEFT);?>" readonly>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Resumen Venta</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">Total</div>
                                    <div class="col-6" id="total">0</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Efectivo</div>
                                    <div class="col-6"><input type="text" id="efectivo" class="form-control" value="0"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Cambio</div>
                                    <div class="col-6" id="cambio">0</div>
                                </div>
                                
                                <button class="btn btn-success" id="Guardar_Venta">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script>
        $(document).ready(function(){
            var bandera = true;
			$(window).on('beforeunload', function (e) {
				if(bandera){
					var message = '¿Estás seguro de que quieres abandonar esta página?';
                (e || window.event).returnValue = message; // Para la mayoría de los navegadores
                return message; // Para algunos navegadores como Firefox
				}
               
            });
            //validar el cambio del efectivo
            $(document).on("change", "#efectivo", function(){
                var total = Quita_Moneda($("#total").text());
                var efectivo = Quita_Moneda($(this).val());
                if(efectivo<total){
                    alert("El efectivo no puede ser menor al total");
                    $(this).val(total);
                    $("#cambio").text("$"+Formato_Moneda(0,2));
                }else{
                    $("#cambio").text("$"+Formato_Moneda(efectivo-total,2));
                }
            });
            //enter para agregar producto
            $(document).on("keypress", "#productos", function(e){
                if(e.which==13){
                    Agregar_Producto();
                }
            });
            function Agregar_Producto(){
                if($("#cantidad").val()==""){
                    alert("El campo Cantidad es obligatorio");
                    $("#cantidad").focus();
                    return false;
                }
                if($("#productos").val()==""){
                    alert("El campo producto es obligatorio");
                    $("#productos").focus();
                    return false;
                }
                var bandera = false;
                var codigo = $("#productos").val().split("-")[0];
                var cantidad = $("#cantidad").val();
                //validar si el producto ya esta agregado
                $("#tabla_detalle tr").each(function(){
                    console.log($(this).find("td:eq(1)").text()+"=="+codigo);
                    if($(this).find("td:eq(1)").text()==codigo){
                        cantidad = parseInt($(this).find("td:eq(0)").find("input").val())+1;
                        $(this).find("td:eq(0)").find("input").val(cantidad);
                        $(this).find("td:eq(4)").text("$"+Formato_Moneda(cantidad*Quita_Moneda($(this).find("td:eq(3)").text()),2));
                        SumarTotal();
                        bandera = true;
                        return false;
                    }
                });
                if(bandera==false){
                    $.ajax({
                        url: 'funciones/pv.php',
                        type: 'POST',
                        data: {funcion: 'Agregar',
                            codigo: codigo,
                            cantidad: cantidad},
                        success: function(response){
                            console.log(response);
                            $("#tabla_detalle").append(response);
                            $("#cantidad").val("1");
                            $("#productos").val("");
                            SumarTotal();
                        }
                    });
                }else{
                    $("#cantidad").val("1");
                    $("#productos").val("");
                }
            }
            
            $(document).on("click", "#Guardar_Venta", function(){
                if($("#tabla_detalle tr").length==0){
                    alert("No hay productos en la venta");
                    return false;
                }
                var productos = [];
                $("#tabla_detalle tr").each(function(){
                    var producto = {
                        cantidad: $(this).find("td:eq(0)").find("input").val(),
                        codigo: $(this).find("td:eq(1)").text(),
                        idproductos: $(this).find("td:eq(1)").attr("idproductos"),
                        precio: Quita_Moneda($(this).find("td:eq(3)").text())
                    };
                    productos.push(producto);
                });
                $.ajax({
                    url: 'funciones/pv.php',
                    type: 'POST',
                    data: {
                        funcion: 'Guardar_Venta',
                        idclientes: $("#clientes").val().split("-")[0],
                        total: Quita_Moneda($("#total").text()),
                        efectivo: Quita_Moneda($("#efectivo").val()),
                        cambio: Quita_Moneda($("#cambio").text()),
                        Detalle: productos},
                    success: function(response){
                       
                        if(response>1){
                            alert("Venta guardada correctamente "+response);
                            bandera = false;
                            var bob = window.open('', '_new');
							bob.location = "ticket_venta.php?folio=" + response;
                            location.reload();
                        }else{
                            alert("Error al guardar la venta");
                            console.log(response);
                        }
                    }
                });
            });
            // si el producto ya esta agregado cambiar la cantidad
            $(document).on("change", ".cantidad", function(){
                var codigo = $(this).val().split("-")[0];
                var cantidad = 1;
                $("#tabla_detalle tr").each(function(){
                    if($(this).find("td:eq(1)").text()==codigo){
                        cantidad = parseInt($(this).find("td:eq(0)").find("input").val())+1;
                        $(this).find("td:eq(0)").find("input").val(cantidad);
                        $(this).find("td:eq(4)").text("$"+Formato_Moneda(cantidad*Quita_Moneda($(this).find("td:eq(3)").text()),2));
                        SumarTotal();
                    }
                });
            });
            $(document).on("click", "#agregar_producto", function(){
                Agregar_Producto();
            });
            function SumarTotal(){
                var total = 0;
                $("#tabla_detalle tr").each(function(){
                    total += Quita_Moneda($(this).find("td:eq(4)").text());
                });
                $("#total").text("$"+Formato_Moneda(total,2));
            }
            $(document).on("click", ".eliminar", function(){
                //confirmar si desea eliminar
                if(!confirm("¿Estas seguro de eliminar el producto?")){
                    return false;
                }
                $(this).parent().parent().remove();
                SumarTotal();
            });
            //change cantidad
            $(document).on("change", ".cantidad", function(){
                var cantidad = $(this).val();
                if(cantidad=="" || cantidad<=0){
                    cantidad = 1;
                    $(this).val(1);
                }
                var precio = Quita_Moneda($(this).parent().parent().find("td:eq(3)").text());
                $(this).parent().parent().find("td:eq(4)").text("$"+Formato_Moneda(cantidad*precio,2));
                SumarTotal();
            });
            function Formato_Moneda(n, c, d, t) {
				var c = isNaN(c = Math.abs(c)) ? 2 : c,
					d = d == undefined ? "." : d,
					t = t == undefined ? "," : t,
					s = n < 0 ? "-" : "",
					i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
					j = (j = i.length) > 3 ? j % 3 : 0;
				return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
			}

			function Quita_Moneda(n) {
				n = String(n);
				var s = parseFloat(n.replace(",", "").replace("$", ""));
				if (isNaN(s)) s = 0;
				return s;
			}
        });
        </script>
</body>
</html>