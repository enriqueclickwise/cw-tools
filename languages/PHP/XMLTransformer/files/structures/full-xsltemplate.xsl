<?xml version="1.0" encoding="UTF-8"?>
    <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
    <productos>
        <xsl:for-each select="xslpath">
        <xsl:text>&#10;&#x9;</xsl:text>
        <producto><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <id><xsl:value-of select="xslid" /></id><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <name><xsl:value-of select="xslname" /></name><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <description><xsl:value-of select="xsldescription" /></description><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <link><xsl:value-of select="xsllink" /></link><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <price><xsl:value-of select="xslprice" /></price><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <image><xsl:value-of select="xslimage" /></image><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <lastprice><xsl:value-of select="xsllastprice" /></lastprice><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <category><xsl:value-of select="xslcategory" /></category><xsl:text>&#xa;&#x9;&#x9;</xsl:text>
        <brand><xsl:value-of select="xslbrand" /></brand><xsl:text>&#xa;&#x9;</xsl:text>
        </producto><xsl:text>&#10;</xsl:text>
        </xsl:for-each>
    </productos>
    </xsl:template>
    </xsl:stylesheet>
