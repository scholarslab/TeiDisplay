<?php
    head(array('title' => 'TEI Display File Configuration', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Manage TEI File Displays</h1>

<div id="primary">
	<?php echo flash(); ?>
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
		<div class="pagination"><?php echo pagination_links(); ?></div>
	    <table class="simple" cellspacing="0" cellpadding="0">
	            <thead>
	                <tr>
	                	<th>ID</th>
	                	<th>TEI ID</th>
	                    <th>Title</th>
	                    <th>Stylesheet</th>
	                    <th>Display Type</th>
						<th>Edit?</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach($entries as $entry): ?>
	                <tr>
						<td><?php echo html_escape($entry['id']); ?></td>
						<td><?php echo html_escape($entry['tei_id']); ?></td>
	                    <td><?php echo html_escape(tei_display_get_title($entry['file_id'])); ?></td>
	                    <td><?php echo html_escape($entry['stylesheet']); ?></td>
	                    <td><?php echo html_escape($entry['display_type']); ?></td>
						<td><a href="<?php echo html_escape(uri('tei-display/config/edit')); ?>?id=<?php echo $entry['id']; ?>" class="edit">Edit</a></td>
	                </tr>
	                <?php endforeach; ?>
	            </tbody>
	        </table>    
</div>

<?php 
    foot(); 
?>
