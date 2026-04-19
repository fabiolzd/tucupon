<?php
session_start();
require 'config.php';

if (isset($_GET['code'])) {
    $client_id = 'CLIENT_ID';
    $client_secret = 'TU_CLIENT_SECRET'; 
    $redirect_uri = 'https://tucupon.com.ve/google-callback.php';

   
    $params = [
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri'  => $redirect_uri,
        'code'          => $_GET['code'],
        'grant_type'    => 'authorization_code',
    ];

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    $response = curl_exec($ch);
    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        // 2. Con el token, pedimos los datos reales (nombre, email, id)
        $user_info = file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $data['access_token']);
        $user = json_decode($user_info, true);

        $google_id = $user['sub'];
        $email = $user['email'];
        $nombre = $user['name'];

        // 3. ¿Ya existe este usuario en nuestra base de datos?
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE google_id = :gid OR email = :email");
        $stmt->execute(['gid' => $google_id, 'email' => $email]);
        $db_user = $stmt->fetch();

        if ($db_user) {
            // LOGIN: Ya existe, así que solo actualizamos el ID de Google si no lo tenía
            if (!$db_user['google_id']) {
                $upd = $conexion->prepare("UPDATE usuarios SET google_id = :gid WHERE id = :id");
                $upd->execute(['gid' => $google_id, 'id' => $db_user['id']]);
            }
            $_SESSION['usuario_id'] = $db_user['id'];
        } else {
            // REGISTRO: Es nuevo, lo insertamos
            // Nota: El password queda NULL porque entró por Google
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, google_id) VALUES (:nom, :em, :gid)");
            $stmt->execute(['nom' => $nombre, 'em' => $email, 'gid' => $google_id]);
            $_SESSION['usuario_id'] = $conexion->lastInsertId();
        }

        $_SESSION['usuario_nombre'] = $nombre;

        // ¡Todo listo! Pa' dentro
        header("Location: index.php");
        exit;
    }
}

// Si algo falla, lo mandamos de vuelta al registro con un error
header("Location: register.php?error=auth_failed");