<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0">
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
								<xsl:apply-templates select="//body"/>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:when>
			</xsl:choose>
		</div>
	</xsl:template>

	<xsl:template name="toc">
		<ul>
			<xsl:for-each select="descendant::body/div1">
				<li>
					<xsl:if test="@type">
						<xsl:value-of
							select="concat(translate(substring(@type, 1, 1), abcdefghijklmnopqrstuvwxyz, ABCDEFGHIJKLMNOPQRSTUVWXYZ), substring(@type, 2))"/>
						<xsl:text>: </xsl:text>
					</xsl:if>
					<a href="?section={@id}">
						<xsl:choose>
							<xsl:when test="string(normalize-space(head))">
								<xsl:value-of select="normalize-space(head)"/>
							</xsl:when>
							<xsl:otherwise>[No Title]</xsl:otherwise>
						</xsl:choose>
					</a>
					<xsl:if test="div2">						
						<a href="#" class="toggle_toc">+</a>
						<ul class="toc_sub" style="display:none;">
							<xsl:for-each select="div2">
								<li>
									<xsl:if test="@type">
										<xsl:value-of
											select="concat(translate(substring(@type, 1, 1), abcdefghijklmnopqrstuvwxyz, ABCDEFGHIJKLMNOPQRSTUVWXYZ), substring(@type, 2))"/>
										<xsl:text>: </xsl:text>
									</xsl:if>
									<a href="?section={@id}">
										<xsl:choose>
											<xsl:when test="string(normalize-space(head))">
												<xsl:value-of select="normalize-space(head)"/>
											</xsl:when>
											<xsl:otherwise>[No Title]</xsl:otherwise>
										</xsl:choose>
									</a>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:if>					
				</li>
			</xsl:for-each>
		</ul>
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
