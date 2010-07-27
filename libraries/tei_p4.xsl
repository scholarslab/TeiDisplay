<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0">
	<xsl:param name="display"/>
	<xsl:param name="section"/>

	<xsl:template match="/">
		<xsl:choose>
			<xsl:when test="$display = 'entire'">
				<xsl:apply-templates select="//body"/>
			</xsl:when>
			<xsl:when test="$display='segmental'">
				<div class="tei_toc">test</div>
				<div class="tei_content">
					<xsl:apply-templates />
				</div>
			</xsl:when>
		</xsl:choose>

	</xsl:template>

	<xsl:template match="body">
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
	</xsl:template>

</xsl:stylesheet>
