<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html"  encoding="UTF-8"/>
    <xsl:key name="error-category" match="/checkstyle/file/error" use="@source" />
    <xsl:key name="file-category" match="/checkstyle/file" use="error/@source" />
    <xsl:param name="root-directory"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <title>CodeSniffer report</title>
                <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$bootstrap.min.css" /></xsl:attribute></link>
                <style>
                    .file {
                        background: #f9f9f9
                    }
                    .fixed-navbar {
                        list-style-type: none;
                        position: fixed;
                        top: 0;
                        right: 1em;
                    }
                </style>
                <script>
                var onDocumentReady = [
                    function () {
                        $('[data-file]').each(function () {
                            var pathWithoutRoot = $(this).text().replace('<xsl:value-of select="$root-directory"></xsl:value-of>', '');
                            $(this).text(pathWithoutRoot);
                        });
                    }
                ];
                </script>
            </head>
            <body>

            <div class="container-fluid">
            
                <h1>phpcs report</h1>

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
                                    <th>Category</th>
                                    <th>Errors</th>
                                    <th>Warnings</th>
                                    <th>Files</th>
                                </tr>
                            </thead>
                            <!-- http://stackoverflow.com/a/9589085/4587679 -->
                            <xsl:for-each select="/checkstyle/file/error[generate-id() = generate-id(key('error-category', ./@source)[1])]">
                                <xsl:variable name="group" select="@source"/>
                                <tr>
                                    <td><xsl:value-of select="$group"/></td>
                                    <td><xsl:value-of select="count(/checkstyle/file/error[@severity='error' and @source = $group])"/></td>
                                    <td><xsl:value-of select="count(/checkstyle/file/error[@severity='warning' and @source = $group])"/></td>
                                    <td><xsl:value-of select="count(key('file-category', $group))" /></td>
                                    <td></td>
                                </tr>
                            </xsl:for-each>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <th><span class="label label-danger"><xsl:value-of select="count(/checkstyle/file/error[@severity='error'])" /></span></th>
                                    <th><span class="label label-warning"><xsl:value-of select="count(/checkstyle/file/error[@severity='warning'])" /></span></th>
                                    <th><span class="label label-info"><xsl:value-of select="count(/checkstyle/file)" /></span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="errors">
                        <div class="fixed-navbar">
                            <div class="input-group" style="width: 20em">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                                <input data-search="errors" type="text" class="form-control" placeholder="sideeffect..." />
                            </div>
                        </div>
                        <script>
                        onDocumentReady.push(function () {
                            var groups = $('[data-filterable] tbody tr[data-permanent]');
                            var rows = $('[data-filterable] tbody tr:not([data-permanent])');

                            $("[data-search]").keyup(function () {
                                var term = $(this).val().toLowerCase();

                                rows.hide();
                                groups.show();
                                matchElements(rows).show();
                                matchEmptyGroups().hide();

                                function matchElements(elements) {
                                    return elements.filter(function () {
                                        var rowContent = $(this).text().toLowerCase();
                                        return rowContent.indexOf(term) !== -1
                                    });
                                }

                                function matchEmptyGroups() {
                                    return groups.filter(function () {
                                        var group = $(this).data('permanent');
                                        return rows
                                            .filter(function () {
                                                return $(this).data('group') == group <![CDATA[&&]]> $(this).is(':visible');
                                            })
                                            .length == 0;
                                    });
                                }
                            });
                        });
                        </script>

                        <table class="table" data-filterable="errors">
                            <thead>
                                <tr>
                                    <th colspan="2">Error</th>
                                    <th>Line</th>
                                    <th>Column</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="/checkstyle/file">
                                <tr>
                                    <xsl:attribute name="data-permanent">
                                        <xsl:value-of select="@name" />
                                    </xsl:attribute>
                                    <td colspan="5" class="file"><strong data-file=""><xsl:value-of select="@name" /></strong></td>
                                </tr>
                                <xsl:for-each select="./error">
                                    <tr>
                                        <xsl:attribute name="data-group">
                                            <xsl:value-of select="../@name" />
                                        </xsl:attribute>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="@severity = 'error'">
                                                    <span class="label label-danger">error</span>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <span class="label label-warning">warning</span>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </td>
                                        <td>
                                            <xsl:value-of select="@message" /><br />
                                            <span class="text-muted"><xsl:value-of select="@source" /></span>
                                        </td>
                                        <td><xsl:value-of select="@line" /></td>
                                        <td><xsl:value-of select="@column" /></td>
                                    </tr>
                                </xsl:for-each>
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
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>