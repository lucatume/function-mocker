<?php

namespace Examples\WPBrowser;

class Logger {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * Initializes the logger singleton instance.
	 */
	public static function start() {
		self::$instance = new self();
	}

	/**
	 * Logs a message.
	 *
	 * @param string $message
	 */
	public static function write( $message ) {
		$instance = self::$instance;

		if ( null === $instance ) {
			return;
		}

		$instance->log( $message );
	}

	public function log( $message, $when = null ) {
		$when = $when ? $when : time();

		// log messages on an hourly base
		$transient = 'log_' . date( 'Y_m_d_H', $when );
		$hourly_log = (array) get_transient( $transient );

		$hourly_log[ date( 'i:s', $when ) ] = $message;

		set_transient( $transient, $hourly_log, DAY_IN_SECONDS );
	}
}