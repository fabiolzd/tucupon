<?php
// Configuración de la base de datos de BanaHosting
$host = "localhost"; 
$db_name = "eplhyumd_tucupon"; // Cámbialo por el nombre real en cPanel
$user = "eplhyumd_admin_tucupon";      // Cámbialo por el usuario que creaste
$pass = "Ws251198$$";    // Cámbialo por la contraseña que pusiste

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db_name", $user, $pass);
    // ¡ESTA LÍNEA ES CRUCIAL PARA VER ERRORES DE CONEXIÓN!
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->exec("set names utf8");
} catch(PDOException $e) {
    // Si falla la conexión, queremos que envíe un JSON para que JS lo entienda
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Fallo al conectar DB: " . $e->getMessage()]);
    // MATAMOS EL SCRIPT AQUÍ para que no siga e imprima nada más
    exit(); 
}