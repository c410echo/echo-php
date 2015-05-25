<?php
/**
 * Profile page
 *
 * If logged in, this page allows user to edit own information.
 * If not logged in, or different user from current profile,
 * this page displays public information for a particular user.
 *
 * @author Hannah Turner
 * @since 0.1.0
 *
 * @todo Validate input fields
 */

global $the_title;
$the_title='Profile';
include_once ('header.php');
global $user;
$u_id=(int)$_REQUEST['profile'];
$user=get_user($u_id);
$u_first=get_user_first($user);
$u_last=get_user_last($user);
$u_email=get_user_email($user);
$u_login_name=get_user_login_name($user);?>

<div id="primary" class="content-area container">
      <div id="content" class="site-content col-lg-12 col-md-12" role="main">
        <div class="row">
          <article class="page type-page status-draft hentry col-lg-12 col-md-12 col-sm-12">
            <header class="entry-header">
              <h1 class="entry-title"><?php echo $the_title; ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">

<section>
  <h1>My Profile</h1>
    <?php echo "$u_first $u_last"; ?><br />
    <?php echo "$u_login_name"; ?>
  <h1>My Contact Info</h1>
        <h5>Email:<?php echo "$u_email"; ?></h5>
</section>


<FORM method="post" action="edit-profile.php">
<p>
  <input type="hidden" name="profile" value="<?php echo $user->u_id; ?>">
  <input type="submit"  value="Edit Profile" name="edit-profile" />
</p>
</FORM>

 </div><!-- .entry-content -->
          </article>
        </div><!-- .row -->
      </div><!-- #content -->
    </div><!-- #primary -->

<?php include_once('footer.php'); ?>
