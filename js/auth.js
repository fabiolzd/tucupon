document.addEventListener("DOMContentLoaded", () => {
    
    // --- 1. ELEMENTOS DEL DOM ---
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const authForm = document.querySelector('form');

    // --- 2. ICONOS SVG (Minimalistas y Profesionales) ---
    const eyeClosedSVG = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#86868b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    const eyeOpenSVG = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#86868b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;

    if(togglePasswordBtn) {
        togglePasswordBtn.innerHTML = eyeClosedSVG;
        Object.assign(togglePasswordBtn.style, {
            position: "absolute", right: "15px", top: "50%", transform: "translateY(-50%)",
            background: "none", border: "none", cursor: "pointer", display: "flex", zIndex: "10"
        });
        passwordInput.parentElement.style.position = "relative";
        passwordInput.style.paddingRight = "45px";
    }

    // --- 3. ELEMENTOS DE LA MASCOTA PARA ANIMAR ---
    const pupils = document.querySelectorAll('.pupil');
    const eyeWhites = document.querySelectorAll('circle[fill="white"], .eye-white'); 
    const lashes = document.querySelector('.lashes'); 
    const handsRest = document.querySelector('.hands-rest');
    const handsCovering = document.querySelector('.hands-covering');
    const allInputs = document.querySelectorAll('input');
    
    gsap.set(lashes, { opacity: 1, x: 50, y: 35 });

    let isPeeking = false;
    let isFocusedOnInput = false; 

    // --- 4. FUNCIÓN MAESTRA DE ESTADOS ---
    const actualizarMascota = (estado, inmediato = false) => {
        gsap.killTweensOf([handsRest, handsCovering, pupils, lashes, eyeWhites]);

        const duracion = inmediato ? 0 : 0.3;
        const duracionRapida = inmediato ? 0 : 0.2;

        if (estado === 'tapado') {
            gsap.to(handsRest, { opacity: 0, duration: 0.1 });
            gsap.to(handsCovering, { opacity: 1, y: 0, x: 0, duration: duracion });

            gsap.to(pupils, { opacity: 0, duration: 0.1 }); 
            gsap.to(eyeWhites, { scaleY: 0.1, transformOrigin: "center", duration: duracionRapida });

            gsap.to(lashes, { x: 50, y: 57, duration: duracionRapida, ease: "power2.out" });
        } 
        else if (estado === 'espiando') {
            gsap.to(handsRest, { opacity: 0, duration: 0.1 });
            gsap.to(handsCovering, { opacity: 1, y: 15, x: (i) => i === 0 ? -12 : 12, duration: duracion });

            gsap.to(eyeWhites, { scaleY: 1, transformOrigin: "center", duration: duracionRapida });
            gsap.to(pupils, { opacity: 1, x: 8, y: 5, duration: duracion });
            gsap.to(lashes, { x: 50, y: 35, duration: duracionRapida });
        } 
        else { // 'normal'
            gsap.to(handsCovering, { opacity: 0, duration: duracionRapida });
            gsap.to(handsRest, { opacity: 1, duration: duracionRapida });

            gsap.to(eyeWhites, { scaleY: 1, transformOrigin: "center", duration: duracionRapida });
            gsap.to(pupils, { opacity: 1, x: 0, y: 0, duration: duracion });
            
            gsap.to(lashes, { x: 50, y: 35, duration: duracionRapida });
        }
    };

    actualizarMascota('normal', true);

    // --- 5. LÓGICA DE PARPADEO (CORREGIDA) ---
    const parpadear = () => {
        const isNormal = gsap.getProperty(handsRest, "opacity") > 0.5;
        // Solo parpadea si está normal Y si la pestaña está activa
        if (isNormal && !document.hidden) {
            gsap.to([eyeWhites, pupils], { scaleY: 0.1, duration: 0.1, transformOrigin: "center", yoyo: true, repeat: 1 });
            gsap.to(lashes, { y: 45, duration: 0.1, yoyo: true, repeat: 1 });
        }
        setTimeout(parpadear, Math.random() * 5000 + 3000);
    };
    setTimeout(parpadear, 4000);

    // --- SEGUIMIENTO DE CURSOR ---
    document.addEventListener('mousemove', (e) => {
        const isNormal = gsap.getProperty(handsRest, "opacity") > 0.5;
        
        if (isNormal && !isFocusedOnInput && !isPeeking) {
            const mouseX = (e.clientX / window.innerWidth) - 0.5;
            const mouseY = (e.clientY / window.innerHeight) - 0.5;

            gsap.to(pupils, { 
                x: mouseX * 16, 
                y: mouseY * 16, 
                duration: 0.5, 
                ease: "power2.out" 
            });
        }
    });

    // --- 6. EVENTOS DE ENTRADA ---
    allInputs.forEach(input => {
        input.addEventListener('focus', () => {
            isFocusedOnInput = true; 
            if (input.type === 'password' || input.id === 'password') {
                actualizarMascota(isPeeking ? 'espiando' : 'tapado');
            } else {
                actualizarMascota('normal');
                gsap.to(pupils, { x: -8, y: 5, duration: 0.3 }); 
            }
        });

        input.addEventListener('input', (e) => {
            if (input.type !== 'password' && input.id !== 'password') {
                let length = input.value.length;
                let xPos = Math.min(-8 + (length * 0.4), 8);
                gsap.to(pupils, { x: xPos, duration: 0.1 });
            }
        });

        input.addEventListener('blur', () => {
            isFocusedOnInput = false; 
            actualizarMascota('normal');
        });
    });

    // --- 7. LÓGICA DEL OJITO ---
    if(togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('mousedown', (e) => e.preventDefault());
        togglePasswordBtn.addEventListener('click', () => {
            const isVisible = passwordInput.getAttribute('type') === 'text';
            passwordInput.setAttribute('type', isVisible ? 'password' : 'text');
            togglePasswordBtn.innerHTML = isVisible ? eyeClosedSVG : eyeOpenSVG;
            isPeeking = !isVisible;
            actualizarMascota(isPeeking ? 'espiando' : 'tapado');
        });
    }

    // --- 8. FONDO DINÁMICO (CORREGIDO) ---
    const crearCuponFondo = () => {
        // Si el usuario cambió de pestaña, no creamos nada para evitar lag al volver
        if (document.hidden) return;

        const cupon = document.createElement('div');
        cupon.innerHTML = '🎟️';
        cupon.style.position = 'fixed';
        cupon.style.left = Math.random() * 100 + 'vw';
        cupon.style.top = '-50px';
        cupon.style.fontSize = Math.random() * 20 + 20 + 'px';
        cupon.style.opacity = '0.1';
        cupon.style.zIndex = '-1';
        cupon.style.pointerEvents = 'none';
        document.body.appendChild(cupon);

        gsap.to(cupon, {
            y: window.innerHeight + 100,
            x: '+=' + (Math.random() * 100 - 50),
            rotation: Math.random() * 360,
            duration: Math.random() * 10 + 10,
            ease: "none",
            onComplete: () => cupon.remove()
        });
    };
    setInterval(crearCuponFondo, 1500);

    // --- 9. AJAX (Formularios) ---
    if (authForm) {
        const alertBox = document.createElement('div');
        alertBox.className = 'auth-alert';
        authForm.parentNode.insertBefore(alertBox, authForm);

        authForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = authForm.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            btn.textContent = 'Cargando...';
            btn.disabled = true;
            alertBox.style.display = 'none';

            try {
                const formData = new FormData(authForm);
                const response = await fetch(authForm.action, { method: 'POST', body: formData });
                const data = await response.json();
                
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }

                alertBox.style.display = 'block';
                
                if (data.status === 'success') {
                    alertBox.className = 'auth-alert success';
                    alertBox.textContent = data.message || 'Ingresando...';
                    actualizarMascota('normal');
                    gsap.to(pupils, { scale: 1.3, duration: 0.3 }); 
                    setTimeout(() => window.location.href = data.redirect, 1500);
                } else {
                    if (data.show_captcha) {
                        const captchaContainer = document.getElementById('captcha-container');
                        if (captchaContainer) {
                            captchaContainer.style.display = 'flex';
                        }
                    }

                    alertBox.className = 'auth-alert error';
                    alertBox.textContent = data.message;
                    btn.textContent = originalText;
                    btn.disabled = false;

                    gsap.fromTo(alertBox, { x: -5 }, { x: 5, duration: 0.05, yoyo: true, repeat: 5 });
                    gsap.fromTo(pupils, { x: -3 }, { x: 3, duration: 0.1, yoyo: true, repeat: 3 });
                }
            } catch (error) {
                console.error("ERROR:", error);
                if (typeof grecaptcha !== 'undefined') { grecaptcha.reset(); }
                alertBox.style.display = 'block';
                alertBox.className = 'auth-alert error';
                alertBox.textContent = "Error al procesar la solicitud.";
                btn.textContent = originalText;
                btn.disabled = false;
            }
        });
    }
});