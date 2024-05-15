<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

defined( 'ABSPATH' ) || exit;

Container::make( 'theme_options', __( 'Slider', 'nast-core' ) )
    ->set_page_parent( 'woocommerce' )
    ->add_fields( array(
        Field::make( 'complex', 'crb_slider', __( 'Slider' ) )
            ->set_header_template( '
                    <% if (url) { %>
                        URL: <%- url %>
                    <% } %>
                ' )
            ->add_fields( array(
                Field::make( 'text', 'url', __( 'URL' ) ),
                Field::make( 'image', 'slider-image-desktop', __( 'Obrázok (Desktop)' ) )
                    ->set_value_type( 'url' ),
                Field::make( 'image', 'slider-image-mobile', __( 'Obrázok (Mobile)' ) )
                    ->set_value_type( 'url' ),
            ) ),

    ) );