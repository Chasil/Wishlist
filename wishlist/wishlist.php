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


/*
 * POMYSŁY:
 *
 * panel: ustawienie limitu na stronie
 * paginacja
 */
class Wishlist {

    public function __construct() {
        $this->registerHooks();
    }

    public function registerHooks() {
        add_action( 'wp_enqueue_scripts', array($this, 'addFrontScripts') );
        add_filter( 'wp_nav_menu', array($this, 'createMenuIcon') );
        add_action( 'woocommerce_before_single_product_summary', array($this, 'createProductWishlistButton') );
        add_action( 'woocommerce_after_single_product', array($this, 'setStatements') );
        add_action( 'wp_ajax_addtowishlist', array($this, 'setCookie') );
        add_action( 'wp_ajax_nopriv_addtowishlist', array($this, 'setCookie') );
        add_action( 'wp_ajax_removefromwishlist', array($this, 'removeFromWishlist') );
        add_action( 'wp_ajax_nopriv_removefromwishlist', array($this, 'removeFromWishlist') );
        add_shortcode( 'wishlist', array($this, 'displayWishlist') );
    }

    public function createMenuIcon($nav_menu) {
        $nav_menu .= '<a href="/wishlist"><span id="wishlist-icon" class="wishlist-icon"></span></a>';
        return $nav_menu;
    }

    public function createProductWishlistButton() {
        global $product;
        $productID = $product->get_id();
        echo '<div class="add-to-wishlist" data-id="'. $productID .'"><span>Dodaj do schowka</span></div>';
    }

    public function setCookie() {

        $productID = $_POST['productId'];
        $itemsLimit = 4;
        $cookieProducts = array();

        if(isset($_COOKIE['WishList'])) {

            $cookieProducts = json_decode(html_entity_decode(stripslashes($_COOKIE['WishList'])), true);
            $currentItemsAmount = sizeof($cookieProducts);

            if($currentItemsAmount < $itemsLimit) {
                if(!in_array($productID, $cookieProducts)) {
                    $cookieProducts[$productID] = $productID;
                } else {
                    echo json_encode(
                        array(
                            'status' => 'alreadyExists'
                        )
                    );
                    die();
                }
            } else {
                echo json_encode(
                    array(
                        'status' => 'limitReached',
                        'itemsLimit' => $itemsLimit
                    ));
                die();
            }
        } else {
            $cookieProducts[$productID] = $productID;
        }

        setcookie('WishList', json_encode($cookieProducts), time() + 96422400, '/', 'wishlist.pandzia.pl');

        echo json_encode(array('status' => 'added'));
        die();
    }

    public function setStatements() {
        require(dirname(__FILE__ ) . '/views/statements.php');
    }

    public function displayWishlist() {

        if(isset($_COOKIE['WishList'])) {
            $cookieProducts = json_decode(html_entity_decode(stripslashes($_COOKIE['WishList'])), true);

            $products = array();
            foreach($cookieProducts as $product => $productId) {
                $product = wc_get_product($productId);
                $products[] = array(
                    'id' => $product->get_id(),
                    'name' => $product->get_name(),
                    'image' => get_the_post_thumbnail_url($productId, 'medium'),
                    'link' => get_permalink($productId)
                );
            }
        }

        require(dirname(__FILE__) . '/views/wishlist.php');
    }

    public function removeFromWishlist() {

        $productId = $_POST['productId'];

        if(isset($_COOKIE['WishList'])) {
            $cookieProducts = json_decode(html_entity_decode(stripslashes($_COOKIE['WishList'])), true);

            if(in_array($productId, $cookieProducts)) {
                unset($cookieProducts[$productId]);
            }
        }

        setcookie('WishList', json_encode($cookieProducts), time() + 96422400, '/', 'wishlist.pandzia.pl');
    }

    public function addFrontScripts() {
        wp_enqueue_script('wishlist', plugins_url('assets/js/wishlist.js', __FILE__), array('jquery'));
        wp_localize_script('wishlist', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_style('wishlist', plugins_url('assets/css/wishlist.css', __FILE__));
    }
}

new Wishlist();