<?php
/**
 * Login page
 *
 * Allows a registered moderator to login
 *
 * @author Hannah Turner
 * @since 0.2.0
 *
 * @todo Validate input fields
 */

global $the_title;
$the_title='Login';
include_once ('header.php');

$mod_login_name = !empty($_POST['mod_login_name']) ? $_POST['mod_login_name'] : null;
$mod_pass = !empty($_POST['mod_pass']) ? $_POST['mod_pass'] : null;

if (!empty($mod_login_name) && !empty($mod_pass)) {
  login_moderator( $mod_login_name, $mod_pass );
}

if (isset($_REQUEST['logout'])) {
  logout_moderator();
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
                  <label for="mod_login_name">Moderatorname</label>
                  <input type="text" class="form-control" id="mod_login_name" name="mod_login_name" placeholder="Moderatorname">
                </div>
                <div class="form-group">
                  <label for="mod_pass">Password</label>
                  <input type="password" class="form-control" id="emod_pass" name="mod_pass" placeholder="Password">
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
