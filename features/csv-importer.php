<?php
namespace NAST_CORE\Features;
defined( 'ABSPATH' ) || exit;

/**
 * Add custom field for product_brand taxonomy in Woocommerce CSV Importer
 * 
 * This is only addition to Woocommerce Importer, which does nothing by itself
 */

class CSV_Importer
{
    // This is the column name in csv Importer table which we want to create product_brand from
    public const PRODUCTBRAND_COLUMN = '_supplier';


    // Add hooks to add custom table column
    public static function init()
    {
        // from Woocommerce docs :

        /**
         * Register the 'Custom Column' column in the importer.
         *
         * @param array $options
         * @return array $options
         */
        add_filter( 'woocommerce_csv_product_import_mapping_options', function( $options ) {

            // column slug => column name
            $options[  CSV_Importer::PRODUCTBRAND_COLUMN  ] = 'Supplier';

            return $options;
        } );

        /**
         * Add automatic mapping support for 'Custom Column'. 
         * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
         *
         * @param array $columns
         * @return array $columns
         */
        add_filter( 'woocommerce_csv_product_import_mapping_default_columns', function( $columns ) {
            
            // potential column name => column slug
            $columns['Supplier'] = CSV_Importer::PRODUCTBRAND_COLUMN;
            $columns['supplier'] = CSV_Importer::PRODUCTBRAND_COLUMN;

            return $columns;
        } );

        /**
         * Process the data read from the CSV file.
         * This just saves the value in meta data, but you can do anything you want here with the data.
         *
         * @param WC_Product $object - Product being imported or updated.
         * @param array $data - CSV data read for the product.
         * @return WC_Product $object
         */
        add_filter( 'woocommerce_product_import_pre_insert_product_object', function( $object, $data ) {
            
            if ( ! empty( $data[  CSV_Importer::PRODUCTBRAND_COLUMN  ] ) ) {
                
                /**
                 * Do what we want with our custom column here..
                 */
                $supplier = $data[  CSV_Importer::PRODUCTBRAND_COLUMN  ];
                $product_id = $object->get_id();

                if ( taxonomy_exists('product_brand') && ! has_term( $supplier, 'product_brand', $product_id ) ){
                    // Create term - product brand
                    $result = wp_set_object_terms( $product_id, $supplier, 'product_brand', true );

                    if ( is_wp_error($result) ) {
                        // We can throw Exception here because it will be catched in Woocommerce Importer loop
                        throw new Exception('[Nast-Core] : Failed to add product_brand for this product.');
                    }

                }

            }

            return $object;
        }, 10, 2 );

    }
}
 


CSV_Importer::init();