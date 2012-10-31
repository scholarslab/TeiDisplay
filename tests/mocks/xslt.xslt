<?xml version="1.0" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0" xmlns:tei="http://www.tei-c.org/ns/1.0">
    
    <xsl:template match="/">
        <div id="tei"><xsl:apply-templates/></div>
    </xsl:template>

    <xsl:template match="tei:publicationStmt"/>
    <xsl:template match="tei:sourceDesc"/>
    <xsl:template match="tei:encodingDesc"/>
    <xsl:template match="tei:profileDesc"/>
    <xsl:template match="tei:revisionDesc"/>

    <xsl:template match="tei:titleStmt">
        <h1 class="title"><xsl:value-of select="tei:title"/></h1>
        <h2 class="author"><xsl:value-of select="tei:author"/></h2>
    </xsl:template>

    <xsl:template match="tei:sp">
        <h2 class="speaker"><xsl:value-of select="tei:speaker"/></h2>
        <div class="line"><xsl:value-of select="tei:ab"/></div>
    </xsl:template>

</xsl:stylesheet>