<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html" indent="yes"  encoding="US-ASCII"/>
<xsl:param name="root-directory"/>

<xsl:template match="PDepend">
    <html>
    <head>
        <title>PHPDepend Analysis</title>
    <style type="text/css">
      table {
            margin: 1em 0;
        }
      table.details tr th {
        font-weight: bold;
        text-align:left;
        background:#a6caf0;
      }
      table.details tr td {
        background:#eeeee0;
      }
      .Error {
        font-weight:bold; color:red;
      }
      .Failure {
        font-weight:bold; color:purple;
      }
      .Properties {
        text-align:right;
      }
      img {
        height: 300px;
      }  
      p.cycle {
        margin-top:0.5em; margin-bottom:1.0em;
        margin-left:2em;
        margin-right:2em;
      }

        .fixed-navbar {
            list-style-type: none;
            position: fixed;
            top: 0;
            right: 1em;
        }
        .fixed-navbar li {
            display: inline-block;
            margin-left: 1ex;
        }
        img {
            max-width: 700px !important;
            width: 100%;
        }
        dt a:target {
            background: yellow;
        }
        bt.btn-link {
            margin-left: 1ex;
        }
        
      ul.methods { -webkit-column-count: 3; }
      .position-top-20 { color: red; }
      .position-top-10 { font-weight: bold; }
      .position-top-20 .badge {
            background: #f0ad4e
      }
      .position-top-10 .badge {
            background: #d9534f
      }
      .ccn-is-low { color: gray;  font-size: 80%; }
      .ccn-is-low button { display: none; }
        
      .datatable {
            width: 100% !Important;
      }
      .well {
            margin: 1ex 0;
      }
      </style>
    <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$bootstrap.min.css" /></xsl:attribute></link>
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
            
            <h1>PDepend report</h1>
            
            <nav>
                <ul class="nav nav-pills" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overview</a>
                    </li>
                    <li role="presentation">
                        <a href="#packages" aria-controls="packages" role="tab" data-toggle="tab">Packages</a>
                    </li>
                    <li role="presentation">
                        <a href="#dependencies" aria-controls="dependencies" role="tab" data-toggle="tab">Dependencies</a>
                    </li>
                    <li role="presentation">
                        <a href="#complexity" aria-controls="complexity" role="tab" data-toggle="tab">Complexity</a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Metrics <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#metrics-file" aria-controls="metrics-file" role="tab" data-toggle="tab">Files</a></li>
                            <li><a href="#metrics-package" aria-controls="metrics-package" role="tab" data-toggle="tab">Packages</a></li>
                            <li><a href="#metrics-class" aria-controls="metrics-class" role="tab" data-toggle="tab">Classes</a></li>
                            <li><a href="#metrics-trait" aria-controls="metrics-trait" role="tab" data-toggle="tab">Traits</a></li>
                            <li><a href="#metrics-method" aria-controls="metrics-method" role="tab" data-toggle="tab">Methods</a></li>
                            <li><a href="#metrics-function" aria-controls="metrics-function" role="tab" data-toggle="tab">Functions</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="overview">
                    <div class="row">
                        <div class="col-sm-6">
                            <img src="pdepend-pyramid.svg" class="img-responsive" />
                        </div>
                        <div class="col-sm-6">
                            <img src="pdepend-jdepend.svg" class="img-responsive" />
                        </div>
                    </div>

                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tr>
                            <th>Cyclomatic Complexity Number</th>
                            <td><xsl:value-of select="./metrics/@ccn"/></td>
                        </tr>
                        <tr>
                            <th>Extended Cyclomatic Complexity</th>
                            <td><xsl:value-of select="./metrics/@ccn2"/></td>
                        </tr>
                        <tr>
                            <th>Number of Method or Function Calls</th>
                            <td><xsl:value-of select="./metrics/@calls"/></td>
                        </tr>
                        <tr>
                            <th>Number of Root Classes</th>
                            <td><xsl:value-of select="./metrics/@roots"/></td>
                        </tr>
                        <tr>
                            <th>Average Hierarchy Height</th>
                            <td><xsl:value-of select="./metrics/@ahh"/></td>
                        </tr>
                        <tr>
                            <th>Average Number of Derived Classes</th>
                            <td><xsl:value-of select="./metrics/@andc"/></td>
                        </tr>
                        <tr>
                            <th>Number of Leaf Classes</th>
                            <td><xsl:value-of select="./metrics/@leafs"/></td>
                        </tr>
                        <tr>
                            <th>Number of Fanouts</th>
                            <td><xsl:value-of select="./metrics/@fanout"/></td>
                        </tr>
                        <tr>
                            <th>Max Depth of Inheritance Tree</th>
                            <td><xsl:value-of select="./metrics/@maxDIT"/></td>
                        </tr>
                        <tr>
                            <th>Lines of Code</th>
                            <td><xsl:value-of select="./metrics/@loc"></xsl:value-of></td>
                        </tr>
                        <tr>
                            <th>Non-Comment Line of Code</th>
                            <td><xsl:value-of select="./metrics/@ncloc"></xsl:value-of></td>
                        </tr>
                        <tr>
                            <th>Comment Lines of Code</th>
                            <td><xsl:value-of select="./metrics/@cloc"></xsl:value-of></td>
                        </tr>
                        <tr>
                            <th>Executable Lines of Code</th>
                            <td><xsl:value-of select="./metrics/@eloc"></xsl:value-of></td>
                        </tr>
                        <tr>
                            <th>Logical Lines Of Code</th>
                            <td><xsl:value-of select="./metrics/@lloc"></xsl:value-of></td>
                        </tr>
                        <tr>
                            <th>Packages</th>
                            <td><xsl:value-of select="./metrics/@nop"/></td>
                        </tr>
                        <tr>
                            <th>Classes</th>
                            <td><xsl:value-of select="./metrics/@noc"/></td>
                        </tr>
                        <tr>
                            <th>Abstract classes</th>
                            <td><xsl:value-of select="./metrics/@clsa"/></td>
                        </tr>
                        <tr>
                            <th>Concrete classes</th>
                            <td><xsl:value-of select="./metrics/@clsc"/></td>
                        </tr>
                        <tr>
                            <th>Interfaces</th>
                            <td><xsl:value-of select="./metrics/@noi"/></td>
                        </tr>
                        <tr>
                            <th>Methods</th>
                            <td><xsl:value-of select="./metrics/@nom"/></td>
                        </tr>
                        <tr>
                            <th>Functions</th>
                            <td><xsl:value-of select="./metrics/@nof"/></td>
                        </tr>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="packages">
                    <xsl:apply-templates select="./Packages"></xsl:apply-templates>
                </div>
                <div role="tabpanel" class="tab-pane" id="dependencies">
                    <xsl:apply-templates select="./dependencies"></xsl:apply-templates>
                </div>
                <div role="tabpanel" class="tab-pane" id="complexity">
                    <xsl:apply-templates select="./metrics"></xsl:apply-templates>
                </div>

                <div role="tabpanel" class="tab-pane" id="metrics-file">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Lines of Code</th>
                                <th>Comment Lines of Code</th>
                                <th>Non-Comment Line of Code</th>
                                <th>Executable Lines of Code</th>
                                <th>Logical Lines Of Code</th>
                            </tr>
                        </thead>
                        <xsl:for-each select="./metrics/files/file">
                        <tr>
                            <td><strong data-file=""><xsl:value-of select="@name"></xsl:value-of></strong></td>
                            <td><xsl:value-of select="@loc"></xsl:value-of></td>
                            <td><xsl:value-of select="@cloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@ncloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@eloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@lloc"></xsl:value-of></td>
                        </tr>
                        </xsl:for-each>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="metrics-package">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Number of Classes</th>
                                <th>Number of Interfaces</th>
                                <th>Number of Methods</th>
                                <th>Number of Functions</th>
                                <th>Code Rank</th>
                                <th>Reverse Code Rank</th>
                            </tr>
                        </thead>
                        <xsl:for-each select="./metrics/package">
                        <tr>
                            <th><xsl:value-of select="@name"></xsl:value-of></th>
                            <td><xsl:value-of select="@noc"></xsl:value-of></td>
                            <td><xsl:value-of select="@noi"></xsl:value-of></td>
                            <td><xsl:value-of select="@nom"></xsl:value-of></td>
                            <td><xsl:value-of select="@nof"></xsl:value-of></td>
                            <td><xsl:value-of select="@cr"></xsl:value-of></td>
                            <td><xsl:value-of select="@rcr"></xsl:value-of></td>
                        </tr>
                        </xsl:for-each>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="metrics-class">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Lines of Code</th>
                                <th>Comment Lines of Code</th>
                                <th>Non-Comment Line of Code</th>
                                <th>Executable Lines of Code</th>
                                <th>Logical Lines Of Code</th>
                                <th>Code Rank</th>
                                <th>Reverse Code Rank</th>
                                <th>Afferent Coupling</th>
                                <th>Efferent Coupling</th>
                                <th>Coupling Between Objects</th>
                                <th>Class Size</th>
                                <th>Class Interface Size</th>
                                <th>Implemented Interfaces</th>
                                <th>Number of Methods</th>
                                <th>Number of Overwritten Methods</th>
                                <th>Number of Public Methods</th>
                                <th>Number of Added Methods</th>
                                <th>Class Properties</th>
                                <th>Inherited Properties</th>
                                <th>Non Private Properties</th>
                                <th>Weighted Method Count</th>
                                <th>Inherited Weighted Method Count</th>
                                <th>Non Private Weighted Method Count</th>
                                <th>Depth of Inheritance Tree</th>
                                <th>Number of Child Classes</th>
                            </tr>
                        </thead>
                        <xsl:for-each select="./metrics/package/class">
                        <tr>
                            <th><xsl:value-of select="../@name"></xsl:value-of>\<xsl:value-of select="@name"></xsl:value-of></th>
                            <td><xsl:value-of select="@loc"></xsl:value-of></td>
                            <td><xsl:value-of select="@cloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@ncloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@eloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@lloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@cr"></xsl:value-of></td>
                            <td><xsl:value-of select="@rcr"></xsl:value-of></td>
                            <td><xsl:value-of select="@ca"></xsl:value-of></td>
                            <td><xsl:value-of select="@ce"></xsl:value-of></td>
                            <td><xsl:value-of select="@cbo"></xsl:value-of></td>
                            <td><xsl:value-of select="@csz"></xsl:value-of></td>
                            <td><xsl:value-of select="@cis"></xsl:value-of></td>
                            <td><xsl:value-of select="@impl"></xsl:value-of></td>
                            <td><xsl:value-of select="@nom"></xsl:value-of></td>
                            <td><xsl:value-of select="@noom"></xsl:value-of></td>
                            <td><xsl:value-of select="@npm"></xsl:value-of></td>
                            <td><xsl:value-of select="@noam"></xsl:value-of></td>
                            <td><xsl:value-of select="@vars"></xsl:value-of></td>
                            <td><xsl:value-of select="@varsi"></xsl:value-of></td>
                            <td><xsl:value-of select="@varsnp"></xsl:value-of></td>
                            <td><xsl:value-of select="@wmc"></xsl:value-of></td>
                            <td><xsl:value-of select="@wmci"></xsl:value-of></td>
                            <td><xsl:value-of select="@wmcnp"></xsl:value-of></td>
                            <td><xsl:value-of select="@dit"></xsl:value-of></td>
                            <td><xsl:value-of select="@nocc"></xsl:value-of></td>
                        </tr>
                        </xsl:for-each>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="metrics-trait">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Trait</th>
                                <th>Afferent Coupling</th>
                                <th>Efferent Coupling</th>
                                <th>Coupling Between Objects</th>
                                <th>Class Size</th>
                                <th>Class Interface Size</th>
                                <th>Implemented Interfaces</th>
                                <th>Number of Public Methods</th>
                                <th>Class Properties</th>
                                <th>Inherited Properties</th>
                                <th>Non Private Properties</th>
                                <th>Weighted Method Count</th>
                                <th>Inherited Weighted Method Count</th>
                                <th>Non Private Weighted Method Count</th>
                            </tr>
                        </thead>
                        <xsl:for-each select="./metrics/package/trait">
                        <tr>
                            <th><xsl:value-of select="../@name"></xsl:value-of>\<xsl:value-of select="@name"></xsl:value-of></th>
                            <td><xsl:value-of select="@ca"></xsl:value-of></td>
                            <td><xsl:value-of select="@ce"></xsl:value-of></td>
                            <td><xsl:value-of select="@cbo"></xsl:value-of></td>
                            <td><xsl:value-of select="@csz"></xsl:value-of></td>
                            <td><xsl:value-of select="@cis"></xsl:value-of></td>
                            <td><xsl:value-of select="@impl"></xsl:value-of></td>
                            <td><xsl:value-of select="@npm"></xsl:value-of></td>
                            <td><xsl:value-of select="@vars"></xsl:value-of></td>
                            <td><xsl:value-of select="@varsi"></xsl:value-of></td>
                            <td><xsl:value-of select="@varsnp"></xsl:value-of></td>
                            <td><xsl:value-of select="@wmc"></xsl:value-of></td>
                            <td><xsl:value-of select="@wmci"></xsl:value-of></td>
                            <td><xsl:value-of select="@wmcnp"></xsl:value-of></td>
                        </tr>
                        </xsl:for-each>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="metrics-method">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Lines of Code</th>
                                <th>Comment Lines of Code</th>
                                <th>Non-Comment Line of Code</th>
                                <th>Executable Lines of Code</th>
                                <th>Logical Lines Of Code</th>
                                <th>Cyclomatic Complexity</th>
                                <th>Extended Cyclomatic Complexity</th>
                                <th>NPath Complexity</th>
                                <th>Maintainability Index</th>
                                <th>Halstead Bugs</th>
                                <th>Halstead Difficulty</th>
                                <th>Halstead Effort</th>
                                <th>Halstead Content</th>
                                <th>Halstead Level</th>
                                <th>Halstead Vocabulary</th>
                                <th>Halstead Length</th>
                                <th>Halstead Time</th>
                                <th>Halstead Volumne</th>
                            </tr>
                        </thead>
                        <xsl:for-each select="./metrics/package/*/method">
                        <tr>
                            <th><xsl:value-of select="../../@name"></xsl:value-of>\<xsl:value-of select="../@name"></xsl:value-of>::<xsl:value-of select="@name"></xsl:value-of>()</th>
                            <td><xsl:value-of select="@loc"></xsl:value-of></td>
                            <td><xsl:value-of select="@cloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@ncloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@eloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@lloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@ccn"></xsl:value-of></td>
                            <td><xsl:value-of select="@ccn2"></xsl:value-of></td>
                            <td><xsl:value-of select="@npath"></xsl:value-of></td>
                            <td><xsl:value-of select="@mi"></xsl:value-of></td>
                            <td><xsl:value-of select="@hb"></xsl:value-of></td>
                            <td><xsl:value-of select="@hd"></xsl:value-of></td>
                            <td><xsl:value-of select="@he"></xsl:value-of></td>
                            <td><xsl:value-of select="@hi"></xsl:value-of></td>
                            <td><xsl:value-of select="@hl"></xsl:value-of></td>
                            <td><xsl:value-of select="@hnd"></xsl:value-of></td>
                            <td><xsl:value-of select="@hnt"></xsl:value-of></td>
                            <td><xsl:value-of select="@ht"></xsl:value-of></td>
                            <td><xsl:value-of select="@hv"></xsl:value-of></td>
                        </tr>
                        </xsl:for-each>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="metrics-function">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Function</th>
                                <th>Lines of Code</th>
                                <th>Comment Lines of Code</th>
                                <th>Non-Comment Line of Code</th>
                                <th>Executable Lines of Code</th>
                                <th>Logical Lines Of Code</th>
                                <th>Cyclomatic Complexity</th>
                                <th>Extended Cyclomatic Complexity</th>
                                <th>NPath Complexity</th>
                                <th>Maintainability Index</th>
                                <th>Halstead Bugs</th>
                                <th>Halstead Difficulty</th>
                                <th>Halstead Effort</th>
                                <th>Halstead Content</th>
                                <th>Halstead Level</th>
                                <th>Halstead Vocabulary</th>
                                <th>Halstead Length</th>
                                <th>Halstead Time</th>
                                <th>Halstead Volumne</th>
                            </tr>
                        </thead>
                        <xsl:for-each select="./metrics/package/function">
                        <tr>
                            <th><xsl:value-of select="../../@name"></xsl:value-of>\<xsl:value-of select="@name"></xsl:value-of>()</th>
                            <td><xsl:value-of select="@loc"></xsl:value-of></td>
                            <td><xsl:value-of select="@cloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@ncloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@eloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@lloc"></xsl:value-of></td>
                            <td><xsl:value-of select="@ccn"></xsl:value-of></td>
                            <td><xsl:value-of select="@ccn2"></xsl:value-of></td>
                            <td><xsl:value-of select="@npath"></xsl:value-of></td>
                            <td><xsl:value-of select="@mi"></xsl:value-of></td>
                            <td><xsl:value-of select="@hb"></xsl:value-of></td>
                            <td><xsl:value-of select="@hd"></xsl:value-of></td>
                            <td><xsl:value-of select="@he"></xsl:value-of></td>
                            <td><xsl:value-of select="@hi"></xsl:value-of></td>
                            <td><xsl:value-of select="@hl"></xsl:value-of></td>
                            <td><xsl:value-of select="@hnd"></xsl:value-of></td>
                            <td><xsl:value-of select="@hnt"></xsl:value-of></td>
                            <td><xsl:value-of select="@ht"></xsl:value-of></td>
                            <td><xsl:value-of select="@hv"></xsl:value-of></td>
                        </tr>
                        </xsl:for-each>
                    </table>
                </div>
            </div>
        </div>        

        <script><xsl:attribute name="src"><xsl:value-of select="$jquery.min.js" /></xsl:attribute></script>
        <script><xsl:attribute name="src"><xsl:value-of select="$bootstrap.min.js" /></xsl:attribute></script>
        <script><xsl:attribute name="src"><xsl:value-of select="$jquery.dataTables.min.js" /></xsl:attribute></script>
        <script><xsl:attribute name="src"><xsl:value-of select="$dataTables.bootstrap.min.js" /></xsl:attribute></script>
        <script><xsl:attribute name="src"><xsl:value-of select="$dataTables.responsive.min.js" /></xsl:attribute></script>
        <script><xsl:attribute name="src"><xsl:value-of select="$responsive.bootstrap.min.js" /></xsl:attribute></script>
        <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$dataTables.bootstrap.min.css" /></xsl:attribute></link>
        <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$responsive.bootstrap.min.css" /></xsl:attribute></link>
        <script><xsl:attribute name="src"><xsl:value-of select="$selectize.min.js" /></xsl:attribute></script>
        <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$selectize.bootstrap3.css" /></xsl:attribute></link>
        <script>
            onDocumentReady.push(function () {
                $('table.datatable').each(function () {
                    var table = $(this);
                    var datatable = table.DataTable({"lengthMenu": [[50, 100, -1], [50, 100, "All"]]});
                    var defaultVisibleColumns = [1,2,3,4,5];

                    var select = $('<![CDATA[<select multiple>]]>');
                    loadOptions();
                    var box = buildCustomControls();

                    var selectize = select.selectize({maxItems: 50})[0].selectize;
                    listenOnSelect();
                    listenOnToggle();
                    showColumns(defaultVisibleColumns);

                    function loadOptions() {
                        var columns = getMetrics();

                        for (var column in columns) {
                            var selected = column &lt;= defaultVisibleColumns.length ? 'selected="selected" ' : '';
                            select.append($(<![CDATA['<option ' + selected + ' value=' + (parseInt(column) + 1) + '>' + columns[column] + '</option>']]>));
                        }
                    }

                    function getMetrics() {
                        return table.find('thead th:not(:first)').map(function () {
                            return $(this).text();
                        }).get();
                    }

                    function buildCustomControls() {
                        return $('<![CDATA[<div class="well well-sm">]]>')
                            .append(<![CDATA[
                                '<small class="text-muted">Metrics</small>' +
                                '<div class="pull-right"><button class="btn btn-link btn-sm" data-show>Show all</button><button class="btn btn-link btn-sm" data-hide>Hide all</button></div>'
                            ]]>)
                            .append(select)
                            .insertBefore(table.closest('.dataTables_wrapper'));
                    }

                    function listenOnSelect() {
                        selectize.on('change', function (values) {
                            var visibleColumns = values ? values.map(Number) : [];
                            showColumns(visibleColumns);
                        });
                    }

                    function listenOnToggle() {
                        box.find('[data-hide]').click(function (e) {
                            e.preventDefault();
                            selectize.clear();
                        });

                        box.find('[data-show]').click(function (e) {
                            e.preventDefault();
                            var allKeys = Object.keys(selectize.options);
                            selectize.setValue(allKeys);
                        });
                    }

                    function showColumns(visibleColumns) {
                        datatable.columns().visible(false);
                        datatable.columns(0).visible(true);
                        datatable.columns(visibleColumns).visible(true);
                    }
                });
            });

            $(document).ready(onDocumentReady);
        </script>
    </body>
    </html>
</xsl:template>

<!-- XSL from https://github.com/elnebuloso/phing-commons/blob/cc8478f930b38fe7542542d9490128e73d707356/resources/ -->
<!--
   Licensed to the Apache Software Foundation (ASF) under one or more
   contributor license agreements.  See the NOTICE file distributed with
   this work for additional information regarding copyright ownership.
   The ASF licenses this file to You under the Apache License, Version 2.0
   (the "License"); you may not use this file except in compliance with
   the License.  You may obtain a copy of the License at
       http://www.apache.org/licenses/LICENSE-2.0
   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
-->
<xsl:template match="Packages">

    <ul class="fixed-navbar">
        <li><a class="label label-default" href="#NVsummary">summary</a></li>
        <li><a class="label label-default" href="#NVpackages">packages</a></li>
        <li><a class="label label-default" href="#NVcycles">cycles</a></li>
        <li><a class="label label-default" href="#NVexplanations">explanations</a></li>
    </ul>

    <h3 id="NVsummary">Summary</h3>

    <table class="details table table-bordered">
        <tr>
            <th>Package</th>
            <th>Total Classes</th>
            <th><a href="#EXnumber">Abstract Classes</a></th>
            <th><a href="#EXnumber">Concrete Classes</a></th>
            <th><a href="#EXafferent">Afferent Couplings</a></th>
            <th><a href="#EXefferent">Efferent Couplings</a></th>
            <th><a href="#EXabstractness">Abstractness</a></th>
            <th><a href="#EXinstability">Instability</a></th>
            <th><a href="#EXdistance">Distance</a></th>

        </tr>
    <xsl:for-each select="./Package">
        <xsl:if test="count(error) = 0">
            <tr>
                <td align="left">
                    <a>
                    <xsl:attribute name="href">#PK<xsl:value-of select="@name"/>
                    </xsl:attribute>
                    <xsl:value-of select="@name"/>
                    </a>
                </td>
                <td align="right"><xsl:value-of select="Stats/TotalClasses"/></td>
                <td align="right"><xsl:value-of select="Stats/AbstractClasses"/></td>
                <td align="right"><xsl:value-of select="Stats/ConcreteClasses"/></td>
                <td align="right"><xsl:value-of select="Stats/Ca"/></td>
                <td align="right"><xsl:value-of select="Stats/Ce"/></td>
                <td align="right"><xsl:value-of select="Stats/A"/></td>
                <td align="right"><xsl:value-of select="Stats/I"/></td>
                <td align="right"><xsl:value-of select="Stats/D"/></td>


            </tr>
        </xsl:if>
    </xsl:for-each>
    <xsl:for-each select="./Package">
        <xsl:if test="count(error) &gt; 0">
            <tr>
                <td align="left">
                    <xsl:value-of select="@name"/>
                </td>
                <td align="left" colspan="8"><xsl:value-of select="error"/></td>
            </tr>
        </xsl:if>
    </xsl:for-each>
    </table>

    <h3 id="NVpackages">Packages</h3>

    <xsl:for-each select="./Package">
        <xsl:if test="count(error) = 0">
            <h4>
                <xsl:attribute name="id">PK<xsl:value-of select="@name"/></xsl:attribute>
                <xsl:value-of select="@name"/>
            </h4>

            <table width="100%"><tr>
                <td><a href="#EXafferent">Afferent Couplings</a>: <xsl:value-of select="Stats/Ca"/></td>
                <td><a href="#EXefferent">Efferent Couplings</a>: <xsl:value-of select="Stats/Ce"/></td>
                <td><a href="#EXabstractness">Abstractness</a>: <xsl:value-of select="Stats/A"/></td>
                <td><a href="#EXinstability">Instability</a>: <xsl:value-of select="Stats/I"/></td>
                <td><a href="#EXdistance">Distance</a>: <xsl:value-of select="Stats/D"/></td>
            </tr></table>

            <table width="100%" class="table table-bordered details">
                <tr>
                    <th>Abstract Classes</th>
                    <th>Concrete Classes</th>
                    <th>Used by Packages</th>
                    <th>Uses Packages</th>
                </tr>
                <tr>
                    <td valign="top" width="25%">
                    <xsl:if test="count(AbstractClasses/Class)=0">
                            <i>None</i>
                        </xsl:if>
                        <xsl:for-each select="AbstractClasses/Class">
                            <xsl:value-of select="node()"/><br/>
                        </xsl:for-each>
                    </td>
                    <td valign="top" width="25%">
                        <xsl:if test="count(ConcreteClasses/Class)=0">
                            <i>None</i>
                        </xsl:if>
                        <xsl:for-each select="ConcreteClasses/Class">
                            <xsl:value-of select="node()"/><br/>
                        </xsl:for-each>
                    </td>
                    <td valign="top" width="25%">
                        <xsl:if test="count(UsedBy/Package)=0">
                            <i>None</i>
                        </xsl:if>
                        <xsl:for-each select="UsedBy/Package">
                            <a>
                                <xsl:attribute name="href">#PK<xsl:value-of select="node()"/></xsl:attribute>
                                <xsl:value-of select="node()"/>
                            </a><br/>
                        </xsl:for-each>
                    </td>
                    <td valign="top" width="25%">
                        <xsl:if test="count(DependsUpon/Package)=0">
                            <i>None</i>
                        </xsl:if>
                        <xsl:for-each select="DependsUpon/Package">
                            <a>
                                <xsl:attribute name="href">#PK<xsl:value-of select="node()"/></xsl:attribute>
                                <xsl:value-of select="node()"/>
                            </a><br/>
                        </xsl:for-each>
                    </td>
                </tr>
            </table>
        </xsl:if>
    </xsl:for-each>

    <h3 id="NVcycles">Cycles</h3>

    <xsl:if test="count(../Cycles/Package) = 0">
        <p>There are no cyclic dependancies.</p>
    </xsl:if>
    <xsl:for-each select="../Cycles/Package">
        <h4><xsl:value-of select="@Name"/></h4>
        <p class="cycle">
        <xsl:for-each select="Package">
            <xsl:value-of select="."/><br/>
        </xsl:for-each>
        </p>
    </xsl:for-each>

    <h3 id="NVexplanations">Explanations</h3>

    <p class="text-muted">The following explanations are for quick reference and are lifted directly from the original <a href="http://www.clarkware.com/software/JDepend.html">JDepend documentation</a>.</p>

    <dl class="dl-horizontal">
      <dt><a name="EXnumber">Number of Classes</a></dt>
      <dd>
          <p>The number of concrete and abstract classes (and interfaces) in the package is an indicator of the extensibility of the package.</p>
      </dd>
    </dl>
    <dl class="dl-horizontal">
      <dt><a name="EXafferent">Afferent Couplings</a></dt>
      <dd>
          <p>The number of other packages that depend upon classes within the package is an indicator of the package's responsibility. </p>
      </dd>
    </dl>
    <dl class="dl-horizontal">
      <dt><a name="EXefferent">Efferent Couplings</a></dt>
      <dd>
          <p>The number of other packages that the classes in the package depend upon is an indicator of the package's independence. </p>
      </dd>
    </dl>
    <dl class="dl-horizontal">
      <dt><a name="EXabstractness">Abstractness</a></dt>
      <dd>
          <p>The ratio of the number of abstract classes (and interfaces) in the analyzed package to the total number of classes in the analyzed package. </p>
            <p>The range for this metric is 0 to 1, with A=0 indicating a completely concrete package and A=1 indicating a completely abstract package. </p>
      </dd>
    </dl>
    <dl class="dl-horizontal">
      <dt><a name="EXinstability">Instability</a></dt>
      <dd>
          <p>The ratio of efferent coupling (Ce) to total coupling (Ce / (Ce + Ca)). This metric is an indicator of the package's resilience to change. </p>
          <p>The range for this metric is 0 to 1, with I=0 indicating a completely stable package and I=1 indicating a completely instable package. </p>
      </dd>
    </dl>
    <dl class="dl-horizontal">
      <dt><a name="EXdistance">Distance</a></dt>
      <dd>
        <p>The perpendicular distance of a package from the idealized line A + I = 1. This metric is an indicator of the package's balance between abstractness and stability. </p>
        <p>A package squarely on the main sequence is optimally balanced with respect to its abstractness and stability. Ideal packages are either completely abstract and stable (x=0, y=1) or completely concrete and instable (x=1, y=0). </p>
        <p>The range for this metric is 0 to 1, with D=0 indicating a package that is coincident with the main sequence and D=1 indicating a package that is as far from the main sequence as possible. </p>
      </dd>
    </dl>


</xsl:template>

<!-- inspired by Dependencies in Qafoo https://github.com/Qafoo/QualityAnalyzer/blob/master/src/images/screen.png -->
<xsl:template match="dependencies">
    <div class="fixed-navbar">
        <div class="input-group" style="width: 20em">
            <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
            <input data-search="dependencies" type="text" class="form-control" placeholder="progressbar..." />
        </div>
        <small class="help-block">Results count: <strong data-results-count=""></strong></small>
    </div>

    <script>
    onDocumentReady.push(function () {
        var rows = $('[data-filterable] tbody tr');
        var dependencies = rows.find('[data-dependency], small.text-muted');
        var resultsCount = $('[data-results-count]');

        $("[data-search]").keyup(function () {
            var term = $(this).val().toLowerCase();

            rows.hide();
            var visibleRows = matchElements(rows);
            visibleRows.show();
            resultsCount.text(visibleRows.length);

            dependencies.removeClass('highlight');
            if (term) {
                matchElements(dependencies).addClass('highlight');
            }

            function matchElements(elements) {
                return elements.filter(function () {
                    var rowContent = $(this).text().toLowerCase();
                    return rowContent.indexOf(term) !== -1
                });
            }
        });
    });
    </script>
    <style>.highlight {background: yellow}</style>

    <table class="table table-bordered details" data-filterable="dependencies">
        <thead>
            <tr>
                <th>Dependency</th>
                <th>Efferent Couplings</th>
                <th>Afferent Couplings</th>
            </tr>
        </thead>
    <xsl:for-each select="./package"> 
        <xsl:for-each select="./*"> 
        <tr>
            <td>
                <strong data-dependency=""> 
                    <xsl:value-of select="./../@name"/>\<xsl:value-of select="@name"/> 
                </strong> 
                <br />
                <small class="text-muted"><xsl:value-of select="name(.)"/></small>
            </td> 
            <td>
                <xsl:for-each select="./efferent/type"> 
                    <span data-dependency=""><xsl:value-of select="@namespace" />\<xsl:value-of select="@name" /></span><br />
                </xsl:for-each> 
            </td>
            <td>
                <xsl:for-each select="./afferent/type"> 
                    <span data-dependency=""><xsl:value-of select="@namespace" />\<xsl:value-of select="@name" /></span><br />
                </xsl:for-each> 
            </td>
        </tr>
        </xsl:for-each> 
    </xsl:for-each> 
    </table>
</xsl:template>

<!-- XSL from https://gist.github.com/garex/5cd9b97c40f3369cb8cf60f253868df9 -->
<xsl:template match="metrics">
    <div class="fixed-navbar">
        <div class="checkbox-inline">
            <label>
                <input type="checkbox" id="class-ranks" />
                show complex classes
            </label>
        </div>
        <div class="checkbox-inline">
            <label title="CCN = 1">
                <input type="checkbox" id="methods-low-ccn" />
                show methods with low complexity
            </label>
        </div>
    </div>
    <script>
        onDocumentReady.push(toggleComplexClasses);
        onDocumentReady.push(toggleMethods);

        function toggleComplexClasses() {
            var checkboxClass = $('#class-ranks');
            var classes = $('.not-top-position').closest('div');
            var packages = $('[data-package]');
        
            toggleClasses();
            checkboxClass.change(toggleClasses);
        
            function toggleClasses() {
                var areHidden = checkboxClass.is(':checked');
                if (areHidden) {
                    classes.hide();
                    getPackagesWithNoComplexClass().hide();
                } else {
                    classes.show();
                    packages.show();
                }
            }

            function getPackagesWithNoComplexClass() {
                return packages.filter(function () {
                    var topClassesCount = $(this).find('h4[class*="position-top"]').length;
                    return topClassesCount == 0;
                });
            }
        }

        function toggleMethods() {
            var checkbox = $('#methods-low-ccn');
            var methods = $('.ccn-is-low');
        
            toggleMethods();
            checkbox.change(toggleMethods);
        
            function toggleMethods() {
                var areHidden = checkbox.is(':not(:checked)');
                if (areHidden) {
                    methods.hide();
                } else {
                    methods.show();
                }
            }
        }
    </script>

    <xsl:for-each select="./package">
        
        <div data-package="">
            <h3>
                <xsl:value-of select="@name"/>
                <button class="btn btn-link btn-sm" title="Google PageRank applied on Packages and Classes. Classes with a high value should be tested frequently.">
                    code rank <span class="badge"><xsl:value-of select="@cr"/></span>
                </button>
            </h3>
            <ul class="classes">
                <xsl:apply-templates select="class">
                    <xsl:sort
                        select="@wmc"
                        data-type="number"
                        order="descending"
                    />
                </xsl:apply-templates>
            </ul>
        </div>
    </xsl:for-each>
</xsl:template>

<xsl:template match="class">
    <div>
        <h4>
            <xsl:attribute name="class">
                <xsl:if test="0.1 > position() div count(../class)">
                    position-top-10
                </xsl:if>
                <xsl:if test="0.2 > position() div count(../class)">
                    position-top-20
                </xsl:if>
                <xsl:if test="position() div count(../class) >= 0.2">
                    not-top-position
                </xsl:if>
            </xsl:attribute>
            <xsl:value-of select="@name"/>
            <button class="btn btn-link btn-sm" title="Sum of the complexities of all declared methods and constructors of class.">
                weighted method count <span class="badge"><xsl:value-of select="@wmc"/></span>
            </button>
            <button class="btn btn-link btn-sm" title="Number of unique outgoing dependencies to other artifacts of the same type">
                outgoing coupling <span class="badge"><xsl:value-of select="@cbo"/></span>
            </button>
        </h4>
        <ul class="methods">
        <xsl:apply-templates select="method">
          <xsl:sort
            select="@npath"
            data-type="number"
            order="descending"
            />
        </xsl:apply-templates>
        </ul>
        
    </div>
</xsl:template>
<xsl:template match="method">
  <p>
    <xsl:attribute name="class">
      <xsl:if test="0.1 > position() div count(../method)">
        position-top-10
      </xsl:if>
      <xsl:if test="0.2 > position() div count(../method)">
        position-top-20
      </xsl:if>
      <xsl:if test="1 >= @ccn">
        ccn-is-low
      </xsl:if>
    </xsl:attribute>
    <xsl:value-of select="@name"/>
    <button class="btn btn-link btn-sm" title="Cyclomatic Complexity Number">
        cyclo <span class="badge"><xsl:value-of select="@ccn"/></span>
    </button>
    <button class="btn btn-link btn-sm" title="NPath Complexity">
        npath <span class="badge"><xsl:value-of select="@npath"/></span>
    </button>
  </p>
</xsl:template>
</xsl:stylesheet>