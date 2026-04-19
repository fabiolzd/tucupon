<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuCupon | Ahorra con Estilo</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar">
        <div class="logo-container">
            <div class="coupon-icon"><div class="coupon-dot"></div></div>
            <div class="logo">TuCupon<span>.</span></div>
        </div>

        <div class="nav-actions">
            <?php if(isset($_SESSION['usuario_id'])): ?>
                <div class="user-profile">
                    <div class="user-info">
                        <span class="welcome-text">Hola,</span>
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                    </div>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['usuario_nombre'], 0, 1)); ?>
                    </div>
                    <div class="user-dropdown">
                        <a href="perfil.php">Mi Perfil</a>
                        <a href="mis-cupones.php">Mis Cupones</a>
                        <hr>
                        <a href="logout.php" class="logout-link">Cerrar Sesi贸n</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="register.php" class="btn-text">Crear cuenta</a>
                <a href="login.php" class="btn-primary">Ingresar</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <h1 class="hero-title">Ahorra con Estilo.</h1>
        <p class="hero-subtitle">La nueva forma de encontrar ofertas y pagar en cuotas en Venezuela.</p>
        <div class="cta-buttons">
            <button class="btn-primary">Ver Cupones</button>
            <button class="btn-secondary">Registrar Empresa</button>
        </div>
    </header>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>