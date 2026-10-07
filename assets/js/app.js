document.addEventListener("DOMContentLoaded", () => {
    // Automatically dismiss Bootstrap alerts after a few seconds.
    document.querySelectorAll(".alert[data-auto-dismiss]").forEach((alert) => {
        setTimeout(() => {
            alert.remove();
        }, 4000);
    });
});
