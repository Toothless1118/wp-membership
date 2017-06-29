<?php
/**
 * Class Affiliate_WP_Zapier_Logs_DB
 *
 * @since  1.0
 * @see    Affiliate_WP_Zapier_DB
 *
 */
class Affiliate_WP_Zapier_Logs_DB extends Affiliate_WP_Zapier_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {

		global $wpdb;

		if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {

			// Allows a single affwp_zapier_logs table for the whole network
			$this->table_name  = 'affwp_zapier_logs';

		} else {

			$this->table_name  = $wpdb->prefix . 'affwp_zapier_logs';

		}

		$this->primary_key = 'log_id';
		$this->version     = '1.0';
	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {

		return array(
			'log_id'    => '%d',
			'object_id' => '%d',
			'object'    => '%s',
			'action'    => '%s',
			'date'      => '%s',
			'meta'      => '%s',
			'queried'   => '%s'
		);
	}
	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_column_defaults() {

		return array(
			'object_id' => 0,
			'action'    => '',
			'date'      => date( 'Y-m-d H:i:s' ),
			'meta'      => '',
			'queried'   => false
		);
	}

	/**
	 * Retrieves a single log object.
	 *
	 * @access  public
	 * @since   1.0
	 * @param   array $args
	*/
	public function get_log( $args = array() ) {

		global $wpdb;

		$where = '';

		$args['number']  = ( isset($args['number'] ) )  ? $args['number']  : null;
		$args['count']   = ( isset($args['count'] ) )   ? $args['count']   : 1;
		$args['orderby'] = ( isset($args['orderby'] ) ) ? $args['orderby'] : null;
		$args['order']   = ( isset($args['order'] ) )   ? $args['order']   : null;
		$args['offset']  = ( isset($args['offset'] ) )  ? $args['offset']  : null;

		if( ! empty( $args['log_id'] ) || isset( $args['log_id'] ) ) {
			if( is_array( $args['log_id'] ) ) {
				$log_ids = implode( ',', $args['log_id'] );
			} else {
				$log_ids = intval( $args['log_id'] );
			}
			$where .= "WHERE `log_id` IN( {$log_ids} ) ";
		}

		if( ! empty( $args['object'] ) || isset( $args['object'] ) ) {
			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			if( is_array( $args['object'] ) ) {
				$where .= " `object` IN(" . implode( ',', $args['object'] ) . ") ";
			} else {
				if( ! empty( $args['search'] ) ) {
					$where .= " `object` LIKE '%%" . $args['object'] . "%%' ";
				} else {
					$where .= " `object` = '" . $args['object'] . "' ";
				}
			}
		}

		if( ! empty( $args['action'] ) ) {
			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			if( is_array( $args['action'] ) ) {
				$where .= " `action` IN(" . implode( ',', $args['action'] ) . ") ";
			} else {
				if( ! empty( $args['search'] ) ) {
					$where .= " `action` LIKE '%%" . $args['action'] . "%%' ";
				} else {
					$where .= " `action` = '" . $args['action'] . "' ";
				}
			}
		}

		if( ! empty( $args['date'] ) ) {
			if( is_array( $args['date'] ) ) {
				if( ! empty( $args['date']['start'] ) ) {
					if( false !== strpos( $args['date']['start'], ':' ) ) {
						$format = 'Y-m-d H:i:s';
					} else {
						$format = 'Y-m-d 00:00:00';
					}
					$start = date( $format, strtotime( $args['date']['start'] ) );
					if( ! empty( $where ) ) {
						$where .= " AND `date` >= '{$start}'";
					} else {
						$where .= " WHERE `date` >= '{$start}'";
					}
				}

				if( ! empty( $args['date']['end'] ) ) {
					if( false !== strpos( $args['date']['end'], ':' ) ) {
						$format = 'Y-m-d H:i:s';
					} else {
						$format = 'Y-m-d 23:59:59';
					}
					$end = date( $format, strtotime( $args['date']['end'] ) );
					if( ! empty( $where ) ) {
						$where .= " AND `date` <= '{$end}'";
					} else {
						$where .= " WHERE `date` <= '{$end}'";
					}
				}

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );
				if( empty( $where ) ) {
					$where .= " WHERE";
				} else {
					$where .= " AND";
				}
				$where .= " $year = YEAR ( date ) AND $month = MONTH ( date ) AND $day = DAY ( date )";
			}
		}

		$cache_key = ( isset( $count ) && true === $count ) ? md5( 'affwp_zapier_log_count' . serialize( $args ) ) : md5( 'affwp_zapier_log_' . serialize( $args ) );

		$results = wp_cache_get( $cache_key, 'log' );


		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
				absint( $args['offset'] ),
				absint( $args['number'] )
			)
		);

		wp_cache_set( $cache_key, $results, 'log', 100 );

		return $results;
	}

	/**
	 * Retrieve logs from the database
	 *
	 * @access  public
	 * @since   1.0
	 * @param   array $args
	 * @param   bool  $count  Return only the total number of results found (optional)
	*/
	public function get_logs( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'  => 20,
			'offset'  => 0,
			'log_id'  => 0,
			'status'  => '',
			'object'  => '',
			'orderby' => 'log_id',
			'order'   => 'DESC',
			'queried' => false
		);

		$args  = wp_parse_args( $args, $defaults );

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = '';

		if( ! empty( $args['log_id'] ) ) {
			if( is_array( $args['log_id'] ) ) {
				$log_ids = implode( ',', $args['log_id'] );
			} else {
				$log_ids = intval( $args['log_id'] );
			}
			$where .= "WHERE `log_id` IN( {$log_ids} ) ";
		}

		if( ! empty( $args['object'] ) ) {
			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			if( is_array( $args['object'] ) ) {
				$where .= " `object` IN(" . implode( ',', $args['object'] ) . ") ";
			} else {
				if( ! empty( $args['search'] ) ) {
					$where .= " `object` LIKE '%%" . $args['object'] . "%%' ";
				} else {
					$where .= " `object` = '" . $args['object'] . "' ";
				}
			}
		}

		if( ! empty( $args['action'] ) ) {
			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			if( is_array( $args['action'] ) ) {
				$where .= " `action` IN(" . implode( ',', $args['action'] ) . ") ";
			} else {
				if( ! empty( $args['search'] ) ) {
					$where .= " `action` LIKE '%%" . $args['action'] . "%%' ";
				} else {
					$where .= " `action` = '" . $args['action'] . "' ";
				}
			}
		}

		if( ! empty( $args['queried'] ) ) {
			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			if( is_array( $args['queried'] ) ) {
				$where .= " `action` IN(" . implode( ',', $args['queried'] ) . ") ";
			} else {
				if( ! empty( $args['search'] ) ) {
					$where .= " `queried` LIKE '%%" . $args['queried'] . "%%' ";
				} else {
					$where .= " `queried` = '" . $args['queried'] . "' ";
				}
			}
		}

		if( ! empty( $args['date'] ) ) {
			if( is_array( $args['date'] ) ) {
				if( ! empty( $args['date']['start'] ) ) {
					if( false !== strpos( $args['date']['start'], ':' ) ) {
						$format = 'Y-m-d H:i:s';
					} else {
						$format = 'Y-m-d 00:00:00';
					}
					$start = date( $format, strtotime( $args['date']['start'] ) );
					if( ! empty( $where ) ) {
						$where .= " AND `date` >= '{$start}'";
					} else {
						$where .= " WHERE `date` >= '{$start}'";
					}
				}

				if( ! empty( $args['date']['end'] ) ) {
					if( false !== strpos( $args['date']['end'], ':' ) ) {
						$format = 'Y-m-d H:i:s';
					} else {
						$format = 'Y-m-d 23:59:59';
					}
					$end = date( $format, strtotime( $args['date']['end'] ) );
					if( ! empty( $where ) ) {
						$where .= " AND `date` <= '{$end}'";
					} else {
						$where .= " WHERE `date` <= '{$end}'";
					}
				}

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );
				if( empty( $where ) ) {
					$where .= " WHERE";
				} else {
					$where .= " AND";
				}
				$where .= " $year = YEAR ( date ) AND $month = MONTH ( date ) AND $day = DAY ( date )";
			}
		}

		$args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? $this->primary_key : $args['orderby'];
		if ( 'total' === $args['orderby'] ) {
			$args['orderby'] = 'total+0';
		} else if ( 'subtotal' === $args['orderby'] ) {
			$args['orderby'] = 'subtotal+0';
		}

		$cache_key = ( true === $count ) ? md5( 'affwp_zapier_logs_count' . serialize( $args ) ) : md5( 'affwp_zapier_logs_' . serialize( $args ) );

		$results = wp_cache_get( $cache_key, 'logs' );

		if ( false === $results ) {
			if ( true === $count ) {
				$results = absint( $wpdb->get_var( "SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};" ) );
			} else {
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
						absint( $args['offset'] ),
						absint( $args['number'] )
					)
				);
			}

			wp_cache_set( $cache_key, $results, 'logs', 3600 );

		}

		return $results;

	}

	/**
	 * Return the number of results found for a given Zapier log query.
	 *
	 * @since  1.0
	 * @param  array  $args
	 * @return int
	 */
	public function count( $args = array() ) {

		return $this->get_logs( $args, true );
	}

	/**
	 * Create the logs table
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function create_table() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
		log_id bigint(20) NOT NULL AUTO_INCREMENT,
		object tinytext NOT NULL,
		object_id bigint(20) NOT NULL,
		date datetime NOT NULL,
		action tinytext NOT NULL,
		queried boolean NOT NULL default 0,
		meta longtext NOT NULL,
		PRIMARY KEY  (log_id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";
		dbDelta( $sql );
		update_option( $this->table_name . '_db_version', $this->version );
	}

}
