var classes = [
    {
        "name": "Edge\\QA\\IgnoredPathsTest",
        "interface": false,
        "methods": [
            {
                "name": "ignore",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testNoOptionWhenNothingIsIgnored",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testShouldIgnoreDirectories",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideTools",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 4,
        "nbMethods": 4,
        "nbMethodsPrivate": 1,
        "nbMethodsPublic": 3,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 1,
        "externals": [
            "PHPUnit_Framework_TestCase",
            "Edge\\QA\\IgnoredPaths"
        ],
        "lcom": 2,
        "length": 85,
        "vocabulary": 41,
        "volume": 455.39,
        "difficulty": 2.1,
        "effort": 957.49,
        "level": 0.48,
        "bugs": 0.15,
        "time": 53,
        "intelligentContent": 216.59,
        "number_operators": 3,
        "number_operands": 82,
        "number_operators_unique": 2,
        "number_operands_unique": 39,
        "cloc": 5,
        "loc": 25,
        "lloc": 23,
        "mi": 83.48,
        "mIwoC": 51.55,
        "commentWeight": 31.94,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 4,
        "relativeDataComplexity": 1.17,
        "relativeSystemComplexity": 5.17,
        "totalStructuralComplexity": 16,
        "totalDataComplexity": 4.67,
        "totalSystemComplexity": 20.67,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 2,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\ConfigTest",
        "interface": false,
        "methods": [
            {
                "name": "testLoadDefaultConfig",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testBuildAbsolutePath",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testOverrideDefaultConfig",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testIgnoreNonExistentUserConfig",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testThrowExceptionWhenFileDoesNotExist",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "shouldStopPhpqa",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 6,
        "nbMethods": 6,
        "nbMethodsPrivate": 1,
        "nbMethodsPublic": 5,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 1,
        "externals": [
            "PHPUnit_Framework_TestCase",
            "Edge\\QA\\Config",
            "Edge\\QA\\Config",
            "Edge\\QA\\Config",
            "Edge\\QA\\Config",
            "Edge\\QA\\Config"
        ],
        "lcom": 4,
        "length": 57,
        "vocabulary": 19,
        "volume": 242.13,
        "difficulty": 2.88,
        "effort": 697.91,
        "level": 0.35,
        "bugs": 0.08,
        "time": 39,
        "intelligentContent": 84,
        "number_operators": 8,
        "number_operands": 49,
        "number_operators_unique": 2,
        "number_operands_unique": 17,
        "cloc": 0,
        "loc": 45,
        "lloc": 45,
        "mi": 47.11,
        "mIwoC": 47.11,
        "commentWeight": 0,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 25,
        "relativeDataComplexity": 0,
        "relativeSystemComplexity": 25,
        "totalStructuralComplexity": 150,
        "totalDataComplexity": 0,
        "totalSystemComplexity": 150,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 6,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\RunningToolTest",
        "interface": false,
        "methods": [
            {
                "name": "testBuildOptionWithDefinedSeparator",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testMarkSuccessWhenXPathIsNotDefined",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testMarkFailureWhenXmlFileDoesNotExist",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testCompareAllowedCountWithErrorsCountFromXml",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideAllowedErrors",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testAnalyzeExitCodeInCliMode",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideProcess",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 7,
        "nbMethods": 7,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 7,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 1,
        "externals": [
            "PHPUnit_Framework_TestCase",
            "Edge\\QA\\RunningTool",
            "Edge\\QA\\RunningTool",
            "Edge\\QA\\RunningTool",
            "Edge\\QA\\RunningTool",
            "Edge\\QA\\RunningTool"
        ],
        "lcom": 6,
        "length": 83,
        "vocabulary": 33,
        "volume": 418.68,
        "difficulty": 3.7,
        "effort": 1549.13,
        "level": 0.27,
        "bugs": 0.14,
        "time": 86,
        "intelligentContent": 113.16,
        "number_operators": 9,
        "number_operands": 74,
        "number_operators_unique": 3,
        "number_operands_unique": 30,
        "cloc": 4,
        "loc": 43,
        "lloc": 41,
        "mi": 69.08,
        "mIwoC": 46.33,
        "commentWeight": 22.76,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 49,
        "relativeDataComplexity": 0.34,
        "relativeSystemComplexity": 49.34,
        "totalStructuralComplexity": 343,
        "totalDataComplexity": 2.38,
        "totalSystemComplexity": 345.38,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 6,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\ReportTest",
        "interface": false,
        "methods": [
            {
                "name": "setUp",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testConvertTwigToHtml",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testConvertXmlToHtml",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideXml",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testIgnoreMissingXmlDocuments",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "tearDown",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 6,
        "nbMethods": 6,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 6,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 2,
        "externals": [
            "PHPUnit_Framework_TestCase"
        ],
        "lcom": 2,
        "length": 39,
        "vocabulary": 20,
        "volume": 168.56,
        "difficulty": 4,
        "effort": 674.22,
        "level": 0.25,
        "bugs": 0.06,
        "time": 37,
        "intelligentContent": 42.14,
        "number_operators": 7,
        "number_operands": 32,
        "number_operators_unique": 4,
        "number_operands_unique": 16,
        "cloc": 1,
        "loc": 37,
        "lloc": 36,
        "mi": 62.79,
        "mIwoC": 50.19,
        "commentWeight": 12.6,
        "kanDefect": 0.22,
        "relativeStructuralComplexity": 0,
        "relativeDataComplexity": 1.33,
        "relativeSystemComplexity": 1.33,
        "totalStructuralComplexity": 0,
        "totalDataComplexity": 8,
        "totalSystemComplexity": 8,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 1,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\OptionsTest",
        "interface": false,
        "methods": [
            {
                "name": "setUp",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "overrideOptions",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testEscapePaths",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testIgnorePdependInCliOutput",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testIgnoreNotInstalledTool",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testLoadDirectoryWithCustomConfig",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideConfig",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testBuildOutput",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideOutputs",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testExecute",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideExecutionMode",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testBuildRootPath",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "provideAnalyzedDir",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "testLoadAllowedErrorsCount",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 14,
        "nbMethods": 14,
        "nbMethodsPrivate": 1,
        "nbMethodsPublic": 13,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 1,
        "externals": [
            "PHPUnit_Framework_TestCase",
            "Edge\\QA\\Options"
        ],
        "lcom": 5,
        "length": 141,
        "vocabulary": 57,
        "volume": 822.44,
        "difficulty": 3.5,
        "effort": 2878.53,
        "level": 0.29,
        "bugs": 0.27,
        "time": 160,
        "intelligentContent": 234.98,
        "number_operators": 15,
        "number_operands": 126,
        "number_operators_unique": 3,
        "number_operands_unique": 54,
        "cloc": 8,
        "loc": 82,
        "lloc": 76,
        "mi": 61.69,
        "mIwoC": 38.43,
        "commentWeight": 23.26,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 49,
        "relativeDataComplexity": 0.72,
        "relativeSystemComplexity": 49.72,
        "totalStructuralComplexity": 686,
        "totalDataComplexity": 10.13,
        "totalSystemComplexity": 696.13,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 2,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\RoboFile",
        "interface": false,
        "methods": [],
        "nbMethodsIncludingGettersSetters": 0,
        "nbMethods": 0,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 0,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 1,
        "externals": [
            "Robo\\Tasks"
        ],
        "lcom": 0,
        "length": 0,
        "vocabulary": 0,
        "volume": 0,
        "difficulty": 0,
        "effort": 0,
        "level": 0,
        "bugs": 0,
        "time": 0,
        "intelligentContent": 0,
        "number_operators": 0,
        "number_operands": 0,
        "number_operators_unique": 0,
        "number_operands_unique": 0,
        "cloc": 0,
        "loc": 5,
        "lloc": 5,
        "mi": 171,
        "mIwoC": 171,
        "commentWeight": 0,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 0,
        "relativeDataComplexity": 0,
        "relativeSystemComplexity": 0,
        "totalStructuralComplexity": 0,
        "totalDataComplexity": 0,
        "totalSystemComplexity": 0,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 1,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\Config",
        "interface": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "loadCustomConfig",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "value",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "path",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "get",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "findInConfig",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "throwInvalidPath",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 7,
        "nbMethods": 7,
        "nbMethodsPrivate": 3,
        "nbMethodsPublic": 4,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 6,
        "externals": [
            "Symfony\\Component\\Yaml\\Yaml",
            "RuntimeException"
        ],
        "lcom": 1,
        "length": 102,
        "vocabulary": 30,
        "volume": 500.5,
        "difficulty": 9.88,
        "effort": 4942.47,
        "level": 0.1,
        "bugs": 0.17,
        "time": 275,
        "intelligentContent": 50.68,
        "number_operators": 23,
        "number_operands": 79,
        "number_operators_unique": 6,
        "number_operands_unique": 24,
        "cloc": 0,
        "loc": 60,
        "lloc": 60,
        "mi": 41.5,
        "mIwoC": 41.5,
        "commentWeight": 0,
        "kanDefect": 0.89,
        "relativeStructuralComplexity": 25,
        "relativeDataComplexity": 1.4,
        "relativeSystemComplexity": 26.4,
        "totalStructuralComplexity": 175,
        "totalDataComplexity": 9.83,
        "totalSystemComplexity": 184.83,
        "pageRank": 0.11,
        "afferentCoupling": 5,
        "efferentCoupling": 2,
        "instability": 0.29,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\Options",
        "interface": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "loadOutput",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getCommonRootPath",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getAnalyzedDirs",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "loadTools",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "buildRunningTools",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "toFile",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "rawFile",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 8,
        "nbMethods": 8,
        "nbMethodsPrivate": 2,
        "nbMethodsPublic": 6,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 6,
        "externals": [
            "Edge\\QA\\IgnoredPaths",
            "Edge\\QA\\RunningTool"
        ],
        "lcom": 1,
        "length": 157,
        "vocabulary": 42,
        "volume": 846.59,
        "difficulty": 10,
        "effort": 8465.94,
        "level": 0.1,
        "bugs": 0.28,
        "time": 470,
        "intelligentContent": 84.66,
        "number_operators": 37,
        "number_operands": 120,
        "number_operators_unique": 6,
        "number_operands_unique": 36,
        "cloc": 9,
        "loc": 88,
        "lloc": 79,
        "mi": 61.07,
        "mIwoC": 37.3,
        "commentWeight": 23.77,
        "kanDefect": 0.82,
        "relativeStructuralComplexity": 16,
        "relativeDataComplexity": 1.58,
        "relativeSystemComplexity": 17.58,
        "totalStructuralComplexity": 128,
        "totalDataComplexity": 12.6,
        "totalSystemComplexity": 140.6,
        "pageRank": 0.1,
        "afferentCoupling": 2,
        "efferentCoupling": 2,
        "instability": 0.5,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\Task\\TableSummary",
        "interface": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "__invoke",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "result",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getStatus",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "writeln",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 5,
        "nbMethods": 5,
        "nbMethodsPrivate": 3,
        "nbMethodsPublic": 2,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 7,
        "externals": [
            "Edge\\QA\\Options",
            "Symfony\\Component\\Console\\Output\\OutputInterface",
            "Symfony\\Component\\Console\\Helper\\Table",
            "Symfony\\Component\\Console\\Helper\\TableSeparator"
        ],
        "lcom": 1,
        "length": 127,
        "vocabulary": 48,
        "volume": 709.29,
        "difficulty": 6.05,
        "effort": 4288.73,
        "level": 0.17,
        "bugs": 0.24,
        "time": 238,
        "intelligentContent": 117.31,
        "number_operators": 23,
        "number_operands": 104,
        "number_operators_unique": 5,
        "number_operands_unique": 43,
        "cloc": 5,
        "loc": 66,
        "lloc": 61,
        "mi": 60.83,
        "mIwoC": 40.15,
        "commentWeight": 20.68,
        "kanDefect": 0.73,
        "relativeStructuralComplexity": 81,
        "relativeDataComplexity": 0.54,
        "relativeSystemComplexity": 81.54,
        "totalStructuralComplexity": 405,
        "totalDataComplexity": 2.7,
        "totalSystemComplexity": 407.7,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 4,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\Task\\ToolVersions",
        "interface": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "__invoke",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "findComposerPackages",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "composerInfo",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "toolToTableRow",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "normalizeVersion",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "groupAuthors",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "consoleInfo",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "loadVersionFromConsoleCommand",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getFirstLine",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 10,
        "nbMethods": 10,
        "nbMethodsPrivate": 8,
        "nbMethodsPublic": 2,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 8,
        "externals": [
            "Symfony\\Component\\Console\\Output\\OutputInterface",
            "Symfony\\Component\\Console\\Helper\\Table",
            "Robo\\Task\\Base\\Exec"
        ],
        "lcom": 1,
        "length": 163,
        "vocabulary": 53,
        "volume": 933.65,
        "difficulty": 10.04,
        "effort": 9377.1,
        "level": 0.1,
        "bugs": 0.31,
        "time": 521,
        "intelligentContent": 92.96,
        "number_operators": 31,
        "number_operands": 132,
        "number_operators_unique": 7,
        "number_operands_unique": 46,
        "cloc": 0,
        "loc": 81,
        "lloc": 81,
        "mi": 36.5,
        "mIwoC": 36.5,
        "commentWeight": 0,
        "kanDefect": 1.12,
        "relativeStructuralComplexity": 225,
        "relativeDataComplexity": 0.64,
        "relativeSystemComplexity": 225.64,
        "totalStructuralComplexity": 2250,
        "totalDataComplexity": 6.38,
        "totalSystemComplexity": 2256.38,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 3,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\Task\\NonParallelExec",
        "interface": false,
        "methods": [
            {
                "name": "run",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 1,
        "nbMethods": 1,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 1,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 9,
        "externals": [
            "Edge\\QA\\Task\\ParallelExec",
            "Symfony\\Component\\Console\\Helper\\ProgressBar",
            "Robo\\Result"
        ],
        "lcom": 1,
        "length": 76,
        "vocabulary": 23,
        "volume": 343.79,
        "difficulty": 9.88,
        "effort": 3397.46,
        "level": 0.1,
        "bugs": 0.11,
        "time": 189,
        "intelligentContent": 34.79,
        "number_operators": 20,
        "number_operands": 56,
        "number_operators_unique": 6,
        "number_operands_unique": 17,
        "cloc": 10,
        "loc": 50,
        "lloc": 40,
        "mi": 78.02,
        "mIwoC": 46.08,
        "commentWeight": 31.94,
        "kanDefect": 1.12,
        "relativeStructuralComplexity": 169,
        "relativeDataComplexity": 0.07,
        "relativeSystemComplexity": 169.07,
        "totalStructuralComplexity": 169,
        "totalDataComplexity": 0.07,
        "totalSystemComplexity": 169.07,
        "pageRank": 0.03,
        "afferentCoupling": 0,
        "efferentCoupling": 3,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\Task\\ParallelExec",
        "interface": false,
        "methods": [
            {
                "name": "process",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 1,
        "nbMethods": 1,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 1,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "ccn": 1,
        "externals": [
            "Robo\\Task\\Base\\ParallelExec",
            "parent"
        ],
        "lcom": 1,
        "length": 4,
        "vocabulary": 3,
        "volume": 6.34,
        "difficulty": 0.75,
        "effort": 4.75,
        "level": 1.33,
        "bugs": 0,
        "time": 0,
        "intelligentContent": 8.45,
        "number_operators": 1,
        "number_operands": 3,
        "number_operators_unique": 1,
        "number_operands_unique": 2,
        "cloc": 4,
        "loc": 13,
        "lloc": 9,
        "mi": 111.3,
        "mIwoC": 73.43,
        "commentWeight": 37.87,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 1,
        "relativeDataComplexity": 1,
        "relativeSystemComplexity": 2,
        "totalStructuralComplexity": 1,
        "totalDataComplexity": 1,
        "totalSystemComplexity": 2,
        "pageRank": 0.06,
        "afferentCoupling": 1,
        "efferentCoupling": 2,
        "instability": 0.67,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\IgnoredPaths",
        "interface": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "csvToArray",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "phpcs",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "pdepend",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "phpmd",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "phpmetrics",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "phpmetrics2",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "bergmann",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "parallelLint",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "phpstan",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "ignore",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "implode",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 12,
        "nbMethods": 11,
        "nbMethodsPrivate": 3,
        "nbMethodsPublic": 8,
        "nbMethodsGetter": 1,
        "nbMethodsSetters": 0,
        "ccn": 3,
        "externals": [],
        "lcom": 1,
        "length": 113,
        "vocabulary": 35,
        "volume": 579.61,
        "difficulty": 7.5,
        "effort": 4347.07,
        "level": 0.13,
        "bugs": 0.19,
        "time": 242,
        "intelligentContent": 77.28,
        "number_operators": 23,
        "number_operands": 90,
        "number_operators_unique": 5,
        "number_operands_unique": 30,
        "cloc": 41,
        "loc": 68,
        "lloc": 29,
        "mi": 95,
        "mIwoC": 48.35,
        "commentWeight": 46.65,
        "kanDefect": 0.29,
        "relativeStructuralComplexity": 9,
        "relativeDataComplexity": 3.48,
        "relativeSystemComplexity": 12.48,
        "totalStructuralComplexity": 108,
        "totalDataComplexity": 41.75,
        "totalSystemComplexity": 149.75,
        "pageRank": 0.2,
        "afferentCoupling": 2,
        "efferentCoupling": 0,
        "instability": 0,
        "violations": {}
    },
    {
        "name": "Edge\\QA\\RunningTool",
        "interface": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "isInstalled",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "buildOption",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getAllowedErrorsCount",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "analyzeResult",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "evaluteErrorsCount",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "areErrorsIgnored",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getXmlFiles",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getEscapedXmlFile",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getMainXml",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "__toString",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 11,
        "nbMethods": 8,
        "nbMethodsPrivate": 3,
        "nbMethodsPublic": 5,
        "nbMethodsGetter": 3,
        "nbMethodsSetters": 0,
        "ccn": 3,
        "externals": [],
        "lcom": 1,
        "length": 118,
        "vocabulary": 29,
        "volume": 573.24,
        "difficulty": 13.2,
        "effort": 7569.4,
        "level": 0.08,
        "bugs": 0.19,
        "time": 421,
        "intelligentContent": 43.41,
        "number_operators": 35,
        "number_operands": 83,
        "number_operators_unique": 7,
        "number_operands_unique": 22,
        "cloc": 1,
        "loc": 81,
        "lloc": 80,
        "mi": 47.33,
        "mIwoC": 38.77,
        "commentWeight": 8.56,
        "kanDefect": 0.29,
        "relativeStructuralComplexity": 25,
        "relativeDataComplexity": 2.42,
        "relativeSystemComplexity": 27.42,
        "totalStructuralComplexity": 275,
        "totalDataComplexity": 26.67,
        "totalSystemComplexity": 301.67,
        "pageRank": 0.23,
        "afferentCoupling": 6,
        "efferentCoupling": 0,
        "instability": 0,
        "violations": {}
    }
]