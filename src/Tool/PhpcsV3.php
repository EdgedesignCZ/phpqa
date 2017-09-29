<?php

namespace Edge\QA\Tool;

class PhpcsV3 extends Phpcs
{
    public function __invoke()
    {
        require_once COMPOSER_VENDOR_DIR . '/squizlabs/php_codesniffer/autoload.php';
        return $this->buildPhpcs(\PHP_CodeSniffer\Util\Standards::getInstalledStandards());
    }
}
