<?php

/*
Plugin Name: Dinsoftware web
Plugin URI: https://premiumwp.ro
Description: Plugin pentru crostopizza 😋.
Author: Din Bogdan
Version: 1.0
Author URI: https://www.linkedin.com/in/dinbogdan/
*/

add_filter( 'woocommerce_cart_item_quantity', 'wc_cart_item_quantity', 10, 3 );
function wc_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ){
    if( is_cart() ){
        $product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
    }
    return $product_quantity;
}


add_action('wp_footer', 'add_this_script_footer');


function add_this_script_footer(){
    if ( is_product() || is_cart()) {



 ?>

        <style>
            .input-text.qty {display:none;}
            .product-quantity {text-align:center;}

        </style>

        <script type="text/javascript">
            //<![CDATA[

            jQuery(document).ready(function () {


                jQuery('.variations_form.cart').submit(function() {
                    // hide body
                    var produse = [];

                    jQuery(".ppom-field-wrapper input:checked").each(function(){


                        var product = jQuery(this).val() +"### <?php echo get_the_title(); ?> "+ jQuery( "#pa_dimensiune option:selected" ).text() +"###"+ jQuery(this).attr('data-price');
                        produse.push(product);

                        console.log( "Produs: " + product );


                      //  jQuery('#ppom-box-1').remove();



                    });

                    jQuery.ajax({
                        type: "POST",
                        url: "/",
                        data: { numeproduse: produse}
                    }).done(function( msg ) {
                        var redirect = 1;
                        console.log( "Data Saved:" + msg);

                        jQuery("#ppom-box-1").remove();
                    });


                    var currentTime = new Date().getTime();
                    while (currentTime + 1200 >= new Date().getTime()) {
                    }
                    jQuery("#ppom-box-1").remove();

                    return true;


                });



            });


            //]]>
        </script>

<?php
   }// end if is product
    } // end add_this_script_footer




function return_id_product($titlu,$pret,$nr){


    $titlu = '#' . $nr . ' ' . $titlu;



        $produs = get_page_by_title($titlu, OBJECT, 'product');


        if(!empty($produs->ID) && $produs->ID > 1){

            return $produs->ID;

        }




        $post_id = wp_insert_post(array(
            'post_title' => $titlu,
            'post_type' => 'product',
            'post_staus' => 'publish',
            'post_author' => '1',
        ));
        //   wp_publish_post( $post_id );

        update_post_meta( $post_id, '_visibility', 'hidden' );
        $product = wc_get_product($post_id);
        $product->set_catalog_visibility('hidden');
        $product->set_status('publish');
        $product->save();

        update_post_meta( $post_id, '_regular_price', $pret );
        update_post_meta( $post_id, '_price', $pret );


       // WC()->cart->add_to_cart($post_id);
        return $post_id;

}



// simple to follow
add_action('template_redirect', 'process_post');
function process_post(){

//    if ( null === WC()->customer ) {
//        WC()->customer = new WC_Customer( get_current_user_id(), true );
//    }
//    if ( null === WC()->cart ) {
//        WC()->cart = new WC_Cart();
//    }
    //WC()->session->set('numeproduse', 'fffffffffffffffffffffffffff');
   // print_r(WC()->session->get('numeproduse'));

    if ( isset(WC()->session) && ! WC()->session->has_session() ) {
        WC()->session->set_customer_session_cookie( true );
    }

        if (isset($_POST['numeproduse'])) {


            $numeproduse = $_POST['numeproduse'];

            WC()->session->set('numeproduse', $numeproduse);

           if(WC()->session->get('comanda_nr')){
               $nr = WC()->session->get('comanda_nr');
               $nr++;
               WC()->session->set('comanda_nr', $nr);
           }else{

               WC()->session->set('comanda_nr', 1);
           }

            die('am adaugat in sesiune');

        } elseif (WC()->session->get('numeproduse')) {


            $numeproduse = WC()->session->get('numeproduse');

            WC()->session->__unset('numeproduse');

            if(WC()->session->get('comanda_nr')){
                $nr = WC()->session->get('comanda_nr');

            }else{

                WC()->session->set('comanda_nr', 1);
                $nr = 1;
            }


            foreach ($numeproduse as $numeprodus) {


               // $re = '/ - (.*?)lei/ms';
                // $numeprodus = preg_replace($re, "", $numeprodus);
                $info_produs = explode('###',$numeprodus);
                $titlu = $info_produs[0] .' - '. $info_produs[1];
                $pret = $info_produs[2];

                WC()->cart->add_to_cart(return_id_product($titlu,$pret,$nr));


            }

        } else {




        }


}



