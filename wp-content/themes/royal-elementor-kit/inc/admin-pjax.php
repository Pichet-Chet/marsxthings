<?php
/**
 * MarsX Admin PJAX - Ajax page navigation
 */

if (!defined('ABSPATH')) {
    exit;
}

function marsx_admin_pjax_scripts() {
    ?>
    <script>
    (function() {
        'use strict';

        console.log('[PJAX] Initializing...');

        const PJAX = {
            loading: false,
            excludePatterns: [
                /logout/,
                /customize\.php/,
                /theme-editor/,
                /plugin-editor/,
                /update-core/,
                /update\.php/,
                /plugins\.php\?action/,
                /admin-post\.php/,
                /post-new\.php/,
                /post\.php\?post=\d+&action=edit/,
                /media-new\.php/,
                /upload\.php\?item/,
                // WooCommerce pages with heavy JS
                /wc-admin/,
                /wc-settings/,
                /admin\.php\?page=wc-/,
                /page=woocommerce/,
                // Elementor
                /elementor/,
                // Other JS-heavy pages
                /options-general/,
                /options-writing/,
                /options-reading/,
                /options-discussion/,
                /options-media/,
                /options-permalink/
            ],

            init() {
                console.log('[PJAX] Init called');
                this.injectStyles();
                this.bindClicks();
                this.bindPopState();
                console.log('[PJAX] Ready!');
            },

            injectStyles() {
                const style = document.createElement('style');
                style.textContent = `
                    /* PJAX Loading Bar */
                    .pjax-progress {
                        position: fixed;
                        top: 32px;
                        left: 160px;
                        right: 0;
                        height: 3px;
                        background: #fd853a;
                        z-index: 999999;
                        transform: scaleX(0);
                        transform-origin: left;
                        transition: transform 0.3s ease;
                    }
                    .pjax-progress.loading {
                        transform: scaleX(0.7);
                        transition: transform 8s cubic-bezier(0.1, 0.5, 0.1, 1);
                    }
                    .pjax-progress.done {
                        transform: scaleX(1);
                        transition: transform 0.2s ease;
                    }
                    .pjax-progress.hide {
                        opacity: 0;
                        transition: opacity 0.3s ease;
                    }

                    /* Content animation */
                    #wpbody-content {
                        transition: opacity 0.25s ease, transform 0.25s ease;
                    }
                    #wpbody-content.fade-out {
                        opacity: 0;
                        transform: translateY(-15px);
                    }
                    #wpbody-content.fade-in {
                        opacity: 0;
                        transform: translateY(15px);
                    }

                    /* Collapsed sidebar */
                    .folded .pjax-progress {
                        left: 36px;
                    }
                `;
                document.head.appendChild(style);

                // Create progress bar
                const progress = document.createElement('div');
                progress.className = 'pjax-progress';
                document.body.appendChild(progress);
            },

            shouldHandle(link) {
                const href = link.getAttribute('href');

                // Basic checks
                if (!href) return false;
                if (href.startsWith('#')) return false;
                if (href.startsWith('javascript:')) return false;
                if (link.target === '_blank') return false;
                if (link.hasAttribute('download')) return false;

                // Check if it's an admin link
                const fullUrl = new URL(href, window.location.origin);
                if (!fullUrl.pathname.includes('wp-admin')) return false;

                // Check exclude patterns
                for (const pattern of this.excludePatterns) {
                    if (pattern.test(href)) {
                        console.log('[PJAX] Excluded:', href);
                        return false;
                    }
                }

                // External links
                if (fullUrl.origin !== window.location.origin) return false;

                return true;
            },

            bindClicks() {
                document.addEventListener('click', (e) => {
                    // Find closest link
                    const link = e.target.closest('a');
                    if (!link) return;

                    // Check if we should handle this
                    if (!this.shouldHandle(link)) return;
                    if (this.loading) return;

                    const href = link.getAttribute('href');
                    console.log('[PJAX] Handling click:', href);

                    e.preventDefault();
                    e.stopPropagation();

                    this.navigate(href);
                }, true); // Use capture phase
            },

            bindPopState() {
                window.addEventListener('popstate', () => {
                    console.log('[PJAX] Popstate:', window.location.href);
                    this.navigate(window.location.href, false);
                });
            },

            async navigate(url, pushState = true) {
                if (this.loading) return;
                this.loading = true;

                console.log('[PJAX] Navigating to:', url);

                const progress = document.querySelector('.pjax-progress');
                const content = document.querySelector('#wpbody-content');

                // Start loading animation
                progress.classList.remove('done', 'hide');
                progress.classList.add('loading');

                // Fade out current content
                if (content) {
                    content.classList.add('fade-out');
                    await this.wait(250);
                }

                try {
                    // Fetch new page
                    const response = await fetch(url, {
                        credentials: 'same-origin'
                    });

                    if (!response.ok) throw new Error('Fetch failed');

                    const html = await response.text();

                    // Parse response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#wpbody-content');

                    if (!newContent || !content) {
                        throw new Error('Content not found');
                    }

                    // Swap content
                    content.innerHTML = newContent.innerHTML;
                    content.classList.remove('fade-out');
                    content.classList.add('fade-in');

                    // Trigger reflow
                    void content.offsetHeight;

                    // Fade in
                    content.classList.remove('fade-in');

                    // Update title
                    const title = doc.querySelector('title');
                    if (title) document.title = title.textContent;

                    // Update URL
                    if (pushState) {
                        history.pushState({ pjax: true }, '', url);
                    }

                    // Update active menu
                    this.updateMenu(url);

                    // Update body classes
                    this.updateBodyClasses(doc);

                    // Execute scripts
                    this.runScripts(content);

                    // Scroll to top smoothly
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // Complete progress
                    progress.classList.remove('loading');
                    progress.classList.add('done');
                    await this.wait(200);
                    progress.classList.add('hide');

                    console.log('[PJAX] Navigation complete');

                } catch (err) {
                    console.error('[PJAX] Error:', err);
                    // Fallback to normal navigation
                    window.location.href = url;
                } finally {
                    this.loading = false;
                }
            },

            updateMenu(url) {
                const fullUrl = new URL(url, window.location.origin);

                // Remove all current states
                document.querySelectorAll('#adminmenu .current').forEach(el => {
                    el.classList.remove('current');
                });
                document.querySelectorAll('#adminmenu .wp-has-current-submenu').forEach(el => {
                    el.classList.remove('wp-has-current-submenu', 'wp-menu-open');
                    el.classList.add('wp-not-current-submenu');
                });

                // Find matching menu item
                let bestMatch = null;
                let bestLength = 0;

                document.querySelectorAll('#adminmenu a[href]').forEach(link => {
                    const linkUrl = new URL(link.href, window.location.origin);

                    // Check if this link matches our URL
                    if (fullUrl.pathname === linkUrl.pathname) {
                        const matchLength = linkUrl.search.length;
                        if (!bestMatch || matchLength > bestLength) {
                            bestMatch = link;
                            bestLength = matchLength;
                        }
                    }
                });

                if (bestMatch) {
                    const li = bestMatch.closest('li');
                    if (li) {
                        li.classList.add('current');

                        // Activate parent menu if in submenu
                        const parentLi = li.closest('ul.wp-submenu')?.closest('li.menu-top');
                        if (parentLi) {
                            parentLi.classList.remove('wp-not-current-submenu');
                            parentLi.classList.add('wp-has-current-submenu', 'wp-menu-open');
                        }
                    }
                }
            },

            updateBodyClasses(doc) {
                const patterns = /\b(post-php|edit-php|upload-php|edit-tags-php|users-php|plugins-php|themes-php|tools-php|options-[\w-]+|toplevel_page_[\w-]+|admin_page_[\w-]+|[\w-]+-php)\b/g;

                // Remove old classes
                const oldClasses = document.body.className.match(patterns) || [];
                oldClasses.forEach(c => document.body.classList.remove(c));

                // Add new classes
                const newClasses = doc.body.className.match(patterns) || [];
                newClasses.forEach(c => document.body.classList.add(c));
            },

            runScripts(container) {
                container.querySelectorAll('script').forEach(oldScript => {
                    if (oldScript.src) return; // Skip external scripts

                    const script = document.createElement('script');
                    script.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(script, oldScript);
                });

                // Trigger jQuery events for plugins
                if (typeof jQuery !== 'undefined') {
                    jQuery(document).trigger('pjax:end');
                }
            },

            wait(ms) {
                return new Promise(r => setTimeout(r, ms));
            }
        };

        // Start
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => PJAX.init());
        } else {
            PJAX.init();
        }
    })();
    </script>
    <?php
}
add_action('admin_footer', 'marsx_admin_pjax_scripts', 999);
