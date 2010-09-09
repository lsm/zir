<?php

/**
 * Distributed Session Manager class
 * 
 * @author Robin Schuil <r.schuil@gmail.com>
 * @version 0.1.0
 * 
 * Usage:
 *   $sessionmanager = new SessionManager();
 *   session_start();
 */

/* Path to your session hosts configuration file */
define( 'SESSIONS_HOSTS_FILE', './session_hosts.ini' );

/* MySQL connect timeout for session servers */
define( 'SESSIONS_CONNECT_TIMEOUT', 2 );

/* The number of replica's you want to be stored of each session */
define( 'SESSIONS_NUM_COPIES', 1 );

class SessionManager {
	
	var $hosts;
	var $dbs;
	
	function SessionManager() {
		
		// Read the maxlifetime setting from PHP
		$this->life_time = get_cfg_var("session.gc_maxlifetime");
		
		// Read the hosts configuration file
		$this->hosts = parse_ini_file( SESSIONS_HOSTS_FILE, true );
		
		// Database pool
		$this->dbs = array();
		
		// Register this object as the session handler
		session_set_save_handler( 
			array( &$this, "open" ), 
			array( &$this, "close" ),
			array( &$this, "read" ),
			array( &$this, "write"),
			array( &$this, "destroy"),
			array( &$this, "gc" )
		);
		
	}
	
	function open( $save_path, $session_name ) {
		
		// Don't need to do anything. Just return TRUE.
		return true;
		
	}
	
	function close() {
		
		// Clean up all opened database connections
		while( $db = array_shift( $this->dbs ) ) {
			@mysql_close( $db );
		}
		
	}
	
	function read( $id ) {
		
		// Get a copy of the hosts configuration
		$tmp_hosts = $this->hosts;
		
		// Seed the pseudo randomizer with a CRC32 checksum of the session ID
		srand( crc32( $id ) );
		
		// 'Randomly' shuffle the array of hosts
		shuffle( $tmp_hosts );
		
		do {
			
			// Get next host from list
			$config = array_shift( $tmp_hosts );
			if( !$config ) {
				// Oops, no hosts left to try. Escape
				break;
			}
			
			// Try to connect
			$db = $this->mysql_quick_connect( $config['host'], $config['user'], $config['password'], $config['database'] );
												
		} while( !$db ); // Repeat until we've succesfully connected to a host
		
		// Set empty result
		$data = '';
		
		if( $db ) {

			// Fetch session data from the selected database
			$sql = 'SELECT `session_data` FROM `sessions` WHERE `session_id` = \'' . addslashes( $id ) . '\' AND `expires` < UNIX_TIMESTAMP();';
			$result = mysql_query( $sql, $db );
			
			if( $result && mysql_num_rows( $result ) ) {
				
				$data = mysql_result( $result, 0, 'session_data' );
				
				@mysql_free_result( $result );
				
			}
			
		}
		
		return $data;
		
	}
	
	function write( $id, $data ) {
		
		// Get a copy of the hosts configuration
		$tmp_hosts = $this->hosts;

		// Seed the pseudo randomizer with a CRC32 checksum of the session ID	
		srand( crc32( $id ) );
		
		// 'Randomly' shuffle the array of hosts
		shuffle( $tmp_hosts );

		// Initialize the replication counter at 0
		$replicate_count = 0;
		
		// Build query before we start looping		
		$sql = 'REPLACE `sessions` (`session_id`,`session_data`,`expires`) VALUES(\'' . addslashes( $id ) . '\', \'' . addslashes( $data ) . '\', UNIX_TIMESTAMP() + ' . $this->life_time . ');';
		
		do {
			
			// Get next host from list
			$config = array_shift( $tmp_hosts );
			if( !$config ) {
				// Oops, no hosts left to try. Escape
				break;
			}
			
			// Try to connect
			$db = $this->mysql_quick_connect( $config['host'], $config['user'], $config['password'], $config['database'] );

			// If we're connected, try to execute the query
			if( $db && mysql_query( $sql, $db ) ) {
				
				// Query was executed succesfull; increase the replication counter
				$replicate_count++;
					
			}
			
		} while( $replicate_count < SESSIONS_NUM_COPIES ); // Keep going until we've reached SESSION_NUM_COPIES
		
		// Return TRUE if at least one copy was stored
		return (bool) ( $replicate_count > 0 );
		
	}
	
	function destroy( $id ) {

		// Get a copy of the hosts configuration
		$tmp_hosts = $this->hosts;
		
		// Seed the pseudo randomizer with a CRC32 checksum of the session ID	
		srand( crc32( $id ) );
		
		// 'Randomly' shuffle the array of hosts
		shuffle( $tmp_hosts );

		// Initialize the replication counter at 0
		$replicate_count = 0;

		// Build query before we start looping		
		$sql = 'DELETE FROM `sessions` WHERE `session_id` = \'' . addslashes( $id ) . '\';';
		
		do {
			
			// Get next host from list
			$config = array_shift( $tmp_hosts );
			if( !$config ) {
				// Oops, no hosts left to try. Escape
				break;
			}
			
			// Try to connect
			$db = $this->mysql_quick_connect( $config['host'], $config['user'], $config['password'], $config['database'] );

			// If we're connected, try to execute the query
			if( $db && mysql_query( $sql, $db ) ) {
				
				// Query was executed succesfull; increase the replication counter
				$replicate_count++;
					
			}
			
		} while( $replicate_count < SESSIONS_NUM_COPIES ); // Keep going until we've reached SESSION_NUM_COPIES
		
		// Return TRUE if at least one query was succesfull
		return (bool) ( $replicate_count > 0 );		
		
	}
	
	function gc() {
		
		// Build DELETE query
		$sql = 'DELETE FROM `sessions` WHERE `expires` >= UNIX_TIMESTAMP();';
		
		// For each open database connection
		foreach( $this->dbs as $db ) {

			// Execute query
			@mysql_query( $sql, $db );
			
		}
		
		// Always return TRUE
		return true;
		
	}
	
	function mysql_quick_connect( $host, $user, $password, $database ) {
		
		// See if it is in the database pool
		if( $this->dbs[$host] ) {
			
			// If so, return the link identifier from the pool
			return $this->dbs[$host];
			
		}
		else {
			
			// Set MySQL connect timeout to our low value
			ini_set( "mysql.connect_timeout", SESSIONS_CONNECT_TIMEOUT );
			
			// Try to connect
			$link_id = @mysql_connect( $host, $user, $password );
			
			if( $link_id ) {
				
				// Connection established!
			
				// Select the database
				@mysql_select_db( $database );
					
				// Add this connection to our pool
				$this->dbs[$host] = $link_id;
				
			}
			
			// Restore PHP's default setting
			ini_restore( "mysql.connect_timeout" );
			
			// Return the link identifier
			return $link_id;
			
		}
		
	}
	
}


?>