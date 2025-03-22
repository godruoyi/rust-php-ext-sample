<?php

namespace Godruoyi\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class InstallerPlugin implements EventSubscriberInterface, PluginInterface
{
    protected Composer $composer;

    protected IOInterface $io;

    protected Installer $installer;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->installer = new Installer($composer, $io);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $this->io->write('<comment>[Rust-PHP-Extension-Sample-Plugin] deactivate</comment>');
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->io->write('<comment>[Rust-PHP-Extension-Sample-Plugin] todo, uninstall plugin</comment>');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => ['installExtensionAfterPackageInstall', -1],
            PackageEvents::POST_PACKAGE_UPDATE => ['installExtensionAfterPackageUpdate', -1],
        ];
    }

    public function installExtensionAfterPackageInstall(PackageEvent $event): void
    {
        $package = $event->getOperation()->getPackage();

        $this->io->write("<comment>InstallExtensionAfterPackageInstall - Package name: {$package->getName()}</comment>");

        if ($this->isOwnPackage($package->getName())) {
            $this->install();
        }
    }

    public function installExtensionAfterPackageUpdate(PackageEvent $event): void
    {
        $package = $event->getOperation()->getTargetPackage();

        $this->io->write("<comment>InstallExtensionAfterPackageUpdate - Package name: {$package->getName()}</comment>");

        if ($this->isOwnPackage($package->getName())) {
            $this->install();
        }
    }

    private function isOwnPackage(string $packageName): bool
    {
        // todo: only install extension when the package is `current` package
        return true;
    }

    private function install(): void
    {
        $this->installer->installExtension();
    }

    private function installxxxx(): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $projectDir = dirname($vendorDir);

        // 查找目标目录下的 .dylib 文件（macOS）
        $targetDir = $projectDir.'/target/release';
        $this->io->write("<info>Searching for extensions in: {$targetDir}</info>");

        if (! is_dir($targetDir)) {
            $this->io->writeError('<error>Target directory not found. Make sure Rust extension is compiled.</error>');

            return;
        }

        // 查找 .dylib 文件
        $extension = null;
        $files = scandir($targetDir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'dylib') {
                $extension = $file;
                break;
            }
        }

        if (! $extension) {
            $this->io->writeError("<error>No .dylib extension found in {$targetDir}</error>");

            return;
        }

        // 获取PHP扩展目录
        $extensionDir = trim(shell_exec('php -r "echo ini_get(\"extension_dir\");"'));
        if (empty($extensionDir)) {
            $this->io->writeError('<error>Could not determine PHP extension directory</error>');

            return;
        }

        $sourcePath = $targetDir.'/'.$extension;
        $destPath = $extensionDir.'/'.$extension;

        // 复制文件到扩展目录
        $this->io->write("<info>Copying {$sourcePath} to {$destPath}</info>");

        try {
            if (! copy($sourcePath, $destPath)) {
                throw new \Exception('Failed to copy file');
            }

            // 设置权���
            chmod($destPath, 0755);

            // 获取当前用户使用的 php.ini 文件
            $iniFile = trim(shell_exec('php -r "echo php_ini_loaded_file();"'));

            $this->io->write("<info>Extension successfully installed to {$destPath}</info>");
            $this->io->write("<info>To enable the extension, add the following to your php.ini ({$iniFile}):</info>");
            $this->io->write("<info>extension={$extension}</info>");

            // 尝试自��启用扩展（可选，取决于用户权限）
            $extName = pathinfo($extension, PATHINFO_FILENAME);
            $this->io->write("<info>Attempting to enable extension {$extName}...</info>");

            if (is_writable($iniFile)) {
                $iniContent = file_get_contents($iniFile);
                if (! preg_match('/extension\s*=\s*'.preg_quote($extension, '/').'/', $iniContent)) {
                    file_put_contents($iniFile, $iniContent."\nextension={$extension}\n");
                    $this->io->write('<info>Extension automatically enabled in php.ini</info>');
                } else {
                    $this->io->write('<info>Extension already enabled in php.ini</info>');
                }
            } else {
                $this->io->write('<comment>Cannot write to php.ini. Please manually enable the extension.</comment>');
            }

        } catch (\Exception $e) {
            $this->io->writeError("<error>Error installing extension: {$e->getMessage()}</error>");

            return;
        }
    }
}
