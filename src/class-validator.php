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
		'array'     => 'is_array',
		'bool'      => 'is_bool',
		'digit'     => 'ctype_digit',
		'float'     => 'is_float',
		'int'       => 'is_int',
		'numeric'   => 'is_numeric',
		'string'    => 'is_string'
	];

	/**
	 * Regex rules.
	 *
	 * @var array
	 */
	protected $regex_rules = [
		'bool' => '/0|1/'
	];

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
	 * Validate that a value is a email.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_email( $name, $value, $parameters ) {
		return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * Validate that a value is a ip.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_ip( $name, $value, $parameters ) {
		return filter_var($value, FILTER_VALIDATE_IP) !== false;
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

		$valid = false;

		// Check callable rule.
		if ( $callable = $this->get_callable_rule( $rule ) ) {
			$valid = call_user_func_array( $callable, [$value] );
		}

		// Check regex rule.
		if ( ! $valid && $pattern = $this->get_regex_rule_pattern( $rule ) ) {
			$valid = preg_match( $pattern, $value );
		}

		$method = 'validate_' . $rule;

		if ( ! $valid && method_exists( $this, $method ) ) {
			return $this->$method( $name, $value, $parameters );
		}

		return $valid;
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

	/**
	 * Validate that a value is a valid url.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  array  $parameters
	 *
	 * @return bool
	 */
	protected function validate_url( $name, $value, $parameters ) {
		/*
         * This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (2.7.4).
         *
         * (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
         */
		$pattern = '~^
            ((aaa|aaas|about|acap|acct|acr|adiumxtra|afp|afs|aim|apt|attachment|aw|barion|beshare|bitcoin|blob|bolo|callto|cap|chrome|chrome-extension|cid|coap|coaps|com-eventbrite-attendee|content|crid|cvs|data|dav|dict|dlna-playcontainer|dlna-playsingle|dns|dntp|dtn|dvb|ed2k|example|facetime|fax|feed|feedready|file|filesystem|finger|fish|ftp|geo|gg|git|gizmoproject|go|gopher|gtalk|h323|ham|hcp|http|https|iax|icap|icon|im|imap|info|iotdisco|ipn|ipp|ipps|irc|irc6|ircs|iris|iris.beep|iris.lwz|iris.xpc|iris.xpcs|itms|jabber|jar|jms|keyparc|lastfm|ldap|ldaps|magnet|mailserver|mailto|maps|market|message|mid|mms|modem|ms-help|ms-settings|ms-settings-airplanemode|ms-settings-bluetooth|ms-settings-camera|ms-settings-cellular|ms-settings-cloudstorage|ms-settings-emailandaccounts|ms-settings-language|ms-settings-location|ms-settings-lock|ms-settings-nfctransactions|ms-settings-notifications|ms-settings-power|ms-settings-privacy|ms-settings-proximity|ms-settings-screenrotation|ms-settings-wifi|ms-settings-workplace|msnim|msrp|msrps|mtqp|mumble|mupdate|mvn|news|nfs|ni|nih|nntp|notes|oid|opaquelocktoken|pack|palm|paparazzi|pkcs11|platform|pop|pres|prospero|proxy|psyc|query|redis|rediss|reload|res|resource|rmi|rsync|rtmfp|rtmp|rtsp|rtsps|rtspu|secondlife|service|session|sftp|sgn|shttp|sieve|sip|sips|skype|smb|sms|smtp|snews|snmp|soap.beep|soap.beeps|soldat|spotify|ssh|steam|stun|stuns|submit|svn|tag|teamspeak|tel|teliaeid|telnet|tftp|things|thismessage|tip|tn3270|turn|turns|tv|udp|unreal|urn|ut2004|vemmi|ventrilo|videotex|view-source|wais|webcal|ws|wss|wtai|wyciwyg|xcon|xcon-userid|xfire|xmlrpc\.beep|xmlrpc.beeps|xmpp|xri|ymsgr|z39\.50|z39\.50r|z39\.50s))://                                 # protocol
            (([\pL\pN-]+:)?([\pL\pN-]+)@)?          # basic auth
            (
                ([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                              # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                 # a IP address
                    |                                              # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # a IPv6 address
            )
            (:[0-9]+)?                              # a port (optional)
            (/?|/\S+|\?\S*|\#\S*)                   # a /, nothing, a / with something, a query or a fragment
        $~ixu';

		return preg_match( $pattern, $value ) > 0;
	}
}
