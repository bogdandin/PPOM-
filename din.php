<?php

/*
Plugin Name: Dinsoftware web
Plugin URI: https://premiumwp.ro
Description: Plugin pentru crostopizza 😋.
Author: Din Bogdan
Version: 1.0
Author URI: https://www.linkedin.com/in/dinbogdan/
*/


add_action('wp_footer', 'add_this_script_footer');


function add_this_script_footer(){
    if ( is_product() ) {
 ?>

        <style>

        </style>

        <script type="text/javascript">
            //<![CDATA[

            jQuery(document).ready(function () {


                jQuery('.variations_form.cart').submit(function() {
                    // hide body
                    var produse = [];

                    jQuery(".ppom-field-wrapper input:checked").each(function(){


                        var product = jQuery(this).val();
                        produse.push(product);

                        console.log( "Produs: " + product );


                      //  jQuery('#ppom-box-1').remove();



                    });

                    jQuery.ajax({
                        type: "POST",
                        url: "/",
                        data: { numeproduse: produse}
                    }).done(function( msg ) {
                        console.log( "Data Saved:" + msg);
                    });


                    return true; // return false to cancel form action

                });



            });


            //]]>
        </script>

<?php
   }// end if is product
    } // end add_this_script_footer



// simple to follow
add_action('wp_loaded', 'process_post');
function process_post(){

    if ( null === WC()->customer ) {
        WC()->customer = new WC_Customer( get_current_user_id(), true );
    }
    if ( null === WC()->cart ) {
        WC()->cart = new WC_Cart();
    }



    if(isset($_POST['numeproduse'])) {





        $numeproduse = $_POST['numeproduse'];

        print_r($numeproduse);



        foreach($numeproduse as $numeprodus){


            echo $numeprodus;

            $re = '/ - (.*?)lei/ms';
            $numeprodus = preg_replace($re, "", $numeprodus);



           // $numeprodus = ucwords(strtolower($numeprodus));


            $produs = get_page_by_title( $numeprodus , OBJECT, 'product' );

       // print_r($produs->ID);


        if($produs->ID){


            $product_ids =  "," . $product_ids;
             echo "---" . WC()->cart->add_to_cart( $produs->ID);


        }





    }
    die();
}

}

