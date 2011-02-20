<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Event Handler Class
 * @package class
 */
class Event {
	
	private static $instance;
	
	/**
	 * Events list to fire
	 *
	 * @var array
	 */
	private $event_list = array();
	
	private function __construct() {}
	
	/**
	 * Throws an exception on clone
	 *
	 * @throws Exception
	 */
	public final function __clone() { 
		throw new BadMethodCallException("Clone is not allowed"); 
	}
	
	/**
	 * Add event
	 *
	 * @param string $event_name 
	 * @param string $callback 
	 * @return void
	 */
	public static function add($event_name,$callback) {
		$event = self::get_instance();
		$event->event_list[$event_name][] = $callback;
	}
	
	/**
	 * Fire a event
	 *
	 * @param string $event_name 
	 * @param string $params 
	 * @return void
	 */
	public static function fire($event_name, $params =null) {
		$event = self::get_instance();
		
		if(array_key_exists($event_name,$event->event_list)){
			foreach ($event->event_list[$event_name] as $callback) {
				call_user_func_array($callback, array(&$params));
			}
		}
	}
	
	/**
	 * Get singleton instance
	 *
	 * @return Event
	 */
	public static function get_instance() {
		if (!isset(self::$instance)) {
    		self::$instance = new Event();
		}
		return self::$instance;
	}
	
}
?>