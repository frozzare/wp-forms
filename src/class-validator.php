<?php

namespace Frozzare\Forms;

class Validator {

	/**
	 * Validaton errors.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Callable rules.
	 *
	 * @var array
	 */
	protected $callable_rules = [
		'alpha'     => 'ctype_alpha',
		'alpha_num' => 'ctype_alnum',
		'digit'     => 'ctype_digit'
	];

	/**
	 * Regex rules.
	 *
	 * @var array
	 */
	protected $regex_rules = [];

	/**
	 * Current rules.
	 *
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Validator constructor.
	 *
	 * @param array $rules
	 */
	public function __construct( array $rules = [] ) {
		$this->set_rules( $rules );
	}

	/**
	 * Cast array or string value.
	 *
	 * @param  string $str
	 *
	 * @return bool|float|int|void
	 */
	protected function cast_value( $str ) {
		if ( is_array( $str ) ) {
			foreach ( $str as $key => $value ) {
				$str[$key] = $this->cast_value( $value );
			}

			return $str;
		}

		if ( ! is_string( $str ) ) {
			return $str;
		}

		if ( is_numeric( $str ) ) {
			return $str == (int) $str ? (int) $str : (float) $str; // WPCS: loose comparison
		}

		if ( $str === 'true' || $str === 'false' ) {
			return $str === 'true';
		}

		if ( $this->is_json( $str ) ) {
			return json_decode( $str );
		}

		return maybe_unserialize( $str );
	}

	/**
	 * Add validation error.
	 *
	 * @param string $name
	 * @param string $rule
	 * @param mixed  $value
	 */
	protected function add_error( $name, $rule, $value ) {
		$key = sprintf( '%s.%s', $name, explode( ':', $rule )[0] );

		$this->errors[$key] = $this->get_error_message( $key, $value );
	}

	/**
	 * Get callable rule.
	 *
	 * @param  string $rule
	 *
	 * @return mixed
	 */
	protected function get_callable_rule( $rule ) {
		$callable = '';

		if ( isset( $this->callable_rules[$rule] ) ) {
			$callable = $this->callable_rules[$rule];
		}

		/**
		 * Modify callable rule.
		 *
		 * @param string $callable
		 * @param string $rule
		 */
		$callable = apply_filters( 'forms_get_callable_rule', $callable, $rule );

		return is_callable( $callable ) ? $callable : null;
	}

	/**
	 * Get error message.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	protected function get_error_message( $key, $value ) {
		/**
		 * Modify error message.
		 *
		 * @param string $message
		 * @param string $key
		 * @param mixed  $value
		 */
		return apply_filters( 'forms_get_error_message', '', $key, $value );
	}

	/**
	 * Get regex rule.
	 *
	 * @param  string $rule
	 *
	 * @return mixed
	 */
	protected function get_regex_rule_pattern( $rule ) {
		$pattern = '';

		if ( isset( $this->regex_rules[$rule] ) ) {
			$pattern = $this->regex_rules[$rule];
		}

		/**
		 * Modify regex rule pattern.
		 *
		 * @param string $pattern
		 * @param string $rule
		 */
		return apply_filters( 'forms_get_regex_rule_pattern', $pattern, $rule );
	}

	/**
	 * Get value size.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 *
	 * @return int
	 */
	protected function get_size( $name, $value ) {
		if ( is_numeric( $value ) ) {
			return $value;
		} else if ( is_array( $value ) ) {
			return count( $value );
		}

		return mb_strlen( $value );
	}

	/**
	 * Test if given object is a JSON string or not.
	 *
	 * @param  mixed $obj
	 *
	 * @return bool
	 */
	protected function is_json( $obj ) {
		return is_string( $obj ) && is_array( json_decode( $obj, true ) ) && json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * Parse rule.
	 *
	 * @param  string $rule
	 *
	 * @return array
	 */
	protected function parse_rule( $rule ) {
		$parameters = [];

		if ( strpos( $rule, ':' ) !== false ) {
			list( $rule, $parameter ) = explode( ':', $rule, 2 );

			$parameters = $this->parse_parameters( $rule, $parameter );
		}

		return [trim( $rule ), $parameters];
	}

	/**
	 * Parse parameters.
	 *
	 * @param  string $rule
	 * @param  string $parameter
	 *
	 * @return array
	 */
	protected function parse_parameters( $rule, $parameter ) {
		if ( strtolower( $rule ) === 'regex' ) {
			return [$parameter];
		}

		return $this->cast_value( str_getcsv( $parameter ) );
	}

	/**
	 * Set rules.
	 *
	 * @param  array $rules
	 *
	 * @return array
	 */
	protected function set_rules( array $rules ) {
		foreach ( $rules as $key => $rule ) {
			$rules[$key] = explode( '|', $rule );
		}

		$this->rules = $rules;
	}

	/**
	 * Validate data.
	 *
	 * @param  array $data
	 *
	 * @return array
	 */
	public function validate( array $data ) {
		foreach ( $data as $key => $value ) {
			if ( empty( $this->rules[$key] ) ) {
				continue;
			}

			$rules = $this->rules[$key];

			foreach ( $rules as $rule ) {
				if ( $this->valiate_rule( $rule, $key, $value ) ) {
					continue;
				}

				$this->add_error( $key, $rule, $value );
			}
		}

		return $this->errors;
	}

	/**
	 * Validate that a value is less than a maximum value.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_between( $name, $value, $parameters ) {
		$size = $this->get_size( $name, $value );

		return $size <= $parameters[0] && $size >= $parameters[0];
	}

	/**
	 * Validate that a value is greater than a minimum value.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_min( $name, $value, $parameters ) {
		return $this->get_size( $name, $value ) >= $parameters[0];
	}

	/**
	 * Validate that a value is less than a maximum value.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_max( $name, $value, $parameters ) {
		return $this->get_size( $name, $value ) <= $parameters[0];
	}

	/**
	 * Validate that a value is required.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_required( $name, $value, $parameters ) {
		if ( is_null( $value ) ) {
			return false;
		} else if ( is_string( $value ) && trim( $value ) === '' ) {
			return false;
		} else if ( is_array( $value ) && count( $value ) < 1 ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate rule.
	 *
	 * @param  string $rule
	 * @param  string $name
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	protected function valiate_rule( $rule, $name, $value ) {
		list( $rule, $parameters ) = $this->parse_rule( $rule );

		// A callable check direct withoyt any custom validation method.
		if ( $callable = $this->get_callable_rule( $rule ) ) {
			return call_user_func_array( $callable, [$value] );
		}

		// A regex check direct without any custom validation method.
		if ( $pattern = $this->get_regex_rule_pattern( $rule ) ) {
			return preg_match( $pattern, $value );
		}

		$method = 'validate_' . $rule;

		if ( method_exists( $this, $method ) ) {
			return $this->$method( $name, $value, $parameters );
		}

		return true;
	}

	/**
	 * Validate that a value is the right size.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_size( $name, $value, $parameters ) {
		return $this->get_size( $name, $value ) === $parameters[0];
	}
}
