<?php

namespace Godruoyi\Extension;

class SystemInfo
{
    /** @var string The operating system family */
    private string $osFamily;

    /** @var string The operating system key used in filenames */
    private string $osKey;

    /** @var string The PHP version (major.minor) */
    private string $phpVersion;

    /** @var string The system architecture */
    private string $architecture;

    /** @var string The PHP extension directory */
    private string $extensionDir;

    /** @var string The PHP extension filename */
    private string $extensionFilename;

    /** @var string The PHP extension name */
    private string $extensionName;

    /** @var string The php.ini file path */
    private string $phpIniPath;

    public function __construct(string $extensionName)
    {
        $this->osFamily = PHP_OS_FAMILY;
        $this->phpVersion = PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
        $this->architecture = (PHP_INT_SIZE === 8) ? 'x86_64' : 'x86';
        $this->extensionDir = ini_get('extension_dir');
        $this->phpIniPath = php_ini_loaded_file();
        $this->extensionName = $extensionName;

        switch ($this->osFamily) {
            case 'Windows':
                $this->osKey = 'windows';
                $this->extensionFilename = $this->extensionName.'.dll';
                break;
            case 'Darwin': // macOS
                $this->osKey = 'macos';
                $this->extensionFilename = $this->extensionName.'.so';
                break;
            case 'Linux':
                $this->osKey = 'linux';
                $this->extensionFilename = $this->extensionName.'.so';
                break;
            default:
                $this->osKey = strtolower($this->osFamily);
                $this->extensionFilename = $this->extensionName.'.so';
        }
    }

    /**
     * Get a summary of the system information
     *
     * @return array System information
     */
    public function getSummary(): array
    {
        return [
            'OS Family' => $this->osFamily,
            'PHP Version' => $this->phpVersion,
            'Architecture' => $this->architecture,
            'Extension Directory' => $this->extensionDir,
            'php.ini Path' => $this->phpIniPath ?: 'Not found',
        ];
    }

    /**
     * Check if the system is supported for binary installation
     *
     * @return bool True if supported
     */
    public function isSupported(): bool
    {
        return in_array($this->osFamily, ['Windows', 'Darwin', 'Linux']);
    }

    /**
     * Get the extension target path
     *
     * @return string Full path to where the extension should be installed
     */
    public function getExtensionTargetPath(): string
    {
        return $this->extensionDir.DIRECTORY_SEPARATOR.$this->extensionFilename;
    }

    /**
     * Build the download URL for the extension binary
     *
     * @param  string  $githubRepo  The GitHub repository name
     * @return string The download URL
     */
    public function buildDownloadUrl(string $githubRepo): string
    {
        // todo: support specific version

        $releaseUrl = "https://github.com/{$githubRepo}/releases/latest/download";
        $binaryName = "$this->extensionName-{$this->osKey}-php{$this->phpVersion}-{$this->architecture}";

        return "{$releaseUrl}/{$binaryName}-{$this->extensionFilename}";
    }

    /**
     * Check if the extension directory is writable
     *
     * @return bool True if writable
     */
    public function isExtensionDirWritable(): bool
    {
        return is_writable($this->extensionDir);
    }

    /**
     * Check if the php.ini file is writable
     *
     * @return bool True if writable
     */
    public function isPhpIniWritable(): bool
    {
        return $this->phpIniPath && is_writable($this->phpIniPath);
    }

    /**
     * Get the OS family
     *
     * @return string The OS family
     */
    public function getOsFamily(): string
    {
        return $this->osFamily;
    }

    /**
     * Get the extension filename
     *
     * @return string The extension filename
     */
    public function getExtensionFilename(): string
    {
        return $this->extensionFilename;
    }

    /**
     * Get the extension name
     *
     * @return string The extension name
     */
    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    /**
     * Get the PHP ini path
     *
     * @return string|null The php.ini path or null if not found
     */
    public function getPhpIniPath(): ?string
    {
        return $this->phpIniPath;
    }

    /**
     * Get the extension directory
     *
     * @return string The extension directory
     */
    public function getExtensionDir(): string
    {
        return $this->extensionDir;
    }
}
