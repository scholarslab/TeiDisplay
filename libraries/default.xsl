<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0">
	<xsl:param name="display"/>
	<xsl:param name="section"/>

	<!-- include component.xsl, created by CDL for XTF -->
	<xsl:include href="includes/component.xsl"/>

	<xsl:template match="/">
		<div id="tei_display">
			<xsl:choose>
				<xsl:when test="$display = 'entire'">
					<xsl:apply-templates select="//*[local-name()='text']"/>
				</xsl:when>
				<xsl:when test="$display='segmental'">
					<div class="tei_toc">
						<xsl:call-template name="toc"/>
					</div>
					<div class="tei_content">
						<xsl:choose>
							<xsl:when test="string($section)">
								<xsl:apply-templates select="descendant::node()[concat(count(ancestor::node()), '0000', count(preceding::node()))=$section]"/>
							</xsl:when>
							<xsl:otherwise>
								<p>
									<b>Select a section from the table of contents on the left.</b>
								</p>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:when>
			</xsl:choose>
		</div>
	</xsl:template>

	<xsl:template name="toc">
		<h4>
			<a href="?section={concat(count(/TEI.2/ancestor::node()), '0000', count(/TEI.2/preceding::node()))}">View Entire Document</a>
		</h4>
		<xsl:if test="//*[local-name()='front']">
			<h4>Front</h4>
			<ul>
				<xsl:apply-templates
					select="descendant::*[local-name()='front']/*[local-name()='div1'] | descendant::*[local-name()='front']/*[local-name()='div']"
					mode="toc"/>
			</ul>
		</xsl:if>

		<h4>Body</h4>
		<ul>
			<xsl:apply-templates
				select="descendant::*[local-name()='body']/*[local-name()='div1'] | descendant::*[local-name()='body']/*[local-name()='div']"
				mode="toc"/>
		</ul>
	</xsl:template>

	<xsl:template match="*[local-name()='div'] | *[local-name()='div1'] | *[local-name()='div2']"
		mode="toc">
		<li>
			<xsl:if test="@type">
				<span class="toc_type">
					<xsl:value-of select="@type"/>
				</span>
				<xsl:text>: </xsl:text>
			</xsl:if>
			<xsl:variable name="title">
				<xsl:choose>
					<xsl:when test="string(normalize-space(*[local-name()='head']))">
						<xsl:value-of select="normalize-space(*[local-name()='head'][1])"/>
					</xsl:when>
					<xsl:otherwise>[No Title]</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="$section=concat(count(ancestor::node()), '0000', count(preceding::node()))">
					<b>
						<xsl:value-of select="$title"/>
					</b>
				</xsl:when>
				<xsl:otherwise>
					<a href="?section={concat(count(ancestor::node()), '0000', count(preceding::node()))}">
						<xsl:value-of select="$title"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if
				test="child::*[local-name()='div2'] or (child::*[local-name()='div'] and (parent::*[local-name()='front'] or parent::*[local-name()='body']))">
				<xsl:variable name="ids">
					<xsl:for-each select="child::*[local-name()='div2'] | child::*[local-name()='div']">
						<xsl:value-of select="concat('|',concat(count(ancestor::node()), '0000', count(preceding::node())),'|')"/>
					</xsl:for-each>
				</xsl:variable>
				<a class="toggle_toc">±</a>
				<xsl:choose>
					<xsl:when test="string($section) and contains($ids, concat('|',$section,'|'))">
						<ul class="toc_sub">
							<xsl:apply-templates
								select="*[local-name()='div'] | *[local-name()='div2']" mode="toc"/>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						<ul class="toc_sub" style="display:none;">
							<xsl:apply-templates
								select="*[local-name()='div'] | *[local-name()='div2']" mode="toc"/>
						</ul>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</li>
	</xsl:template>

	<xsl:template
		match="*[local-name()='div1'] | *[local-name()='div'][parent::*[local-name()='body']] | *[local-name()='div'][parent::*[local-name()='front']]">
		<div class="tei_section">
			<xsl:apply-templates/>
		</div>
	</xsl:template>

	<xsl:template
		match="*[local-name()='div2'] | *[local-name()='div'][parent::*[local-name()='body']]/*[local-name()='div'] | *[local-name()='div'][parent::*[local-name()='front']]/*[local-name()='div']">
		<div class="tei_subsection">
			<xsl:apply-templates/>
		</div>
	</xsl:template>

	<!-- disable teiHeader from public display, by default.  pertinent metadata is display with DC fields -->
	<xsl:template match="*[local-name()='teiHeader']"/>

</xsl:stylesheet>
