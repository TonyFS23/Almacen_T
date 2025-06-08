<?php
    
    if($_POST['funcion'] == 'iniciar'){
        include("inc/conectar.php");
        $nombre = $_POST['nombre'];
        $contra = $_POST['contra'];
        $Auto = $consulta->query("SELECT * FROM usuarios WHERE nombre LIKE  '$nombre' AND contra LIKE '".MD5($contra)."'");
        foreach ($Auto as $row);
        if($row['idusuarios']>0){
            echo $row['tipo'];
            $_SESSION['SISTEMA']['idusuarios'] = $row['idusuarios'];
            $_SESSION['SISTEMA']['tipo'] = $row['tipo'];
            $_SESSION['SISTEMA']['nombre'] = $row['nombre'];
        }else{  
            echo "error";
        }
        exit();
    }
?>
