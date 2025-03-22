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

        $binaryName = Packager::buildDefaultExtensionName();

        try {
            $binaryPath = $this->downloadExtensionFromGitHub($binaryName);

            $this->copyExtensionToPHPExtensionDir($binaryPath);
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

    /**
     * @throws Exception
     */
    private function downloadExtensionFromGitHub(string $binaryName): string
    {

        throw new Exception('Download failed');
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

    private function download(string $url)
    {
        $ch = curl_init($url);
        $fp = fopen($tempDir.'/'.$extension_filename, 'w+');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $success = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

    }
}
