<?php

namespace Godruoyi\Extension;

class ExtensionInstaller
{
    private Console $console;

    private SystemInfo $systemInfo;

    private string $githubRepo;

    public function __construct(
        Console $output,
        string $githubRepo = 'godruoyi/rust-php-ext-sample',
        string $extensionName = 'rust_php_extension_sample',
    ) {
        $this->console = $output;
        $this->systemInfo = new SystemInfo($extensionName);
        $this->githubRepo = $githubRepo;
    }

    public function run(): int
    {
        $this->console->writeln('<info>🍒 Install sample PHP extension that written in Rust</info>');
        $this->console->writeln();

        if (extension_loaded($extensionName = $this->systemInfo->getExtensionName())) {
            $this->console->success($extensionName.' extension is already installed (version: '.phpversion($extensionName).')');
            $this->console->writeln('   To reinstall, please remove the existing extension first.');
            $this->console->writeln('   You can remove the extension by deleting the extension file and restarting PHP.');

            return 0;
        }

        $this->displaySystemInfo();

        if (! $this->systemInfo->isSupported()) {
            $this->console->error('Unsupported operating system: '.$this->systemInfo->getOsFamily());
            $this->provideBuildFromSourceInstructions();

            return 1;
        }

        $downloadUrl = $this->systemInfo->buildDownloadUrl($this->githubRepo);
        $this->console->writeln("Checking for pre-compiled binary at: $downloadUrl");

        if (! $this->binaryExists($downloadUrl)) {
            $this->console->error('No pre-compiled binary found for your system.');
            $this->provideBuildFromSourceInstructions();

            return 1;
        }

        $this->console->success('Found a compatible binary for your system!');
        $this->console->writeln("Downloading from: $downloadUrl\n");

        $tempFile = $this->downloadFile($downloadUrl);
        if ($tempFile === false) {
            $this->console->error('Failed to download the extension.');

            return 1;
        }

        $result = $this->installFile($tempFile);
        if (! $result) {
            return 1;
        }

        $this->updatePHPIni();

        // Installation complete
        $this->console->writeln("\nTo verify the extension is correctly installed, restart PHP and run:");
        $this->console->writeln('php -r "echo rust_php_ext_sample(\'World\');"');

        return 0;
    }

    private function displaySystemInfo(): void
    {
        $info = $this->systemInfo->getSummary();
        $this->console->writeln('<info>System Information:</info>');

        foreach ($info as $key => $value) {
            $this->console->writeln("  - $key: $value");
        }
        $this->console->writeln();
    }

    /**
     * Check if binary exists at URL
     *
     * @param  string  $url  The URL to check
     * @return bool True if exists
     */
    private function binaryExists(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        $message = match ($httpCode) {
            404 => 'Not found',
            403 => 'Forbidden',
            default => 'Error',
        };

        $this->console->error("Binary not found: $message");

        return false;
    }

    /**
     * Download file from URL
     *
     * @param  string  $url  The URL to download from
     * @return string|false Path to downloaded file or false on failure
     */
    private function downloadFile(string $url): bool|string
    {
        // Create temp directory
        $tempDir = sys_get_temp_dir().'/php-rust-sample-php-installer-'.uniqid();
        if (! mkdir($tempDir, 0755, true) && ! is_dir($tempDir)) {
            $this->console->error('Failed to create temporary directory.');

            return false;
        }

        $tempFile = $tempDir.'/'.$this->systemInfo->getExtensionFilename();

        $ch = curl_init($url);
        $fp = fopen($tempFile, 'w+');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if (! $success || $httpCode !== 200) {
            $this->console->error('Download failed.');

            return false;
        }

        return $tempFile;
    }

    /**
     * Install the downloaded file
     *
     * @param  string  $tempFile  Path to the downloaded file
     * @return bool True on success
     */
    private function installFile(string $tempFile): bool
    {
        $targetPath = $this->systemInfo->getExtensionTargetPath();
        $this->console->writeln("Installing extension to $targetPath...");

        if (! $this->systemInfo->isExtensionDirWritable()) {
            $this->console->error('No permission to write to extension directory. Please run with administrator/root privileges.');

            if ($this->systemInfo->getOsFamily() === 'Windows') {
                $this->console->writeln('On Windows, run Command Prompt as Administrator and try again.');
            } else {
                $this->console->writeln('On Linux/macOS, use sudo: sudo composer run-script install-extension');
            }

            $this->console->writeln("\nYou can manually copy the extension file:");
            $this->console->writeln("From: $tempFile");
            $this->console->writeln("To: $targetPath");

            return false;
        }

        if (! copy($tempFile, $targetPath)) {
            $this->console->error('Failed to copy file.');

            return false;
        }

        // Set permissions
        chmod($targetPath, 0755);

        // Clean up
        @unlink($tempFile);
        @rmdir(dirname($tempFile));

        $this->console->success('Extension file has been copied to the PHP extension directory.');

        return true;
    }

    /**
     * Update php.ini file to enable the extension
     */
    private function updatePHPIni(): void
    {
        $phpIniPath = $this->systemInfo->getPhpIniPath();
        $extensionFilename = $this->systemInfo->getExtensionFilename();

        $this->console->writeln("\nTo enable the extension, add the following line to your php.ini:");
        $this->console->writeln("extension=$extensionFilename");

        if (! $phpIniPath) {
            $this->console->writeln("\nCouldn't locate your php.ini file.");

            return;
        }

        $this->console->writeln("\nYour php.ini is located at: $phpIniPath");

        // Check if we can write to php.ini
        if (! $this->systemInfo->isPhpIniWritable()) {
            $this->console->writeln("You don't have permission to modify php.ini. Please add the line manually.");

            return;
        }

        if ($this->console->confirm('Would you like to automatically add the extension to php.ini?')) {
            $iniContent = file_get_contents($phpIniPath);

            if (str_contains($iniContent, "extension=$extensionFilename")) {
                $this->console->writeln('Extension is already enabled in php.ini.');

                return;
            }

            if (file_put_contents($phpIniPath, "\nextension=$extensionFilename\n", FILE_APPEND)) {
                $this->console->success('Extension has been added to php.ini');
                $this->console->writeln('Please restart your web server or PHP-FPM for the changes to take effect.');
            } else {
                $this->console->error('Failed to update php.ini');
            }
        } else {
            $this->console->writeln('php.ini not modified. Please add the extension manually.');
        }
    }

    private function provideBuildFromSourceInstructions(): void
    {
        $this->console->writeln();
        $this->console->info('You need to build the extension from source:');
        $this->console->writeln('1. Ensure you have Rust and PHP development environment installed');
        $this->console->writeln("2. Clone the repository: git clone https://github.com/$this->githubRepo.git");
        $this->console->writeln('3. Change to the repository directory');
        $this->console->writeln('4. Compile the extension: cargo build --release');
        $this->console->writeln('5. Copy the generated extension file to your PHP extension directory');
        $this->console->writeln('6. Add to your php.ini: extension='.$this->systemInfo->getExtensionFilename());
        $this->console->writeln("For detailed instructions, visit: https://github.com/$this->githubRepo#building-from-source");
    }
}
