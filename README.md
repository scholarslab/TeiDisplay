

  About TeiDisplay 

----------

TeiDisplay is a plugin created by the [Scholars' Lab][1]  at the University of Virginia Library.  This will render an uploaded TEI file attached to an item in the display.  The default XSLT stylesheet allows for two display types: entire and segmental.  The entire display type will render out the entire document in HTML while the segmental display type includes a table of contents for displaying a selected div1 or div2, which is a useful feature for larger documents.  The display type and XSLT stylesheet can be customized for each TEI File in the database through the TEI Config tab in the administrative interface.  Additionally, metadata from the TEI Header is automatically mapped to Dublin Core fields for both the item and file.

  Requirements in the TEI Document 

----------

The TEI document requires an id attribute in the TEI.2 root element.  For the table of contents in the segmental display to work properly, each div1, div2, or div element at the top two levels under <front> or <body> must also have an id attribute since generate-id() does not provide the consistent results in PHP-XSL as it does in Xalan or Saxon.  The Entire display type will function without ids attached to divs.

###   Required Yum or Aptitude Packages 
 ###

*  php-xml

*  php-xsl

  Download 

----------

* Subversion: [https://addons.omeka.org/svn/plugins/TeiDisplay/trunk/][2]

* Package: [TeiDisplay 0.9][3]

  Installing and Configuring 

----------

1.  The php5-xsl package is required for transformation.  Please refer to Google for directions for installing the package on your operating system.

2.  Checkout from svn or download/extract zipped package to omeka/plugins (see [Installing_a_Plugin][4]).

3.  Install TeiDisplay on the Settings->Plugins page.

4.  Use the plugin Configure page to set the default stylesheet (default.xsl is packaged in libraries) and the display type (entire by default).

5.  XML uploads need to be enabled.  Go to admin Settings->Security Settings and add 'xml' to allowed file extensions and 'text/xml,application/xml' to allowed file types.

6.  To upload a new TEI file, create an item.  Select an appropriate TEI file to upload from the disk.  Save the item, and metadata will be pulled from the header automatically.  The Type field in the File metadata is set to 'TEI Document.'

7.  The TEI Config tab can be used to change the default display behavior for each TEI XML File

8.  Edit the item display for your theme in themes/[your theme]/items/show.php .  Add the following code where you wish the TEI document to be serialized (e. g., under the titles and above show_item_metadata()):

<?php if (function_exists('tei_display_installed')){ echo render_tei_files($item->id,  $_GET['section']); } ?>

  XSLT Stylesheet Customization 

----------

The TeiDisplay/libraries folder contains XSLT stylesheets made available to the plugin for serialization customization.  All stylesheets require two parameters, one for the display type ("entire" and "segmental") and the other used in the segmental display for section.  Refer to default.xsl in the folder for an example.  Below is the root template:

	<xsl:param name="display"/>
	<xsl:param name="section"/>

	<xsl:template match="/">
		<div id="tei_display">
			<xsl:choose>
				<xsl:when test="$display = 'entire'">
					<xsl:apply-templates select="//body"/>
				</xsl:when>
				<xsl:when test="$display='segmental'">
					<div class="tei_toc">
						<xsl:call-template name="toc"/>
					</div>
					<div class="tei_content">
						<xsl:choose>
							<xsl:when test="string($section)">
								<xsl:apply-templates select="descendant::node()[@id=$section]"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:apply-templates select="//front"/>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:when>
			</xsl:choose>
		</div>
	</xsl:template>

If one is familiar with XSLT, he or she can see that if the "entire" display type is selected, a template is called on the TEI <body>.  If the display type is "segmental", the table of contents and TEI content divs are constructed.  The style of div.tei_toc and div.tei_content is controlled in the plugin's public CSS file, and therefore customized stylesheets should include divs of these classes unless the CSS file will be modified.

  FedoraConnector/TeiDisplay Integration 

----------

As of December 2010, the TeiDisplay plugin integrates with FedoraConnector plugin functions (if the plugin is installed) to render TEI datastreams directly from a Fedora repository.  The plugins assume a Text Encoding Initiative XML datastream is designated the id "TEI" in Fedora.  This can be modified in TeiDisplay/plugin.php tei_display_install() and FedoraConnector/plugin.php render_fedora_datastream().  Like Omeka Files, TEI files can be configured for segmental/entire display and custom stylesheets.

  Other Notes 

----------

It is possible to upload multiple TEI files per item, but it is not recommended.  The documents can be displayed in their entirety, but problems will arise attempting to render multiple TEI documents in Segmental display mode.  The metadata from multiple files will populate the item metadata, but that metadata is not removed from the item if a file is deleted.

<!-- 
NewPP limit report
Preprocessor node count: 15/1000000
Post-expand include size: 0/2097152 bytes
Template argument size: 0/2097152 bytes
Expensive parser function count: 0/100
-->

Retrieved from "[http://omeka.org/codex/Plugins/TeiDisplay](http://omeka.org/codex/Plugins/TeiDisplay)"

[1]: http://scholarslab.org/ "http://scholarslab.org/"
[2]: https://addons.omeka.org/svn/plugins/TeiDisplay/trunk/ "https://addons.omeka.org/svn/plugins/TeiDisplay/trunk/"
[3]: http://www.scholarslab.org/wp-content/uploads/2010/09/TeiDisplay-0.9.zip "http://www.scholarslab.org/wp-content/uploads/2010/09/TeiDisplay-0.9.zip"
[4]: /codex/Installing_a_Plugin "Installing a Plugin"
