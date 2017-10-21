<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html"  encoding="UTF-8"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <title>PHP CS Fixer report</title>
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
                        $('[data-fixers]').each(function () {
                            var original = $(this).text();
                            var fixers = original.replace("applied fixers:\n---------------", '');
                            var html = fixers.split('* ').splice(1).join('<br />');
                            $(this).html(html);
                        });
                    }
                ];
                </script>
            </head>
            <body>

            <div class="container-fluid">
            
                <h1>PHP CS Fixer report</h1>

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
                                    <th>Files</th>
                                    <th>Errors</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tr>
                                <td><xsl:value-of select="/testsuites/testsuite/@tests" /></td>
                                <th><span class="label label-danger"><xsl:value-of select="/testsuites/testsuite/@failures" /></span></th>
                                <th><span class="label label-info"><xsl:value-of select="/testsuites/testsuite/@time" /></span></th>
                            </tr>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="errors">
                        <div class="fixed-navbar">
                            <div class="input-group" style="width: 20em">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                                <input data-search="errors" type="text" class="form-control" placeholder="trailing..." />
                            </div>
                        </div>
                        <script>
                        onDocumentReady.push(function () {
                            var rows = $('[data-filterable] tbody tr');

                            $("[data-search]").keyup(function () {
                                var term = $(this).val().toLowerCase();

                                rows.hide();
                                matchElements(rows).show();

                                function matchElements(elements) {
                                    return elements.filter(function () {
                                        var rowContent = $(this).text().toLowerCase();
                                        return rowContent.indexOf(term) !== -1
                                    });
                                }
                            });
                        });
                        </script>
                        <table class="table table-striped table-hover" data-filterable="errors">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Errors (fixers)</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="/testsuites/testsuite/testcase">
                                <xsl:for-each select="./failure">
                                    <tr>
                                        <td><strong><xsl:value-of select="../@name" /></strong></td>
                                        <td data-fixers=""><xsl:value-of select="current()" /></td>
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