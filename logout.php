<?php 

session_start();

session_unset();
session_destroy();

echo "Sesión Cerrada con éxito";
echo "<script> 
        setTimeout(function(){
            window.location.href = 'index.php';}, 1300); 
    </script>";