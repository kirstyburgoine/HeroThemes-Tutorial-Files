<?php
function twentysixteenchild_scripts() {
    wp_enqueue_style( 'hkb-style', get_stylesheet_directory_uri() . '/css/hkb-style.css', array( 'twentysixteen-style' ), '20151217' );
    /* Add more styles or scripts here as needed */
}
add_action( 'wp_enqueue_scripts', 'twentysixteenchild_scripts' );



/* Insert custom functions below */
add_theme_support( 'ht-kb-category-icons' );


function alter_ht_kb_title( $title, $id = null ) {

	if ( 'ht_kb' == $title ) {
		return 'Our Knowledge Base';
	}
		return $title;
	}
	 
add_filter( 'the_title', 'alter_ht_kb_title', 10, 2 );




/**
 * Create new tab for Woocommerce
 *
 */
add_filter( 'woocommerce_product_tabs', 'woo_knowledge_base_tab' );
function woo_knowledge_base_tab( $tabs ) {
	
	// Adds the new tab
	
	$tabs['faqs_tab'] = array(
		'title' 	=> __( 'FAQs', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'woo_knowledge_base_content'
	);

	return $tabs;

}

/**
 * Create new tab content for Woocommerce
 *
 */
function woo_knowledge_base_content() {

	global $post;
	// The new tab content
	// Get the taxonomy field
	$kb_category = get_field('kb_category');

	// if the field has been populated...
	if ( $kb_category ) : 

		// creayed a custoim query looking for this
		$kb_args = array(	
			'post_type' => 'ht_kb',
			'tax_query' => array(
				array(
					'taxonomy' => 'ht_kb_category',
					'field' => 'term_id',
					'terms' => $kb_category
				)
			)
		);

		$kb_query = new WP_Query( $kb_args );
		
		// if there any posts in the query
		if ( $kb_query->have_posts() ) : ?>
		
		<div id="hkb">
		<div class="hkb-category">
			<h2> Frequently Asked Questions</h2>
			<ul class="hkb-article-list">  
			
			<?php 
			// loop through all of the posts to display the relevent articles
			while ( $kb_query->have_posts() ) : $kb_query->the_post(); ?>
			
				<li class="hkb-article-list__<?php hkb_post_format_class($post->ID); ?>">
                    <a href="<?php echo get_permalink(); ?>" rel="bookmark">
                        <?php echo get_the_title(); ?>
                    </a>
                </li>
			
			<?php
			endwhile; ?>

			</ul><!-- and article list -->
		</div>
		</div>
		<?php
		endif;
		
		// reset the post data so we don't mess with the main loop
		wp_reset_postdata();
		
	endif; 
	
}



function output_page_type() {
	global $post;
	$pagename = $post->post_name;
	return $pagename;
}


function add_role_caps() {
    global $wp_roles;
    $role = get_role('contributor');
    $role->add_cap('read_private_posts');
};
add_action ('admin_head','add_role_caps');




/**
  * Add REST API support to an already registered post type.
  */
  add_action( 'init', 'my_custom_post_type_rest_support', 30 );

  function my_custom_post_type_rest_support() {
    global $wp_post_types;

    //be sure to set this to the name of your post type!
    $post_type_name = 'ht_kb';
    if( isset( $wp_post_types[ $post_type_name ] ) ) {
        $wp_post_types[$post_type_name]->show_in_rest = true;
        $wp_post_types[$post_type_name]->rest_base = $post_type_name;
        $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
    }

  }


 /**
  * Add REST API support to an already registered taxonomy.
  */
  add_action( 'init', 'my_custom_taxonomy_rest_support', 30 );
  function my_custom_taxonomy_rest_support() {
    global $wp_taxonomies;

    //be sure to set this to the name of your taxonomy!
    $taxonomy_name = 'ht_kb_category';

    if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
        $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
        $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
        $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
    }


  }