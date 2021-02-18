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
	 * Print a field id attr.
	 *
	 * @param string $field The field name.
	 * @return void
	 */
	public function print_id( $field ) {
		$this->print( $this->widget->get_field_id( $field ) );
	}

	/**
	 * Print a field name attr.
	 *
	 * @param string $field The field name.
	 * @return void
	 */
	public function print_name( $field ) {
		$this->print( $this->widget->get_field_name( $field ) );
	}

	/**
	 * Print a string.
	 *
	 * @param string $str The string to print.
	 * @return void
	 */
	public function print( $str ) {
		echo $str;
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
				id="<?php $this->print_id( $property ); ?>"
				name="<?php $this->print_name( $property ); ?>" />
			<label for="<?php $this->print_id( $property ); ?>">
				<?php $this->print( $label ); ?>
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
			<label for="<?php $this->print_id( $property ); ?>">
				<?php $this->print( $label ); ?>
			</label>
			<input class="<?php $this->print( $class ); ?>"
				id="<?php $this->print_id( $property ); ?>"
				name="<?php $this->print_name( $property ); ?>"
				type="<?php $this->print( $type ); ?>" value="<?php $this->print( $value ); ?>" />
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
			<span><?php _e( 'Date format:', 'wordpress-ultimate-toolkit' ); ?></span><br/>
			<label>
				<input type="radio" 
					name="<?php $this->print_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], $config['date_format_default'] ); ?> 
					value="<?php echo $config['date_format_default']; ?>"/>
				<span style="display:inline-block;min-width:10em;">
					<?php echo date( $config['date_format_default'] ); ?>
				</span>
				<code><?php echo $config['date_format_default']; ?></code>
			</label><br/>
			<label>
				<input type="radio" 
					name="<?php $this->print_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], 'M d' ); ?> value="M d"/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( 'M d' ); ?></span>
				<code>M d</code>
			</label><br/>
			<label>
				<input type="radio" 
					name="<?php $this->print_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], 'd F y' ); ?> value="d F y"/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( 'd F y' ); ?></span>
				<code>d F y</code>
			</label><br/>
			<label>
				<input type="radio" 
					name="<?php $this->print_name( $config['date_format_property'] ); ?>"
					<?php checked( $config['date_format_value'], 'custom' ); ?> value="custom"/>
				<span style="display:inline-block;min-width:10em;"><?php _e( 'Custom', 'wordpress-ultimate-toolkit' ); ?></span>
				<input class="medium-text" 
					id="<?php $this->print_id( $config['custom_format_property'] ); ?>"
					name="<?php $this->print_name( $config['custom_format_property'] ); ?>"
					type="text" step="1" min="1" value="<?php echo $config['custom_format_value']; ?>"
					size="6" />
			</label><br/>
			<strong><?php _e( 'Preview: ', 'wordpress-ultimate-toolkit' ); ?></strong>
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
