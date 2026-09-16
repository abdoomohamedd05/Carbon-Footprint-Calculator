/* ==========================================
   Carbon Footprint AI Dashboard
========================================== */

document.addEventListener("DOMContentLoaded", function () {

    const counters = document.querySelectorAll(".counter");
    counters.forEach(counter => {
        const raw = counter.innerText.replace(/,/g, "");
        const target = parseFloat(raw);
        if (isNaN(target)) return;

        let current = 0;
        const increment = target / 80 || 1;
        const decimals = (raw.split(".")[1] || "").length;

        function updateCounter() {
            if (current < target) {
                current += increment;
                const value = Math.min(current, target);
                counter.innerText = decimals > 0
                    ? value.toFixed(Math.min(decimals, 2))
                    : Math.ceil(value).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.innerText = decimals > 0
                    ? target.toFixed(Math.min(decimals, 2))
                    : target.toLocaleString();
            }
        }
        updateCounter();
    });

    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("menuToggle");
    if (toggle && sidebar) {
        toggle.addEventListener("click", function () {
            sidebar.classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        });
    }

    /* Draggable sidebar footer (Logout block) */
    (function initDraggableFooter() {
        const footer = document.getElementById("sidebarFooter");
        const handle = document.getElementById("footerDragHandle");
        const resetBtn = document.getElementById("footerResetBtn");
        const side = document.getElementById("sidebar");
        if (!footer || !handle || !side) return;

        const STORAGE_KEY = "sidebarFooterTop";

        function clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        }

        function applyTop(top) {
            const maxTop = Math.max(0, side.clientHeight - footer.offsetHeight);
            const safeTop = clamp(top, 0, maxTop);
            footer.classList.add("is-moved");
            footer.style.top = safeTop + "px";
            localStorage.setItem(STORAGE_KEY, String(safeTop));
        }

        function resetFooter() {
            footer.classList.remove("is-moved", "is-dragging");
            footer.style.top = "";
            localStorage.removeItem(STORAGE_KEY);
        }

        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved !== null && saved !== "") {
            applyTop(parseFloat(saved));
        }

        let dragging = false;
        let startY = 0;
        let startTop = 0;

        handle.addEventListener("mousedown", function (e) {
            if (e.target.closest("#footerResetBtn")) return;
            e.preventDefault();
            dragging = true;
            footer.classList.add("is-dragging", "is-moved");

            const rect = footer.getBoundingClientRect();
            const sideRect = side.getBoundingClientRect();
            startY = e.clientY;
            startTop = rect.top - sideRect.top;
            footer.style.top = startTop + "px";
        });

        document.addEventListener("mousemove", function (e) {
            if (!dragging) return;
            const delta = e.clientY - startY;
            applyTop(startTop + delta);
        });

        document.addEventListener("mouseup", function () {
            if (!dragging) return;
            dragging = false;
            footer.classList.remove("is-dragging");
        });

        handle.addEventListener("touchstart", function (e) {
            if (e.target.closest("#footerResetBtn")) return;
            const touch = e.touches[0];
            dragging = true;
            footer.classList.add("is-dragging", "is-moved");
            const rect = footer.getBoundingClientRect();
            const sideRect = side.getBoundingClientRect();
            startY = touch.clientY;
            startTop = rect.top - sideRect.top;
            footer.style.top = startTop + "px";
        }, { passive: true });

        document.addEventListener("touchmove", function (e) {
            if (!dragging) return;
            const touch = e.touches[0];
            applyTop(startTop + (touch.clientY - startY));
        }, { passive: true });

        document.addEventListener("touchend", function () {
            dragging = false;
            footer.classList.remove("is-dragging");
        });

        if (resetBtn) {
            resetBtn.addEventListener("click", function (e) {
                e.stopPropagation();
                resetFooter();
            });
        }
    })();

    const darkBtn = document.getElementById("darkModeBtn");
    if (darkBtn) {
        darkBtn.addEventListener("click", function () {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem(
                "darkMode",
                document.body.classList.contains("dark-mode")
            );
        });
    }

    if (localStorage.getItem("darkMode") === "true") {
        document.body.classList.add("dark-mode");
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.target.classList.contains("show")) return;
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    });

    document.querySelectorAll(".dashboard-card,.kpi-card,.welcome-card").forEach(el => {
        el.classList.add("hidden");
        observer.observe(el);
    });

    const chartData = window.dashboardCharts || {};
    const carbonLabels = chartData.carbonLabels || ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
    const carbonValues = chartData.carbonValues || [0, 0, 0, 0, 0, 0];
    const esgValues = chartData.esgValues || [0, 0, 0];

    if (document.getElementById("carbonChart")) {
        new Chart(document.getElementById("carbonChart"), {
            type: "line",
            data: {
                labels: carbonLabels,
                datasets: [{
                    label: "CO₂e (kg)",
                    data: carbonValues,
                    borderColor: "#16a34a",
                    backgroundColor: "rgba(22,163,74,.15)",
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } }
            }
        });
    }

    if (document.getElementById("esgChart")) {
        new Chart(document.getElementById("esgChart"), {
            type: "doughnut",
            data: {
                labels: ["Environment", "Social", "Governance"],
                datasets: [{
                    data: esgValues,
                    backgroundColor: ["#16a34a", "#2563eb", "#7c3aed"]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const companySearch = document.getElementById("companySearch");
    const companyTable = document.getElementById("companyTable");
    if (companySearch && companyTable) {
        companySearch.addEventListener("keyup", function () {
            const q = companySearch.value.toLowerCase();
            companyTable.querySelectorAll("tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q) ? "" : "none";
            });
        });
    }

    const emissionSearch = document.getElementById("emissionSearch");
    const emissionTable = document.getElementById("emissionTable");
    if (emissionSearch && emissionTable) {
        emissionSearch.addEventListener("keyup", function () {
            const q = emissionSearch.value.toLowerCase();
            emissionTable.querySelectorAll("tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q) ? "" : "none";
            });
        });
    }

    window.showToast = function (message) {
        const toast = document.createElement("div");
        toast.className = "toast-message";
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add("show"), 100);
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    };
});
