<?php
session_start(); 
error_reporting(0); 

require 'config.php';
header('Content-Type: application/json');

// --- CONFIGURACIÓN DE SEGURIDAD ---
$ip = $_SERVER['REMOTE_ADDR'];
$max_intentos = 5;
$secret_key = "6Lc94b4sAAAAADKU5b9I-MpEV7MJPdodww31zvYI"; // REEMPLAZA CON TU CLAVE SECRETA DE GOOGLE

// 1. Verificar intentos previos de esta IP
try {
    $stmt_check = $conexion->prepare("SELECT attempts FROM login_attempts WHERE ip_address = :ip");
    $stmt_check->execute(['ip' => $ip]);
    $intentos_actuales = $stmt_check->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $intentos_actuales = 0; // Si falla la tabla, dejamos pasar por ahora
}

// 2. Si excedió el límite, validar el Captcha antes de procesar nada
if ($intentos_actuales >= $max_intentos) {
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    if (empty($recaptcha_response)) {
        echo json_encode(["status" => "error", "message" => "Demasiados intentos. Completa el captcha.", "show_captcha" => true]);
        exit;
    }

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        echo json_encode(["status" => "error", "message" => "Captcha inválido. Inténtalo de nuevo.", "show_captcha" => true]);
        exit;
    }
}

// 3. Procesar el Login normal
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(["status" => "error", "message" => "Faltan datos."]);
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

try {
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        // LOGIN EXITOSO: Limpiar el contador de intentos para esta IP
        $stmt_del = $conexion->prepare("DELETE FROM login_attempts WHERE ip_address = :ip");
        $stmt_del->execute(['ip' => $ip]);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        
        echo json_encode(["status" => "success", "message" => "Ingresando...", "redirect" => "index.php"]);
    } else {
        // LOGIN FALLIDO: Incrementar el contador de intentos
        $stmt_ins = $conexion->prepare("INSERT INTO login_attempts (ip_address, attempts) VALUES (:ip, 1) ON DUPLICATE KEY UPDATE attempts = attempts + 1");
        $stmt_ins->execute(['ip' => $ip]);

        $intentos_restantes = $max_intentos - ($intentos_actuales + 1);
        $show_captcha = ($intentos_actuales + 1 >= $max_intentos);
        
        $msg = $show_captcha ? "Demasiados intentos fallidos. Completa el captcha." : "Correo o clave incorrectos. Intentos restantes: $intentos_restantes";

        echo json_encode([
            "status" => "error", 
            "message" => $msg, 
            "show_captcha" => $show_captcha
        ]);
    }
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error del servidor."]);
}