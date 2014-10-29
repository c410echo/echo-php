<?php
/**
 * Login page
 *
 * Allows a registered user to login
 *
 * @author Hannah Turner
 * @since 0.1.1
 *
 * @todo Validate input fields
 */

global $the_title;
$the_title='Login';
include_once ('header.php');

$u_login_name = !empty($_POST['u_login_name']) ? $_POST['u_login_name'] : null;
$u_pass = !empty($_POST['u_pass']) ? $_POST['u_pass'] : null;

if (!empty($u_login_name) && !empty($u_pass)) {
  login_user( $u_login_name, $u_pass );
}

if (isset($_REQUEST['logout'])) {
  logout_user();
}

?>
<div id="primary" class="content-area container">
      <div id="content" class="site-content col-lg-12 col-md-12" role="main">
        <div class="row">
          <article class="page type-page status-draft hentry col-lg-12 col-md-12 col-sm-12">
            <header class="entry-header">
              <h1 class="entry-title"><?php echo $the_title; ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">
              <form role="form" action="login.php" method="post">
                <div class="form-group">
                  <label for="u_login_name">Username</label>
                  <input type="text" class="form-control" id="u_login_name" name="u_login_name" placeholder="Username">
                </div>
                <div class="form-group">
                  <label for="u_pass">Password</label>
                  <input type="password" class="form-control" id="eu_pass" name="u_pass" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <a class="btn btn-default" href="register.php">Register</a>
              </form>
            </div><!-- .entry-content -->
          </article>
        </div><!-- .row -->
      </div><!-- #content -->
    </div><!-- #primary -->
<?php include_once ('footer.php'); ?>
