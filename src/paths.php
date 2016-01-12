<?php

namespace Edge\QA;

function pathToBinary($tool)
{
    return COMPOSER_BINARY_DIR . $tool;
}

function escapePath($path)
{
    return "\"{$path}\"";
}
