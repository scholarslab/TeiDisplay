<?php
    head(array('title' => 'TEI Display File Configuration', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Configure TEI File Display</h1>

<div id="primary">
	<h2>Title: <?php echo tei_display_get_title($file_id) ?></h2>
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
     <?php echo $form; ?>
</div>

<?php 
    foot(); 
?>
