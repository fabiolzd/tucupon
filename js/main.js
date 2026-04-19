window.onload = () => {
    const tl = gsap.timeline();
    
    tl.from(".navbar", { duration: 1, y: -50, opacity: 0, ease: "power2.out" })
      .from(".logo-container", { duration: 0.5, x: -20, opacity: 0 }, "-=0.5")
      .from(".nav-actions", { duration: 0.5, x: 20, opacity: 0 }, "-=0.5")
      .from(".hero-title", { duration: 0.8, y: 30, opacity: 0, ease: "power3.out" }, "-=0.2")
      .from(".hero-subtitle", { duration: 0.8, opacity: 0 }, "-=0.5")
      .from(".cta-buttons", { duration: 0.8, scale: 0.9, opacity: 0 }, "-=0.5");
};