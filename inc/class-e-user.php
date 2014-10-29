<?php
/**
 * Defines class E_User and related functions
 *
 * @author Matt Beall
 */

/**
 * User class
 *
 * Connects to database and creates user object.
 *
 * @author Matt Beall
 * @since 0.1.1
 */
class E_User {

  /**
   * @var int $u_id_PK The ID of the user
   */
  public $u_id_PK;

  /**
   * @var string $u_ip The IP address of the user
   */
  public $u_ip = '';

  /**
   * @var int $u_admin If 1, user is admin; else, user is not admin.
   */
  public $u_admin = 0;

  /**
   * @var int $u_visible If 0, user has been "deleted"; else, user is visible.
   */
  public $u_visible = 1;

  /**
   * @var string $u_first The first name of the user
   */
  public $u_first = '';

  /**
   * @var string $u_last The last name of the user
   */
  public $u_last = '';

  /**
   * @var string $u_email The email address of the user
   */
  public $u_email = '';

  /**
   * @var string $u_login_name The login name that the user uses to login
   */
  public $u_login_name = '';

  /**
   * @var string $u_pass The password that the user uses to login
   */
  public $u_pass = '';

  /**
   * Construct E_User object
   *
   * Takes PDO and constructs E_User class
   *
   * @since 0.0.1
   *
   * @param  object $users The PHP Data Object
   */
  public function __construct( $users ) {
    foreach ( $users as $user ) {
      get_class($user);
      foreach ( $user as $key => $value )
        $this->$key = $value;
    }
  }

  /**
   * Execute query
   *
   * Attempt to connect to database and execute SQL query
   * If successful, return results.
   *
   * @since 0.0.1
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
   * Get user information from database
   *
   * Prepare and execute query to select user from database
   *
   * @since 0.0.1
   *
   * @uses self::query()
   *
   * @param  int    $u_id The primary key of the user being retrieved from the database
   * @return object       Data retrieved from database
   * @var    string $conn The PHP Data Object for the connection
   */
  public static function get_instance( $u_id ) {
    global $edb;

    $u_id = (int) $u_id;

    if ( ! $u_id )
      return false;

    $_user = self::query("SELECT TOP 1 * FROM users LEFT JOIN registered_users ON u_id_PK = reg_u_id_PK_FK WHERE u_id_PK = $u_id");

    return new E_User ( $_user );
  }

  /**
   * Register user in database
   *
   * Prepare and execute query to register user in registered_users table
   *
   * @since 0.0.4
   *
   * @uses self::get_user_id()
   * @uses self::login_name_exists()
   * @uses self::email_exists()
   * @uses edb::insert()
   *
   * @param string $u_email      The requested email address for the registered user
   * @param string $u_login_name The requested username for the registered user
   * @param string $u_pass       The password for the registered user
   * @param string $u_first      The first name of the registered user
   * @param string $u_last       The last name of the registered user
   *
   * @return void
   *
   * @var int $u_id The primary key of the user being registered, as created in user database
   *
   * @todo Test
   */
  public static function new_instance( $u_email, $u_login_name, $u_pass, $u_first = null, $u_last = null ) {
    global $edb;

    $u_email      = _email( $u_email     , 64 );
    $u_login_name = _text ( $u_login_name, 32 );
    $u_pass       = _text ( $u_pass      , 32 );
    $u_first      = _text ( $u_first     , 32 );
    $u_last       = _text ( $u_last      , 32 );
    $u_ip         = $_SERVER['REMOTE_ADDR'];
    $u_admin      = 0;
    $u_visible    = 1;

    $edb->insert('users', 'u_ip,u_admin,u_visible', "'$u_ip', $u_admin, $u_visible" );
    $u_id = (int) self::get_user_id( $u_ip );
    if (!empty($u_id)) {
      if (self::login_name_exists( $u_login_name )) {
        echo '<div class="alert alert-danger"><strong>Username unavailable.</strong> Please enter a different username.</div>';
      }
      elseif (self::email_exists( $u_email )) {
        echo '<div class="alert alert-danger">An account with this email address already exists.</div>';
      }
      else {
        $edb->insert( 'registered_users', 'reg_u_id_PK_FK,u_first,u_last,u_email,u_login_name,u_pass', "$u_id,'$u_first','$u_last','$u_email','$u_login_name','$u_pass'" );
        header("Location: login.php?new=1");
        exit;
      }
    }
  }

  /**
   * Update user in database
   *
   * Prepare and execute query to update user in registered_users table
   *
   * @since 0.0.4
   *
   * @uses edb::update()
   *
   * @param int    $u_id         The ID of the user to update
   * @param string $u_email      The requested email address for the registered user
   * @param string $u_login_name The requested username for the registered user
   * @param string $u_pass       The password for the registered user
   * @param string $u_first      The first name of the registered user
   * @param string $u_last       The last name of the registered user
   * @param int    $u_admin      If 1, user is admin; else, user is not admin.
   * @param int    $u_visible    If 0, user has been "deleted"; else, user is visible.
   *
   * @return void
   *
   * @var int $u_id The primary key of the user being registered, as created in user database
   *
   * @todo Test
   */
  public static function set_instance( $u_id, $u_email, $u_login_name, $u_pass, $u_first = null, $u_last = null, $u_admin = 0, $u_visible = 1 ) {
    global $edb;

    $u_id = (int) $u_id;

    $_user = self::get_instance( $u_id );

    $u_email      = !empty($u_email)      ? _email( $u_email     , 64 ) : $_user->u_email;
    $u_login_name = !empty($u_login_name) ? _text ( $u_login_name, 32 ) : $_user->u_login_name;
    $u_pass       = !empty($u_pass)       ? _text ( $u_pass      , 32 ) : $_user->u_pass;
    $u_first      = !empty($u_first)      ? _text ( $u_first     , 32 ) : $_user->u_first;
    $u_last       = !empty($u_last)       ? _text ( $u_last      , 32 ) : $_user->u_last;

    $u_admin      = !empty($u_admin)   ? (int) $u_admin   : (int) $_user->u_admin;
    $u_visible    = !empty($u_visible) ? (int) $u_visible : (int) $_user->u_visible;

    $edb->update('users', 'u_admin,u_visible', "$u_admin, $u_visible", "u_id_PK = $u_id" );
    $edb->update('registered_users', 'u_email, u_login_name, u_pass, u_first, u_last', "$u_email, $u_login_name, $u_pass, $u_first, $u_last", "reg_u_id_PK_FK = $u_id" );
  }

  /**
   * Checks to see if email address is already in use
   *
   * @since 0.0.1
   *
   * @uses edb::select Queries database
   *
   * @param  string     $u_email The email address to search for
   * @return true|false          If true, the email address exists; else, false.
   * @var    object     $users   The user(s), if any, that use the email address in $u_email
   */
  private static function email_exists( $u_email ) {
    global $edb;
    $users = $edb->select('registered_users', 'reg_u_id_PK_FK,u_email', "u_email = '$u_email'");
    if (!empty($users))
      return true;
    else
      return false;
  }

  /**
   * Checks to see if login name is already in use
   *
   * @since 0.0.1
   *
   * @uses edb::select Queries database
   *
   * @param  string     $u_login_name The email address to search for
   * @return true|false               If true, the login name is already taken; else, false.
   * @var    object     $users        The user(s), if any, that use the login name in $u_login_name
   */
  private static function login_name_exists( $u_login_name ) {
    global $edb;
    $users = $edb->select('registered_users', 'reg_u_id_PK_FK,u_login_name', "u_login_name = '$u_login_name'");
    if (!empty($users))
      return true;
    else
      return false;
  }

  /**
   * Retrieve user's id that matches an IP address
   *
   * @since 0.0.1
   *
   * @uses self::query() to query the database
   *
   * @param  string     $u_ip  The IP address to check for
   * @return int               The ID of the user
   * @var    object     $users The user(s), if any, that have the IP address
   */
  private static function get_user_id( $u_ip ) {
    global $edb;
    $users = self::query("SELECT TOP 1 * FROM users WHERE u_ip = '$u_ip' ORDER BY u_id_PK DESC");
    foreach ( $users as $user ) {
        get_class($user);
        foreach ( $user as $key => $value )
          $key = $value;
    }
    $u_id = (int) $user->u_id_PK;
    return $u_id;
  }

  /**
   * Retrieve user's id that matches an IP address
   *
   * @since 0.0.1
   *
   * @uses self::query() to query the database
   *
   * @param  string     $u_ip  The IP address to check for
   * @return int               The ID of the user
   * @var    object     $users The user(s), if any, that have the IP address
   */
  public static function authenticate_user( $u_login_name, $u_pass ) {
    global $edb;
    $users = self::query("SELECT TOP 1 * FROM users JOIN registered_users ON u_id_PK = reg_u_id_PK_FK WHERE u_login_name = '$u_login_name' AND u_pass = '$u_pass' ORDER BY u_id_PK DESC");
    foreach ( $users as $user ) {
        get_class($user);
        foreach ( $user as $key => $value )
          $key = $value;
    }
    $u_id = (int) $user->u_id_PK;
    if ($u_id > 0)
      return $u_id;
    else
      return false;
  }
}

/**
 * Create user
 *
 * @since 0.0.4
 *
 * @uses E_User::new_instance() Constructs E_User class and inserts into database
 *
 * @param string $u_email      The requested email address for the registered user
 * @param string $u_login_name The requested username for the registered user
 * @param string $u_pass       The password for the registered user
 * @param string $u_first      The first name of the registered user
 * @param string $u_last       The last name of the registered user
 */
function create_user( $u_email, $u_login_name, $u_pass, $u_first = null, $u_last = null ) {
  $user = E_User::new_instance( $u_email, $u_login_name, $u_pass, $u_first, $u_last );
  return $user;
}

/**
 * Update user
 *
 * @since 0.0.4
 *
 * @uses E_User::set_instance() Constructs E_User class and updates in database
 *
 * @param int    $u_id         The ID of the user to update
 * @param string $u_email      The requested email address for the registered user
 * @param string $u_login_name The requested username for the registered user
 * @param string $u_pass       The password for the registered user
 * @param string $u_first      The first name of the registered user
 * @param string $u_last       The last name of the registered user
 * @param int    $u_admin      If 1, user is admin; else, user is not admin.
 * @param int    $u_visible    If 0, user has been "deleted"; else, user is visible.
 */
function update_user( $u_id, $u_email = null, $u_login_name = null, $u_pass = null, $u_first = null, $u_last = null, $u_admin = null, $u_visible = null ) {
  $user = E_User::set_instance( $u_id, $u_email, $u_login_name, $u_pass, $u_first, $u_last, $u_admin, $u_visible );
  return $user;
}

/**
 * Get the E_User class
 *
 * @since 0.0.1
 *
 * @uses E_User::get_instance() Constructs E_User class and gets class object
 *
 * @param  int    $u_id The ID of the user to get
 * @return object $user The E_User class with the user's data
 */
function get_user( $u_id ) {
  $u_id = (int) $u_id;
  $user = E_User::get_instance( $u_id );
  return $user;
}

/**
 * Get specific data from a user object
 *
 * @since 0.0.1
 *
 * @param  object $user The E_User class containing the data for a user
 * @param  string $key  The name of the field to be retrieved
 * @return mixed        The value of the data retreived
 */
function get_user_data( $user, $key ) {
  if (!empty($user))
    return $user->$key;
  else
    echo 'ERROR: There is no data in the user object.';
    die;
}

/**
 * Get the IP address of the user
 *
 * @since 0.0.1
 *
 * @uses get_user_data()
 *
 * @param  object $user The E_User class containing the data for the user
 * @return string       The IP address of the user
 * @var    string $u_ip The IP address of the user
 */
function get_user_ip( $user ) {
  $u_ip = get_user_data( $user , 'u_ip' );
  return $u_ip;
}

/**
 * Get the first name of the user
 *
 * @since 0.0.1
 *
 * @uses get_user_data()
 *
 * @param  object $user    The E_User class containing the data for the user
 * @return string          The first name of the user
 * @var    string $u_first The first name of the user
 */
function get_user_first( $user ) {
  $u_first = get_user_data( $user , 'u_first' );
  return $u_first;
}

/**
 * Get the last name of the user
 *
 * @since 0.0.1
 *
 * @uses get_user_data()
 *
 * @param  object $user   The E_User class containing the data for the user
 * @return string         The last name of the user
 * @var    string $u_last The last name of the user
 */
function get_user_last( $user ) {
  $u_last = get_user_data( $user , 'u_last' );
  return $u_last;
}

/**
 * Get the login name of the user
 *
 * @since 0.0.1
 *
 * @uses get_user_data()
 *
 * @param  object $user         The E_User class containing the data for the user
 * @return string               The login name of the user
 * @var    string $u_login_name The login name of the user
 */
function get_user_login_name( $user ) {
  $u_login_name = get_user_data( $user , 'u_login_name' );
  return $u_login_name;
}

/**
 * Get the email address of the user
 *
 * @since 0.0.1
 *
 * @uses get_user_data()
 *
 * @param  object $user    The E_User class containing the data for the user
 * @return string          The email address of the user
 * @var    string $u_email The email address of the user
 */
function get_user_email( $user ) {
  $u_email = get_user_data( $user , 'u_email' );
  return $u_email;
}

/**
 * Check if the user is an admin user
 *
 * @since 0.0.4
 *
 * @uses get_user_data()
 *
 * @param  object $user    The E_User class containing the data for the user
 * @return bool
 * @var    int    $u_admin If 1, user is admin; else, user is not admin.
 */
function is_user_admin( $user ) {
  $u_admin = get_user_data( $user , 'u_admin' );
  $u_admin = (int) $u_admin;

  if ($u_admin == 1)
    return true;
  else
    return false;
}

/**
 * Check if the user is visible or not
 *
 * @since 0.0.4
 *
 * @uses get_user_data()
 *
 * @param  object $user      The E_User class containing the data for the user
 * @return bool
 * @var    int    $u_visible If 0, user has been "deleted"; else, user is visible.
 */
function is_user_visible( $user ) {
  $u_visible = get_user_data( $user , 'u_visible' );
  $u_visible = (int) $u_visible;

  if ($u_visible == 1)
    return true;
  else
    return false;
}

/** @since 0.1.1 */
function login_user( $username, $password ) {
  $u_login_name = _text( $username );
  $u_pass = _text( $password );

  $u_id = E_User::authenticate_user($u_login_name, $u_pass);
  $u_id = (int) $u_id;
  if ($u_id > 0) {
    $_SESSION['u_id'] = $u_id;
	$_SESSION['u_login_name'] = $u_login_name;
    
    header("Location: profile.php?profile=$u_id");
    exit;
  }
  else {
    header("Location: login.php?invalid=1");
    exit;
  }
}

function logout_user() {
session_start();
unset($_SESSION['u_id']);
unset($_SESSION['u_login_name']);
session_destroy();
}
