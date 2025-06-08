<?
//formatear fecha


if($_POST['funcion']=='Carga_Ventas'){
	include("inc/conectar.php");
	$tipo = '';
	$cliente = '';
	if($_POST['cliente']!=''){
		$clien = explode("-",$_POST['cliente']);
		$cliente = ' AND idclientes='.$clien[0];
	}
	$cancelado = '';
	$resultados=$consulta->query("SELECT ventas.*, clientes.nombre AS cliente, usuarios.nombre AS usuario FROM ventas LEFT JOIN clientes ON clientes.idclientes=ventas.idclientes LEFT JOIN usuarios ON usuarios.idusuarios=ventas.idusuarios WHERE fecha BETWEEN '".$_POST['fechai']." 00:00:00' AND '".$_POST['fechaf']." 23:59:59' $cliente");
	foreach ($resultados as $row) {
		if($row['fechacancelada']!='')$cancelado = 'bg-danger';
		$fecha=explode(" ",$row["fecha"]);
		$ano=explode("-",$fecha[0]);
		$mes=$ano[1];
		$dia=$ano[2];
		$horas = explode(":",$fecha[1]);
        $hora = $horas[0];
	?>
	<tr class="<?=$cancelado?> text-uppercase">
		<td><?=str_pad($row["folio"], 6, "0", STR_PAD_LEFT)?></td>
		<td><?=$dia."/".$mes."/".$ano[0]." ".$hora.":".$horas[1].":".$horas[2]?></td>
		<td><?=$row['cliente']?></td>
		<td><?=$row['usuario']?></td>
		<td align="right">$ <?=number_format($row['total'],2)?></td>
		<td width="200">
			<a href="ticket_venta.php?folio=<?=$row['folio']?>" target="_blank"><button class='btn-group btn-group-sm btn-info' title='Reimprimir' >Ticket</button></a>
			<?php
			if($row['tipo']!='Cancelada' and $row['estado']=='Pedido'){
				//if($_SESSION["SISTEMA"]["tipo"]=='admin'){
				?>
			<button class='btn-group btn-group-xs btn-warning cancelar' idventas="<?=$row[0]?>" title='Cancelar Venta' >
            <i class="bi bi-x-circle"></i></button>
			<?
           		// }
			}
			if($row['fechacancelada']==''){
				//boton para cancelar	
				?>
					<button class='btn-group btn-group-xs btn-danger cancelar' idventas="<?=$row['idventas']?>" title='Cancelar' >Cancelar</button>
			<?
			}
			?>
		</td>
	</tr>
<?
	$cancelado = '';
	}
	exit();
}
if($_POST['funcion']=='Eliminar'){
include('inc/conectar.php');
	$Auto = $consulta->query("UPDATE ventas SET fechacancelada='".date("Y-m-d H:i:s")."' WHERE idventas=".$_POST['idventas']." ");
	foreach ($Auto as $Autocontador);
	//REGRESO INVENTARIO
	//$Auto = $consulta->query("SELECT * FROM ventas_detalle WHERE idventas=".$_POST['idregistro']." ");
	//foreach ($Auto as $row){
	//	$Auto = $consulta->query("UPDATE productos SET existencias=(existencias+".$row['cantidad'].") WHERE idproductos=".$row['idproductos']." ");
	//	foreach ($Auto as $Autocontador);
	//}

	//$Auto = $consulta->query("DELETE FROM cxc WHERE idventas=".$_POST['idregistro']." ");
	//foreach ($Auto as $Autocontador);
	exit();
}
function Auto_Tabla(){
	include("inc/conectar.php");
	$Auto = $consulta->query("SHOW TABLE STATUS Like 'cxc'");
	foreach ($Auto as $Autocontador);
	$Auto = ($Autocontador["Auto_increment"]);
	return $Auto;
}
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="alertifyjs/css/alertify.css">
	<link rel="stylesheet" href="alertifyjs/css/themes/bootstrap.css">
	<script src="alertifyjs/alertify.js"></script>
	</head>
<body>
	<?php
	include("inc/conectar.php");
	$estado = 'todos';
	$tipo = 'todos';
	if(isset($_GET['fechainicial'])){$fechainicial = $_GET['fechainicial'];}else{$fechainicial = date("Y-m-d");}
	if(isset($_GET['fechafinal'])){$fechafinal = $_GET['fechafinal'];}else{$fechafinal = date("Y-m-d");}
	if(isset($_GET['idcliente'])){$idcliente = $_GET['idcliente'];}else{$idcliente = '';}
    include("menu.php");
	?>
<div class="container-fluid" style="margin-top:5px;">
	<div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 text-center ">
                	<h4><i class="bi bi-calendar2-week"></i> Consulta de Pedidos</h4>
                </div>
            </div>
            <div class="row">
				
                <div class="col-md-2 text-center ">
                    <b>Fecha Inicial</b>
                    <input type="date" class="form-control " id="fechainicial" value="<?=$fechainicial?>">
                </div>
                <div class="col-md-2 text-center ">
                    <b>Fecha Final</b>
                    <input type="date" class="form-control " id="fechafinal" value="<?=$fechafinal?>">
                </div>
			
                <div class="col-md-4 text-center ">
                    <b>Cliente</b><br>
					<input list="datosClientes" name="" autocomplete="off" value="<?=$idcliente?>" class="form-control" id="clientes" placeholder="Buscar clientes">
					<datalist id="datosClientes" active>
					<?
						$Auto = $consulta->query("SELECT * FROM clientes ");
						foreach ($Auto as $producto){
							echo "<option value ='$producto[idclientes]-$producto[nombre]'>";
						}
						?>
					</datalist>

                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 ">
                    <table id="example" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Usuario</th>
                                <th width="150">Importe</th>
                                <th align="center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody id="resultados_productos">
                            <tr>
                                <td colspan="5">Sin Resultados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cxc" role="dialog">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Abonos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modal_abonos">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(e) {

	Carga_Entradas();

	$(document).on("change","#fechainicial, #fechafinal, #clientes",function(){
		var ruta = '<?=$_SERVER["REQUEST_URI"];?>';
		ruta = ruta.split("?");
		if(ruta[1]== undefined){
			window.location = "ver_ventas.php?&fechainicial="+$("#fechainicial").val()+"&fechafinal="+$("#fechafinal").val()+"&idcliente="+$("#clientes").val();
		}else{
			window.location = "ver_ventas.php?&fechainicial="+$("#fechainicial").val()+"&fechafinal="+$("#fechafinal").val()+"&idcliente="+$("#clientes").val();
		}
	});
	
	


	function Carga_Entradas(){

		$.ajax({
			type: "POST",
			url: "ver_ventas.php",
			data: ({
				funcion : "Carga_Ventas",
				fechai : $("#fechainicial").val(),
				fechaf : $("#fechafinal").val(),
				cliente : $("#clientes").val()
			}),
			dataType: "html",
			async:false,
			success: function(msg){
				$("#resultados_productos").html(msg);
				$('#example').DataTable( {
					order: [
						[0, 'desc']
					],
					language: {
						processing:     "Procesando...",
						search:         "Buscar:",
						lengthMenu:    "Mostrar _MENU_ ",
						info:           "Mostrando _START_ de _END_ de Total de  _TOTAL_ resultados",
						infoEmpty:      "Sin Registros 0 de 0 de 0 Mostrando",
						infoFiltered:   "(Filtrando de _MAX_ Filtrados)",
						infoPostFix:    "",
						loadingRecords: "Chargement en cours...",
						zeroRecords:    "Sin Resultados",
						emptyTable:     "Sin Resultados en la Tabla",
						paginate: {
							first:      "Primero",
							previous:   "Anterior",
							next:       "Siguiente",
							last:       "Ultimo"
						},
						aria: {
							sortAscending:  ": Ordenar Ascendente",
							sortDescending: ": Ordenar Desendente"
						}
					}
				} );
				
			}
		});
	}
	
	
	$(document).on("click",".cancelar",function(){
		var idventas = $(this).attr("idventas");
		alertify.confirm("Eliminacion",'¿Estas Seguro de Cancelar la Venta ?', function(){
			$.ajax({
				type: "POST",
				url: "<?=$_SERVER["PHP_SELF"]?>",
				data: ({
					funcion : "Eliminar",
					idventas : idventas
				}),
				dataType: "html",
				async:false,
				success: function(msg){
					alertify.success("Venta Cancelada Exitosamente "+msg);
					window.location = "ver_ventas.php?estado="+$("#estado").val()+"&tipo="+$("#tipo").val()+"&fechainicial="+$("#fechainicial").val()+"&fechafinal="+$("#fechafinal").val()+"&idcliente="+$("#clientes").val()

				}
			});
		}, function(){
		alertify.error('Cancelado')});
	});

	function Quita_Moneda(n){
		n=String(n);
		var s=parseFloat(n.replace(",","").replace("$",""));
		if(isNaN(s))s=0;
		return s;
	}
});
</script>
</body>
