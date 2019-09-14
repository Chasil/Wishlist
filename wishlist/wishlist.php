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
        add_action( 'wp_ajax_addtowishlist', array($this, 'setCookies') );
        add_action( 'wp_ajax_nopriv_addtowishlist', array($this, 'setCookie') );
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
        $itemsLimit = 1;
        $cookieData = array();

        if(isset($_COOKIE['WishList'])) {

            $cookieData = json_decode(html_entity_decode(stripslashes($_COOKIE['WishList'])), true);
            $currentItemsAmount = sizeof($cookieData);

            if($currentItemsAmount < $itemsLimit) {
                if(!in_array($productID, $cookieData)) {
                    $cookieData[] = $productID;
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
            $cookieData[] = $productID;
        }

        setcookie('WishList', json_encode($cookieData), time() + 96422400, '/', 'wishlist.pandzia.pl');

        echo json_encode(array('status' => 'added'));
        die();
    }

    public function setStatements() {
        require(dirname(__FILE__ ) . '/views/statements.php');
    }

    public function addFrontScripts() {
        wp_enqueue_script('wishlist', plugins_url('assets/js/wishlist.js', __FILE__), array('jquery'));
        wp_localize_script('wishlist', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_style('wishlist', plugins_url('assets/css/wishlist.css', __FILE__));
    }
}

new Wishlist();