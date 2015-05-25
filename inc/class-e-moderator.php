<?php
/**
 * Defines class E_Moderator and related functions
 *
 * @author Matt Beall
 */

/**
 * Moderator class
 *
 * Connects to database and creates moderator object.
 *
 * @author Matt Beall
 * @since 0.2.0
 */
class E_Moderator {

  /**
   * @var int $mod_id_PK The ID of the moderator
   */
  public $mod_id_PK;

  /**
   * @var string $mod_ip The IP address of the moderator
   */
  public $mod_ip = '';

  /**
   * @var int $mod_admin If 1, moderator is admin; else, moderator is not admin.
   */
  public $mod_admin = 0;

  /**
   * @var string $mod_first The first name of the moderator
   */
  public $mod_first = '';

  /**
   * @var string $mod_last The last name of the moderator
   */
  public $mod_last = '';

  /**
   * @var string $mod_email The email address of the moderator
   */
  public $mod_email = '';

  /**
   * @var string $mod_login_name The login name that the moderator uses to login
   */
  public $mod_login_name = '';

  /**
   * @var string $mod_pass The password that the moderator uses to login
   */
  public $mod_pass = '';

  /**
   * Construct E_Moderator object
   *
   * Takes PDO and constructs E_Moderator class
   *
   * @since 0.2.0
   *
   * @param  object $moderators The PHP Data Object
   */
  public function __construct( $moderators ) {
    foreach ( $moderators as $moderator ) {
      get_class($moderator);
      foreach ( $moderator as $key => $value )
        $this->$key = $value;
    }
  }

  /**
   * Execute query
   *
   * Attempt to connect to database and execute SQL query
   * If successful, return results.
   *
   * @since 0.2.0
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
   * Get moderator information from database
   *
   * Prepare and execute query to select moderator from database
   *
   * @since 0.2.0
   *
   * @uses self::query()
   *
   * @param  int    $mod_id The primary key of the moderator being retrieved from the database
   * @return object       Data retrieved from database
   * @var    string $conn The PHP Data Object for the connection
   */
  public static function get_instance( $mod_id ) {
    global $edb;

    $mod_id = (int) $mod_id;

    if ( ! $mod_id )
      return false;

    $_moderator = self::query("SELECT * FROM moderators WHERE mod_id_PK = $mod_id LIMIT 1");

    return new E_Moderator ( $_moderator );
  }

  /**
   * Register moderator in database
   *
   * Prepare and execute query to register moderator in moderators table
   *
   * @since 0.2.0
   *
   * @uses self::get_moderator_id()
   * @uses self::login_name_exists()
   * @uses self::email_exists()
   * @uses edb::insert()
   *
   * @param string $mod_email      The requested email address for the moderator
   * @param string $mod_login_name The requested moderatorname for the moderator
   * @param string $mod_pass       The password for the moderator
   * @param string $mod_first      The first name of the moderator
   * @param string $mod_last       The last name of the moderator
   *
   * @return void
   *
   * @var int $mod_id The primary key of the moderator being registered, as created in moderator database
   *
   * @todo Test
   */
  public static function new_instance( $mod_email, $mod_login_name, $mod_pass, $mod_first = null, $mod_last = null ) {
    global $edb;

    $mod_email      = _email( $mod_email     , 64 );
    $mod_login_name = _text ( $mod_login_name, 32 );
    $mod_pass       = _text ( $mod_pass      , 32 );
    $mod_first      = _text ( $mod_first     , 32 );
    $mod_last       = _text ( $mod_last      , 32 );
    $mod_ip         = $_SERVER['REMOTE_ADDR'];
    $mod_admin      = 0;

    $edb->insert('moderators', 'mod_ip,mod_admin', "'$mod_ip', $mod_admin" );
    $mod_id = (int) self::get_moderator_id( $mod_ip );
    if (!empty($mod_id)) {
      if (self::login_name_exists( $mod_login_name )) {
        echo '<div class="alert alert-danger"><strong>Moderatorname unavailable.</strong> Please enter a different moderatorname.</div>';
      }
      elseif (self::email_exists( $mod_email )) {
        echo '<div class="alert alert-danger">An account with this email address already exists.</div>';
      }
      else {
        $edb->insert( 'moderators', 'reg_mod_id_PK_FK,mod_first,mod_last,mod_email,mod_login_name,mod_pass', "$mod_id,'$mod_first','$mod_last','$mod_email','$mod_login_name','$mod_pass'" );
        header("Location: login.php?new=1");
        exit;
      }
    }
  }

  /**
   * Update moderator in database
   *
   * Prepare and execute query to update moderator in moderators table
   *
   * @since 0.2.0
   *
   * @uses edb::update()
   *
   * @param int    $mod_id         The ID of the moderator to update
   * @param string $mod_email      The requested email address for the moderator
   * @param string $mod_login_name The requested moderatorname for the moderator
   * @param string $mod_pass       The password for the moderator
   * @param string $mod_first      The first name of the moderator
   * @param string $mod_last       The last name of the moderator
   * @param int    $mod_admin      If 1, moderator is admin; else, moderator is not admin.
   *
   * @return void
   *
   * @var int $mod_id The primary key of the moderator being registered, as created in moderator database
   *
   * @todo Test
   */
  public static function set_instance( $mod_id, $mod_email, $mod_login_name, $mod_pass, $mod_first = null, $mod_last = null, $mod_admin = 0 ) {
    global $edb;

    $mod_id = (int) $mod_id;

    $_moderator = self::get_instance( $mod_id );

    $mod_email      = !empty($mod_email)      ? _email( $mod_email     , 64 ) : $_moderator->mod_email;
    $mod_login_name = !empty($mod_login_name) ? _text ( $mod_login_name, 32 ) : $_moderator->mod_login_name;
    $mod_pass       = !empty($mod_pass)       ? _text ( $mod_pass      , 32 ) : $_moderator->mod_pass;
    $mod_first      = !empty($mod_first)      ? _text ( $mod_first     , 32 ) : $_moderator->mod_first;
    $mod_last       = !empty($mod_last)       ? _text ( $mod_last      , 32 ) : $_moderator->mod_last;

    $mod_admin      = !empty($mod_admin)   ? (int) $mod_admin   : (int) $_moderator->mod_admin;

    $edb->update('moderators', 'mod_admin', "$mod_admin", "mod_id_PK = $mod_id" );
    $edb->update('moderators', 'mod_email, mod_login_name, mod_pass, mod_first, mod_last', "$mod_email, $mod_login_name, $mod_pass, $mod_first, $mod_last", "reg_mod_id_PK_FK = $mod_id" );
  }

  /**
   * Checks to see if email address is already in use
   *
   * @since 0.2.0
   *
   * @uses edb::select Queries database
   *
   * @param  string     $mod_email The email address to search for
   * @return true|false          If true, the email address exists; else, false.
   * @var    object     $moderators   The moderator(s), if any, that use the email address in $mod_email
   */
  private static function email_exists( $mod_email ) {
    global $edb;
    $moderators = $edb->select('moderators', 'reg_mod_id_PK_FK,mod_email', "mod_email = '$mod_email'");
    if (!empty($moderators))
      return true;
    else
      return false;
  }

  /**
   * Checks to see if login name is already in use
   *
   * @since 0.2.0
   *
   * @uses edb::select Queries database
   *
   * @param  string     $mod_login_name The email address to search for
   * @return true|false               If true, the login name is already taken; else, false.
   * @var    object     $moderators        The moderator(s), if any, that use the login name in $mod_login_name
   */
  private static function login_name_exists( $mod_login_name ) {
    global $edb;
    $moderators = $edb->select('moderators', 'reg_mod_id_PK_FK,mod_login_name', "mod_login_name = '$mod_login_name'");
    if (!empty($moderators))
      return true;
    else
      return false;
  }

  /**
   * Retrieve moderator's id that matches an IP address
   *
   * @since 0.2.0
   *
   * @uses self::query() to query the database
   *
   * @param  string     $mod_ip  The IP address to check for
   * @return int               The ID of the moderator
   * @var    object     $moderators The moderator(s), if any, that have the IP address
   */
  private static function get_moderator_id( $mod_ip ) {
    global $edb;
    $moderators = self::query("SELECT * FROM moderators WHERE mod_ip = '$mod_ip' ORDER BY mod_id_PK DESC LIMIT 1");
    foreach ( $moderators as $moderator ) {
        get_class($moderator);
        foreach ( $moderator as $key => $value )
          $key = $value;
    }
    $mod_id = (int) $moderator->mod_id_PK;
    return $mod_id;
  }

  /**
   * Retrieve moderator's id that matches an IP address
   *
   * @since 0.2.0
   *
   * @uses self::query() to query the database
   *
   * @param  string     $mod_ip  The IP address to check for
   * @return int               The ID of the moderator
   * @var    object     $moderators The moderator(s), if any, that have the IP address
   */
  public static function authenticate_moderator( $mod_login_name, $mod_pass ) {
    global $edb;
    $moderators = self::query("SELECT * FROM moderators WHERE mod_login_name = '$mod_login_name' AND mod_pass = '$mod_pass' ORDER BY mod_id_PK DESC LIMIT 1");
    foreach ( $moderators as $moderator ) {
        get_class($moderator);
        foreach ( $moderator as $key => $value )
          $key = $value;
    }
    $mod_id = (int) $moderator->mod_id_PK;
    if ($mod_id > 0)
      return $mod_id;
    else
      return false;
  }
}

/**
 * Create moderator
 *
 * @since 0.2.0
 *
 * @uses E_Moderator::new_instance() Constructs E_Moderator class and inserts into database
 *
 * @param string $mod_email      The requested email address for the moderator
 * @param string $mod_login_name The requested moderatorname for the moderator
 * @param string $mod_pass       The password for the moderator
 * @param string $mod_first      The first name of the moderator
 * @param string $mod_last       The last name of the moderator
 */
function create_moderator( $mod_email, $mod_login_name, $mod_pass, $mod_first = null, $mod_last = null ) {
  $moderator = E_Moderator::new_instance( $mod_email, $mod_login_name, $mod_pass, $mod_first, $mod_last );
  return $moderator;
}

/**
 * Update moderator
 *
 * @since 0.2.0
 *
 * @uses E_Moderator::set_instance() Constructs E_Moderator class and updates in database
 *
 * @param int    $mod_id         The ID of the moderator to update
 * @param string $mod_email      The requested email address for the moderator
 * @param string $mod_login_name The requested moderatorname for the moderator
 * @param string $mod_pass       The password for the moderator
 * @param string $mod_first      The first name of the moderator
 * @param string $mod_last       The last name of the moderator
 * @param int    $mod_admin      If 1, moderator is admin; else, moderator is not admin.
 */
function update_moderator( $mod_id, $mod_email = null, $mod_login_name = null, $mod_pass = null, $mod_first = null, $mod_last = null, $mod_admin = null ) {
  $moderator = E_Moderator::set_instance( $mod_id, $mod_email, $mod_login_name, $mod_pass, $mod_first, $mod_last, $mod_admin );
  return $moderator;
}

/**
 * Get the E_Moderator class
 *
 * @since 0.2.0
 *
 * @uses E_Moderator::get_instance() Constructs E_Moderator class and gets class object
 *
 * @param  int    $mod_id The ID of the moderator to get
 * @return object $moderator The E_Moderator class with the moderator's data
 */
function get_moderator( $mod_id ) {
  $mod_id = (int) $mod_id;
  $moderator = E_Moderator::get_instance( $mod_id );
  return $moderator;
}

/**
 * Get specific data from a moderator object
 *
 * @since 0.2.0
 *
 * @param  object $moderator The E_Moderator class containing the data for a moderator
 * @param  string $key  The name of the field to be retrieved
 * @return mixed        The value of the data retreived
 */
function get_moderator_data( $moderator, $key ) {
  if (!empty($moderator))
    return $moderator->$key;
  else
    echo 'ERROR: There is no data in the moderator object.';
    die;
}

/**
 * Get the IP address of the moderator
 *
 * @since 0.2.0
 *
 * @uses get_moderator_data()
 *
 * @param  object $moderator The E_Moderator class containing the data for the moderator
 * @return string       The IP address of the moderator
 * @var    string $mod_ip The IP address of the moderator
 */
function get_moderator_ip( $moderator ) {
  $mod_ip = get_moderator_data( $moderator , 'mod_ip' );
  return $mod_ip;
}

/**
 * Get the first name of the moderator
 *
 * @since 0.2.0
 *
 * @uses get_moderator_data()
 *
 * @param  object $moderator    The E_Moderator class containing the data for the moderator
 * @return string          The first name of the moderator
 * @var    string $mod_first The first name of the moderator
 */
function get_moderator_first( $moderator ) {
  $mod_first = get_moderator_data( $moderator , 'mod_first' );
  return $mod_first;
}

/**
 * Get the last name of the moderator
 *
 * @since 0.2.0
 *
 * @uses get_moderator_data()
 *
 * @param  object $moderator   The E_Moderator class containing the data for the moderator
 * @return string         The last name of the moderator
 * @var    string $mod_last The last name of the moderator
 */
function get_moderator_last( $moderator ) {
  $mod_last = get_moderator_data( $moderator , 'mod_last' );
  return $mod_last;
}

/**
 * Get the login name of the moderator
 *
 * @since 0.2.0
 *
 * @uses get_moderator_data()
 *
 * @param  object $moderator         The E_Moderator class containing the data for the moderator
 * @return string               The login name of the moderator
 * @var    string $mod_login_name The login name of the moderator
 */
function get_moderator_login_name( $moderator ) {
  $mod_login_name = get_moderator_data( $moderator , 'mod_login_name' );
  return $mod_login_name;
}

/**
 * Get the email address of the moderator
 *
 * @since 0.2.0
 *
 * @uses get_moderator_data()
 *
 * @param  object $moderator    The E_Moderator class containing the data for the moderator
 * @return string          The email address of the moderator
 * @var    string $mod_email The email address of the moderator
 */
function get_moderator_email( $moderator ) {
  $mod_email = get_moderator_data( $moderator , 'mod_email' );
  return $mod_email;
}

/**
 * Check if the moderator is an admin moderator
 *
 * @since 0.2.0
 *
 * @uses get_moderator_data()
 *
 * @param  object $moderator    The E_Moderator class containing the data for the moderator
 * @return bool
 * @var    int    $mod_admin If 1, moderator is admin; else, moderator is not admin.
 */
function is_moderator_admin( $moderator ) {
  $mod_admin = get_moderator_data( $moderator , 'mod_admin' );
  $mod_admin = (int) $mod_admin;

  if ($mod_admin == 1)
    return true;
  else
    return false;
}

/** @since 0.2.0 */
function login_moderator( $moderatorname, $password ) {
  $mod_login_name = _text( $moderatorname );
  $mod_pass = _text( $password );

  $mod_id = E_Moderator::authenticate_moderator($mod_login_name, $mod_pass);
  $mod_id = (int) $mod_id;
  if ($mod_id > 0) {
    $_SESSION['mod_id'] = $mod_id;
	$_SESSION['mod_login_name'] = $mod_login_name;
    
    header("Location: profile.php?profile=$mod_id");
    exit;
  }
  else {
    header("Location: login.php?invalid=1");
    exit;
  }
}

function logout_moderator() {
session_start();
unset($_SESSION['mod_id']);
unset($_SESSION['mod_login_name']);
session_destroy();
}
