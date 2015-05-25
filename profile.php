<?php
/**
 * Profile page
 *
 * If logged in, this page allows moderator to edit own information.
 * If not logged in, or different moderator from current profile,
 * this page displays public information for a particular moderator.
 *
 * @author Hannah Turner
 * @since 0.2.0
 *
 * @todo Validate input fields
 */

global $the_title;
$the_title='Profile';
include_once ('header.php');
global $moderator;
$mod_id=(int)$_REQUEST['profile'];
$moderator=get_moderator($mod_id);
$mod_first=get_moderator_first($moderator);
$mod_last=get_moderator_last($moderator);
$mod_email=get_moderator_email($moderator);
$mod_login_name=get_moderator_login_name($moderator);?>

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
    <?php echo "$mod_first . $mod_last"; ?><br />
    <?php echo "$mod_login_name"; ?>
  <h1>My Contact Info</h1>
        <h5>Email:<?php echo "$mod_email"; ?></h5>
</section>


<FORM method="post" action="edit-profile.php">
<p>
  <input type="hidden" name="profile" value="<?php echo $moderator->mod_id_PK; ?>">
  <input type="submit"  value="Edit Profile" name="edit-profile" />
</p>
</FORM>

 </div><!-- .entry-content -->
          </article>
        </div><!-- .row -->
      </div><!-- #content -->
    </div><!-- #primary -->

<?php include_once('footer.php'); ?>
