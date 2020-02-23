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

		$openwp = new OpenAgendaApi\OpenAgendaApi();

		$uid = $openwp->openwp_get_uid($instance['url']);
		$embed = $openwp->openwp_get_embed($uid, $key);

		echo $openwp->openwp_main_widget_html__premium_only($embed, $uid, $instance);

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
				for="<?php echo $this->get_field_name( 'url' ); ?>"> <?php _e( 'OpenAgenda URL:', 'wp-openagenda' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>"
			       name="<?php echo $this->get_field_name( 'url' ); ?>" type="text"
			       value="<?php echo $instance['url']; ?>"/>

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
				for="<?php echo $this->get_field_name( 'widget' ); ?>"> <?php _e( 'OpenAgenda Widget to display:', 'wp-openagenda' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'widget' ); ?>">
				<option <?php if ( $instance['widget'] === 'general') { echo 'selected'; } ?> value="general"><?php _e( 'General', 'wp-openagenda' ); ?></option>
				<option <?php if ( $instance['widget'] === 'map') { echo 'selected'; } ?> value="map"><?php _e( 'Map', 'wp-openagenda' ); ?></option>
				<option <?php if ( $instance['widget'] === 'search') { echo 'selected'; } ?> value="search"><?php _e( 'Search', 'wp-openagenda' ); ?></option>
				<option <?php if ( $instance['widget'] === 'categories') { echo 'selected'; } ?> value="categories"><?php _e( 'Categories', 'wp-openagenda' ); ?></option>
				<option <?php if ( $instance['widget'] === 'tags') { echo 'selected'; } ?> value="tags"><?php _e( 'Tags', 'wp-openagenda' ); ?></option>
				<option <?php if ( $instance['widget'] === 'calendrier') { echo 'selected'; } ?> value="calendrier"><?php _e( 'calendar', 'wp-openagenda' ); ?></option>
				<option <?php if ( $instance['widget'] === 'preview') { echo 'selected'; } ?> value="preview"><?php _e( 'preview', 'wp-openagenda' ); ?></option>
			</select>
		</p>
		<?php
	}

}
new Openagenda_Main_Widget();
