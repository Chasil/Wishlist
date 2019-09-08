<?php
/*
Plugin Name: Wishlist
Plugin URI:  wishlist.pandzia.pl
Description: WooCommerce Wishlist
Version:     1.0
Author:      Mateusz Wójcik
Author URI:  https://linkedin.com/in/mateusz-wojcik-601445107/
License:     https://linkedin.com/in/mateusz-wojcik-601445107/
License URI: https://linkedin.com/in/mateusz-wojcik-601445107/
Text Domain: wporg
Domain Path: /languages
*/

class Wishlist {

    public function __construct() {
        $this->registerHooks();
    }

    public function registerHooks() {
        add_action( 'wp_enqueue_scripts', array($this, 'frontScripts') );
        add_filter( 'wp_nav_menu', array($this, 'hookMenuWishlist') );
        add_action( 'woocommerce_before_single_product_summary', array($this, 'hookAddToWishlist') );
        add_action( 'wp_ajax_addtowishlist', array($this, 'addToWishlist') );
        add_action( 'wp_ajax_nopriv_addtowishlist', array($this, 'addToWishlist') );
    }

    /*
     * Add icon to menu
     */
    public function hookMenuWishlist($nav_menu) {
        $nav_menu .= '<div id="wishlist-icon" class="wishlist-icon"></div>';
        return $nav_menu;
    }

    /*
     * Add button to product
     */
    public function hookAddToWishlist() {
        global $product;
        $productID = $product->get_id();
        echo '<div class="add-to-wishlist" data-id="'. $productID .'"><span>Dodaj do schowka</span></div>';
    }

    /*
     * Add product to wishlist
     *
     * @param int - product ID
     */
    public function addToWishlist($productId) {
        $productId = $_POST['productId'];
    }

    public function frontScripts() {
        wp_enqueue_script('wishlist', plugins_url('assets/js/wishlist.js', __FILE__), array('jquery'));
        wp_localize_script('wishlist', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_style('wishlist', plugins_url('assets/css/wishlist.css', __FILE__));

    }
}

new Wishlist();