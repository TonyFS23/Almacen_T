<?
include('../inc/conectar.php');
if($_POST['funcion']=="Tabla"){
    $Auto = $consulta->query("SELECT productos.*, categorias.nombre AS categoria FROM productos LEFT JOIN categorias ON categorias.idcategorias=productos.idcategoria WHERE productos.fechabaja IS NULL");
	foreach ($Auto as $producto){
        $tabla .= "<tr>
            <td>".$producto['codigo']."</td>
            <td>".$producto['nombre']."</td>
            <td>".$producto['precio']."</td>
            <td>".$producto['stock']."</td>
            <td>".$producto['categoria']."</td>
            <td><button class='btn btn-warning editar' idregistros='".$producto['idproductos']."'>Editar</button> <button class='btn btn-danger eliminar' idregistros='".$producto['idproductos']."' >Eliminar</button></td>
        </tr>";
    }
    echo $tabla;
    exit();
}
if($_POST['funcion']=='Guardar'){
    //consulta para validar que el codigo no exista
    $Auto = $consulta->query("SELECT * FROM productos WHERE codigo='".$_POST['codigo']."'");
    foreach ($Auto as $producto);
    if($producto['idproductos']>0){
        echo "El codigo ya existe";
        exit();
    }
    $Auto = $consulta->query("INSERT INTO productos SET codigo='".$_POST['codigo']."', nombre='".$_POST['nombre']."', precio=".$_POST['precio'].", stock=".$_POST['stock'].", idcategoria=".$_POST['categoria']);
     foreach ($Auto as $producto);
 }
 if($_POST['funcion']=='Editar'){
    $Auto = $consulta->query("UPDATE productos SET codigo='".$_POST['codigo']."', nombre='".$_POST['nombre']."', precio=".$_POST['precio'].", stock=".$_POST['stock'].", idcategoria=".$_POST['categoria']." WHERE idproductos=".$_POST['idregistros']);
     foreach ($Auto as $producto);
 }
 if($_POST['funcion']=='Eliminar'){
    $Auto = $consulta->query("UPDATE productos SET fechabaja='".date("Y-m-d H:i:s")."' WHERE idproductos=".$_POST['idregistros']);
     foreach ($Auto as $producto);
 }
if($_POST['funcion']=="Modal"){
    if($_POST['tipo']=="Editar"){
        $Auto = $consulta->query("SELECT * FROM productos WHERE idproductos=".$_POST['id']);
        foreach ($Auto as $row);
    }
        $modal = "
        <div class='row'>
            <div class='col-3'>
                <div class='form-group'>
                    <b for='codigo'>Codigo</b>
                    <input type='text' class='form-control' id='codigo' value='".$row['codigo']."' name='codigo'>
                </div>
            </div>
            <div class='col-9'>
                <div class='form-group'>
                    <b for='nombre'>Nombre</b>
                    <input type='text' class='form-control' id='nombre' value='".$row['nombre']."'  name='nombre'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='precio'>Precio</b>
                    <input type='text' class='form-control' id='precio' value='".$row['precio']."'  name='precio'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='stock'>Stock</b>
                    <input type='text' class='form-control' id='stock' value='".$row['stock']."'  name='stock'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='categoria'>Categoria</b>
                    <select class='form-control' id='categoria' name='categoria'>
                       ";
                       $modal.="<option value=''>Seleccione</option>";
                       $Auto = $consulta->query("SELECT * FROM categorias");
                        foreach ($Auto as $rowc){
                            $seleccionado = ($row['idcategoria']==$rowc['idcategorias']) ? "selected" : "";
                            $modal.="<option value='".$rowc['idcategorias']."' $seleccionado>".$rowc['nombre']."</option>";
                        }                     
                   $modal .=" </select>
                </div>
            </div>
        </div>";
        echo $modal;
    exit();
}
?>