<?php

namespace Godruoyi\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;

class Installer
{
    const GitHubReleaseEndpoint = 'https://api.github.com/repos/godruoyi/rust-php-extension-sample/releases/latest';

    public function __construct(protected Composer $composer, protected IOInterface $io) {}

    public function install(): void
    {
        $this->io->write('<comment>[Rust-PHP-Extension-Sample-Plugin] todo, install extension</comment>');
    }

    protected function buildPreBuildExtensionPath()
    {
        // todo more variable factors support such as glide, etc.
        // todo more platform support such as Windows, etc.
        // example: rust-php-extension-sample-7.4-MacOS.dylib
        return sprintf('rust-php-extension-sample-%s-%s.dylib', currentPHPVersion(), currentOS());
    }
}
