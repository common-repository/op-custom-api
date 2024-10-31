<?php
/**
 * Plugin Name:       OP Custom APi
 * Description:       OP Custom APi !
 * Version:           5.1.6
 * Author:            Maiuoc
 * Author URI:        http://magebay.com
 * Text Domain:       magebay.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/2Fwebd/feedier-wordpress
 */
/*
 * Plugin constants
 */

define( 'OCA_WP_VERSION', '5.0' );

define( 'OCA_PLUGIN', __FILE__ );


define( 'OCA_PLUGIN_DIR', untrailingslashit( dirname( OCA_PLUGIN ) ) );

define( 'OCA_PLUGIN_MODULES_DIR', OCA_PLUGIN_DIR . '/modules' );

/*
 * Main class
 */
/**
 * Class MbExport
 *
 * This class creates the option page and add the web app script
 */

class OCA_Admin
{
    private $oca_screen_name;
    /**
     * Feedier constructor.
     *
     * The main plugin actions registered for WordPress
     */
    public function __construct()
    {

    }
    public function OcaInitPlugin()
    {
        add_action('admin_menu', array($this, 'OcaPluginMenu'));
    }
    public function OcaPluginMenu()
    {
        $this->oca_screen_name = add_menu_page(
            'OP API',
            'OP API',
            'manage_options',
            'op-custom-api',
            array($this, 'OcaRenderPage')
        );
        //  echo __FILE__;
    }

    public function OcaRenderPage(){
        do_action('oca_main_menu');
    }
}

/*
 * Starts our plugin class, easy!
 */
$oca = new OCA_Admin();
$oca->OcaInitPlugin();
add_action('oca_main_menu', 'oca_main_menu_function'); 
function oca_main_menu_function () {
	// continue update 
}
function oca_rest_prepare_order_object( $response, $object, $request ) {
    // Get the value
    $order = wc_get_order( $object->get_id() );
    $items = $order->get_items();
    $productIds = array();
    $productData = array();
	// add product data to order data 
    foreach ($items  as $key =>  $item) {
		$product = wc_get_product( $item->get_product_id());
        $images = wp_get_attachment_image_src( get_post_thumbnail_id( $item->get_product_id()), 'single-post-thumbnail' );
        $image = $images[0];
        $productData[$key]['product_id'] = $item->get_product_id();
        $productData[$key]['date_created'] = $product->get_date_created();
        $productData[$key]['image'] = $image;
        $productData[$key]['link'] = get_permalink($item->get_product_id());
    }
    $response->data['op_product_data'] = $productData;

    return $response;
}
add_filter( 'woocommerce_rest_prepare_shop_order_object', 'oca_rest_prepare_order_object', 10, 3 );
