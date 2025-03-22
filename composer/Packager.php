<?php

namespace Godruoyi\Composer;

class Packager
{
    /**
     * Userinfo about current system.
     *
     * @return array [php_version, os, extension_dir, arch]
     */
    public static function systemInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'os' => PHP_OS,
            'extension_dir' => ini_get('extension_dir'),
            'arch' => php_uname('m'),
        ];
    }

    public static function buildDefaultExtensionName(): string
    {
        // todo more variable factors support such as glide, etc.
        // todo now is only for MacOS, please add more support for other OS
        // example: rust-php-extension-sample-7.4-MacOS.dylib
        return sprintf('rust-php-extension-sample-%s-%s.dylib', self::currentPHPVersion(), self::currentOS());
    }

    public static function buildExtensionName(string $phpVersion, string $os, string $extensionSuffix = 'dylib'): string
    {
        // todo should include package version as well like opendal-0.45.16-cp313-cp313t-manylinux_2_28_aarch64.whl
        return sprintf('rust-php-extension-sample-%s-%s.%s', $phpVersion, $os, $extensionSuffix);
    }
}
