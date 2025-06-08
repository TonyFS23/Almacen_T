<?
include('../inc/conectar.php');
if($_POST['funcion']=="Agregar"){
    $Auto = $consulta->query("SELECT * FROM productos WHERE codigo LIKE '".$_POST['codigo']."'");
	foreach ($Auto as $producto);
        $tabla .= "<tr>
            <td><input type='number' class='cantidad' value='".$_POST['cantidad']."' ></td>
            <td idproductos='".$producto['idproductos']."'>".$_POST['codigo']."</td>
            <td>".$producto['nombre']."</td>
            <td>".number_format(($producto['precio']),2)."</td>
            <td>".number_format(($producto['precio']*$_POST['cantidad']),2)."</td>
            <td> <button class='btn btn-danger eliminar' idregistros='".$producto['idproductos']."' >Eliminar</button></td>
        </tr>";                              
    echo $tabla;
    exit();
}
function FolioVenta(){
    include "../inc/conectar.php";
    $Auto = $consulta->query("SELECT MAX(idventas)+1 AS autoincrement  FROM ventas");
    foreach ($Auto as $row);

    if($row['autoincrement']==""){
        $folio = 1;
    }else{   
        $folio = $row['autoincrement'];
    }  
    return $folio;
}
//Guardar_Venta
if($_POST['funcion']=="Guardar_Venta"){
    $consulta->query("INSERT INTO ventas (fecha, total, folio, idclientes, idusuarios,efectivo) VALUES ('".date('Y-m-d H:i:s')."','".$_POST['total']."', '".str_pad(FolioVenta(), 4, "0", STR_PAD_LEFT)."', '".$_POST['idclientes']."', '".$_SESSION['SISTEMA']['idusuarios']."', ".$_POST['efectivo'].")");
    $idventas = $consulta->lastInsertId();
    
    foreach($_POST["Detalle"] as $key=>$val){
       
        $consulta->query("INSERT INTO ventasdetalle (idventas, idproductos, cantidad, precio) VALUES ('".$idventas."','".$val['idproductos']."','".$val['cantidad']."','".$val['precio']."')");
       // $consulta->query("UPDATE productos SET stock = stock - '".$val['cantidad']."' WHERE idproductos = '".$val['idproductos']."'");
    }

    echo str_pad($idventas, 4, "0", STR_PAD_LEFT);
    exit();
}

?>