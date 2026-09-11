
    const headers = document.querySelectorAll(".accordion-header");

        headers.forEach(header => {
            header.addEventListener("click", () => {
                const item = header.closest(".accordion-item");
                const content = header.nextElementSibling;
                const icon = header.querySelector(".icon");
                const isOpen = content.classList.contains("open");

                // Close all
                document.querySelectorAll(".accordion-item").forEach(i =>
                    i.classList.remove("active")
                );
                document.querySelectorAll(".accordion-content").forEach(c =>
                    c.classList.remove("open")
                );
                document.querySelectorAll(".icon").forEach(i =>
                    i.classList.remove("open") 
                );
                headers.forEach(h =>
                    h.setAttribute("aria-expanded", "false")
                );

                // Open current
                if (!isOpen) {
                    item.classList.add("active");
                    content.classList.add("open");
                    icon.classList.add("open"); 
                    header.setAttribute("aria-expanded", "true");
                }
            });
        });

