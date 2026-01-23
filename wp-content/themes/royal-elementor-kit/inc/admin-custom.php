<?php
/**
 * MarsX Admin - Material Design
 */

if (!defined('ABSPATH')) {
    exit;
}

function marsx_admin_custom_styles() {
    $logo_url = get_template_directory_uri() . '/inc/logo-marsx.avif';
    ?>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap");

        :root {
            --md-primary: #fd853a;
            --md-primary-dark: #e5742d;
            --md-surface: #ffffff;
            --md-background: #fafafa;
            --md-on-surface: #1f1f1f;
            --md-on-surface-variant: #5f6368;
            --md-outline: #dadce0;
            --md-shadow-1: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
            --md-shadow-2: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
            --md-radius: 8px;
        }

        /* Typography */
        body, #wpadminbar, .wp-core-ui, .wp-admin {
            font-family: "Noto Sans Thai", "Google Sans", Roboto, sans-serif !important;
            font-size: 13px !important;
            -webkit-font-smoothing: antialiased;
        }

        /* Background */
        #wpcontent, #wpfooter {
            background: var(--md-background) !important;
        }

        /* ===== Page Layout & Spacing ===== */
        .wrap {
            margin: 20px 24px 20px 20px !important;
        }

        .wrap > h1:first-of-type,
        .wrap > h2:first-of-type {
            font-size: 22px !important;
            font-weight: 600 !important;
            margin-bottom: 16px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        /* Subsubsub filter links */
        .subsubsub {
            margin: 8px 0 16px !important;
            font-size: 13px !important;
        }

        .subsubsub li {
            margin: 0 !important;
        }

        /* Tablenav spacing */
        .tablenav {
            margin: 12px 0 !important;
            height: auto !important;
        }

        .tablenav .actions {
            padding: 0 !important;
        }

        .tablenav select {
            height: 36px !important;
            min-height: 36px !important;
            padding: 0 24px 0 10px !important;
            font-size: 13px !important;
        }

        .tablenav .button {
            padding: 6px 14px !important;
            height: 36px !important;
            line-height: 1.4 !important;
        }

        .tablenav-pages {
            margin: 0 !important;
        }

        .tablenav-pages .pagination-links {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .tablenav-pages .pagination-links a,
        .tablenav-pages .pagination-links span {
            min-width: 32px !important;
            height: 32px !important;
            padding: 0 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 6px !important;
        }

        .tablenav-pages .current-page {
            width: 50px !important;
            height: 32px !important;
            text-align: center !important;
            padding: 0 8px !important;
        }

        /* Search box */
        .search-box {
            margin: 0 0 12px !important;
        }

        .search-box input[type="search"] {
            height: 36px !important;
            padding: 6px 12px !important;
            width: 200px !important;
        }

        .search-box .button {
            height: 36px !important;
            padding: 6px 14px !important;
        }

        /* ===== Admin Bar - Material Elevated ===== */
        #wpadminbar {
            background: var(--md-surface) !important;
            box-shadow: var(--md-shadow-1) !important;
        }

        #wpadminbar .ab-item,
        #wpadminbar a.ab-item,
        #wpadminbar > #wp-toolbar span.ab-label {
            color: var(--md-on-surface) !important;
        }

        #wpadminbar .ab-icon:before,
        #wpadminbar .ab-item:before {
            color: var(--md-on-surface-variant) !important;
        }

        #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
            content: "" !important;
            background: url('<?php echo esc_url($logo_url); ?>') center/contain no-repeat !important;
            width: 20px !important;
            height: 20px !important;
        }

        #wpadminbar .ab-top-menu > li:hover > .ab-item,
        #wpadminbar .ab-top-menu > li.hover > .ab-item {
            background: rgba(0,0,0,.04) !important;
        }

        #wpadminbar .quicklinks .ab-sub-wrapper {
            background: var(--md-surface) !important;
            border: none !important;
            border-radius: var(--md-radius) !important;
            box-shadow: var(--md-shadow-2) !important;
            margin-top: 0 !important;
        }

        #wpadminbar .ab-submenu .ab-item {
            color: var(--md-on-surface) !important;
        }

        #wpadminbar .ab-submenu .ab-item:hover {
            background: rgba(0,0,0,.04) !important;
        }

        /* ===== Sidebar - Clean & Professional ===== */
        #adminmenuback, #adminmenuwrap, #adminmenu {
            background: #1e1e1e !important;
        }

        #adminmenuwrap {
            box-shadow: none !important;
        }

        #adminmenu a {
            color: rgba(255,255,255,.85) !important;
        }

        #adminmenu div.wp-menu-image:before {
            color: rgba(255,255,255,.6) !important;
        }

        #adminmenu li.menu-top:hover > a,
        #adminmenu li.opensub > a.menu-top {
            background: rgba(255,255,255,.08) !important;
            color: #fff !important;
        }

        #adminmenu li.menu-top:hover div.wp-menu-image:before {
            color: rgba(255,255,255,.85) !important;
        }

        /* Active - Subtle indicator */
        #adminmenu li.current a.menu-top,
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
            background: rgba(255,255,255,.12) !important;
            color: #fff !important;
            border-left: 3px solid var(--md-primary) !important;
        }

        #adminmenu li.wp-has-current-submenu div.wp-menu-image:before,
        #adminmenu li.current div.wp-menu-image:before {
            color: #fff !important;
        }

        #adminmenu .wp-submenu {
            background: #2d2d2d !important;
        }

        #adminmenu .wp-submenu a {
            color: rgba(255,255,255,.7) !important;
        }

        #adminmenu .wp-submenu a:hover {
            color: #fff !important;
            background: transparent !important;
        }

        #adminmenu .wp-submenu li.current a {
            color: var(--md-primary) !important;
            background: transparent !important;
        }

        #adminmenu li.wp-menu-separator {
            background: rgba(255,255,255,.1) !important;
            margin: 8px 12px !important;
        }

        #collapse-button {
            color: rgba(255,255,255,.6) !important;
        }

        #collapse-button:hover {
            color: #fff !important;
        }

        /* ===== Buttons - Material Filled ===== */
        .wp-core-ui .button,
        .wp-core-ui .button-secondary {
            background: var(--md-surface) !important;
            border: 1px solid var(--md-outline) !important;
            border-radius: 6px !important;
            color: var(--md-on-surface) !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding: 6px 14px !important;
            height: auto !important;
            min-height: 32px !important;
            line-height: 1.4 !important;
            box-shadow: none !important;
            transition: all .15s ease !important;
        }

        .wp-core-ui .button:hover,
        .wp-core-ui .button-secondary:hover {
            background: #f8f9fa !important;
            border-color: #c4c7c9 !important;
        }

        .wp-core-ui .button-primary {
            background: var(--md-primary) !important;
            border: none !important;
            border-radius: 6px !important;
            color: #fff !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding: 6px 16px !important;
            min-height: 32px !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        .wp-core-ui .button-primary:hover {
            background: var(--md-primary-dark) !important;
        }

        .page-title-action,
        a.page-title-action,
        .wrap .page-title-action,
        .subsubsub + .page-title-action {
            background: var(--md-primary) !important;
            border: none !important;
            border-radius: 6px !important;
            color: #fff !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding: 6px 14px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            height: 32px !important;
        }

        .page-title-action:hover,
        a.page-title-action:hover {
            background: var(--md-primary-dark) !important;
            color: #fff !important;
        }

        /* WooCommerce specific buttons */
        .wrap.woocommerce a.page-title-action,
        .wrap a.page-title-action,
        .wp-header-end + a.page-title-action,
        h1.wp-heading-inline + a.page-title-action,
        .woocommerce .page-title-action {
            background: var(--md-primary) !important;
            border: none !important;
            border-radius: 6px !important;
            color: #fff !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding: 6px 14px !important;
            text-decoration: none !important;
        }

        /* Force all page-title-action to white text */
        .page-title-action,
        .page-title-action:visited,
        .page-title-action:active,
        a.page-title-action,
        a.page-title-action:visited,
        a.page-title-action:active,
        #wpbody .page-title-action,
        #wpbody a.page-title-action,
        .wrap .page-title-action,
        .wrap a.page-title-action {
            color: #fff !important;
            background: var(--md-primary) !important;
        }

        #wpbody .page-title-action:hover,
        #wpbody a.page-title-action:hover {
            color: #fff !important;
            background: var(--md-primary-dark) !important;
        }

        /* ===== Cards - Material Surface ===== */
        .postbox {
            background: var(--md-surface) !important;
            border: none !important;
            border-radius: var(--md-radius) !important;
            box-shadow: var(--md-shadow-1) !important;
            margin-bottom: 16px !important;
        }

        .postbox .hndle {
            border-bottom: 1px solid var(--md-outline) !important;
            padding: 12px 14px !important;
        }

        .postbox .hndle span {
            font-weight: 600 !important;
            font-size: 13px !important;
            color: var(--md-on-surface) !important;
        }

        .postbox .inside {
            padding: 14px !important;
            font-size: 13px !important;
        }

        .postbox-header {
            border-radius: var(--md-radius) var(--md-radius) 0 0 !important;
        }

        /* Dashboard specific */
        #dashboard-widgets .postbox {
            margin-bottom: 16px !important;
        }

        #dashboard-widgets .postbox .inside {
            padding: 14px !important;
        }

        /* ===== Forms - Material Outlined ===== */
        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="number"],
        input[type="search"],
        input[type="url"],
        input[type="tel"],
        textarea,
        select {
            background: var(--md-surface) !important;
            border: 1px solid var(--md-outline) !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            color: var(--md-on-surface) !important;
            transition: border-color .15s, box-shadow .15s !important;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="search"]:focus,
        input[type="url"]:focus,
        input[type="tel"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--md-primary) !important;
            box-shadow: 0 0 0 2px rgba(253, 133, 58, 0.15) !important;
            outline: none !important;
        }

        input[type="checkbox"],
        input[type="radio"] {
            width: 16px !important;
            height: 16px !important;
            margin: 0 !important;
        }

        input[type="checkbox"]:checked,
        input[type="radio"]:checked {
            background: var(--md-primary) !important;
            border-color: var(--md-primary) !important;
        }

        /* ===== Tables - Material Data Table ===== */
        .wp-list-table {
            background: var(--md-surface) !important;
            border: none !important;
            border-radius: var(--md-radius) !important;
            box-shadow: var(--md-shadow-1) !important;
            overflow: hidden !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .wp-list-table thead th {
            background: #f8f9fa !important;
            border-bottom: 1px solid var(--md-outline) !important;
            color: var(--md-on-surface-variant) !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            padding: 12px 10px !important;
        }

        .wp-list-table thead th a {
            color: var(--md-on-surface-variant) !important;
        }

        .wp-list-table tbody tr {
            background: var(--md-surface) !important;
        }

        .wp-list-table tbody tr:hover {
            background: #f8f9fa !important;
        }

        .wp-list-table tbody td {
            border: none !important;
            border-bottom: 1px solid var(--md-outline) !important;
            padding: 12px 10px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .wp-list-table tbody td.column-title,
        .wp-list-table tbody td.column-name {
            font-weight: 500 !important;
        }

        .wp-list-table .column-cb,
        .wp-list-table .check-column {
            width: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
            padding: 8px !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        .wp-list-table th.check-column,
        .wp-list-table td.check-column {
            padding: 8px !important;
        }

        .wp-list-table .check-column input[type="checkbox"],
        .wp-list-table th input[type="checkbox"],
        .wp-list-table td input[type="checkbox"] {
            margin: 0 !important;
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            border: 1.5px solid #bdc1c6 !important;
            border-radius: 3px !important;
            cursor: pointer !important;
            vertical-align: middle !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            background: var(--md-surface) !important;
            transition: all 0.15s ease !important;
        }

        .wp-list-table .check-column input[type="checkbox"]:hover {
            border-color: var(--md-primary) !important;
        }

        .wp-list-table .check-column input[type="checkbox"]:checked {
            background: var(--md-primary) !important;
            border-color: var(--md-primary) !important;
        }

        .wp-list-table .check-column input[type="checkbox"]:checked::before {
            content: "" !important;
            display: block !important;
            width: 16px !important;
            height: 16px !important;
            background: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e") center/10px no-repeat !important;
            margin: 0 !important;
            float: none !important;
        }

        /* Row actions */
        .wp-list-table .row-actions {
            font-size: 12px !important;
            line-height: 1.6 !important;
            padding: 4px 0 0 !important;
        }

        .wp-list-table .row-actions span {
            padding-right: 8px !important;
        }

        /* Thumbnail column */
        .wp-list-table .column-thumb img,
        .wp-list-table .column-image img,
        .wp-list-table .column-thumbnail img {
            border-radius: 4px !important;
            max-width: 48px !important;
            height: auto !important;
        }

        /* ===== Notices - Material Snackbar style ===== */
        .notice {
            background: var(--md-surface) !important;
            border: none !important;
            border-radius: 6px !important;
            box-shadow: var(--md-shadow-1) !important;
            padding: 12px 14px !important;
            margin: 12px 0 !important;
            border-left: 4px solid !important;
            font-size: 13px !important;
        }

        .notice p {
            margin: 0 !important;
            padding: 0 !important;
        }

        .notice-success, .updated {
            border-left-color: #34a853 !important;
        }

        .notice-error, .error {
            border-left-color: #ea4335 !important;
        }

        .notice-warning {
            border-left-color: #fbbc04 !important;
        }

        .notice-info {
            border-left-color: #4285f4 !important;
        }

        /* ===== Tabs - Material Tab ===== */
        .nav-tab-wrapper {
            border-bottom: 1px solid var(--md-outline) !important;
            margin-bottom: 16px !important;
            padding: 0 !important;
        }

        .nav-tab {
            background: transparent !important;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            color: var(--md-on-surface-variant) !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding: 10px 14px !important;
            margin: 0 4px 0 0 !important;
            transition: all .15s !important;
        }

        .nav-tab:hover {
            background: rgba(0,0,0,.04) !important;
            color: var(--md-on-surface) !important;
        }

        .nav-tab-active {
            border-bottom-color: var(--md-primary) !important;
            color: var(--md-on-surface) !important;
        }

        /* ===== Links ===== */
        #wpbody a {
            color: #1a73e8 !important;
        }

        #wpbody a:hover {
            color: #1557b0 !important;
        }

        /* ===== Footer ===== */
        #wpfooter {
            border-top: 1px solid var(--md-outline) !important;
            padding: 12px 20px !important;
        }

        #wpfooter #footer-left,
        #wpfooter #footer-thankyou {
            font-size: 0 !important;
        }

        #wpfooter #footer-left::after,
        #wpfooter #footer-thankyou::after {
            content: "MARSX THINGS CO., LTD." !important;
            font-size: 12px !important;
            color: var(--md-on-surface-variant) !important;
        }

        /* ===== Login Page ===== */
        body.login {
            background: var(--md-background) !important;
        }

        body.login #login h1 a {
            background: url('<?php echo esc_url($logo_url); ?>') center/contain no-repeat !important;
            width: 160px !important;
            height: 60px !important;
        }

        body.login form {
            background: var(--md-surface) !important;
            border: none !important;
            border-radius: var(--md-radius) !important;
            box-shadow: var(--md-shadow-2) !important;
            padding: 24px !important;
        }

        body.login input[type="text"],
        body.login input[type="password"] {
            border-radius: 4px !important;
            padding: 14px 16px !important;
        }

        body.login .button-primary {
            background: var(--md-primary) !important;
            border: none !important;
            border-radius: 20px !important;
            padding: 10px 24px !important;
            font-weight: 500 !important;
            text-shadow: none !important;
            box-shadow: var(--md-shadow-1) !important;
        }

        body.login .button-primary:hover {
            background: var(--md-primary-dark) !important;
            box-shadow: var(--md-shadow-2) !important;
        }

        body.login input:focus {
            border-color: var(--md-primary) !important;
            box-shadow: 0 0 0 1px var(--md-primary) !important;
        }

        body.login #backtoblog a,
        body.login #nav a {
            color: var(--md-on-surface-variant) !important;
        }

        body.login #backtoblog a:hover,
        body.login #nav a:hover {
            color: var(--md-primary) !important;
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--md-outline);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--md-on-surface-variant);
        }

        /* ===== Mobile Responsive - Minimal ===== */
        @media screen and (max-width: 782px) {
            /* Larger touch targets */
            .wp-core-ui .button,
            .wp-core-ui .button-secondary,
            .wp-core-ui .button-primary {
                min-height: 44px !important;
                padding: 10px 16px !important;
                font-size: 14px !important;
            }

            /* Prevent zoom on input focus (iOS) */
            input[type="text"],
            input[type="password"],
            input[type="email"],
            input[type="number"],
            input[type="search"],
            input[type="url"],
            input[type="tel"],
            textarea,
            select {
                font-size: 16px !important;
            }

            /* Better checkbox size */
            .wp-list-table .check-column input[type="checkbox"] {
                width: 20px !important;
                height: 20px !important;
            }
        }
    </style>
    <?php
}
add_action('admin_head', 'marsx_admin_custom_styles');
add_action('login_head', 'marsx_admin_custom_styles');
