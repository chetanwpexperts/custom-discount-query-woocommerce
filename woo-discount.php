<?php 

/**
* Date:100821
* description: YOu can set custom meta value for woocommerce product discount to calculate the 
  regular price and sale price. After calculate save it into post meta with key and value
*/
function check_values($post_ID, $post_after, $post_before)
{
    $product = new WC_Product_Variable( $post_ID );
    $variations = $product->get_available_variations();
    delete_post_meta($post_ID, '_variation_discount');
    foreach( $variations as $value )
    {
        $prd_discount = array();
        $sale_price = 0;
        $regular_price = $value['display_regular_price'];
        $sale_price = $value['display_price'];
        $percent = (($regular_price -$sale_price)*100)/$regular_price ;
        $p = round($percent);
        $prd_discount[$value['variation_id']] = $p;
        if(isset($sale_price))
        { 
            add_post_meta(  $post_ID, '_variation_discount', $p ); 
        }
    }
}
add_action( 'post_updated', 'check_values', 10, 3 );

/**
* Date:100821
* description: update woocommerce query with your custom parameter 
*/
function so_27971630_product_query( $q ) 
{
    $meta_query = $q->get( 'meta_query' );
    if ( get_option( 'woocommerce_hide_out_of_stock_items' ) == 'no' ) 
    {
        $meta_query[] = array( 'key' => '_variation_discount', 'compare' => '>=', 'value' => $_GET['discount'] );
        $q->set( 'meta_query', $meta_query );
    }
}
add_action( 'woocommerce_product_query', 'so_27971630_product_query' );
