<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:func="http://exslt.org/functions" extension-element-prefixes="func"
                xmlns:phpqa="https://github.com/EdgedesignCZ/phpqa/functions"
                version="1.0">
<!-- XSL from https://github.com/theseer/phpdox/blob/master/templates/html/index.xsl -->
    <xsl:output method="html" indent="yes"/>

    <func:function name="phpqa:format-number">
        <xsl:param name="value"/>
        <xsl:param name="format">0.##</xsl:param>
            <func:result>
                <xsl:choose>
                <xsl:when test="string(number($value))='NaN'">
                    <xsl:value-of select="format-number(0, $format)"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="format-number($value, $format)"/>
                </xsl:otherwise>
            </xsl:choose>
        </func:result>
    </func:function>

    <xsl:template match="phploc">
        <style>
        .indent {
            text-indent: 1em;
        }

        .indent2 {
            text-indent: 2em;
        }

        .nummeric {
            text-align: right;
        }

        .nummeric a {
            display: block;
            background-color: #CCD;
            padding: 0 0.5em;
        }

        .nummeric a:hover, .nummeric a:focus, .nummeric a:active {
            background-color: #DDE;
            outline: dotted 2px #AAB;
        }

        .nummeric a:hover {
            outline-style: solid;
        }

        .nummeric a:active {
            outline: solid 2px #667;
        }

        .percent {
            text-align: right;
            width:5em;
        }
        nav {
            margin-bottom: 1em;
        }
        </style>
        <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$bootstrap.min.css" /></xsl:attribute></link>
        

        <div class="container">
            
            <h1>phploc report</h1>

            <nav>
                <ul class="nav nav-pills" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overview</a>
                    </li>
                    <li role="presentation">
                        <a href="#ccn" aria-controls="ccn" role="tab" data-toggle="tab">Cyclomatic Complexity</a>
                    </li>
                    <li role="presentation">
                        <a href="#dependencies" aria-controls="dependencies" role="tab" data-toggle="tab">Dependencies</a>
                    </li>
                </ul>
            </nav>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="overview">
                    <table class="table table-striped">
                        <tr>
                            <td>Directories</td>
                            <td class="nummeric"><xsl:value-of select="directories" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td>Files</td>
                            <td class="nummeric"><xsl:value-of select="files" /></td>
                            <td class="percent" />
                        </tr>
                    </table>

                    <h2>Size</h2>
                    <table class="table table-striped">
                        <tr>
                            <td>Lines of Code (LOC)</td>
                            <td class="nummeric"><xsl:value-of select="loc" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td>Comment Lines of Code (CLOC)</td>
                            <td class="nummeric"><xsl:value-of select="cloc" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(cloc div loc * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Non-Comment Lines of Code (NCLOC)</td>
                            <td class="nummeric"><xsl:value-of select="ncloc" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(ncloc div loc * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Logical Lines of Code (LLOC)</td>
                            <td class="nummeric"><xsl:value-of select="lloc" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(lloc div loc * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Classes</td>
                            <td class="nummeric"><xsl:value-of select="llocClasses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(llocClasses div lloc * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Average Class Length</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(classLlocAvg)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td class="indent2">Minimum Class Length</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(classLlocMin)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td class="indent2">Maximum Class Length</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(classLlocMax)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td class="indent">Average Method Length</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(methodLlocAvg)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td class="indent2">Minimum Method Length</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(methodLlocMin)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td class="indent2">Maximum Method Length</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(methodLlocMax)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td>Functions</td>
                            <td class="nummeric"><xsl:value-of select="llocFunctions" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(llocFunctions div lloc * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Average Function Length</td>
                            <td class="nummeric"><xsl:value-of select="round(llocByNof)" /></td>
                            <td/>
                        </tr>
                        <tr>
                            <td>Not in classes or functions</td>
                            <td class="nummeric"><xsl:value-of select="llocGlobal" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(llocGlobal div lloc * 100,'0.##')" />%)</td>
                        </tr>
                    </table>

                    <h2>Structure</h2>
                    <table class="table table-striped">
                        <tr>
                            <td>Namespaces</td>
                            <td class="nummeric"><xsl:value-of select="namespaces" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td>Interfaces</td>
                            <td class="nummeric"><xsl:value-of select="interfaces" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td>Traits</td>
                            <td class="nummeric"><xsl:value-of select="traits" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td>Classes</td>
                            <td class="nummeric"><xsl:value-of select="classes" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Abstract Classes</td>
                            <td class="nummeric"><xsl:value-of select="abstractClasses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(abstractClasses div classes * 100, '0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Concrete Classes</td>
                            <td class="nummeric"><xsl:value-of select="concreteClasses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(concreteClasses div classes * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Methods</td>
                            <td class="nummeric"><xsl:value-of select="methods" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Scope</td>
                            <td class="percent" />
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent2">Non-Static Methods</td>
                            <td class="nummeric"><xsl:value-of select="nonStaticMethods" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(nonStaticMethods div methods * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent2">Static Methods</td>
                            <td class="nummeric"><xsl:value-of select="staticMethods" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(staticMethods div methods * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Visibility</td>
                            <td class="percent" />
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent2">Public Method</td>
                            <td class="nummeric"><xsl:value-of select="publicMethods" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(publicMethods div methods * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent2">Non-Public Methods</td>
                            <td class="nummeric"><xsl:value-of select="nonPublicMethods" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(nonPublicMethods div methods * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Functions</td>
                            <td class="nummeric"><xsl:value-of select="functions" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Named Functions</td>
                            <td class="nummeric"><xsl:value-of select="namedFunctions" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(namedFunctions div functions * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Anonymous Functions</td>
                            <td class="nummeric"><xsl:value-of select="anonymousFunctions" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(anonymousFunctions div functions * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Constants</td>
                            <td class="nummeric"><xsl:value-of select="constants" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Global Constants</td>
                            <td class="nummeric"><xsl:value-of select="globalConstants" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(globalConstants div constants * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Class Constants</td>
                            <td class="nummeric"><xsl:value-of select="classConstants" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(classConstants div constants * 100,'0.##')" />%)</td>
                        </tr>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="ccn">
                    <table class="table table-striped">
                        <tr>
                            <td>Average Complexity per LLOC</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(ccnByLloc, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                        <tr>
                            <td>Average Complexity per Class</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(classCcnAvg, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                        <tr>
                            <td class="indent">Minimum Class Complexity</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(classCcnMin, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                        <tr>
                            <td class="indent">Maximum Class Complexity</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(classCcnMax, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                        <tr>
                            <td>Average Complexity per Method</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(methodCcnAvg, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                        <tr>
                            <td class="indent">Minimum Method Complexity</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(methodCcnMin, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                        <tr>
                            <td class="indent">Maximum Method Complexity</td>
                            <td class="nummeric"><xsl:value-of select="phpqa:format-number(methodCcnMax, '0.##')" /></td>
                            <td class="percent"/>
                        </tr>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="dependencies">
                    <table class="table table-striped">
                        <tr>
                            <td>Global Accesses</td>
                            <td class="nummeric"><xsl:value-of select="globalAccesses" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Global Constants</td>
                            <td class="nummeric"><xsl:value-of select="globalConstantAccesses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(globalConstantAccesses div globalAccesses * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Global Variables</td>
                            <td class="nummeric"><xsl:value-of select="globalVariableAccesses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(globalVariableAccesses div globalAccesses * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Super-Global Variables</td>
                            <td class="nummeric"><xsl:value-of select="superGlobalVariableAccesses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(superGlobalVariableAccesses div globalAccesses * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Attribute Accesses</td>
                            <td class="nummeric"><xsl:value-of select="attributeAccesses" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Non-Static</td>
                            <td class="nummeric"><xsl:value-of select="instanceAttributeAccesses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(instanceAttributeAccesses div attributeAccesses * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Static</td>
                            <td class="nummeric"><xsl:value-of select="staticAttributeAccesses" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(staticAttributeAccesses div attributeAccesses * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td>Method Calls</td>
                            <td class="nummeric"><xsl:value-of select="methodCalls" /></td>
                            <td class="percent" />
                        </tr>
                        <tr>
                            <td class="indent">Non-Static</td>
                            <td class="nummeric"><xsl:value-of select="instanceMethodCalls" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(instanceMethodCalls div methodCalls * 100,'0.##')" />%)</td>
                        </tr>
                        <tr>
                            <td class="indent">Static</td>
                            <td class="nummeric"><xsl:value-of select="staticMethodCalls" /></td>
                            <td class="percent">(<xsl:value-of select="phpqa:format-number(staticMethodCalls div methodCalls * 100,'0.##')" />%)</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <script><xsl:attribute name="src"><xsl:value-of select="$jquery.min.js" /></xsl:attribute></script>
        <script><xsl:attribute name="src"><xsl:value-of select="$bootstrap.min.js" /></xsl:attribute></script>
    </xsl:template>

</xsl:stylesheet>
