<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<!-- XSL from https://github.com/OpenDTP/OpenDTPAPI/tree/03b05727a3b28439fe9e0b4b5225964c616511c7/build/xsl -->

    <xsl:output method="html"  encoding="UTF-8"/>
    <xsl:template match="/">
        <html>
            <head>
                <title>CodeSniffer report</title>
                <style>
                    #summary {font: 38px 'Arial'; color:#555; padding:20px 0 20px 30px;}
                    .errors-count {color:red;}
                    .warnings-count {color:orange}
                    .files-count {color:black;}

                    #files {padding:20px 0 0 30px;}
                    .file {padding:20px 5px; border-bottom:2px solid #CCC; font-size:22px;}
                    .file .file-info {display:none;}
                    .file a:link {border-bottom:1px dotted #888; text-decoration:none; color:black;}
                    .file a:link:hover {border-bottom:none;}

                    .errors {padding:5px 15px; margin:5px 0; border-left:3px solid red; font-size:16px;}
                    .warnings {padding:0 15px; border-left:3px solid orange; font-size:18px; color:#666;}

                    .error, .warning {padding:5px 0; border-bottom:1px solid #DDD;}
                    .error .info, .warning .info {color:#666; font-size:15px;}

                    .file:last-child, .error:last-child, .warning:last-child {border-bottom:none;}
                </style>
                <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.min.js" />
                <script type="text/javascript" src="http://code.jquery.com/ui/1.7.2/jquery-ui.min.js" />
                <script type="text/javascript">
                    $(function() {
                        $(".file a").click(function() {
                            var info = $(this).parent().find(".file-info");

                            if (info.is(":visible")) {
                                info.slideUp();
                            } else {
                                info.slideDown();
                            }
                        })
                    })
                </script>
            </head>
            <body>
                <div id="summary">
                    CodeSniffer report<br />
                    Summary: <span class="errors-count"><xsl:value-of select="count(/checkstyle/file/error[@severity='error'])" /></span> errors
                    and <span class="warnings-count"><xsl:value-of select="count(/checkstyle/file/error[@severity='warning'])" /></span> warnings
                    in <span class="files-count"><xsl:value-of select="count(/checkstyle/file)" /></span> files
                </div>
                <div id="files">
                    <xsl:apply-templates select="/checkstyle/file">
                        <xsl:sort select="count(error)" data-type="number" order="descending" />
                    </xsl:apply-templates>
                </div>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="/checkstyle/file">
        <div class="file">
            <a href="javascript:void(null)"><xsl:value-of select="@name" /></a>
            <b>
                (<span class="errors-count"><xsl:value-of select="count(error[@severity='error'])" /></span> /
                <span class="warnings-count"><xsl:value-of select="count(error[@severity='warning'])" /></span>)
            </b>
            <div class="file-info">
                <div class="errors">
                    <xsl:apply-templates select="error[@severity='error']" />
                </div>
                <div class="warnings">
                    <xsl:apply-templates select="error[@severity='warning']" />
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="/checkstyle/file/error[@severity='error']">
        <div class="error">
            <xsl:value-of select="@message" />
            <div class="info">
                <u>at line <xsl:value-of select="@line" />, column <xsl:value-of select="@column" /></u>
                from <xsl:value-of select="@source" />
            </div>
        </div>
    </xsl:template>

    <xsl:template match="/checkstyle/file/error[@severity='warning']">
        <div class="warning">
            <xsl:value-of select="@message" />
            <div class="info">
                <u>at line <xsl:value-of select="@line" />, column <xsl:value-of select="@column" /></u>
                from <xsl:value-of select="@source" />
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>