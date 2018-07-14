<?php


class Openagenda_Slider_Widget extends WP_Widget {
	public function __construct() {
		$widget_args = array(
			'classname'   => 'Openagenda Slider Widget',
			'description' => __( 'Display your Openagenda.com\'s Events in a slider in your WordPress Sidebar with a beautiful widget', 'wp-openagenda-pro' ),
		);
		parent::__construct(
			'openwp_slider_widget',
			'Openagenda Slider Widget',
			$widget_args
		);
		add_action( 'widgets_init', array( $this, 'init_openwp_slider_widget' ) );
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'];

		echo apply_filters( 'widget_title', $instance['title'] );

		echo $args['after_title'];

		$instance['agenda_date_color'] = '#cec2ab';

		wp_enqueue_script( 'slickjs' );
		wp_enqueue_script( 'openagendaSliderJS' );
		wp_enqueue_style( 'slickcss' );
		wp_enqueue_style( 'slickthemecss' );
		$slide = new OpenagendaSliderShortcode();

		echo $slide->openwp_slider_html( $instance );
	}

	public function form( $instance ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"> <?php esc_attr_e( 'Title:' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo $instance['title']; ?>"/>
		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'agenda_url' ); ?>"> <?php _e( 'OpenAgenda URL', 'wp-openagenda' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'agenda_url' ); ?>"
			       name="<?php echo $this->get_field_name( 'agenda_url' ); ?>" type="text"
			       value="<?php echo $instance['agenda_url']; ?>"/>

		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'agenda_url_intern' ); ?>"> <?php _e( 'Internal URL of Main Agenda Page:', 'wp-openagenda-pro' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'agenda_url_intern' ); ?>"
			       name="<?php echo $this->get_field_name( 'agenda_url_intern' ); ?>" type="text"
			       value="<?php echo $instance['agenda_url_intern']; ?>"/>

		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'agenda_lieu' ); ?>"> <?php _e( 'Display venue', 'wp-openagenda-pro' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'agenda_lieu' ); ?>"
			       name="<?php echo $this->get_field_name( 'agenda_lieu' ); ?>" type="radio" value="true"
				<?php if ( 'true' === $instance['agenda_lieu'] ) {
					echo "checked";
				} ?>
			/> Yes
			<input class="widefat" id="<?php echo $this->get_field_id( 'agenda_lieu' ); ?>"
			       name="<?php echo $this->get_field_name( 'agenda_lieu' ); ?>" type="radio" value="false"
				<?php if ( 'false' === $instance['agenda_lieu'] ) {
					echo "checked";
				} ?> /> No

		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'number' ); ?>"> <?php _e( 'Number of Events', 'wp-openagenda-pro' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>"
			       name="<?php echo $this->get_field_name( 'number' ); ?>" type="text"
			       value="<?php echo $instance['number']; ?>"/>

		</p>
		<?php
	}

	public function init_openwp_slider_widget() {
		register_widget( 'Openagenda_Slider_Widget' );
	}
}

new Openagenda_Slider_Widget();
