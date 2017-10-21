<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html"  encoding="UTF-8"/>
    <xsl:key name="file-duplication" match="/pmd-cpd/duplication/file" use="@path" />
    <xsl:param name="root-directory"/>
    
    <xsl:template match="pmd-cpd">
        <html>
            <head>
                <title>phpcpd report</title>
                <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$bootstrap.min.css" /></xsl:attribute></link>
                <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$prism.min.css" /></xsl:attribute></link>
                <script>
                var onDocumentReady = [
                    function () {
                        $('[data-file]').each(function () {
                            var pathWithoutRoot = $(this).text().replace('<xsl:value-of select="$root-directory"></xsl:value-of>', '');
                            $(this).text(pathWithoutRoot);
                        });
                    },
                ];
                </script>
            </head>
            <body>

            <div class="container-fluid">
            
                <h1>phpcpd report</h1>

                <nav>
                    <ul class="nav nav-pills" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overview</a>
                        </li>
                        <li role="presentation">
                            <a href="#errors" aria-controls="errors" role="tab" data-toggle="tab">Errors</a>
                        </li>
                    </ul>
                </nav>

                <div class="tab-content">
                    
                    <div role="tabpanel" class="tab-pane active" id="overview">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td colspan="2"></td>
                                </tr>
                            </thead>
                            <tr>
                                <th>Duplications</th>
                                <td><span class="label label-danger"><xsl:value-of select="count(./duplication)" /></span></td>
                            </tr>
                            <tr>
                                <th>
                                    - lines<br />
                                    - tokens
                                </th>
                                <td>
                                    <xsl:value-of select="sum(./duplication/@lines)" /><br />
                                    <xsl:value-of select="sum(./duplication/@tokens)" />
                                </td>
                            </tr>
                            <tr>
                                <th>Files</th>
                                <td>
                                    <span class="label label-info">
                                        <xsl:value-of select="count(./duplication/file[generate-id() = generate-id(key('file-duplication', @path)[1])])"/>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="errors">

                        <table class="table" data-filterable="errors">
                            <thead>
                                <tr>
                                    <th width="40">Line</th>
                                    <th>Files</th>
                                    <th>Duplications</th>
                                    <th width="50" class="text-right">Code</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="./duplication">
                                <tr>
                                    <td>
                                        <xsl:for-each select="./file">
                                            <span class="text-muted"><xsl:value-of select="@line"/></span><br />
                                        </xsl:for-each>
                                    </td>
                                    <td>
                                        <xsl:for-each select="./file">
                                            <strong data-file=""><xsl:value-of select="@path"/></strong><br />
                                        </xsl:for-each>
                                    </td>
                                    <td>
                                        <span class="label label-danger"><xsl:value-of select="@lines" /></span> lines<br />
                                        <span class="label label-warning"><xsl:value-of select="@tokens" /></span> tokens
                                    </td>
                                    <td class="text-right">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" aria-expanded="true">
                                            <xsl:attribute name="data-target">#duplication-<xsl:value-of select="position()" /></xsl:attribute>
                                            <xsl:attribute name="aria-controls">duplication-<xsl:value-of select="position()" /></xsl:attribute>
                                            <span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse in">
                                    <xsl:attribute name="id">duplication-<xsl:value-of select="position()" /></xsl:attribute>
                                    <td colspan="4"><pre><code class="language-php"><xsl:value-of select="codefragment" /></code></pre></td>
                                </tr>
                            </xsl:for-each>
                        </table>

                    </div>
                </div>
            </div>    


            <script><xsl:attribute name="src"><xsl:value-of select="$jquery.min.js" /></xsl:attribute></script>
            <script><xsl:attribute name="src"><xsl:value-of select="$bootstrap.min.js" /></xsl:attribute></script>
            <script>
                $(document).ready(onDocumentReady);
            </script>
            <script><xsl:attribute name="src"><xsl:value-of select="$prism.js" /></xsl:attribute></script>
            <script><xsl:attribute name="src"><xsl:value-of select="$prism-php.min.js" /></xsl:attribute></script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>