<?php
/**
 * The form helper class to generate the widget form easily.
 *
 * @package wut
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

	public function default( $haystack, $key, $type, $default ) {
		if ( isset( $haystack[ $key ] ) ) {
			switch ( $type ) {
				case 'string':
					return sanitize_text_field( $haystack[ $key ] );
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

}
