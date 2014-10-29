<?php
/**
 * Register page
 *
 * Allows users to register
 *
 * @author Hannah Turner
 * @since 0.1.1
 *
 * @todo Validate input fields
 */

global $the_title;
$the_title='User Registration';
include_once ('header.php');

$u_login_name = !empty($_POST['u_login_name']) ? $_POST['u_login_name'] : null;
$u_pass = !empty($_POST['u_pass']) ? $_POST['u_pass'] : null;
$u_first = !empty($_POST['u_first']) ? $_POST['u_first'] : null;
$u_last = !empty($_POST['u_last']) ? $_POST['u_last'] : null;
$u_email = !empty($_POST['u_email']) ? $_POST['u_email'] : null;

if(!empty($u_login_name) && !empty($u_pass) && !empty($u_email)) {
  create_user($u_email, $u_login_name, $u_pass, $u_first, $u_last);
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

            <form role="form" action="register.php" method="post">
              <div class="form-group">
                <label for="u_login_name">Username</label>
                <input type="text" class="form-control" id="u_login_name" name="u_login_name" required>
              </div>
              <div class="form-group">
                <label for="u_pass">Password</label>
                <input type="password" class="form-control" id="u_pass" name="u_pass" required>
              </div>
              <div class="form-group">
                <label for="u_first">First Name</label>
                <input type="text" class="form-control" id="u_first" name="u_first">
              </div>
              <div class="form-group">
                <label for="u_last">Last Name</label>
                <input type="text" class="form-control" id="u_last" name="u_last">
              </div>
              <div class="form-group">
                <label for="u_email">Email address</label>
                <input type="email" class="form-control" id="u_email" name="u_email" required>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="reset" class="btn btn-default">Reset</button>
            </form>
            </div><!-- .entry-content -->
          </article>
        </div><!-- .row -->
      </div><!-- #content -->
    </div><!-- #primary -->
<?php include_once ('footer.php'); ?>
