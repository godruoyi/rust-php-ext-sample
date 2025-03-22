<?php

namespace Godruoyi\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Exception;

class Installer
{
    public function __construct(protected Composer $composer, protected IOInterface $io) {}

    public function installExtension(): int
    {
        $this->io->write('<info>🚀 Installing Rust PHP Extension Sample...</info>');
        $this->printSystemInfos();

        try {
            if (! Packager::exists($binaryName = Packager::buildBinaryName())) {
                $this->io->write('<error>❌ Precompiled extension not found</error>');
                $this->io->write('<error>   - Please compile the extension from source</error>');

                return 1;
            }

            $binaryPath = Packager::download($binaryName);

            $this->copyExtensionToPHPExtensionDir($binaryPath, Packager::extensionDir());
            $this->tryEnableExtension();

            return 0;
        } catch (Exception $e) {
            return $this->processException($e);
        }
    }

    private function processException(Exception $e): int
    {
        $this->io->writeError('<error>❌ Install failed: '.$e->getMessage().'</error>');

        return 1;
    }

    private function printSystemInfos(): void
    {
        $message = Packager::systemInfo();

        $this->io->write('<info>📊 System Info:</info>');
        $this->io->write("<info>  - OS: {$message['os']}</info>");
        $this->io->write("<info>  - PHP Version: {$message['php_version']}</info>");
        $this->io->write("<info>  - Arch: {$message['arch']}</info>");
        $this->io->write("<info>  - Extension Dir: {$message['extension_dir']}</info>");
    }
}
