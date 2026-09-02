
        (() => {
            const track = document.getElementById("track");
            const wrap = track.parentElement;
            const cards = Array.from(track.children);
            const prev = document.getElementById("prev");
            const next = document.getElementById("next");
            const dotsBox = document.getElementById("dots");

            const isMobile = () => matchMedia("(max-width:1023px)").matches;

            cards.forEach((_, i) => {
                const dot = document.createElement("span");
                dot.className = "dot";
                dot.onclick = () => activate(i, true);
                dotsBox.appendChild(dot);
            });
            const dots = Array.from(dotsBox.children);

            let current = 0;
            let autoSlideInterval;

            function startAutoSlide() {
                if (autoSlideInterval) clearInterval(autoSlideInterval);
                autoSlideInterval = setInterval(() => {
                    if (isMobile() && cards.length > 0) {
                        go(1);
                    }
                }, 3000);
            }

            function stopAutoSlide() {
                if (autoSlideInterval) {
                    clearInterval(autoSlideInterval);
                    autoSlideInterval = null;
                }
            }

            function checkAutoSlide() {
                if (isMobile()) {
                    startAutoSlide();
                } else {
                    stopAutoSlide();
                }
            }

            function center(i) {
                const card = cards[i];
                const axis = isMobile() ? "top" : "left";
                const size = isMobile() ? "clientHeight" : "clientWidth";
                const start = isMobile() ? card.offsetTop : card.offsetLeft;
                wrap.scrollTo({
                    [axis]: start - (wrap[size] / 2 - card[size] / 2),
                    behavior: "smooth"
                });
            }

            function toggleUI(i) {
                cards.forEach((c, k) => c.toggleAttribute("active", k === i));
                dots.forEach((d, k) => d.classList.toggle("active", k === i));
                prev.disabled = i === 0;
                next.disabled = i === cards.length - 1;
            }

            function activate(i, scroll) {
                if (i === current) return;
                current = i;
                toggleUI(i);
                if (scroll) center(i);
            }

            function go(step) {
                const nextIndex = Math.min(Math.max(current + step, 0), cards.length - 1);
                if (isMobile()) {
                    // For mobile, wrap around for continuous auto-sliding
                    const wrappedIndex = nextIndex >= cards.length ? 0 : nextIndex < 0 ? cards.length - 1 : nextIndex;
                    activate(wrappedIndex, false);
                } else {
                    activate(nextIndex, true);
                }
            }

            prev.onclick = () => go(-1);
            next.onclick = () => go(1);

            addEventListener("keydown", (e) => {
                if (["ArrowRight", "ArrowDown"].includes(e.key)) go(1);
                if (["ArrowLeft", "ArrowUp"].includes(e.key)) go(-1);
            }, { passive: true });

            cards.forEach((card, i) => {
                card.addEventListener("mouseenter", () => {
                    if (isMobile()) stopAutoSlide();
                    if (matchMedia("(hover:hover)").matches) activate(i, true);
                });
                card.addEventListener("mouseleave", () => {
                    if (isMobile()) startAutoSlide();
                });
                card.addEventListener("click", () => activate(i, true));
            });

            let sx = 0, sy = 0;
            track.addEventListener("touchstart", (e) => {
                sx = e.touches[0].clientX;
                sy = e.touches[0].clientY;
            }, { passive: true });

            track.addEventListener("touchend", (e) => {
                const dx = e.changedTouches[0].clientX - sx;
                const dy = e.changedTouches[0].clientY - sy;
                const threshold = 30;
                
                if (isMobile()) {
                    // For mobile, use horizontal swipe
                    if (Math.abs(dx) > threshold && Math.abs(dx) > Math.abs(dy)) {
                        go(dx > 0 ? -1 : 1);
                        // Restart auto-slide after manual swipe
                        stopAutoSlide();
                        setTimeout(startAutoSlide, 1000);
                    }
                } else {
                    // For desktop, use vertical/horizontal as before
                    if (Math.abs(dy) > 60 || Math.abs(dx) > 60)
                        go((isMobile() ? dy : dx) > 0 ? -1 : 1);
                }
            }, { passive: true });

            if (isMobile()) dotsBox.hidden = false;

            addEventListener("resize", () => {
                center(current);
                checkAutoSlide();
            });

            toggleUI(0);
            center(0);
            checkAutoSlide();
        })();
 