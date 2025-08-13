<?php
session_start();
if ($_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}
echo "<h1>Bienvenido empleado, " . $_SESSION['usuario'] . "</h1>";
?>