<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Edit a stylesheet.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */
?>


<?php echo head(array('title' => __('TEI Display | Edit Stylesheet'))); ?>

<div id="primary">
    <?php echo flash(); ?>
    <?php echo $form; ?>
</div>

<?php echo foot(); ?>
