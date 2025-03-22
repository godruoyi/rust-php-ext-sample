<?php

namespace Godruoyi\Composer;

class Packager
{
    //    const GITHUB_API = 'https://api.github.com/repos/godruoyi/rust-php-ext-sample/releases';
    const GITHUB_API = 'https://github.com/apache/opendal/releases';

    /**
     * Userinfo about current system.
     *
     * @return array [php_version, os, extension_dir, arch]
     */
    public static function systemInfo(): array
    {
        return [
            'php_version' => self::currentPHPVersion(),
            'os' => self::currentOS(),
            'extension_dir' => ini_get('extension_dir'),
            'arch' => php_uname('m'),
        ];
    }

    public static function buildBinaryName(): string
    {
        // todo more variable factors support such as glide, etc.
        // todo now is only for MacOS, please add more support for other OS
        // example: rust-php-extension-sample-7.4-MacOS.dylib
        return sprintf('rust-php-extension-sample-%s-%s.dylib', self::currentPHPVersion(), self::currentOS());
    }

    public static function exists(string $extension, string $version = 'latest'): bool
    {
        $url = self::GITHUB_API.'/tags/'.$version.'/assets';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Extension Installer');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($response)) {
            return false;
        }

        $assets = json_decode($response, true);
        if (! is_array($assets)) {
            return false;
        }

        var_dump($assets);

        foreach ($assets as $asset) {
            if (isset($asset['name']) && $asset['name'] === $extension) {
                return true;
            }
        }

        return false;
    }

    public static function download(string $binaryName): string
    {
        return 'todo';
    }

    public static function extensionDir(): string
    {
        return ini_get('extension_dir');
    }

    public static function currentPHPVersion(): string
    {
        return str_replace('.', '', phpversion());
    }

    public static function currentOS(): string
    {
        return match (PHP_OS) {
            'Darwin' => 'MacOS',
            'Linux' => 'Linux',
            default => PHP_OS,
        };
    }
}
