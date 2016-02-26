<?php 
/**
 * Plugin Name: KB - REST API Widget
 * Description: Widget to display posts from external websites using the REST API
 * Version: 1.0
 * Author: Kirsty Burgoine
 * Author URI: http://kirstyburgoine.co.uk/wordpress-plugins/
 * License: GPLv2 or later
*/

class REST_API_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array( 
            'classname' => 'rest-api-widget',
            'description' => 'A REST API widget that pulls posts from a different website'
        );
        parent::__construct( 'rest_api_widget', 'REST API Widget', $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        // outputs the content of the widget

        $response = wp_remote_get( 'http://knowledgebase.herothemes.wpengine.com/wp-json/wp/v2/ht_kb/' );


        if( is_wp_error( $response ) ) {
        return;
        }

        $posts = json_decode( wp_remote_retrieve_body( $response ) );

        if( empty( $posts ) ) {
            return;
        }

        echo $args['before_widget'];
        if( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
        }
        
        // Main Widget Content Here
        if( !empty( $posts ) ) {
        
            echo '<ul>';
            foreach( $posts as $post ) {
                echo '<li><a href="' . $post->link. '">' . $post->title->rendered . '</a></li>';
            }
            echo '</ul>';
        
        }


        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin

        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
        ?>

        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>">Title: </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p> 
    <?php
    }


}


add_action( 'widgets_init', function(){
     register_widget( 'REST_API_Widget' );
});
?>