<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
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

<xsl:output method="html" indent="yes"  encoding="US-ASCII"/>

<xsl:template match="PDepend">
    <html>
    <head>
        <title>PHPDepend Analysis</title>
    <style type="text/css">
        table, nav {
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

        .fixed-links {
            list-style-type: none;
            position: fixed;
            top: 3em;
            right: 1em;
        }
        .fixed-links li {
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
      </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" />
    <script>var onDocumentReady = [];</script>

    </head>
    <body>

        <div class="container-fluid">
            <nav>
                <ul class="nav nav-pills" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#summary" aria-controls="summary" role="tab" data-toggle="tab">Summary</a>
                    </li>
                    <li role="presentation">
                        <a href="#packages" aria-controls="packages" role="tab" data-toggle="tab">Packages</a>
                    </li>
                    <li role="presentation">
                        <a href="#metrics" aria-controls="metrics" role="tab" data-toggle="tab">Metrics</a>
                    </li>
                </ul>
            </nav>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="summary">
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
                            <th>Number of Method or Function Calls</th>
                            <td><xsl:value-of select="./metrics/@calls"/></td>
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
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="packages">
                    <xsl:apply-templates select="./Packages"></xsl:apply-templates>
                </div>
                <div role="tabpanel" class="tab-pane" id="metrics">
                    <xsl:apply-templates select="./metrics"></xsl:apply-templates>
                </div>
            </div>
        </div>        

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(onDocumentReady);
        </script>
    </body>
    </html>
</xsl:template>

<!-- XSL from https://github.com/elnebuloso/phing-commons/blob/cc8478f930b38fe7542542d9490128e73d707356/resources/ -->
<xsl:template match="Packages">

    <ul class="fixed-links">
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

<!-- XSL from https://gist.github.com/garex/5cd9b97c40f3369cb8cf60f253868df9 -->
<xsl:template match="metrics">
    <div class="fixed-links">
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