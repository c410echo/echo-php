<?php
/**
 * Edit tag page
 *
 * Allows a registered user to create or edit a tag
 *
 * @author Crystal Carr
 * @since  0.1.0
 *
 * @todo Validate input fields
 */
global $the_title;
$the_title= 'Edit a Tag';

include_once('header.php');
global $tag;
$tag_id = (int)$_REQUEST['tag_id'];
$tag = get_tag($tag_id);
$tag_name = get_tag_name($tag);
$tag_color = get_tag_color($tag);
$tag_bg = get_tag_bg($tag);

$tag_name      = !empty($_POST['tag_name'    ]) ? $_POST['tag_name'    ] : '';
$tag_color     = !isset($_POST['tag_color'    ]) ? $_POST['tag_color'    ] : '';
$tag_bg       = !isset($_POST['tag_bg']) ? $_POST['tag_bg'] : '';


if(!empty($tag_name) && !isset($tag_color) && !isset($tag_bg)) {
  update_tag($tag_name, $tag_color, $tag_bg);
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
               <?php if (is_admin()) { ?>

              <form class="col-xs-6" action="edit-tag.php" method="post" name="edit_tag" id="edit_tag">
                    <input type="hidden" name="tag_id" value="">
                    <div class="form-group">
                      <label for="tag_name">Tag Name</label>
                      <input class="form-control" type="text" name="tag_name" id="tag_name" maxlength="32" value="<?php echo $tag_name; ?>">
                    </div>
                    <div class="form-group">
                      <label for="tag_color">Tag Color</label>
                      <input type="text" name="tag_color" Pattern ="^#+([a-fA-F0-9]{6}"  maxlength="7" value="<?php echo $tag_color; ?>">
                    </div>
                    <div class="form-group">
                      <label for="tag_bg">Tag Background</label>
                      <input type="text" name="tag_bg" Pattern ="^#+([a-fA-F0-9]{6}"  maxlength="7" value="<? echo $tag_bg; ?>">
                    </div>
                   <p><input type="submit" value="Sumbit">
                  <a href="index.php">Cancel</a>
                </p>
                  </form>

              <?php }else {  ?>

          <?php ('Refresh: 5, URL=login.php');
            echo '<h2> You need to be logged in to Edit a tag.';
            ?>

          <?php  } ?>

              </div> <!--.entry-content -->
          </article>
        </div> <!--.row -->
      </div> <!-- #content -->
    </div> <!-- #primary -->
