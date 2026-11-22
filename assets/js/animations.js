/**
 * EVENTIX - High-Performance Motion & Smooth Scroll Orchestration
 * 1. Lenis (lenis.dev) -> Butter-smooth scrolling
 * 2. GSAP + ScrollTrigger (gsap.com) -> Title word-by-word reveal on scroll
 * 3. Motion (motion.dev) -> Card entrance stagger & smooth hover physics
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. LENIS SMOOTH SCROLL (lenis.dev)
    // ==========================================
    let lenis = null;
    if (typeof Lenis !== 'undefined') {
        lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            gestureOrientation: 'vertical',
            smoothWheel: true,
            wheelMultiplier: 0.95,
            touchMultiplier: 1.8,
        });

        // Sync with GSAP ScrollTrigger if available
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            lenis.on('scroll', ScrollTrigger.update);

            gsap.ticker.add((time) => {
                lenis.raf(time * 1000);
            });

            gsap.ticker.lagSmoothing(0);
        } else {
            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);
        }

        window.lenis = lenis;
    }

    // ==========================================
    // 2. GSAP + SCROLLTRIGGER: WORD-BY-WORD REVEAL
    // ==========================================
    function initWordReveals() {
        if (typeof gsap === 'undefined') return;

        // Ensure ScrollTrigger is registered
        if (typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
        }

        const targets = document.querySelectorAll('.gsap-words-reveal');
        targets.forEach((el) => {
            if (el.dataset.splitDone) return;
            el.dataset.splitDone = 'true';

            const rawText = el.textContent.trim();
            const words = rawText.split(/\s+/);

            el.innerHTML = words.map(w => 
                `<span class="inline-block overflow-hidden mr-[0.22em] align-top"><span class="inline-block word-reveal will-change-transform">${w}</span></span>`
            ).join('');

            const wordSpans = el.querySelectorAll('.word-reveal');

            if (typeof ScrollTrigger !== 'undefined') {
                gsap.fromTo(wordSpans,
                    {
                        y: '110%',
                        opacity: 0,
                        rotateZ: 2
                    },
                    {
                        y: '0%',
                        opacity: 1,
                        rotateZ: 0,
                        duration: 0.75,
                        stagger: 0.045,
                        ease: 'power3.out',
                        scrollTrigger: {
                            trigger: el,
                            start: 'top 88%',
                            toggleActions: 'play none none none'
                        }
                    }
                );
            } else {
                gsap.fromTo(wordSpans,
                    { y: '110%', opacity: 0 },
                    { y: '0%', opacity: 1, duration: 0.75, stagger: 0.045, ease: 'power3.out' }
                );
            }
        });
    }

    // ==========================================
    // 3. MOTION (motion.dev): CARD ENTRANCE & HOVER
    // ==========================================
    function initCardsAnimation() {
        const cards = document.querySelectorAll('.modern-card:not(.motion-initialized)');
        if (!cards.length) return;

        // Use Motion library if loaded
        if (window.Motion && typeof window.Motion.animate === 'function') {
            const { animate, stagger } = window.Motion;

            animate(
                Array.from(cards),
                { 
                    opacity: [0, 1], 
                    y: [32, 0], 
                    scale: [0.97, 1] 
                },
                { 
                    delay: stagger(0.06), 
                    duration: 0.65, 
                    easing: [0.16, 1, 0.3, 1] 
                }
            );

            cards.forEach(card => {
                card.classList.add('motion-initialized');
                card.addEventListener('mouseenter', () => {
                    animate(card, { y: -5, scale: 1.012 }, { duration: 0.22, easing: 'ease-out' });
                });
                card.addEventListener('mouseleave', () => {
                    animate(card, { y: 0, scale: 1 }, { duration: 0.22, easing: 'ease-out' });
                });
            });
        } else if (typeof gsap !== 'undefined') {
            // Fallback gracefully to GSAP
            gsap.fromTo(cards,
                { opacity: 0, y: 32, scale: 0.97 },
                { opacity: 1, y: 0, scale: 1, duration: 0.6, stagger: 0.06, ease: 'power2.out' }
            );

            cards.forEach(card => {
                card.classList.add('motion-initialized');
                card.addEventListener('mouseenter', () => {
                    gsap.to(card, { y: -5, scale: 1.012, duration: 0.22, ease: 'power2.out' });
                });
                card.addEventListener('mouseleave', () => {
                    gsap.to(card, { y: 0, scale: 1, duration: 0.22, ease: 'power2.out' });
                });
            });
        }
    }

    // Expose for dynamic rendering in catalog.js
    window.initCardsAnimation = initCardsAnimation;
    window.initWordReveals = initWordReveals;

    // Run on initial page load
    initWordReveals();
    initCardsAnimation();
});
