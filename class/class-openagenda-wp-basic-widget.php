<?php
/**
 * Create a basic Widget to display a Agenda from Openagenda.com on a sidebar.
 *
 * @package openagenda-basic-widget
 */

/**
 * Class Openwp_WP_Basic_Widget
 */
class Openagenda_WP_Basic_Widget extends WP_Widget {
	/**
	 * OpenwpBasicWidget constructor.
	 */
	public function __construct() {
		$widget_args = array(
			'classname'   => 'Openagenda Basic Widget',
			'description' => __( 'Display an Openagenda.com\'s Agenda in your WordPress Sidebar with a beautiful widget', 'wp-openagenda' ),
		);
		parent::__construct(
			'openwp_basic_widget',
			'Openagenda Basic Widget',
			$widget_args
		);
		add_action( 'widgets_init', array( $this, 'init_openwp_basic_widget' ) );
	}

	/**
	 * Display the Widget in Front Office.
	 *
	 * @param array $args     Argument of Widget.
	 * @param array $instance Settings of widget.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'];

		echo apply_filters( 'widget_title', $instance['title'] );

		echo $args['after_title'];

		$openwp = new OpenAgendaApi\OpenAgendaApi();

		$openwp_data = $openwp->thfo_openwp_retrieve_data( $instance['slug'], $instance['nb'] );

		$lang = $instance['lang'];


		$openwp->openwp_basic_html( $openwp_data, $lang, $instance );
	}

	/**
	 * Widget Settings
	 *
	 * @param array $instance Store Widget Settings.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : ''; ?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"> <?php esc_attr_e( 'Title:' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>


		<p>
			<label
					for="<?php echo $this->get_field_name( 'slug' ); ?>"> <?php _e( 'OpenAgenda URL', 'wp-openagenda' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'slug' ); ?>"
			       name="<?php echo $this->get_field_name( 'slug' ); ?>" type="text"
			       value="<?php echo $instance['slug']; ?>"/>

		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'nb' ); ?>"> <?php _e( 'Number of events:', 'wp-openagenda' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'nb' ); ?>"
			       name="<?php echo $this->get_field_name( 'nb' ); ?>" type="text"
			       value="<?php echo $instance['nb']; ?>"/>

		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'lang' ); ?>"> <?php _e( 'Languages of events:', 'wp-openagenda' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'nb' ); ?>"
			       name="<?php echo $this->get_field_name( 'lang' ); ?>" type="text"
			       value="<?php echo $instance['lang']; ?>"/>

		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'img' ); ?>"> <?php _e( 'Display Image:', 'wp-openagenda' ); ?>
			</label>

 			<input class="widefat" id="<?php echo $this->get_field_name( 'img' ); ?>"
			       name="<?php echo $this->get_field_name( 'img' ); ?>" type="checkbox" value="yes" <?php checked( $instance['img'], 'yes' ); ?>>
		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'event-title' ); ?>"> <?php _e( 'Display event title:', 'wp-openagenda' ); ?>
			</label>

			<input class="widefat" id="<?php echo $this->get_field_name( 'event-title' ); ?>"
			       name="<?php echo $this->get_field_name( 'event-title' ); ?>" type="checkbox" value="yes" <?php checked( $instance['event-title'], 'yes' ); ?>>
		</p>
		<p>
			<label
					for="<?php echo $this->get_field_name( 'event-description' ); ?>"> <?php _e( 'Display event description:', 'wp-openagenda' ); ?>
			</label>

			<input class="widefat" id="<?php echo $this->get_field_name( 'event-description' ); ?>"
			       name="<?php echo $this->get_field_name( 'event-description' ); ?>" type="checkbox" value="yes" <?php checked( $instance['event-description'], 'yes' ); ?>>
		</p>
		<?php
	}

	/**
	 * Initialize a new Widget.
	 */
	public function init_openwp_basic_widget() {
		register_widget( 'Openagenda_WP_Basic_Widget' );
	}

}

new Openagenda_WP_Basic_Widget();

