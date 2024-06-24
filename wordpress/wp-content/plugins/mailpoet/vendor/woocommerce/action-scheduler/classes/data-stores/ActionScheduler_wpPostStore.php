<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_wpPostStore extends ActionScheduler_Store {
 const POST_TYPE = 'scheduled-action';
 const GROUP_TAXONOMY = 'action-group';
 const SCHEDULE_META_KEY = '_action_manager_schedule';
 const DEPENDENCIES_MET = 'as-post-store-dependencies-met';
 private $claim_before_date = null;
 protected $local_timezone = null;
 public function save_action( ActionScheduler_Action $action, DateTime $scheduled_date = null ) {
 try {
 $this->validate_action( $action );
 $post_array = $this->create_post_array( $action, $scheduled_date );
 $post_id = $this->save_post_array( $post_array );
 $this->save_post_schedule( $post_id, $action->get_schedule() );
 $this->save_action_group( $post_id, $action->get_group() );
 do_action( 'action_scheduler_stored_action', $post_id );
 return $post_id;
 } catch ( Exception $e ) {
 throw new RuntimeException( sprintf( __( 'Error saving action: %s', 'action-scheduler' ), $e->getMessage() ), 0 );
 }
 }
 protected function create_post_array( ActionScheduler_Action $action, DateTime $scheduled_date = null ) {
 $post = array(
 'post_type' => self::POST_TYPE,
 'post_title' => $action->get_hook(),
 'post_content' => wp_json_encode( $action->get_args() ),
 'post_status' => ( $action->is_finished() ? 'publish' : 'pending' ),
 'post_date_gmt' => $this->get_scheduled_date_string( $action, $scheduled_date ),
 'post_date' => $this->get_scheduled_date_string_local( $action, $scheduled_date ),
 );
 return $post;
 }
 protected function save_post_array( $post_array ) {
 add_filter( 'wp_insert_post_data', array( $this, 'filter_insert_post_data' ), 10, 1 );
 add_filter( 'pre_wp_unique_post_slug', array( $this, 'set_unique_post_slug' ), 10, 5 );
 $has_kses = false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' );
 if ( $has_kses ) {
 // Prevent KSES from corrupting JSON in post_content.
 kses_remove_filters();
 }
 $post_id = wp_insert_post( $post_array );
 if ( $has_kses ) {
 kses_init_filters();
 }
 remove_filter( 'wp_insert_post_data', array( $this, 'filter_insert_post_data' ), 10 );
 remove_filter( 'pre_wp_unique_post_slug', array( $this, 'set_unique_post_slug' ), 10 );
 if ( is_wp_error( $post_id ) || empty( $post_id ) ) {
 throw new RuntimeException( __( 'Unable to save action.', 'action-scheduler' ) );
 }
 return $post_id;
 }
 public function filter_insert_post_data( $postdata ) {
 if ( self::POST_TYPE === $postdata['post_type'] ) {
 $postdata['post_author'] = 0;
 if ( 'future' === $postdata['post_status'] ) {
 $postdata['post_status'] = 'publish';
 }
 }
 return $postdata;
 }
 public function set_unique_post_slug( $override_slug, $slug, $post_ID, $post_status, $post_type ) {
 if ( self::POST_TYPE === $post_type ) {
 $override_slug = uniqid( self::POST_TYPE . '-', true ) . '-' . wp_generate_password( 32, false );
 }
 return $override_slug;
 }
 protected function save_post_schedule( $post_id, $schedule ) {
 update_post_meta( $post_id, self::SCHEDULE_META_KEY, $schedule );
 }
 protected function save_action_group( $post_id, $group ) {
 if ( empty( $group ) ) {
 wp_set_object_terms( $post_id, array(), self::GROUP_TAXONOMY, false );
 } else {
 wp_set_object_terms( $post_id, array( $group ), self::GROUP_TAXONOMY, false );
 }
 }
 public function fetch_action( $action_id ) {
 $post = $this->get_post( $action_id );
 if ( empty( $post ) || self::POST_TYPE !== $post->post_type ) {
 return $this->get_null_action();
 }
 try {
 $action = $this->make_action_from_post( $post );
 } catch ( ActionScheduler_InvalidActionException $exception ) {
 do_action( 'action_scheduler_failed_fetch_action', $post->ID, $exception );
 return $this->get_null_action();
 }
 return $action;
 }
 protected function get_post( $action_id ) {
 if ( empty( $action_id ) ) {
 return null;
 }
 return get_post( $action_id );
 }
 protected function get_null_action() {
 return new ActionScheduler_NullAction();
 }
 protected function make_action_from_post( $post ) {
 $hook = $post->post_title;
 $args = json_decode( $post->post_content, true );
 $this->validate_args( $args, $post->ID );
 $schedule = get_post_meta( $post->ID, self::SCHEDULE_META_KEY, true );
 $this->validate_schedule( $schedule, $post->ID );
 $group = wp_get_object_terms( $post->ID, self::GROUP_TAXONOMY, array( 'fields' => 'names' ) );
 $group = empty( $group ) ? '' : reset( $group );
 return ActionScheduler::factory()->get_stored_action( $this->get_action_status_by_post_status( $post->post_status ), $hook, $args, $schedule, $group );
 }
 protected function get_action_status_by_post_status( $post_status ) {
 switch ( $post_status ) {
 case 'publish':
 $action_status = self::STATUS_COMPLETE;
 break;
 case 'trash':
 $action_status = self::STATUS_CANCELED;
 break;
 default:
 if ( ! array_key_exists( $post_status, $this->get_status_labels() ) ) {
 throw new InvalidArgumentException( sprintf( 'Invalid post status: "%s". No matching action status available.', $post_status ) );
 }
 $action_status = $post_status;
 break;
 }
 return $action_status;
 }
 protected function get_post_status_by_action_status( $action_status ) {
 switch ( $action_status ) {
 case self::STATUS_COMPLETE:
 $post_status = 'publish';
 break;
 case self::STATUS_CANCELED:
 $post_status = 'trash';
 break;
 default:
 if ( ! array_key_exists( $action_status, $this->get_status_labels() ) ) {
 throw new InvalidArgumentException( sprintf( 'Invalid action status: "%s".', $action_status ) );
 }
 $post_status = $action_status;
 break;
 }
 return $post_status;
 }
 protected function get_query_actions_sql( array $query, $select_or_count = 'select' ) {
 if ( ! in_array( $select_or_count, array( 'select', 'count' ), true ) ) {
 throw new InvalidArgumentException( __( 'Invalid schedule. Cannot save action.', 'action-scheduler' ) );
 }
 $query = wp_parse_args(
 $query,
 array(
 'hook' => '',
 'args' => null,
 'date' => null,
 'date_compare' => '<=',
 'modified' => null,
 'modified_compare' => '<=',
 'group' => '',
 'status' => '',
 'claimed' => null,
 'per_page' => 5,
 'offset' => 0,
 'orderby' => 'date',
 'order' => 'ASC',
 'search' => '',
 )
 );
 global $wpdb;
 $sql = ( 'count' === $select_or_count ) ? 'SELECT count(p.ID)' : 'SELECT p.ID ';
 $sql .= "FROM {$wpdb->posts} p";
 $sql_params = array();
 if ( empty( $query['group'] ) && 'group' === $query['orderby'] ) {
 $sql .= " LEFT JOIN {$wpdb->term_relationships} tr ON tr.object_id=p.ID";
 $sql .= " LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id=tt.term_taxonomy_id";
 $sql .= " LEFT JOIN {$wpdb->terms} t ON tt.term_id=t.term_id";
 } elseif ( ! empty( $query['group'] ) ) {
 $sql .= " INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id=p.ID";
 $sql .= " INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id=tt.term_taxonomy_id";
 $sql .= " INNER JOIN {$wpdb->terms} t ON tt.term_id=t.term_id";
 $sql .= ' AND t.slug=%s';
 $sql_params[] = $query['group'];
 }
 $sql .= ' WHERE post_type=%s';
 $sql_params[] = self::POST_TYPE;
 if ( $query['hook'] ) {
 $sql .= ' AND p.post_title=%s';
 $sql_params[] = $query['hook'];
 }
 if ( ! is_null( $query['args'] ) ) {
 $sql .= ' AND p.post_content=%s';
 $sql_params[] = wp_json_encode( $query['args'] );
 }
 if ( $query['status'] ) {
 $post_statuses = array_map( array( $this, 'get_post_status_by_action_status' ), (array) $query['status'] );
 $placeholders = array_fill( 0, count( $post_statuses ), '%s' );
 $sql .= ' AND p.post_status IN (' . join( ', ', $placeholders ) . ')';
 $sql_params = array_merge( $sql_params, array_values( $post_statuses ) );
 }
 if ( $query['date'] instanceof DateTime ) {
 $date = clone $query['date'];
 $date->setTimezone( new DateTimeZone( 'UTC' ) );
 $date_string = $date->format( 'Y-m-d H:i:s' );
 $comparator = $this->validate_sql_comparator( $query['date_compare'] );
 $sql .= " AND p.post_date_gmt $comparator %s";
 $sql_params[] = $date_string;
 }
 if ( $query['modified'] instanceof DateTime ) {
 $modified = clone $query['modified'];
 $modified->setTimezone( new DateTimeZone( 'UTC' ) );
 $date_string = $modified->format( 'Y-m-d H:i:s' );
 $comparator = $this->validate_sql_comparator( $query['modified_compare'] );
 $sql .= " AND p.post_modified_gmt $comparator %s";
 $sql_params[] = $date_string;
 }
 if ( true === $query['claimed'] ) {
 $sql .= " AND p.post_password != ''";
 } elseif ( false === $query['claimed'] ) {
 $sql .= " AND p.post_password = ''";
 } elseif ( ! is_null( $query['claimed'] ) ) {
 $sql .= ' AND p.post_password = %s';
 $sql_params[] = $query['claimed'];
 }
 if ( ! empty( $query['search'] ) ) {
 $sql .= ' AND (p.post_title LIKE %s OR p.post_content LIKE %s OR p.post_password LIKE %s)';
 for ( $i = 0; $i < 3; $i++ ) {
 $sql_params[] = sprintf( '%%%s%%', $query['search'] );
 }
 }
 if ( 'select' === $select_or_count ) {
 switch ( $query['orderby'] ) {
 case 'hook':
 $orderby = 'p.post_title';
 break;
 case 'group':
 $orderby = 't.name';
 break;
 case 'status':
 $orderby = 'p.post_status';
 break;
 case 'modified':
 $orderby = 'p.post_modified';
 break;
 case 'claim_id':
 $orderby = 'p.post_password';
 break;
 case 'schedule':
 case 'date':
 default:
 $orderby = 'p.post_date_gmt';
 break;
 }
 if ( 'ASC' === strtoupper( $query['order'] ) ) {
 $order = 'ASC';
 } else {
 $order = 'DESC';
 }
 $sql .= " ORDER BY $orderby $order";
 if ( $query['per_page'] > 0 ) {
 $sql .= ' LIMIT %d, %d';
 $sql_params[] = $query['offset'];
 $sql_params[] = $query['per_page'];
 }
 }
 return $wpdb->prepare( $sql, $sql_params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
 }
 public function query_actions( $query = array(), $query_type = 'select' ) {
 global $wpdb;
 $sql = $this->get_query_actions_sql( $query, $query_type );
 return ( 'count' === $query_type ) ? $wpdb->get_var( $sql ) : $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
 }
 public function action_counts() {
 $action_counts_by_status = array();
 $action_stati_and_labels = $this->get_status_labels();
 $posts_count_by_status = (array) wp_count_posts( self::POST_TYPE, 'readable' );
 foreach ( $posts_count_by_status as $post_status_name => $count ) {
 try {
 $action_status_name = $this->get_action_status_by_post_status( $post_status_name );
 } catch ( Exception $e ) {
 // Ignore any post statuses that aren't for actions.
 continue;
 }
 if ( array_key_exists( $action_status_name, $action_stati_and_labels ) ) {
 $action_counts_by_status[ $action_status_name ] = $count;
 }
 }
 return $action_counts_by_status;
 }
 public function cancel_action( $action_id ) {
 $post = get_post( $action_id );
 if ( empty( $post ) || ( self::POST_TYPE !== $post->post_type ) ) {
 throw new InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'action-scheduler' ), $action_id ) );
 }
 do_action( 'action_scheduler_canceled_action', $action_id );
 add_filter( 'pre_wp_unique_post_slug', array( $this, 'set_unique_post_slug' ), 10, 5 );
 wp_trash_post( $action_id );
 remove_filter( 'pre_wp_unique_post_slug', array( $this, 'set_unique_post_slug' ), 10 );
 }
 public function delete_action( $action_id ) {
 $post = get_post( $action_id );
 if ( empty( $post ) || ( self::POST_TYPE !== $post->post_type ) ) {
 throw new InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'action-scheduler' ), $action_id ) );
 }
 do_action( 'action_scheduler_deleted_action', $action_id );
 wp_delete_post( $action_id, true );
 }
 public function get_date( $action_id ) {
 $next = $this->get_date_gmt( $action_id );
 return ActionScheduler_TimezoneHelper::set_local_timezone( $next );
 }
 public function get_date_gmt( $action_id ) {
 $post = get_post( $action_id );
 if ( empty( $post ) || ( self::POST_TYPE !== $post->post_type ) ) {
 throw new InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'action-scheduler' ), $action_id ) );
 }
 if ( 'publish' === $post->post_status ) {
 return as_get_datetime_object( $post->post_modified_gmt );
 } else {
 return as_get_datetime_object( $post->post_date_gmt );
 }
 }
 public function stake_claim( $max_actions = 10, DateTime $before_date = null, $hooks = array(), $group = '' ) {
 $this->claim_before_date = $before_date;
 $claim_id = $this->generate_claim_id();
 $this->claim_actions( $claim_id, $max_actions, $before_date, $hooks, $group );
 $action_ids = $this->find_actions_by_claim_id( $claim_id );
 $this->claim_before_date = null;
 return new ActionScheduler_ActionClaim( $claim_id, $action_ids );
 }
 public function get_claim_count() {
 global $wpdb;
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
 return $wpdb->get_var(
 $wpdb->prepare(
 "SELECT COUNT(DISTINCT post_password) FROM {$wpdb->posts} WHERE post_password != '' AND post_type = %s AND post_status IN ('in-progress','pending')",
 array( self::POST_TYPE )
 )
 );
 }
 protected function generate_claim_id() {
 $claim_id = md5( microtime( true ) . wp_rand( 0, 1000 ) );
 return substr( $claim_id, 0, 20 ); // to fit in db field with 20 char limit.
 }
 protected function claim_actions( $claim_id, $limit, DateTime $before_date = null, $hooks = array(), $group = '' ) {
 // Set up initial variables.
 $date = null === $before_date ? as_get_datetime_object() : clone $before_date;
 $limit_ids = ! empty( $group );
 $ids = $limit_ids ? $this->get_actions_by_group( $group, $limit, $date ) : array();
 // If limiting by IDs and no posts found, then return early since we have nothing to update.
 if ( $limit_ids && 0 === count( $ids ) ) {
 return 0;
 }
 global $wpdb;
 $update = "UPDATE {$wpdb->posts} SET post_password = %s, post_modified_gmt = %s, post_modified = %s";
 $params = array(
 $claim_id,
 current_time( 'mysql', true ),
 current_time( 'mysql' ),
 );
 // Build initial WHERE clause.
 $where = "WHERE post_type = %s AND post_status = %s AND post_password = ''";
 $params[] = self::POST_TYPE;
 $params[] = ActionScheduler_Store::STATUS_PENDING;
 if ( ! empty( $hooks ) ) {
 $placeholders = array_fill( 0, count( $hooks ), '%s' );
 $where .= ' AND post_title IN (' . join( ', ', $placeholders ) . ')';
 $params = array_merge( $params, array_values( $hooks ) );
 }
 if ( $limit_ids ) {
 $where .= ' AND ID IN (' . join( ',', $ids ) . ')';
 } else {
 $where .= ' AND post_date_gmt <= %s';
 $params[] = $date->format( 'Y-m-d H:i:s' );
 }
 // Add the ORDER BY clause and,ms limit.
 $order = 'ORDER BY menu_order ASC, post_date_gmt ASC, ID ASC LIMIT %d';
 $params[] = $limit;
 // Run the query and gather results.
 $rows_affected = $wpdb->query( $wpdb->prepare( "{$update} {$where} {$order}", $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
 if ( false === $rows_affected ) {
 throw new RuntimeException( __( 'Unable to claim actions. Database error.', 'action-scheduler' ) );
 }
 return (int) $rows_affected;
 }
 protected function get_actions_by_group( $group, $limit, DateTime $date ) {
 // Ensure the group exists before continuing.
 if ( ! term_exists( $group, self::GROUP_TAXONOMY ) ) {
 throw new InvalidArgumentException( sprintf( __( 'The group "%s" does not exist.', 'action-scheduler' ), $group ) );
 }
 // Set up a query for post IDs to use later.
 $query = new WP_Query();
 $query_args = array(
 'fields' => 'ids',
 'post_type' => self::POST_TYPE,
 'post_status' => ActionScheduler_Store::STATUS_PENDING,
 'has_password' => false,
 'posts_per_page' => $limit * 3,
 'suppress_filters' => true, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.SuppressFilters_suppress_filters
 'no_found_rows' => true,
 'orderby' => array(
 'menu_order' => 'ASC',
 'date' => 'ASC',
 'ID' => 'ASC',
 ),
 'date_query' => array(
 'column' => 'post_date_gmt',
 'before' => $date->format( 'Y-m-d H:i' ),
 'inclusive' => true,
 ),
 'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
 array(
 'taxonomy' => self::GROUP_TAXONOMY,
 'field' => 'slug',
 'terms' => $group,
 'include_children' => false,
 ),
 ),
 );
 return $query->query( $query_args );
 }
 public function find_actions_by_claim_id( $claim_id ) {
 global $wpdb;
 $action_ids = array();
 $before_date = isset( $this->claim_before_date ) ? $this->claim_before_date : as_get_datetime_object();
 $cut_off = $before_date->format( 'Y-m-d H:i:s' );
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 $results = $wpdb->get_results(
 $wpdb->prepare(
 "SELECT ID, post_date_gmt FROM {$wpdb->posts} WHERE post_type = %s AND post_password = %s",
 array(
 self::POST_TYPE,
 $claim_id,
 )
 )
 );
 // Verify that the scheduled date for each action is within the expected bounds (in some unusual
 // cases, we cannot depend on MySQL to honor all of the WHERE conditions we specify).
 foreach ( $results as $claimed_action ) {
 if ( $claimed_action->post_date_gmt <= $cut_off ) {
 $action_ids[] = absint( $claimed_action->ID );
 }
 }
 return $action_ids;
 }
 public function release_claim( ActionScheduler_ActionClaim $claim ) {
 $action_ids = $this->find_actions_by_claim_id( $claim->get_id() );
 if ( empty( $action_ids ) ) {
 return; // nothing to do.
 }
 $action_id_string = implode( ',', array_map( 'intval', $action_ids ) );
 global $wpdb;
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 $result = $wpdb->query(
 $wpdb->prepare(
 "UPDATE {$wpdb->posts} SET post_password = '' WHERE ID IN ($action_id_string) AND post_password = %s", //phpcs:ignore
 array(
 $claim->get_id(),
 )
 )
 );
 if ( false === $result ) {
 throw new RuntimeException( sprintf( __( 'Unable to unlock claim %s. Database error.', 'action-scheduler' ), $claim->get_id() ) );
 }
 }
 public function unclaim_action( $action_id ) {
 global $wpdb;
 //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 $result = $wpdb->query(
 $wpdb->prepare(
 "UPDATE {$wpdb->posts} SET post_password = '' WHERE ID = %d AND post_type = %s",
 $action_id,
 self::POST_TYPE
 )
 );
 if ( false === $result ) {
 throw new RuntimeException( sprintf( __( 'Unable to unlock claim on action %s. Database error.', 'action-scheduler' ), $action_id ) );
 }
 }
 public function mark_failure( $action_id ) {
 global $wpdb;
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 $result = $wpdb->query(
 $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d AND post_type = %s", self::STATUS_FAILED, $action_id, self::POST_TYPE )
 );
 if ( false === $result ) {
 throw new RuntimeException( sprintf( __( 'Unable to mark failure on action %s. Database error.', 'action-scheduler' ), $action_id ) );
 }
 }
 public function get_claim_id( $action_id ) {
 return $this->get_post_column( $action_id, 'post_password' );
 }
 public function get_status( $action_id ) {
 $status = $this->get_post_column( $action_id, 'post_status' );
 if ( null === $status ) {
 throw new InvalidArgumentException( __( 'Invalid action ID. No status found.', 'action-scheduler' ) );
 }
 return $this->get_action_status_by_post_status( $status );
 }
 private function get_post_column( $action_id, $column_name ) {
 global $wpdb;
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 return $wpdb->get_var(
 $wpdb->prepare(
 "SELECT {$column_name} FROM {$wpdb->posts} WHERE ID=%d AND post_type=%s", // phpcs:ignore
 $action_id,
 self::POST_TYPE
 )
 );
 }
 public function log_execution( $action_id ) {
 global $wpdb;
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 $status_updated = $wpdb->query(
 $wpdb->prepare(
 "UPDATE {$wpdb->posts} SET menu_order = menu_order+1, post_status=%s, post_modified_gmt = %s, post_modified = %s WHERE ID = %d AND post_type = %s",
 self::STATUS_RUNNING,
 current_time( 'mysql', true ),
 current_time( 'mysql' ),
 $action_id,
 self::POST_TYPE
 )
 );
 if ( ! $status_updated ) {
 throw new Exception(
 sprintf(
 __( 'Unable to update the status of action %1$d to %2$s.', 'action-scheduler' ),
 $action_id,
 self::STATUS_RUNNING
 )
 );
 }
 }
 public function mark_complete( $action_id ) {
 $post = get_post( $action_id );
 if ( empty( $post ) || ( self::POST_TYPE !== $post->post_type ) ) {
 throw new InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'action-scheduler' ), $action_id ) );
 }
 add_filter( 'wp_insert_post_data', array( $this, 'filter_insert_post_data' ), 10, 1 );
 add_filter( 'pre_wp_unique_post_slug', array( $this, 'set_unique_post_slug' ), 10, 5 );
 $result = wp_update_post(
 array(
 'ID' => $action_id,
 'post_status' => 'publish',
 ),
 true
 );
 remove_filter( 'wp_insert_post_data', array( $this, 'filter_insert_post_data' ), 10 );
 remove_filter( 'pre_wp_unique_post_slug', array( $this, 'set_unique_post_slug' ), 10 );
 if ( is_wp_error( $result ) ) {
 throw new RuntimeException( $result->get_error_message() );
 }
 do_action( 'action_scheduler_completed_action', $action_id );
 }
 public function mark_migrated( $action_id ) {
 wp_update_post(
 array(
 'ID' => $action_id,
 'post_status' => 'migrated',
 )
 );
 }
 public function migration_dependencies_met( $setting ) {
 global $wpdb;
 $dependencies_met = get_transient( self::DEPENDENCIES_MET );
 if ( empty( $dependencies_met ) ) {
 $maximum_args_length = apply_filters( 'action_scheduler_maximum_args_length', 191 );
 $found_action = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 $wpdb->prepare(
 "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND CHAR_LENGTH(post_content) > %d LIMIT 1",
 $maximum_args_length,
 self::POST_TYPE
 )
 );
 $dependencies_met = $found_action ? 'no' : 'yes';
 set_transient( self::DEPENDENCIES_MET, $dependencies_met, DAY_IN_SECONDS );
 }
 return 'yes' === $dependencies_met ? $setting : false;
 }
 protected function validate_action( ActionScheduler_Action $action ) {
 try {
 parent::validate_action( $action );
 } catch ( Exception $e ) {
 $message = sprintf( __( '%s Support for strings longer than this will be removed in a future version.', 'action-scheduler' ), $e->getMessage() );
 _doing_it_wrong( 'ActionScheduler_Action::$args', esc_html( $message ), '2.1.0' );
 }
 }
 public function init() {
 add_filter( 'action_scheduler_migration_dependencies_met', array( $this, 'migration_dependencies_met' ) );
 $post_type_registrar = new ActionScheduler_wpPostStore_PostTypeRegistrar();
 $post_type_registrar->register();
 $post_status_registrar = new ActionScheduler_wpPostStore_PostStatusRegistrar();
 $post_status_registrar->register();
 $taxonomy_registrar = new ActionScheduler_wpPostStore_TaxonomyRegistrar();
 $taxonomy_registrar->register();
 }
}
