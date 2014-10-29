<?php
/**
 * Single ticket page
 *
 * Allows a ticket to be created or edited
 *
 * @author Crystal Carr
 * @since 0.0.1
 *
 * @todo Validate input fields
 */


global $the_title;
$the_title= 'Submit Ticket';

include_once('header.php');?>

<div id="primary" class="content-area container">
      <div id="content" class="site-content col-lg-12 col-md-12" role="main">
        <div class="row">
          <article class="page type-page status-draft hentry col-lg-12 col-md-12 col-sm-12">
            <header class="entry-header">
              <h1 class="entry-title"><?php echo $the_title; ?></h1>
            </header><!-- .entry-header -->
              <div class="entry-content">
              <form class="col-xs-6" action="ticket.php" method="post" name="create_edit_ticket" id="create_edit_ticket">
                <input type="hidden" name="tkt_id" value="">
                <div class="form-group">
                  <label for="tkt_name">Ticket Name</label>
                  <input class="form-control" type="text" name="tkt_name" id="tkt_name" maxlength="45">
                </div>
                <input type="hidden" name="tkt_priority" value="">
                <div class="form-group">
                <label>Status:</label>
                  <select class="form-control" name="tkt_status" id="tkt_status" size="1">
                    <option value="Open">Open</option>
                    <option value="Close">Close</option>
                    <option value="Review">Review</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="tkt_desc">Description:</label>
                  <textarea class="form-control"></textarea>
                </div>
                <p><input type="submit" value="Sumbit">
              <a href="index.php">Cancel</a>
            </p>
              </form>

              </div> <!--.entry-content -->
          </article>
        </div> <!--.row -->
      </div> <!-- #content -->
    </div> <!-- #primary -->




<?php include_once('footer.php'); ?>

