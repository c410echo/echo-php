<?php
/**
 * Defines class E_Ticket and related functions
 *
 * @author Matt Beall
 */

/**
 * Ticket class
 *
 * Connects to database and creates ticket object.
 *
 * @author Matt Beall
 * @since 0.0.3
 */
class E_Ticket {

  /**
   * @var int $u_id The ID of the user who created the ticket
   */
  public $u_id;

  /**
   * @var int $tkt_id The ID of the ticket
   */
  public $tkt_id;

  /**
   * @var string $tkt_name The name of the ticket
   */
  public $tkt_name = '';

  /**
   * @var string $tkt_desc The description of the ticket
   */
  public $tkt_desc = '';

  /**
   * @var string $tkt_priority The priority of the ticket
   */
  public $tkt_priority = '';

  /**
   * @var string $tkt_status The status of the ticket
   */
  public $tkt_status = '';

  /**
   * @var int $tkt_visible If 0, then ticket is "deleted", otherwise ticket is visible.
   */
  public $tkt_visible = 1;

  /**
   * Construct E_Ticket object
   *
   * Takes PDO and constructs E_Ticket class
   *
   * @since 0.0.3
   *
   * @param  object $tickets The PHP Data Object
   */
  public function __construct( $tickets ) {
    foreach ( $tickets as $ticket ) {
      get_class($ticket);
      foreach ( $ticket as $key => $value )
        $this->$key = $value;
    }
  }

  /**
   * Execute query
   *
   * Attempt to connect to database and execute SQL query
   * If successful, return results.
   *
   * @since 0.0.3
   *
   * @uses edb::connect()
   * @throws PDOException if connection or query cannot execute
   *
   * @param  string $query The SQL query to be executed
   * @return object        Data retrieved from database
   * @var    string $conn  The PHP Data Object
   */
  public static function query( $query ) {
    global $edb;
    $conn = $edb->connect();
    try {
      $query = $conn->query($query);
      do {
        if ($query->columnCount() > 0) {
            $results = $query->fetchAll(PDO::FETCH_OBJ);
        }
      }
      while ($query->nextRowset());

      $conn = null;

      return $results;
    }
    catch (PDOException $e) {
      $conn = null;
      die ('Query failed: ' . $e->getMessage());
    }
  }

  /**
   * Get ticket information from database
   *
   * Prepare and execute query to select ticket from database
   *
   * @since 0.0.3
   *
   * @uses self::query()
   *
   * @param  int    $tkt_id The primary key of the ticket being retrieved from the database
   * @return object         Data retrieved from database
   * @var    string $conn   The PHP Data Object for the connection
   */
  public static function get_instance( $tkt_id ) {
    global $edb;

    $tkt_id = (int) $tkt_id;

    if ( ! $tkt_id )
      return false;

    $_ticket = self::query("SELECT TOP 1 * FROM tickets WHERE tkt_id_PK = $tkt_id");

    return new E_Ticket ( $_ticket );
  }

  /**
   * Insert ticket into database
   *
   * Prepare and execute query to register ticket in tickets table
   *
   * @since 0.0.3
   *
   * @uses edb::insert()
   *
   * @param string $tkt_name     The title of the ticket
   * @param string $tkt_desc     The description of the ticket
   * @param string $tkt_priority The priority of the ticket
   * @param string $tkt_status   The status of the ticket
   *
   * @todo Add ability to specify tags
   * @todo Test
   */
  public static function set_instance( $tkt_name, $tkt_desc, $tkt_priority = 'normal', $tkt_status = 'open' ) {
    global $edb;

    $tkt_visible = 1;

    $edb->insert('tickets', 'tkt_name,tkt_desc,tkt_priority,tkt_status', "'$tkt_name', '$tkt_desc', '$tkt_priority', '$tkt_status', $tkt_visible" );
  }
}

/**
 * Get the E_Ticket class
 *
 * @since 0.0.3
 *
 * @uses E_Ticket::get_instance() Constructs E_Ticket class and gets class object
 *
 * @param  int    $tkt_id The ID of the ticket to get
 * @return object $ticket The E_Ticket class with the ticket's data
 */
function get_ticket( $tkt_id ) {
  $tkt_id = (int) $tkt_id;
  $ticket = E_Ticket::get_instance( $tkt_id );
  return $ticket;
}

/**
 * Get specific data from a ticket object
 *
 * @since 0.0.3
 *
 * @param  object $ticket The E_Ticket class containing the data for a ticket
 * @param  string $key    The name of the field to be retrieved
 * @return mixed          The value of the data retreived
 */
function get_ticket_data( $ticket, $key ) {
  if (!empty($ticket))
    return $ticket->$key;
  else
    echo 'ERROR: There is no data in the ticket object.';
    die;
}

/**
 * Get the title of the ticket
 *
 * @since 0.0.3
 *
 * @uses get_ticket_data()
 *
 * @param  object $ticket   The E_Ticket class containing the data for the ticket
 * @return string           The title of the ticket
 * @var    string $tkt_name The title of the ticket
 */
function get_ticket_name( $ticket ) {
  $tkt_name = get_ticket_data( $ticket , 'tkt_name' );
  return $tkt_name;
}

/**
 * Get the description of the ticket
 *
 * @since 0.0.3
 *
 * @uses get_ticket_data()
 *
 * @param  object $ticket   The E_Ticket class containing the data for the ticket
 * @return string           The description of the ticket
 * @var    string $tkt_desc The description of the ticket
 */
function get_ticket_desc( $ticket ) {
  $tkt_desc = get_ticket_data( $ticket , 'tkt_desc' );
  return $tkt_desc;
}

/**
 * Get the priority of the ticket
 *
 * @since 0.0.3
 *
 * @uses get_ticket_data()
 *
 * @param  object $ticket       The E_Ticket class containing the data for the ticket
 * @return string               The priority of the ticket
 * @var    string $tkt_priority The priority of the ticket
 */
function get_ticket_priority( $ticket ) {
  $tkt_priority = get_ticket_data( $ticket , 'tkt_priority' );
  return $tkt_priority;
}

/**
 * Get the status of the ticket
 *
 * @since 0.0.3
 *
 * @uses get_ticket_data()
 *
 * @param  object $ticket     The E_Ticket class containing the data for the ticket
 * @return string             The status of the ticket
 * @var    string $tkt_status The status of the ticket
 */
function get_ticket_status( $ticket ) {
  $tkt_status = get_ticket_data( $ticket , 'tkt_status' );
  return $tkt_status;
}
