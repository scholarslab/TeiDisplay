<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns="http://www.w3.org/1999/xhtml">

<!--
Copyright (c) 2008, Regents of the University of California
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

- Redistributions of source code must retain the above copyright notice,
this list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
- Neither the name of the University of California nor the names of its
contributors may be used to endorse or promote products derived from
this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
 
-->

  <!-- This stylesheet was adapted in May 2012 from the original to reflect the TEI Lite customization. The order below reflects the TEI Lite documentation outlined here: http://www.tei-c.org/release/doc/tei-p5-exemplars/html/teilite.doc.html.
  -->
  
  <!-- Allows for an additional customization, the inclusion of page images associated with the 'pb' element through the 'facs' attribute. -->

<!-- ====================================================================== -->
<!-- Encoding the Body                                                      -->
<!-- ====================================================================== -->
  
<xsl:template match="*[local-name()='front']">
  <span class="front">
    <xsl:apply-templates/>
  </span>
</xsl:template>

<xsl:template match="*[local-name()='group']">
  <span class="group">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='body']">
  <span class="body">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='back']">
  <span class="back">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Text Division Elements                                                 -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='p'][not(ancestor::note[@type='endnote' or @place='end'])]">
    <span class="p">
    <xsl:choose>
      <xsl:when test="@rend='center'">
        <p class="center">
          <xsl:apply-templates/>
        </p>
      </xsl:when>
      <xsl:when test="name(preceding-sibling::node()[1])='pb'">
        <p class="noindent">
          <xsl:apply-templates/>
        </p>
      </xsl:when>
      <xsl:when test="parent::td">
        <p>
          <xsl:apply-templates/>
        </p>
      </xsl:when>
      <xsl:when test="contains(@rend, 'IndentHanging')">
        <p class="{@rend}">
          <xsl:apply-templates/>
        </p>
      </xsl:when>
      <xsl:when test="not(preceding-sibling::p)">
        <xsl:choose>
          <xsl:when test="@rend='hang'">
            <p class="hang">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:when test="@rend='indent'">
            <p class="indent">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:when test="@rend='noindent'">
            <p class="noindent">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:otherwise>
            <p class="noindent">
              <xsl:apply-templates/>
            </p>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:when test="not(following-sibling::p)">
        <xsl:choose>
          <xsl:when test="@rend='hang'">
            <p class="hang">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:when test="@rend='indent'">
            <p class="indent">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:when test="@rend='noindent'">
            <p class="noindent">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:otherwise>
            <p class="padded">
              <xsl:apply-templates/>
            </p>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:choose>
          <xsl:when test="@rend">
            <p class="{@rend}">
              <xsl:apply-templates/>
            </p>
          </xsl:when>
          <xsl:otherwise>-->
            <p class="normal">
              <xsl:apply-templates/>
            </p>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Headings and Closings                                                  -->
  <!-- ====================================================================== -->

  <xsl:template match="*[local-name()='head']">
    <xsl:variable name="type" select="parent::*/@type"/>
    <xsl:variable name="class">
       <xsl:choose>
        <xsl:when test="@rend">
          <xsl:value-of select="@rend"/>
        </xsl:when>
        <xsl:otherwise>normal</xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <span class="head">
    <xsl:choose>
      <xsl:when test="@type='sub' or @type='subtitle'">
        <!-- Needs more choices here -->
        <h3 class="{$class}">
          <xsl:apply-templates/>
        </h3>
      </xsl:when>
      <xsl:when test="$type='fmsec'">
        <h2 class="{$class}">
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:when test="$type='volume'">
        <h1 class="{$class}">
<!-- If the text of <head> has the volume number in it, this is redundant.
          <xsl:if test="parent::*/@n">
            <xsl:value-of select="parent::*/@n"/>
            <xsl:text>. </xsl:text>
          </xsl:if>
-->
          <xsl:apply-templates/>
        </h1>
      </xsl:when>
      <xsl:when test="$type='part'">
        <h1 class="{$class}">
<!-- If the text of <head> has the part number in it, this is redundant.          
          <xsl:if test="parent::*/@n">
            <xsl:value-of select="parent::*/@n"/>
            <xsl:text>. </xsl:text>
          </xsl:if>
-->
          <xsl:apply-templates/>
        </h1>
      </xsl:when>
      <xsl:when test="$type='chapter'">
        <h2 class="{$class}">
<!-- If the text of <head> has the chapter number in it, this is redundant.
          <xsl:if test="parent::*/@n">
            <xsl:value-of select="parent::*/@n"/>
            <xsl:text>. </xsl:text>
          </xsl:if>
-->
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:when test="$type='ss1'">
        <h3 class="{$class}">
          <xsl:if test="parent::*/@n">
            <xsl:value-of select="parent::*/@n"/>
            <xsl:text>. </xsl:text>
          </xsl:if>
          <xsl:apply-templates/>
        </h3>
      </xsl:when>
      <xsl:when test="$type='ss2'">
        <h3 class="{$class}">
          <xsl:apply-templates/>
        </h3>
      </xsl:when>
      <xsl:when test="$type='ss3'">
        <h3 class="{$class}">
          <xsl:apply-templates/>
        </h3>
      </xsl:when>
      <xsl:when test="$type='ss4'">
        <h4 class="{$class}">
          <xsl:apply-templates/>
        </h4>
      </xsl:when>
      <xsl:when test="$type='ss5'">
        <h4 class="{$class}">
          <xsl:apply-templates/>
        </h4>
      </xsl:when>
      <xsl:when test="$type='bmsec'">
        <h2 class="{$class}">
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:when test="$type='appendix'">
        <h2 class="{$class}">
          <xsl:if test="parent::*/@n">
            <xsl:value-of select="parent::*/@n"/>
            <xsl:text>. </xsl:text>
          </xsl:if>
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:when test="$type='endnotes'">
        <h3 class="{$class}">
          <xsl:apply-templates/>
        </h3>
      </xsl:when>
      <xsl:when test="$type='bibliography'">
        <h2 class="{$class}">
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:when test="$type='glossary'">
        <h2 class="{$class}">
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:when test="$type='index'">
        <h2 class="{$class}">
          <xsl:apply-templates/>
        </h2>
      </xsl:when>
      <xsl:otherwise>
        <h4 class="{$class}">
          <xsl:apply-templates/>
        </h4>
      </xsl:otherwise>
    </xsl:choose>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='trailer']">
    <span class="trailer">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

  <!-- ====================================================================== -->
  <!-- Prose, Verse and Drama                                                 -->
  <!-- ====================================================================== -->

  <xsl:template match="*[local-name()='l']">
    <span class="l">
    <xsl:choose>
      <xsl:when test="parent::lg">
          <tr>
            <td width="30">
              <xsl:choose>
                <xsl:when test="@n">
                  <xsl:value-of select="@n"/>
                </xsl:when>
                <xsl:otherwise>
                  <xsl:text> </xsl:text>
                </xsl:otherwise>
             </xsl:choose>
            </td>
            <td>
              <xsl:apply-templates/>
            </td>
          </tr>
      </xsl:when>
      <xsl:otherwise>
          <tr>
            <td width="30">
              <xsl:if test="@n">
                <xsl:value-of select="@n"/>
              </xsl:if>
            </td>
            <td>
              <xsl:apply-templates/>
            </td>
          </tr>
      </xsl:otherwise>
    </xsl:choose>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='lg']">
    <span class="lg">
    <table class="linegroup">
      <xsl:apply-templates/>
    </table>
    <br />
    </span>
  </xsl:template>

  <xsl:template match="*[local-name()='sp']">
<span class="sp">
    <xsl:apply-templates/>
    <br/>
</span>
  </xsl:template>

  <xsl:template match="*[local-name()='speaker']">
    <span class="speaker">
    <b>
      <xsl:apply-templates/>
    </b>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='stage']">
    <span class="stage">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='sp']/*[local-name()='p']">
    <span class="sp">
    <p class="noindent">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
<!-- ====================================================================== -->
<!-- Page and Line Numbers                                                  -->
<!-- ====================================================================== -->

<xsl:template match="*[local-name()='pb']">
  <span class="pb">
  <xsl:choose>
     <xsl:when test="@facs">
       <br/>
       <span class="run-head">
         &#x2015; <xsl:value-of select="@n"/>&#x2015;
       </span>
       <br/>
       <a href="{@facs}" target="_blank"><img src="{@facs}" class="pb" alt="page image"/></a>
       <br/>
    </xsl:when>
    <xsl:otherwise>
      <br/>
      <span class="run-head">
        &#x2015; <xsl:value-of select="@n"/>&#x2015;
      </span>
      <br/>
    </xsl:otherwise>
  </xsl:choose>
  </span>
</xsl:template>

<xsl:template match="*[local-name()='lb']">
  <span class="lb">
  <br/>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='milestone']">
  <span class="milestone">
 <xsl:choose>
  <xsl:when test="@rend='ornament' or @rend='ornamental_break'">
    <div class="ornament" align="center">
      <table border="0" width="40%">
        <tr align="center">
          <td>&#x2022;</td>
          <td>&#x2022;</td>
          <td>&#x2022;</td>
        </tr>
      </table>
    </div>
  </xsl:when>
  <xsl:otherwise>
      <xsl:apply-templates />
  </xsl:otherwise> 
 </xsl:choose>
  </span>
</xsl:template>

<!-- ====================================================================== -->
<!-- Marking Highlighted Phrases                                            -->
<!-- ====================================================================== -->
  
  <!-- ====================================================================== -->
  <!-- Changes of Typeface, etc.                                              -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='hi']">
    <span class="hi">
    <xsl:choose>
      <xsl:when test="@rend='bold'">
        <b>
          <xsl:apply-templates/>
        </b>
      </xsl:when>
      <xsl:when test="@rend='italic'">
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:when>
      <xsl:when test="@rend='mono'">
        <code>
          <xsl:apply-templates/>
        </code>
      </xsl:when>
      <xsl:when test="@rend='roman'">
        <span class="normal">
          <xsl:apply-templates/>
        </span>
      </xsl:when>
      <xsl:when test="@rend='smallcaps'">
        <span class="sc">
          <xsl:apply-templates/>
        </span>
      </xsl:when>
      <xsl:when test="@rend='sub' or @rend='subscript'">
        <sub>
          <xsl:apply-templates/>
        </sub>
      </xsl:when>
      <xsl:when test="@rend='sup' or @rend='superscript'">
        <sup>
          <xsl:apply-templates/>
        </sup>
      </xsl:when>
      <xsl:when test="@rend='underline'">
        <u>
          <xsl:apply-templates/>
        </u>
      </xsl:when>
      <xsl:otherwise>
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:otherwise>
    </xsl:choose>
    </span>
  </xsl:template>  

  <xsl:template match="*[local-name()='emph']">
    <span class="emph">
      <em><xsl:apply-templates/></em>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='foreign']">
    <span class="foreign">
    <i><xsl:apply-templates/></i>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='gloss']">
    <span class="gloss">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='label']">
    <span class="label">
    <dt><xsl:apply-templates/></dt>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='mentioned']">
    <span class="mentioned">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='term']">
    <span class="term">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- Because of order in the following, "rend" takes precedence over "level" -->
  <xsl:template match="*[local-name()='title']">
    <span class="title">
    <xsl:choose>
      <xsl:when test="@rend='italic'">
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:when>
      <xsl:when test="@level='m'">
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:when>
      <xsl:when test="@level='a'"> 
        &#x201C;<xsl:apply-templates/>&#x201D; 
      </xsl:when>
      <xsl:when test="@level='j'">
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:when>
      <xsl:when test="@level='s'">
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:when>
      <xsl:when test="@level='u'">
        <i>
          <xsl:apply-templates/>
        </i>
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-templates/>
      </xsl:otherwise>
    </xsl:choose>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Quotations and Related Features                                        -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='q']">
    <span class="q">
    <blockquote>
      <xsl:apply-templates/>
    </blockquote>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='quote']">
    <span class="quote">
    <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- For 'mentioned' see "Changes of Typeface, etc." section above -->
  
  <xsl:template match="*[local-name()='soCalled']">
    <span class="soCalled">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- For 'gloss' see "Changes of Typeface, etc." section above -->

<!-- ====================================================================== -->
<!-- Notes                                                                  -->
<!-- ====================================================================== -->
  
<xsl:template match="*[local-name()='note']">
  <span class="note">
    <xsl:choose>
      <xsl:when test="@type='footnote' or @place='foot'">
        <p>
          <xsl:if test="@n">
            <xsl:text>[</xsl:text>
            <xsl:value-of select="@n"/>
            <xsl:text>] </xsl:text>
          </xsl:if>
          <xsl:choose>
            <xsl:when test="@xml:id">
              <a name="{@xml:id}"><xsl:apply-templates/></a>
            </xsl:when>
            <xsl:otherwise>  
              <xsl:apply-templates/>
            </xsl:otherwise>
          </xsl:choose>
        </p>
      </xsl:when>
      <xsl:when test="@type='endnote' or @place='end'">
        <xsl:variable name="n" select="parent::note/@n"/>
          <xsl:variable name="class">
            <xsl:choose>
              <xsl:when test="position()=1">noindent</xsl:when>
              <xsl:otherwise>indent</xsl:otherwise>
          </xsl:choose>
          </xsl:variable>
        <p class="{$class}">
          <xsl:choose>
            <xsl:when test="@xml:id">
              <a name="{@xml:id}"><xsl:apply-templates/></a>
            </xsl:when>
            <xsl:otherwise>  
              <xsl:apply-templates/>
            </xsl:otherwise>
          </xsl:choose>
        </p>
      </xsl:when>
      <xsl:when test="@resp">
          [<em><span style="text-transform:capitalize;"><xsl:value-of select="@resp"/></span> note: <xsl:apply-templates/></em>]
      </xsl:when>
      <xsl:otherwise>
        [<em>Note: <xsl:apply-templates/></em>]
      </xsl:otherwise>
    </xsl:choose>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='p'][ancestor::note[@type='footnote' or @place='foot']]">
  <xsl:variable name="n" select="parent::note/@n"/>
  <span class="p">
    <p>
      <xsl:if test="position()=1">
        <xsl:if test="$n != ''">
          <xsl:text>[</xsl:text>
          <xsl:value-of select="$n"/>
          <xsl:text>] </xsl:text>
        </xsl:if>
      </xsl:if>
      <xsl:apply-templates/>
    </p>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='p'][ancestor::note[@type='endnote' or @place='end']]">
  <xsl:variable name="n" select="parent::note/@n"/>
  <xsl:variable name="class">
     <xsl:choose>
      <xsl:when test="position()=1">noindent</xsl:when>
      <xsl:otherwise>indent</xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <span class="p">
  <p class="{$class}">
    <xsl:apply-templates/>
  </p>
  </span>
</xsl:template>

<!-- ====================================================================== -->
<!-- Cross References and Links                                             -->
<!-- ====================================================================== -->
  
  <!-- ====================================================================== -->
  <!-- Simple Cross References                                                -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='ref']">
    <!-- process refs -->
    <span class="ref">
    <xsl:choose>
      <xsl:when test="string(@target)">
        <a href="{@target}" class="ref">
          <xsl:apply-templates/>
        </a>
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-templates/>
      </xsl:otherwise>
    </xsl:choose>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='ptr']">
    <span class="ptr">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='anchor']">
    <span class="anchor">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='seg']">
    <span class="seg">
    <xsl:if test="position() > 1">
      <xsl:text>&#160;&#160;&#160;&#160;</xsl:text>
    </xsl:if>
    <xsl:apply-templates/>
    <br/>
    </span>
  </xsl:template>
  
<!-- ====================================================================== -->
<!-- Editorial Interventions                                                -->
<!-- ====================================================================== -->
  
  <!-- ====================================================================== -->
  <!-- Correction and Normalization                                           -->
  <!-- ====================================================================== -->

  <xsl:template match="*[local-name()='choice']">
    <span class="choice">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='sic']">
    <xsl:choose>
      
      <xsl:when test="following-sibling::*">
        <span class="sic.hide" style="display:none;"><xsl:apply-templates /></span>
      </xsl:when>
      
      <xsl:otherwise>
        <span class="sic">
          <xsl:apply-templates/><em> [sic] </em>
        </span>
      </xsl:otherwise>
      
    </xsl:choose>
  </xsl:template>
  
  <xsl:template match="*[local-name()='corr']">
    <span class="corr">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='orig']">
    <span class="orig">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='reg']">
    <span class="reg">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Omissions, Deletions, and Additions                                    -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='add']">
    <span class="add" style="color: red;">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='gap']">
    <span class="gap">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='del']">
    <span class="del">
      <strike><xsl:apply-templates/></strike>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='unclear']">
    <span class="unclear">
      <xsl:apply-templates/><em> [unclear] </em>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Abbreviations and their Expansion                                      -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='abbr']">
    <span class="abbr">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='expan']">
    <span class="expan">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
<!-- ====================================================================== -->
<!-- Names, Dates, and Numbers                                              -->
<!-- ====================================================================== -->
  
  <!-- ====================================================================== -->
  <!-- Names and Referring Strings                                            -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='rs']">
    <span class="rs">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='name']">
    <span class="name">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Dates and Times                                                        -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='date']">
    <span class="date">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='time']">
    <span class="time">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Numbers                                                                -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='num']">
    <span class="num">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
<!-- ====================================================================== -->
<!-- Lists                                                                  -->
<!-- ====================================================================== -->
  
<xsl:template match="*[local-name()='list']">
  <span class="list">
  <xsl:choose>
    <xsl:when test="@type='gloss'">
      <dl>
        <xsl:apply-templates/>
      </dl>
    </xsl:when>
    <xsl:when test="@type='simple'">
      <ul class="nobull">
        <xsl:apply-templates/>
      </ul>
    </xsl:when>
    <xsl:when test="@type='ordered'">
      <xsl:choose>
        <xsl:when test="@rend='alpha'">
          <ol class="alpha">
            <xsl:apply-templates/>
          </ol>
        </xsl:when>
        <xsl:otherwise>
          <ol>
            <xsl:apply-templates/>
          </ol>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:when>
    <xsl:when test="@type='unordered'">
      <ul>
        <xsl:apply-templates/>
      </ul>
    </xsl:when>
    <xsl:when test="@type='bulleted'">
      <xsl:choose>
        <xsl:when test="@rend='dash'">
          <ul class="nobull">
            <xsl:text>- </xsl:text>
            <xsl:apply-templates/>
          </ul>
        </xsl:when>
        <xsl:otherwise>
          <ul>
            <xsl:apply-templates/>
          </ul>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:when>
    <xsl:when test="@type='bibliographic'">
      <ol>
        <xsl:apply-templates/>
      </ol>
    </xsl:when>
    <xsl:when test="@type='special'">
      <ul>
        <xsl:apply-templates/>
      </ul>
    </xsl:when>
    <xsl:otherwise>
      <ul>
        <xsl:apply-templates/>
      </ul>
    </xsl:otherwise>
  </xsl:choose>
  </span>
</xsl:template>

<xsl:template match="*[local-name()='item']">
  <span class="item">
  <xsl:choose>
    <xsl:when test="parent::list[@type='gloss']">
      <dd>
        <xsl:apply-templates/>
      </dd>
    </xsl:when>
    <xsl:otherwise>
      <li>
        <xsl:apply-templates/>
      </li>
    </xsl:otherwise>
  </xsl:choose>
  </span>
</xsl:template>
  
<!-- For 'label' see Changes of Typeface, etc. section above -->
  
<!-- ====================================================================== -->
<!-- Bibliographic Citations                                                -->
<!-- ====================================================================== -->

<xsl:template match="*[local-name()='bibl']">
  <span class="bibl">
  <xsl:choose>
    <xsl:when test="parent::listBibl">
      <p class="hang">
        <xsl:apply-templates/>
      </p>
    </xsl:when>
    <xsl:otherwise>
      <xsl:apply-templates/>
    </xsl:otherwise>
  </xsl:choose>
  </span>
</xsl:template>

<xsl:template match="*[local-name()='epigraph']/*[local-name()='bibl']">
  <p class="right">
    <xsl:apply-templates/>
  </p>
</xsl:template>

<xsl:template match="*[local-name()='cit']/*[local-name()='bibl']">
  <p class="right">
    <xsl:apply-templates/>
  </p>
</xsl:template>
  
<xsl:template match="*[local-name()='author']">
<span class="author">
  <xsl:choose>
    <xsl:when test="@rend='hide'">
      <xsl:text>&#x2014;&#x2014;&#x2014;</xsl:text>
    </xsl:when>
    <xsl:otherwise>
      <xsl:apply-templates/>
    </xsl:otherwise>
  </xsl:choose>
</span>
</xsl:template>

<xsl:template match="*[local-name()='biblScope']">
  <span class="biblScope">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<!-- For 'date' see Dates and Times section above -->

<xsl:template match="*[local-name()='editor']">
  <span class="editor">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='publisher']">
  <span class="publisher">
    <xsl:apply-templates/>
  </span>
</xsl:template>

<xsl:template match="*[local-name()='pubPlace']">
  <span class="pubPlace">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<!-- For 'title' see Changes of Typeface, etc. section above -->
  
<!-- ====================================================================== -->
<!-- Tables                                                                 -->
<!-- ====================================================================== -->

<xsl:template match="*[local-name()='table']">
  <span class="table">
  <table>
    <xsl:apply-templates/>
  </table>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='row']">
  <span class="row">
  <tr>
    <xsl:apply-templates/>
  </tr>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='cell']">
  <span class="cell">
  <td>
    <xsl:apply-templates/>
  </td>
  </span>
</xsl:template>

<!-- ====================================================================== -->
<!-- Figures and Graphics                                                   -->
<!-- ====================================================================== -->

<xsl:template match="*[local-name()='graphic']">
  <span class="graphic">
    <xsl:choose>
      <xsl:when test="@url">
        <xsl:variable name="img_src">
          <xsl:value-of select="@url"/>
        </xsl:variable>
        <xsl:choose>
          <xsl:when test="@rend='hide'">
            <p>Image Withheld</p>
          </xsl:when>
          <xsl:when test="@rend='inline'">
            <a href="{$img_src}" target="_blank"><img src="{$img_src}" alt="inline image"/></a>
          </xsl:when>
          <xsl:when test="@rend='block'">
            <a href="{$img_src}" target="_blank"><img src="{$img_src}" width="400" alt="block image"/></a>
          </xsl:when>
          <xsl:when test="contains(@rend, 'popup(')">
            <a href="{$img_src}" target="_blank"><img src="{$img_src}" alt="figure"/></a>
          </xsl:when>
          <xsl:otherwise>
            <a href="{$img_src}" target="_blank"><img src="{$img_src}" width="400" alt="image"/></a>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-templates/>
      </xsl:otherwise>
    </xsl:choose>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='figure']">
  <span class="figure">
  <xsl:choose>
    <xsl:when test="@rend='hide'">
      <div class="illgrp">
        <p>Image Withheld</p>
        <!-- for figDesc -->
        <xsl:apply-templates/>
      </div>
    </xsl:when>
    <xsl:otherwise>
      <div class="illgrp">
        <!-- for figDesc -->
        <xsl:apply-templates/>
      </div>
    </xsl:otherwise>
  </xsl:choose>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='figDesc']">
  <span class="figDesc">
  <br/>
  <span class="down1">
    <xsl:if test="@n"><xsl:value-of select="@n"/>. </xsl:if>
    <xsl:apply-templates/>
  </span>
  </span>
</xsl:template>
  
<!-- ====================================================================== -->
<!-- Interpretation and Analysis                                            -->
<!-- ====================================================================== -->

  <!-- ====================================================================== -->
  <!-- Orthographic Sentences                                                 -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='s']">
    <span class="s">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

  <!-- ====================================================================== -->
  <!-- General-Purpose Interpretation Elements                                -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='interp']">
    <span class="interp">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='interpGrp']">
    <span class="interpGrp">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
<!-- ====================================================================== -->
<!-- Technical Documentation                                                -->
<!-- ====================================================================== -->
  
  <!-- ====================================================================== -->
  <!-- Additional Elements for Technical Documents                            -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='eg']">
    <span class="eg">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='code']">
    <span class="code">
    <code>
      <xsl:apply-templates/>
    </code>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='ident']">
    <span class="ident">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='gi']">
    <span class="gi">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='att']">
    <span class="att">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='formula']">
    <span class="formula">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='value']">
    <span class="value">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Generated Divisions                                                    -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='divGen']">
    <span class="divGen">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Index Generation                                                       -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='index']">
    <span class="index">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

  <!-- ====================================================================== -->
  <!-- Addresses                                                              -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='address']">
    <span class="address">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='addrLine']">
    <xsl:for-each select=".">
      <span class="addrLine">
        <xsl:apply-templates/><br />
      </span>
      <xsl:if test="position() != last()">
        <span class="addrLine">
          <xsl:apply-templates/>
        </span>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>
  
<!-- ====================================================================== -->
<!-- Front Matter                                                           -->
<!-- ====================================================================== -->  

  <!-- ====================================================================== -->
  <!-- Title Page                                                             -->
  <!-- ====================================================================== -->

  <xsl:template match="*[local-name()='titlePage']">
    <span class="titlePage">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='docTitle']">
    <span class="docTitle">
    <p class="docTitle">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='titlePart']">
    <span class="titlePart">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='byline']">
    <span class="byline">
    <p class="byline">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>

  <xsl:template match="*[local-name()='docAuthor']">
    <span class="docAuthor">
    <p class="docAuthor">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='docDate']">
    <span class="docDate">
    <p class="docDate">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
   
  <xsl:template match="*[local-name()='docEdition']">
    <span class="docEdition">
    <p class="docEdition">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='docImprint']">
    <span class="docImprint">
    <p class="docImprint">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='epigraph']">
    <span class="epigraph">
    <blockquote>
      <xsl:apply-templates/>
    </blockquote>
    <br/>
    </span>
  </xsl:template>
  
  <!-- ====================================================================== -->
  <!-- Prefatory Matter                                                       -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='salute']">
    <span class="salute">
    <p class="salute">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='signed']">
    <span class="signed">
    <p class="signed">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>

  <!-- For 'byline' see Title Page section above -->
  
  <xsl:template match="*[local-name()='dateline']">
    <span class="dateline">
    <p class="dateline">
      <xsl:apply-templates/>
    </p>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='argument']">
    <span class="argument">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

  <xsl:template match="*[local-name()='cit']">
    <span class="cit">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='opener']">
    <span class="opener">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

  <xsl:template match="*[local-name()='closer']">
    <span class="closer">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  

<!-- ====================================================================== -->
<!-- The Electronic Title Page                                              -->
<!-- ====================================================================== -->

<xsl:template match="*[local-name()='fileDesc']">
  <span class="fileDesc">
    <xsl:apply-templates/>
  </span>
</xsl:template>

<xsl:template match="*[local-name()='encodingDesc']">
  <span class="encodingDesc">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='profileDesc']">
  <span class="profileDesc">
    <xsl:apply-templates/>
  </span>
</xsl:template>
  
<xsl:template match="*[local-name()='revisionDesc']">
  <span class="revisionDesc">
    <xsl:apply-templates/>
  </span>
</xsl:template>

  <!-- ====================================================================== -->
  <!-- The File Description                                                   -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='titleStmt']">
    <span class="titleStmt">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='editionStmt']">
    <span class="editionStmt">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='extent']">
    <span class="extent">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='publicationStmt']">
    <span class="publicationStmt">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='seriesStmt']">
    <span class="seriesStmt">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='notesStmt']">
    <span class="notesStmt">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='sourceDesc']">
    <span class="sourceDesc">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

    <!-- ====================================================================== -->
    <!-- The Title Statement                                                    -->
    <!-- ====================================================================== -->

    <xsl:template match="TEI//teiHeader//fileDesc//titleStmt//title">
      <span class="teiHeader.Title">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

    <xsl:template match="TEI//teiHeader//fileDesc//titleStmt//author">
      <span class="teiHeader.Author">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='sponsor']">
      <span class="sponsor">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='funder']">
      <span class="funder">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='principal']">
      <span class="principal">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//fileDesc//titleStmt//respStmt">
      <span class="titleStmt.respStmt">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//fileDesc//titleStmt//respStmt">
      <span class="titleStmt.respStmt">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
  <xsl:template match="*[local-name()='resp']">
      <span class="resp">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//fileDesc//titleStmt//respStmt//name">
      <span class="titleStmt.respStmt.Name">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

    <!-- ====================================================================== -->
    <!-- The Edition Statement                                                  -->
    <!-- ====================================================================== -->

    <xsl:template match="*[local-name()='edition']">
      <span class="edition">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//fileDesc//editionStmt//respStmt">
      <span class="editionStmt.respStmt">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

    <!-- ====================================================================== -->
    <!-- The Publication Statement                                              -->
    <!-- ====================================================================== -->

    <xsl:template match="TEI//teiHeader//fileDesc//publicationStmt//publisher">
      <span class="publicationStmt.publisher">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

    <xsl:template match="*[local-name()='distributor']">
      <span class="distributor">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

    <xsl:template match="*[local-name()='authority']">
      <span class="authority">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//sourceDesc//bibful//publicationStmt//pubPlace">
      <span class="publicationStmt.pubPlace">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//sourceDesc//bibful//publicationStmt//address">
      <span class="publicationStmt.address">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='idno']">
      <span class="idno">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='availability']">
      <span class="availability">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="TEI//teiHeader//fileDesc//publicationStmt//date">
      <span class="publicationStmt.date">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

    <!-- ====================================================================== -->
    <!-- The Source Description                                                 -->
    <!-- ====================================================================== -->
  
    <!-- For 'bibl' see Bibliographic Citations section above -->
  
    <xsl:template match="*[local-name()='biblFull']">
      <span class="biblFull">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='listBibl']">
      <span class="listBibl">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

  <!-- ====================================================================== -->
  <!-- The Encoding Description                                               -->
  <!-- ====================================================================== -->

  <xsl:template match="*[local-name()='projectDesc']">
    <span class="projectDesc">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='samplingDecl']">
    <span class="samplingDecl">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='editorialDecl']">
    <span class="editorialDecl">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='refsDecl']">
    <span class="refsDecl">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='classDecl']">
    <span class="classDecl">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

    <!-- ====================================================================== -->
    <!-- Reference and Classification Declarations                              -->
    <!-- ====================================================================== -->

    <xsl:template match="*[local-name()='taxonomy']">
      <span class="taxonomy">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <!-- For 'bibl' see Bibliographic Citations section above -->

    <xsl:template match="*[local-name()='category']">
      <span class="category">
        <xsl:apply-templates/>
      </span>
    </xsl:template>
  
    <xsl:template match="*[local-name()='catDesc']">
      <span class="catDesc">
        <xsl:apply-templates/>
      </span>
    </xsl:template>

  <!-- ====================================================================== -->
  <!-- The Profile Description                                                -->
  <!-- ====================================================================== -->
  
  <xsl:template match="*[local-name()='creation']">
    <span class="creation">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='langUsage']">
    <span class="langUsage">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='language']">
    <span class="language">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='textClass']">
    <span class="textClass">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='keywords']">
    <span class="keywords">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='classCode']">
    <span class="classCode">
      <xsl:apply-templates/>
    </span>
  </xsl:template>
  
  <xsl:template match="*[local-name()='catRef']">
    <span class="catRef">
      <xsl:apply-templates/>
    </span>
  </xsl:template>

<!-- ====================================================================== -->
<!-- The Revision Description                                               -->
<!-- ====================================================================== -->
  
<xsl:template match="*[local-name()='change']">
  <span class="change">
    <xsl:apply-templates/>
  </span>
</xsl:template>

</xsl:stylesheet>

