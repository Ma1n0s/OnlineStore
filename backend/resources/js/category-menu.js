document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('[href*="category"]').forEach((link) => {
        link.addEventListener("click", function () {
            localStorage.setItem("last_category", this.href);
        });
    });

    const lastCategory = localStorage.getItem("last_category");
    if (lastCategory && window.location.href === lastCategory) {
        setTimeout(() => {
            const activeItem = document.querySelector(".menu-item.active");
            if (activeItem) {
                activeItem.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            }
        }, 300);
    }
});
