<?php
/**
 * Edit profile page
 *
 * Allows a registered moderator to edit profile
 *
 * @author Hannah Turner, Matt Beall
 * @since 0.2.0
 *
 * @todo Validate input fields
 */

global $the_title;
$the_title='Edit Profile';
include_once ('header.php');
global $moderator;
$mod_id=(int)$_REQUEST['profile'];
$moderator=get_moderator($mod_id);
$mod_first=get_moderator_first($moderator);
$mod_last=get_moderator_last($moderator);
$mod_email=get_moderator_email($moderator);
$mod_login_name=get_moderator_login_name($moderator);
?>

<div id="primary" class="content-area container">
      <div id="content" class="site-content col-lg-12 col-md-12" role="main">
        <div class="row">
          <article class="page type-page status-draft hentry col-lg-12 col-md-12 col-sm-12">
            <header class="entry-header">
              <h1 class="entry-title"><?php echo $the_title; ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">

<form class="form col-xs-6" name ="addEditForm" id="addEditForm" action="edit-profile.php" method="post" onsubmit="return checkForm(this)">

   <h4><?php echo $mod_login_name; ?></h4>

<?php
    <div class="form-group">
  <label for="firstname">First Name</label>
   <input type="text" name="mod_first" id ="firstname" value="<?php echo $mod_first; ?>" maxlength="20" class="form-control" required="required" pattern="^[a-zA-Z-]+$" title="First Name has invalid characters" /></div>
    <div class="form-group">
  <label for="lastname">Last Name</label>
   <input type="text" name="mod_last" id ="lastname" value="<?php echo $mod_last; ?>" maxlength="20" class="form-control" required="required" pattern="^[a-zA-Z-]+$" title="Last Name has invalid characters" /></div>
    <div class="form-group">
  <label for="email">Email</label>
   <input type="text" name="mod_email" id ="email" value="<?php echo $mod_email; ?>" maxlength="50" class="form-control" required="required" pattern="^[\w-\.]+@[\w]+\.[a-zA-Z]{2,4}$" title="Enter a valid email" /></div>
     <button class="btn btn-primary" type="submit">Update profile</button>
     <a class="btn btn-default" href="profile.php">Cancel</a>

</form>



 </div><!-- .entry-content -->
          </article>
        </div><!-- .row -->
      </div><!-- #content -->
    </div><!-- #primary -->

<?php include_once('footer.php'); ?>
