<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html"  encoding="UTF-8"/>
    <xsl:key name="error-category" match="/pmd/file/violation" use="@rule" />
    <xsl:key name="file-category" match="/pmd/file" use="violation/@rule" />

    <xsl:template match="/">
        <html>
            <head>
                <title>PHPMD report</title>
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
                    .priority-1 {background: #d9534f;}
                    .priority-2 {background: #f0ad4e;}
                    .priority-3 {background: #f0ad4e;}
                    .priority-4 {background: #5bc0de;}
                    .priority-5 {background: #5bc0de;}
                    .alert {
                        margin: 1em 0;
                    }
                </style>
                <script>
                var onDocumentReady = [
                    function () {
                        $('[data-file]').each(function () {
                            var pathWithoutRoot = $(this).text().replaceAll('<xsl:value-of select="$root-directory"></xsl:value-of>', '');
                            $(this).text(pathWithoutRoot);
                        });
                    }
                ];
                </script>
            </head>
            <body>

            <div class="container-fluid">
            
                <h1>phpmd report</h1>

                <nav>
                    <ul class="nav nav-pills" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overview</a>
                        </li>
                        <li role="presentation">
                            <a href="#errors" aria-controls="errors" role="tab" data-toggle="tab">Errors</a>
                        </li>
                        <li role="presentation">
                            <a href="#parsing" aria-controls="parsing" role="tab" data-toggle="tab">Parsing Errors</a>
                        </li>
                    </ul>
                </nav>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="overview">
                        <xsl:if test="count(/pmd/error) > 0">
                            <div class="alert alert-danger">
                                <strong>Errors are not analyzed in <xsl:value-of select="count(/pmd/error)" /> file(s)!</strong>
                                PHPMD cannot parse the file(s). Check <a href="#parsing" data-toggle="tab">Parsing Errors</a>
                            </div>
                        </xsl:if>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rule</th>
                                    <th>Errors</th>
                                    <th>Files</th>
                                </tr>
                            </thead>
                            <!-- http://stackoverflow.com/a/9589085/4587679 -->
                            <xsl:for-each select="/pmd/file/violation[generate-id() = generate-id(key('error-category', ./@rule)[1])]">
                                <xsl:variable name="group" select="@rule"/>
                                <tr>
                                    <td><xsl:value-of select="$group"/></td>
                                    <td><xsl:value-of select="count(key('error-category', $group))" /></td>
                                    <td><xsl:value-of select="count(key('file-category', $group))" /></td>
                                </tr>
                            </xsl:for-each>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <th><span class="label label-danger"><xsl:value-of select="count(/pmd/file/violation)" /></span></th>
                                    <th><span class="label label-info"><xsl:value-of select="count(/pmd/file)" /></span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="errors">
                        <div class="fixed-navbar">
                            <div class="input-group" style="width: 20em">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                                <input data-search="errors" type="text" class="form-control" placeholder="unused..." />
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
                                    <th colspan="2">Rule</th>
                                    <th>Error</th>
                                    <th>Lines</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="/pmd/file">
                                <tr>
                                    <xsl:attribute name="data-permanent">
                                        <xsl:value-of select="@name" />
                                    </xsl:attribute>
                                    <td colspan="5" class="file"><strong data-file=""><xsl:value-of select="@name" /></strong></td>
                                </tr>
                                <xsl:for-each select="./violation">
                                    <tr>
                                        <xsl:attribute name="data-group">
                                            <xsl:value-of select="../@name" />
                                        </xsl:attribute>
                                        <td>
                                            <span>
                                                <xsl:attribute name="class">
                                                label priority-<xsl:value-of select="@priority" />
                                                </xsl:attribute>
                                                <xsl:value-of select="@priority" />
                                            </span>
                                        </td>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="@externalInfoUrl != '#'">
                                                    <a>
                                                        <xsl:attribute name="href">
                                                            <xsl:value-of select="@externalInfoUrl" />
                                                        </xsl:attribute>
                                                        <xsl:value-of select="@rule" />
                                                    </a>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:value-of select="@rule" />
                                                </xsl:otherwise>
                                            </xsl:choose>
                                            <br />
                                            <span class="text-muted"><xsl:value-of select="@ruleset" /></span>
                                        </td>
                                        <td>
                                            <xsl:value-of select="text()" /><br />
                                            <small class="text-muted">
                                                <xsl:if test="@package"><xsl:value-of select="@package" />\</xsl:if>
                                                <xsl:if test="@class"><xsl:value-of select="@class" /></xsl:if>
                                                <xsl:if test="@method">::<xsl:value-of select="@method" /></xsl:if>
                                                <xsl:value-of select="@function" />
                                            </small>
                                        </td>
                                        <td><xsl:value-of select="@beginline" />-<xsl:value-of select="@endline" /></td>
                                    </tr>
                                </xsl:for-each>
                            </xsl:for-each>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="parsing">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="/pmd/error">
                                <tr>
                                    <td><strong data-file=""><xsl:value-of select="@filename" /></strong></td>
                                    <td><span data-file=""><xsl:value-of select="@msg" /></span></td>
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
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>