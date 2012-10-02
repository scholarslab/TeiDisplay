<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Stylesheet browse.
 *
 * @package     omeka
 * @subpackage  teidisplay
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */
?>


<?php echo head(array('title' => __('TEI Display | Browse Stylesheets'))); ?>

<p class="add-button">
    <a class="add green button" href="<?php echo html_escape(url('tei-display/stylesheets/add')); ?>">
        <?php echo __('Upload a Stylesheet'); ?>
    </a>
</p>

<div id="primary">

<?php echo flash(); ?>

<?php if(has_stylesheets_for_loop()): ?>
<div class="pagination"><?php echo pagination_links(); ?></div>

<table class="tei-display">

    <thead>
        <tr>
        <!-- Column headings. -->
        <?php echo browse_headings(array(
            __('Title') => 'title',
            __('Modified') => 'modified',
            __('Actions') => null
        )); ?>
        </tr>
    </thead>

    <tbody>
        <!-- Stylesheet listings. -->
        <?php foreach(loop('TeiDisplayStylesheet') as $stylesheet): ?>
        <tr id="stylesheet-<?php echo stylesheet('id'); ?>">
            <td class="title"><?php echo link_to_stylesheet(null, array('class' => 'edit'), 'edit', null); ?></td>
            <td class="modified"><?php echo format_date(stylesheet('modified')); ?></td>
            <td class="actions">
                <?php echo link_to_stylesheet(__('Edit'), null, 'edit', null); ?>
                <?php echo link_to_stylesheet(__('Delete'), array('class' => 'delete delete-confirm'), 'delete-confirm', null); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>

</table>

<div class="pagination"><?php echo pagination_links(); ?></div>

<?php else: ?>

    <p class="tei-alert">
        <?php echo __('There are no stylesheets exhibits yet.'); ?>
        <a href="<?php echo url('tei-display/stylesheets/add'); ?>"><?php echo __('Upload one!'); ?></a>
    </p>

<?php endif; ?>

</div>

<?php echo foot(); ?>
