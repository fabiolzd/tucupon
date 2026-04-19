<?php
require 'config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Validar Captcha Primero
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $secret_key = "TU_SECRET_KEY_AQUI"; // Tu clave secreta real
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        echo json_encode(["status" => "error", "message" => "Por favor, completa el captcha correctamente."]);
        exit;
    }

    // 2. Recoger y Limpiar Datos
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $fecha_nac = $_POST['fecha_nacimiento']; // Nueva variable
    $password = $_POST['password'];

    // Validar fecha (No permitir registros de menores de 13 años, por ejemplo)
    $nacimiento = new DateTime($fecha_nac);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;

    if ($edad < 13) {
        echo json_encode(["status" => "error", "message" => "Lo sentimos, debes ser mayor de 13 años."]);
        exit;
    }

    // 3. Password Hash
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // QUERY ACTUALIZADA CON LA FECHA
        $sql = "INSERT INTO usuarios (nombre, email, fecha_nacimiento, password) VALUES (:nombre, :email, :fecha_nac, :password)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':fecha_nac', $fecha_nac);
        $stmt->bindParam(':password', $password_hash);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "¡Registro exitoso!", "redirect" => "login.php"]);
        }
    } catch(PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo json_encode(["status" => "error", "message" => "Este correo ya está registrado."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Hubo un error en el servidor."]);
        }
    }
}

require 'config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos el token del captcha
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // 1. VERIFICAR SI EL TOKEN EXISTE
    if (empty($recaptcha_response)) {
        echo json_encode(["status" => "error", "message" => "Por favor, completa el captcha."]);
        exit;
    }

    // 2. VALIDAR CON LA API DE GOOGLE
    $secret_key = "6Lc94b4sAAAAADKU5b9I-MpEV7MJPdodww31zvYI"; // Tu clave secreta (la que no debe verse)
    
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret'   => $secret_key,
        'response' => $recaptcha_response
    ];

    // Usamos curl para la petición (más robusto que file_get_contents)
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        echo json_encode(["status" => "error", "message" => "Validación de Captcha fallida. Intenta de nuevo."]);
        exit;
    }

    // --- SI EL CAPTCHA ES CORRECTO, SIGUE TU LÓGICA DE REGISTRO AQUÍ ---
    // (Nombre, Email, Password, etc.)
}

require 'config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiamos los datos
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Verificamos que no estén vacíos
    if(empty($nombre) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    // 2. VALIDACIÓN DE CORREO ESTÁNDAR
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "El formato del correo no es válido."]);
        exit;
    }

    // 3. VALIDACIÓN DE CONTRASEÑA SEGURA
    // Exigimos al menos 8 caracteres, 1 número y 1 letra mayúscula
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        echo json_encode(["status" => "error", "message" => "La clave debe tener mínimo 8 caracteres, una mayúscula y un número."]);
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "¡Registro exitoso! Llevándote al login...", "redirect" => "login.php"]);
        }
    } catch(PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo json_encode(["status" => "error", "message" => "Este correo ya está registrado."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Hubo un problema, intenta más tarde."]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>