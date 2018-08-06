<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:key name="error-category" match="/report/item" use="type" />
    <xsl:key name="file-category" match="/report/item" use="file_name" />
    <xsl:key name="node-uid" match="/report/item" use="item" />
    <xsl:output method="html"  encoding="UTF-8"/>
    <xsl:param name="root-directory"/>

    <xsl:template name="group_by_file">
        <xsl:param name="file_name"/>
        <xsl:for-each select="/report/item[file_name = $file_name]">
            <xsl:variable name="uid" select="generate-id(.)" />
            <tr style="cursor: pointer">
                <xsl:attribute name="data-group">
                    <xsl:value-of select="file_name/text()" />
                </xsl:attribute>
                <xsl:attribute name="data-severity">
                    <xsl:value-of select="severity/text()" />
                </xsl:attribute>
                <xsl:attribute name="onclick">
                    <xsl:text>$('#</xsl:text>
                    <xsl:value-of select="$uid" />
                    <xsl:text>').toggle()</xsl:text>
                </xsl:attribute>
                <td>
                    <xsl:choose>
                        <xsl:when test="severity/text() = 'error'">
                            <span class="label label-danger">error</span>
                        </xsl:when>
                        <xsl:otherwise>
                            <span class="label label-warning">info</span>
                        </xsl:otherwise>
                    </xsl:choose>
                </td>
                <td>
                    <xsl:value-of select="message/text()" /><br />
                    <span class="text-muted"><xsl:value-of select="type/text()" /></span>
                </td>
                <td><xsl:value-of select="line_from" /></td>
                <td><xsl:value-of select="line_to" /></td>
            </tr>
            <tr style="display:none">
                <xsl:attribute name="id">
                    <xsl:value-of select="$uid" />
                </xsl:attribute>
                <xsl:attribute name="data-snippet">
                    <xsl:value-of select="$uid" />
                </xsl:attribute>
                <td colspan="5">
                    <pre class="text-muted">
                        <xsl:value-of select="substring(snippet/text(), 0, number(from) - number(snippet_from) + 1)" />
                        <span class="text-danger bg-danger">
                            <xsl:value-of select="substring(snippet/text(), number(from) - number(snippet_from) + 1, number(to) - number(from))" />
                        </span>
                        <xsl:value-of select="substring(snippet/text(), number(to) - number(snippet_from) + 1, number(snippet_to) - number(to))" />
                    </pre>
                </td>
            </tr>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="/">
        <html>
            <head>
                <title>Psalm report</title>
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
                    <h1>psalm report</h1>

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
                                        <th>Infos</th>
                                        <th>Files</th>
                                    </tr>
                                </thead>
                                <!-- http://stackoverflow.com/a/9589085/4587679 -->
                                <xsl:for-each select="/report/item[generate-id() = generate-id(key('error-category', ./type)[1])]">
                                    <xsl:variable name="group" select="type/text()"/>
                                    <tr>
                                        <td><xsl:value-of select="$group"/></td>
                                        <td><xsl:value-of select="count(/report/item[severity/text()='error' and type/text() = $group])"/></td>
                                        <td><xsl:value-of select="count(/report/item[severity/text()='info' and type/text() = $group])"/></td>
                                        <td><xsl:value-of select="count(//file_name[../type/text() = $group and not(preceding::file_name/. = .)])" /></td>
                                    </tr>
                                </xsl:for-each>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <th><span class="label label-danger"><xsl:value-of select="count(/report/item[severity/text()='error'])" /></span></th>
                                        <th><span class="label label-warning"><xsl:value-of select="count(/report/item[severity/text()='info'])" /></span></th>
                                        <th><span class="label label-info"><xsl:value-of select="count(//file_name[not(preceding::file_name/. = .)])" /></span></th>
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
                                    var errorsTr = $('tr[data-group][data-severity=error]');
                                    var otherTr = $('tr[data-group][data-severity]:not([data-severity=error])');
                                    var snippetTr = $('tr[data-snippet]');

                                    function matchElements(elements) {
                                        var term = $("[data-search]").val();
                                        return elements.filter(function () {
                                            var rowContent = $(this).text().toLowerCase();
                                            return rowContent.indexOf(term) !== -1 <![CDATA[&&]]> $(this).is(':not([data-snippet])')
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

                                    function filter() {
                                        snippetTr.hide();
                                        groups.show();
                                        rows.hide();

                                        matchElements(rows).show();

                                        var active = $('#only-error').is(':checked');

                                        if (active) {
                                            otherTr.hide();
                                        }

                                        matchEmptyGroups().hide();
                                    }

                                    $("[data-search]").keyup(function () {
                                        filter();
                                    });
                                    $("#only-error").change(function () {
                                        filter();
                                    });
                                });
                            </script>

                            <table class="table" data-filterable="errors">
                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            Error

                                            <label class="checkbox-inline">
                                                <input type="checkbox" value="" id="only-error" />
                                                Only errors
                                            </label>
                                        </th>
                                        <th>Line From</th>
                                        <th>Line To</th>
                                    </tr>
                                </thead>
                                <xsl:for-each select="//item[file_name[not(preceding::file_name/. = .)]]">
                                    <tr>
                                        <xsl:attribute name="data-permanent">
                                            <xsl:value-of select="file_name/text()" />
                                        </xsl:attribute>
                                        <xsl:variable name="file" select="file_name/text()"/>
                                        <td colspan="5" class="file"><strong data-file=""><xsl:value-of select="$file" /></strong></td>
                                    </tr>
                                    <xsl:call-template name="group_by_file">
                                        <xsl:with-param name="file_name" select="file_name" />
                                    </xsl:call-template>
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
