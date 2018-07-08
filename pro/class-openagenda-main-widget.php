<?php
/**
 *  Create a Native WP WIdget to display Openagenda Widgets on sidebar or Page Builders.
 *
 * @package openagenda_widgets
 */

/**
 * Class Openagenda_Main_Widget
 */
class Openagenda_Main_Widget extends WP_Widget {


	/**
	 * Openagenda_Main_Widget constructor.
	 */
	public function __construct() {
		$widget_args = array(
			'classname'   => 'Openagenda Main Widget',
			'description' => __( 'Display the main Widget from OpenAgenda where ever you want in your WordPress Website', 'wp-openagenda' ),
		);
		parent::__construct(
			'openagenda_main_widget',
			'Openagenda Main Widget',
			$widget_args
		);
		add_action( 'widgets_init', array( $this, 'openwp_main_openagenda_widget' ) );
	}

	/**
	 * Initialize a new Widget.
	 */
	public function openwp_main_openagenda_widget() {
		register_widget( 'Openagenda_Main_Widget' );
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

		$openwp = new OpenAgendaApi();

		$uid = $openwp->openwp_get_uid($instance['url']);

		$embed = new Openwp_Main_Widget();

		$lang = $instance['lang'];

		var_dump($instance['widget']);

		echo $embed->openwp_main_widget_html($instance['widget'], $uid, $instance);

		echo $args['after_widget'];
	}

	/**
	 * @param $instance
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
				for="<?php echo $this->get_field_name( 'url' ); ?>"> <?php _e( 'OpenAgenda URL:', 'openagenda-wp' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>"
			       name="<?php echo $this->get_field_name( 'url' ); ?>" type="text"
			       value="<?php echo $instance['url']; ?>"/>

		</p>

		<p>
			<label
				for="<?php echo $this->get_field_name( 'lang' ); ?>"> <?php _e( 'Languages of events:', 'openagenda-wp' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'nb' ); ?>"
			       name="<?php echo $this->get_field_name( 'lang' ); ?>" type="text"
			       value="<?php echo $instance['lang']; ?>"/>

		</p>
		<p>
			<label
				for="<?php echo $this->get_field_name( 'widget' ); ?>"> <?php _e( 'OpenAgenda Widget to display:', 'openagenda-wp' ); ?></label>
			<select>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="general"><?php _e( 'General', 'wp-openagenda' ); ?></option>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="map"><?php _e( 'Map', 'wp-openagenda' ); ?></option>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="search"><?php _e( 'Search', 'wp-openagenda' ); ?></option>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="categories"><?php _e( 'Categories', 'wp-openagenda' ); ?></option>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="tags"><?php _e( 'Tags', 'wp-openagenda' ); ?></option>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="calendrier"><?php _e( 'calendrier', 'wp-openagenda' ); ?></option>
				<option name="<?php echo $this->get_field_name( 'widget' ); ?>" value="preview"><?php _e( 'preview', 'wp-openagenda' ); ?></option>
			</select>
		</p>
		<?php
	}

}
new Openagenda_Main_Widget();
