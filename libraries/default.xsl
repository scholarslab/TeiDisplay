<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0">
	<xsl:param name="display"/>
	<xsl:param name="section"/>

	<!-- include component.xsl, created by CDL for XTF -->
	<xsl:include href="component.xsl"/>

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

	<xsl:template name="toc">
		<xsl:if test="//front">
			<h4>Front</h4>
			<ul>
				<xsl:apply-templates select="descendant::front/div1" mode="toc"/>
			</ul>
		</xsl:if>

		<h4>Body</h4>
		<ul>
			<xsl:apply-templates select="descendant::body/div1" mode="toc"/>
		</ul>
	</xsl:template>

	<xsl:template match="div1" mode="toc">
		<li>
			<xsl:if test="@type">
				<span class="toc_type">
					<xsl:value-of select="@type"/>
				</span>
				<xsl:text>: </xsl:text>
			</xsl:if>
			<xsl:variable name="title">
				<xsl:choose>
					<xsl:when test="string(normalize-space(head))">
						<xsl:value-of select="normalize-space(head)"/>
					</xsl:when>
					<xsl:otherwise>[No Title]</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="$section=@id">
					<b>
						<xsl:value-of select="$title"/>
					</b>
				</xsl:when>
				<xsl:otherwise>
					<a href="?section={@id}">
						<xsl:value-of select="$title"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="div2">
				<a class="toggle_toc">±</a>
				<ul class="toc_sub" style="display:none;">
					<xsl:apply-templates select="div2" mode="toc"/>
				</ul>
			</xsl:if>
		</li>
	</xsl:template>

	<xsl:template match="div2" mode="toc">
		<li>
			<xsl:if test="@type">
				<span class="toc_type">
					<xsl:value-of select="@type"/>
				</span>
				<xsl:text>: </xsl:text>
			</xsl:if>
			<xsl:variable name="title">
				<xsl:choose>
					<xsl:when test="string(normalize-space(head))">
						<xsl:value-of select="normalize-space(head)"/>
					</xsl:when>
					<xsl:otherwise>[No Title]</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="$section=@id">
					<b>
						<xsl:value-of select="$title"/>
					</b>
				</xsl:when>
				<xsl:otherwise>
					<a href="?section={@id}">
						<xsl:value-of select="$title"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>

	<!--<xsl:template match="body">
		<xsl:apply-templates/>
	</xsl:template>

	<xsl:template match="p">
		<p>
			<xsl:apply-templates/>
		</p>
	</xsl:template>

	<xsl:template match="pb">
		<div style="width:50%;text-align:center;margin:10px auto;">
			<xsl:value-of select="@n"/>
			<br/>
			<hr/>
		</div>
	</xsl:template>

	<xsl:template match="head">
		<xsl:choose>
			<xsl:when test="parent::div1">
				<h2>
					<xsl:value-of select="."/>
				</h2>
			</xsl:when>
			<xsl:when test="parent::div2">
				<h3>
					<xsl:value-of select="."/>
				</h3>
			</xsl:when>
			<xsl:when test="parent::div3">
				<h4>
					<xsl:value-of select="."/>
				</h4>
			</xsl:when>
			<xsl:when test="parent::div4">
				<h5>
					<xsl:value-of select="."/>
				</h5>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="hi">
		<xsl:choose>
			<xsl:when test="@rend = 'italic'">
				<i>
					<xsl:apply-templates/>
				</i>
			</xsl:when>
			<xsl:when test="@rend = 'bold'">
				<b>
					<xsl:apply-templates/>
				</b>
			</xsl:when>
		</xsl:choose>
	</xsl:template>-->

</xsl:stylesheet>
