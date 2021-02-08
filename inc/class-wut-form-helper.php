<?php
/**
 * The form helper class to generate the widget form easily.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage admin
 */

/**
 * The form helper.
 */
class WUT_Form_Helper {

	/**
	 * The reference of WP_Widget object.
	 *
	 * @var WP_Widget The widget reference
	 */
	public $widget;

	/**
	 * The Constructor
	 *
	 * @param WP_Widget $widget the widget reference.
	 */
	public function __construct( $widget ) {
		$this->widget = $widget;
	}

	/**
	 * Print a checkbox form control on page.
	 *
	 * @param string $property The property name.
	 * @param string $value The value of the property.
	 * @param string $label The label tip of the checkbox.
	 */
	public function checkbox( $property, $value, $label ) {
		?>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $value ); ?> 
				id="<?php echo $this->widget->get_field_id( $property ); ?>" 
				name="<?php echo $this->widget->get_field_name( $property ); ?>" />
			<label for="<?php echo $this->widget->get_field_id( $property ); ?>">
				<?php echo $label; ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Print a widefat text input control on page.
	 *
	 * @param string $property The property name.
	 * @param string $value The value of the property.
	 * @param string $label The label tip of the checkbox.
	 * @param string $type The type of input control.
	 * @param string $class The class of input control.
	 */
	public function text( $property, $value, $label, $type = 'text', $class = 'widefat' ) {
		?>
		<p>
			<label for="<?php echo $this->widget->get_field_id( $property ); ?>">
				<?php echo $label; ?>
			</label>
			<input class="<?php echo $class; ?>" 
				id="<?php echo $this->widget->get_field_id( $property ); ?>" 
				name="<?php echo $this->widget->get_field_name( $property ); ?>" 
				type="<?php echo $type; ?>" value="<?php echo $value; ?>" />
		</p>
		<?php
	}

	/**
	 * Check the value type of the sepcific key and give it a default value if not set.
	 *
	 * @param array  $haystack The haystack which contains the key.
	 * @param string $key The key to check.
	 * @param string $type Could be `int`, `uint`, `string`, `bool`.
	 * @param mixed  $default The default value of the key.
	 * @param bool   $allow_empty Dose the value allow empty.
	 * @return mixed The sanitized value or default value if not set.
	 */
	public function default( $haystack, $key, $type, $default, $allow_empty = true ) {
		if ( isset( $haystack[ $key ] ) ) {
			switch ( $type ) {
				case 'string':
					$value = sanitize_text_field( $haystack[ $key ] );
					return ( ! $allow_empty && empty( $value ) ) ? $default : $value;
				case 'int':
					return intval( $haystack[ $key ] );
				case 'uint':
					return absint( $haystack[ $key ] );
				case 'bool':
					return (bool) $haystack[ $key ];
				default:
					trigger_error( 'Dose not support this type check now.' );
					return $haystack[ $key ];
			}
		}
		return $default;
	}

	/**
	 * This will print a full date format choose control group.
	 *
	 * $config should be like:
	 * array(
	 *      'date_format_property'   => 'date_format',
	 *      'date_format_value'      => $date_format,
	 *      'date_format_default'    => $site_date_format,
	 *      'custom_format_property' => 'custom_format',
	 *      'custom_format_value'    => $custom_format,
	 * )
	 *
	 * @param array $config The properties and values of this control group.
	 */
	public function date_format_chooser( $config ) {
		?>
		<p>
			<span><?php _e( 'Date format:', 'wut' ); ?></span><br/>
			<label>
				<input type="radio" 
					name="<?php echo $this->widget->get_field_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], $config['date_format_default'] ); ?> 
					value="<?php echo $config['date_format_default']; ?>"/>
				<span style="display:inline-block;min-width:10em;">
					<?php echo date( $config['date_format_default'] ); ?>
				</span>
				<code><?php echo $config['date_format_default']; ?></code>
			</label><br/>
			<label>
				<input type="radio" 
					name="<?php echo $this->widget->get_field_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], 'M d' ); ?> value="M d"/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( 'M d' ); ?></span>
				<code>M d</code>
			</label><br/>
			<label>
				<input type="radio" 
					name="<?php echo $this->widget->get_field_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], 'd F y' ); ?> value="d F y"/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( 'd F y' ); ?></span>
				<code>d F y</code>
			</label><br/>
			<label>
				<input type="radio" 
					name="<?php echo $this->widget->get_field_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], 'custom' ); ?> value="custom"/>
				<span style="display:inline-block;min-width:10em;"><?php _e( 'Custom', 'wut' ); ?></span>
				<input class="medium-text" 
					id="<?php echo $this->widget->get_field_id( $config['custom_format_property'] ); ?>" 
					name="<?php echo $this->widget->get_field_name( $config['custom_format_property'] ); ?>" 
					type="text" step="1" min="1" value="<?php echo $config['custom_format_value']; ?>" 
					size="6" />
			</label><br/>
			<strong><?php _e( 'Preview: ', 'wut' ); ?></strong>
			<span><?php echo 'custom' === $config['date_format_value'] ? date( $config['custom_format_value'] ) : date( $config['date_format_value'] ); ?></span>
		</p>
		<?php
	}

	/**
	 * This helper is used to print a widget content.
	 *
	 * @param array  $args Site widget configurations.
	 * @param string $title Widget title.
	 * @param string $content Widget content.
	 * @return void
	 */
	public function print_widget( $args, $title, $content ) {
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'], $title, $args['after_title'];
		}
		echo $content, $args['after_widget'];
	}

}
