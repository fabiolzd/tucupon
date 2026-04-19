<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuCupon | Crear Cuenta</title>
    <link rel="stylesheet" href="css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="coupon-bg"></div>
    <div id="floating-elements"></div>

    <div class="split-container">
        <div class="mascot-side">
            <div class="mascot-wrapper">
                <svg id="mascot" viewBox="0 0 200 200">
                    <rect x="20" y="20" width="160" height="160" rx="30" fill="#FFCBA4"/>
                    <circle cx="20" cy="100" r="15" fill="#FFF0EE"/>
                    <circle cx="180" cy="100" r="15" fill="#FFF0EE"/>
                    
                    <g class="eyes">
                        <circle cx="70" cy="85" r="18" fill="white"/>
                        <circle cx="130" cy="85" r="18" fill="white"/>
                        <circle class="pupil" cx="70" cy="85" r="7" fill="#2d3436"/>
                        <circle class="pupil" cx="130" cy="85" r="7" fill="#2d3436"/>
                    </g>
                    
                    <g class="hands-rest">
                        <path d="M 40 140 Q 30 160 50 170" stroke="#FF6F61" stroke-width="12" stroke-linecap="round" fill="none"/>
                        <path d="M 160 140 Q 170 160 150 170" stroke="#FF6F61" stroke-width="12" stroke-linecap="round" fill="none"/>
                    </g>

                    <g class="hands-covering" style="opacity: 0;">
                        <path d="M 40 140 Q 20 85 70 85" stroke="#FF6F61" stroke-width="14" stroke-linecap="round" fill="none"/>
                        <path d="M 160 140 Q 180 85 130 85" stroke="#FF6F61" stroke-width="14" stroke-linecap="round" fill="none"/>
                    </g>

                    <path d="M85 135 Q100 150 115 135" stroke="white" stroke-width="4" fill="none"/>
                    <g class="lashes" style="opacity: 0;">
    <path class="lash-left" d="M 12 18 Q 20 12 28 18" stroke="#1d1d1f" stroke-width="2.5" fill="none" stroke-linecap="round"/>
    <path class="lash-right" d="M 72 18 Q 80 12 88 18" stroke="#1d1d1f" stroke-width="2.5" fill="none" stroke-linecap="round"/>
</g>
                </svg>
                <div class="mascot-label">¡Únete a la familia!</div>
            </div>
        </div>

        <div class="form-side">
            <div class="auth-card">
                <h2>Crea tu cuenta</h2>
                <p>Empieza a ahorrar con los mejores cupones.</p>
                
                <form action="procesar_registro.php" method="POST">
    <div class="input-box">
        <label>Nombre completo</label>
        <input type="text" name="nombre" placeholder="Ej. Fabio" required>
    </div>
    <div class="input-box">
        <label>Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required>
    </div>
<div class="input-box">
    <label for="fecha_nacimiento">Fecha de nacimiento</label>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
</div>
    <div class="input-box">
        <label>Contraseña</label>
        <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Crea una clave segura" required>
            <button type="button" class="toggle-password" id="togglePassword">👁️</button>
        </div>
    </div>
    <div class="g-recaptcha" 
     data-sitekey="6Lc94b4sAAAAAL9ZyitOQDOCPzY7b9QB8YhCzLkq" 
     style="margin-bottom: 20px; display: flex; justify-content: center;">
</div>
    <button type="submit" class="btn-auth">Registrarme</button>
    <?php
// Configuración de Google
$client_id = '15019764602-e4ecjkajqbqc6kkjka6jeitrm0slkg36.apps.googleusercontent.com'; // El código largo que ya tienes
$redirect_uri = 'https://tucupon.com.ve/google-callback.php';
$google_url = "https://accounts.google.com/o/oauth2/v2/auth?client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code&scope=email%20profile&access_type=offline";
?>

<div style="display: flex; align-items: center; margin: 20px 0;">
    <hr style="flex: 1; border: 0; border-top: 1px solid #f2f2f7;">
    <span style="margin: 0 10px; color: #86868b; font-size: 13px;">o regístrate con</span>
    <hr style="flex: 1; border: 0; border-top: 1px solid #f2f2f7;">
</div>

<a href="<?php echo $google_url; ?>" class="btn-google" style="
    display: flex; 
    align-items: center; 
    justify-content: center; 
    gap: 10px;
    width: 100%; 
    padding: 15px; 
    background: white; 
    border: 2px solid #f2f2f7; 
    border-radius: 15px; 
    text-decoration: none; 
    color: #1d1d1f; 
    font-weight: 600; 
    transition: 0.3s;
    font-size: 14px;">
    <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" width="20">
    Registrarme con Google
</a>
</form>
                
                <div class="footer-link">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
                </div>
            </div>
            <a href="index.php" class="back-link">← Volver al inicio</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="js/auth.js"></script>

</body>
</html>