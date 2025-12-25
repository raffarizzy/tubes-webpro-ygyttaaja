// =====================================================
// UNIFIED NAVBAR SYSTEM - SpareHub (LARAVEL VERSION)
// Handles: User Auth Display, Cart Count, Active Menu, Logout
// =====================================================

console.log(
    "%c🔧 Navbar Manager Loading...",
    "color: #4CAF50; font-weight: bold"
);

class NavbarManager {
    constructor() {
        this.currentUser = null;
        this.cartData = [];
        this.debugMode = true; // Set to false in production

        this.log("NavbarManager constructor called");
        this.init();
    }

    // =====================================================
    // LOGGING UTILITY
    // =====================================================
    log(message, data = null) {
        if (this.debugMode) {
            console.log(
                `%c[NavbarManager] ${message}`,
                "color: #2196F3",
                data || ""
            );
        }
    }

    error(message, error = null) {
        console.error(
            `%c[NavbarManager ERROR] ${message}`,
            "color: #f44336",
            error || ""
        );
    }

    // =====================================================
    // INITIALIZATION
    // =====================================================
    init() {
        this.log("Initializing...");

        // Check if DOM is ready
        if (document.readyState === "loading") {
            this.log("DOM not ready, waiting...");
            document.addEventListener("DOMContentLoaded", () => this.setup());
        } else {
            this.log("DOM ready, setting up immediately");
            this.setup();
        }
    }

    setup() {
        try {
            this.log("Setup starting...");

            // Load user data
            this.loadUserData();

            // Update user display
            this.updateUserDisplay();

            // Update cart count (only if element exists)
            this.updateCartCount();

            // Set active menu
            this.setActiveMenu();

            // Setup event listeners
            this.setupEventListeners();

            this.log("Setup complete!");
        } catch (error) {
            this.error("Setup failed", error);
        }
    }

    // =====================================================
    // USER MANAGEMENT
    // =====================================================
    loadUserData() {
        try {
            const userData = localStorage.getItem("loggedInUser");
            this.log("Loading user data from localStorage", userData);

            if (userData) {
                this.currentUser = JSON.parse(userData);
                this.log("User loaded:", this.currentUser);
            } else {
                this.log("No user in localStorage");
                this.currentUser = null;
            }
        } catch (error) {
            this.error("Error loading user data", error);
            this.currentUser = null;
        }
    }

    updateUserDisplay() {
        try {
            this.log("Updating user display...");

            const profilSection = document.getElementById("profil");

            if (!profilSection) {
                this.error("Profil section (#profil) not found in DOM!");
                return;
            }

            this.log("Profil section found", profilSection);

            if (this.currentUser && this.currentUser.nama) {
                this.log("Showing logged-in user:", this.currentUser.nama);

                // User is logged in - show name and logout button
                profilSection.innerHTML = `
          <img src="/img/iconPengguna.png" id="iconPengguna" alt="User Icon" />
          <a href="/profile" class="user-name-link">
            <span class="user-name">${this.escapeHtml(
                this.currentUser.nama
            )}</span>
          </a>
          <span class="nav-separator">|</span>
          <button class="btn-logout" id="btn-logout">Logout</button>
        `;

                this.log("User display updated, attaching logout listener...");
                // Re-attach logout event listener
                this.attachLogoutListener();
            } else {
                this.log("No user logged in, showing login link");

                // User not logged in - show login link
                profilSection.innerHTML = `
          <img src="/img/iconPengguna.png" id="iconPengguna" alt="User Icon" />
          <a href="/login" class="login-link">Login</a>
        `;
            }

            this.log("User display updated successfully");
        } catch (error) {
            this.error("Error updating user display", error);
        }
    }

    attachLogoutListener() {
        try {
            const logoutBtn = document.getElementById("btn-logout");
            if (logoutBtn) {
                this.log("Attaching logout listener");
                logoutBtn.addEventListener("click", () => this.handleLogout());
            } else {
                this.error("Logout button not found after creating it!");
            }
        } catch (error) {
            this.error("Error attaching logout listener", error);
        }
    }

    handleLogout() {
        try {
            this.log("Logout button clicked");
            const confirmed = confirm("Apakah Anda yakin ingin logout?");

            if (confirmed) {
                this.log("Logout confirmed, clearing data...");

                // Clear user data
                localStorage.removeItem("loggedInUser");

                // Optional: Clear cart data on logout
                // localStorage.removeItem('keranjangData');

                // Laravel logout - submit hidden form
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "/logout";

                // Add CSRF token
                const csrfToken = document.querySelector(
                    'meta[name="csrf-token"]'
                );
                if (csrfToken) {
                    const csrfInput = document.createElement("input");
                    csrfInput.type = "hidden";
                    csrfInput.name = "_token";
                    csrfInput.value = csrfToken.content;
                    form.appendChild(csrfInput);
                }

                document.body.appendChild(form);
                form.submit();
            } else {
                this.log("Logout cancelled");
            }
        } catch (error) {
            this.error("Error during logout", error);
        }
    }

    // =====================================================
    // CART MANAGEMENT
    // =====================================================
    updateCartCount() {
        try {
            const cartCountElement = document.getElementById("cart-count");

            if (!cartCountElement) {
                this.log(
                    "Cart count element not found (this is OK for pages without cart counter)"
                );
                return;
            }

            this.log("Updating cart count...");

            // Load cart data from localStorage
            const cartDataStr = localStorage.getItem("keranjangData");
            this.cartData = cartDataStr ? JSON.parse(cartDataStr) : [];

            this.log("Cart data loaded:", this.cartData);

            // Get current user ID
            const userId = this.currentUser ? this.currentUser.id : null;

            if (!userId) {
                this.log("No user ID, hiding cart count");
                cartCountElement.style.display = "none";
                return;
            }

            // Calculate total items for this user
            const userCart = this.cartData.filter(
                (item) => item.userId === userId
            );
            const totalItems = userCart.reduce(
                (sum, item) => sum + item.jumlah,
                0
            );

            this.log(`Total cart items: ${totalItems}`);

            // Update display
            cartCountElement.textContent = totalItems;

            if (totalItems > 0) {
                cartCountElement.style.display = "inline-block";
                this.log("Cart count displayed:", totalItems);
            } else {
                cartCountElement.style.display = "none";
                this.log("Cart is empty, hiding badge");
            }
        } catch (error) {
            this.error("Error updating cart count", error);
            const cartCountElement = document.getElementById("cart-count");
            if (cartCountElement) {
                cartCountElement.style.display = "none";
            }
        }
    }

    // =====================================================
    // ACTIVE MENU MANAGEMENT
    // =====================================================
    setActiveMenu() {
        try {
            this.log("Setting active menu...");

            // Get current page path
            let currentPath = window.location.pathname;

            this.log("Current path:", currentPath);

            // Remove all active classes first
            const navLinks = document.querySelectorAll("nav ul li a");
            this.log(`Found ${navLinks.length} nav links`);

            navLinks.forEach((link) => {
                link.classList.remove("active");
            });

            // Map paths to menu items (Laravel routes)
            const pathMenuMap = {
                "/": "/",
                "/keranjang": "/keranjang",
                "/checkout": "/keranjang", // Checkout counts as Keranjang
                "/profile": "/profile",
                "/edit_profil": "/profile",
                "/riwayat_pesanan": "/profile",
            };

            // Check if path starts with /produk (detail pages)
            if (currentPath.startsWith("/produk/")) {
                currentPath = "/"; // Product detail pages count as Beranda
            }

            const targetPath = pathMenuMap[currentPath] || currentPath;
            this.log("Target path for highlighting:", targetPath);

            // Find and activate the correct link
            let activated = false;
            navLinks.forEach((link) => {
                const href = link.getAttribute("href");
                this.log(`Checking link: ${href} against ${targetPath}`);

                if (href === targetPath) {
                    link.classList.add("active");
                    activated = true;
                    this.log(`Activated menu: ${href}`);
                }
            });

            if (!activated) {
                this.log("⚠️ No menu item was activated");
            }
        } catch (error) {
            this.error("Error setting active menu", error);
        }
    }

    // =====================================================
    // EVENT LISTENERS
    // =====================================================
    setupEventListeners() {
        try {
            this.log("Setting up event listeners...");

            // Logo click - go to homepage
            const logo = document.getElementById("logo");
            if (logo) {
                logo.addEventListener("click", () => {
                    this.log("Logo clicked, going to homepage");
                    window.location.href = "/";
                });
                logo.style.cursor = "pointer";
                this.log("Logo click listener attached");
            } else {
                this.log("Logo element not found");
            }

            // Listen for storage changes (for cart updates from other tabs/windows)
            window.addEventListener("storage", (e) => {
                this.log("Storage event detected:", e.key);

                if (e.key === "keranjangData") {
                    this.log("Cart data changed, updating count...");
                    this.updateCartCount();
                }
                if (e.key === "loggedInUser") {
                    this.log("User data changed, reloading...");
                    this.loadUserData();
                    this.updateUserDisplay();
                }
            });

            this.log("Event listeners set up");
        } catch (error) {
            this.error("Error setting up event listeners", error);
        }
    }

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================
    escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    // =====================================================
    // PUBLIC API (for external use)
    // =====================================================
    refreshCartCount() {
        this.log("Manual refresh cart count requested");
        this.updateCartCount();
    }

    getCurrentUser() {
        return this.currentUser;
    }

    isLoggedIn() {
        return this.currentUser !== null;
    }
}

// =====================================================
// INITIALIZE NAVBAR WHEN DOM IS READY
// =====================================================
let navbarManager;

// Try to initialize immediately if DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        console.log(
            "%c📍 DOM Ready - Initializing NavbarManager",
            "color: #4CAF50; font-weight: bold"
        );
        navbarManager = new NavbarManager();
        window.navbarManager = navbarManager;
    });
} else {
    console.log(
        "%c📍 DOM Already Ready - Initializing NavbarManager",
        "color: #4CAF50; font-weight: bold"
    );
    navbarManager = new NavbarManager();
    window.navbarManager = navbarManager;
}

// =====================================================
// GLOBAL HELPER FUNCTIONS
// =====================================================

// Function to refresh cart count (can be called from other scripts)
window.refreshCartCount = function () {
    if (window.navbarManager) {
        window.navbarManager.refreshCartCount();
    } else {
        console.warn("[NavbarManager] navbarManager not initialized yet");
    }
};

// Function to get current logged in user
window.getCurrentUser = function () {
    if (window.navbarManager) {
        return window.navbarManager.getCurrentUser();
    }
    return null;
};

// Function to check if user is logged in
window.isUserLoggedIn = function () {
    if (window.navbarManager) {
        return window.navbarManager.isLoggedIn();
    }
    return false;
};
