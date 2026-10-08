# Project Overview & Architecture Guide: Jewellery WP E-Commerce

## 1. Executive Summary
This workspace contains a **WordPress + WooCommerce** e-commerce application tailored for a **Jewelry Store** (`jewellery_wp`). The site runs on a local PHP/MySQL stack (XAMPP on Windows) and features a custom theme with advanced WooCommerce modifications, interactive frontend modules, AJAX-driven authentication, custom taxonomies, and bespoke product tabs.

---

## 2. Technology Stack & Infrastructure

- **CMS / Core**: WordPress
- **E-Commerce Engine**: WooCommerce
- **Server Environment**: Local XAMPP (Apache + PHP + MySQL) on Windows (`c:\xampp\htdocs\wordpress`)
- **Theme Foundation**: Custom theme `jewellery_wp` (scaffolded from `_s` / Underscores)
- **Frontend Framework & Libraries**:
  - **CSS**: Bootstrap 5, Animate.css, Swiper CSS, Custom SCSS/CSS bundle (`assets/css/styles.css`)
  - **Icons**: IcoMoon font icon library
  - **JavaScript**: jQuery 3.7.1, Swiper Slider, WOW.js, LazySizes, Parallaxie, NouiSlider, Countdown.js, Bootstrap Select

---

## 3. Directory Structure

```text
wordpress/
├── .htaccess                     # Apache rewrite rules & permalink setup
├── wp-config.php                 # Database credentials, keys, debug settings
├── wp-admin/                     # WordPress Admin core
├── wp-includes/                  # WordPress Core libraries
└── wp-content/
    ├── mu-plugins/               # Must-use plugins (e.g. hostinger-auto-updates.php)
    ├── plugins/                  # Active & installed WooCommerce plugins
    ├── themes/
    │   ├── jewellery_wp/         # PRIMARY ACTIVE CUSTOM THEME
    │   │   ├── assets/           # CSS, JS, Fonts, Icons, and Images
    │   │   ├── inc/              # Theme functions, customizer, template tags
    │   │   ├── template/         # Page templates (Home, Cart, Checkout, Login, etc.)
    │   │   ├── template-parts/   # Reusable partials (inner-banner, content, etc.)
    │   │   ├── woocommerce/      # WooCommerce template overrides
    │   │   ├── functions.php     # Core theme setup, AJAX hooks, custom filters
    │   │   ├── header.php        # Global header & navigation bar
    │   │   ├── footer.php        # Global footer & newsletter subscription
    │   │   └── style.css         # Theme metadata and base styling
    │   ├── twentytwentyfive/
    │   ├── twentytwentyfour/
    │   └── twentytwentythree/
    └── uploads/                  # Media library uploads
```

---

## 4. Key Custom Theme Features (`jewellery_wp`)

### A. Custom Taxonomy & Metadata
- **Material Taxonomy (`material`)**: Custom taxonomy registered on `product` post type for categorizing items by precious metals (Gold, Silver, Platinum, Diamond, etc.).
- **ACF Pro Integration**: Custom fields used for accordion-style FAQs and dynamic shipping/return tabs on single product pages.

### B. E-Commerce Enhancements
- **Dynamic Cart Expiration & Countdown**: Auto-expiration timer logic for reserved items in user carts.
- **Direct Buy Now Flow**: Instant checkout redirect button (`custom_buy_now_button`).
- **AJAX Authentication**:
  - `custom_ajax_register`: User registration with validation and error reporting.
  - `custom_ajax_login`: Password & credential authentication without full page reload.
  - `update_profile_image`: AJAX media upload for customer profile pictures.
- **Single Product Page Customizations**:
  - Interactive product review slider.
  - Dynamic tabs for description, material specifics, FAQs, and shipping details.
  - Product gallery zoom, lightbox, and slider support.

### C. Custom Page Templates (`template/`)
- `home.php`: Hero banner swiper, featured collections, product grid carousel, promotional offers, brand trust badges, and newsletter integration.
- `cart.php`: Custom styled shopping cart with countdown and real-time total updates.
- `checkout.php`: Streamlined multi-column checkout layout.
- `login.php` & `register.php`: Custom authentication user interfaces.

---

## 5. Active Plugin Ecosystem

| Plugin | Purpose / Role |
| :--- | :--- |
| **WooCommerce** | Core shopping cart, catalog, and order handling |
| **Advanced Custom Fields Pro** | Dynamic metadata (FAQs, custom tabs, banner data) |
| **Filter Everything / Woo Ajax Filters** | Faceted filtering by material, price, categories |
| **Gallery Slider for WooCommerce** | Enhanced multi-image presentation on product details |
| **Woo Smart Quick View / Compare / Wishlist** | Interactive customer shopping tools |
| **PSM Woo Multi Currency** | Multi-currency conversions for international shoppers |
| **GTranslate** | Multi-language translation support |
| **HurryTimer** | Marketing urgency and countdown bars |
| **WooCommerce PayPal Payments** | Payment gateway integration |

---

## 6. Developer Guidelines & Working with the Codebase

1. **Custom PHP Functions**: Place custom hooks and helpers in `wp-content/themes/jewellery_wp/functions.php` or create modular files in `wp-content/themes/jewellery_wp/inc/`.
2. **Assets & Enqueuing**: All scripts and stylesheets are enqueued via `mytheme_assets()` and `jewellery_wp_scripts()` in `functions.php`. Cache-busting uses `filemtime()`.
3. **Template Hierarchy**:
   - Custom shop layouts are located in `wp-content/themes/jewellery_wp/woocommerce/`.
   - Modifying core store flows should prioritize WooCommerce hooks/filters over direct core edits.
4. **Database & Config**: Database connections are defined in `wp-config.php`. Do not commit sensitive environment credentials to version control.
