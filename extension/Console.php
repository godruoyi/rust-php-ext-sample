<?php

namespace Godruoyi\Extension;

class Console
{
    /**
     * Whether to use colors in the output.
     */
    private bool $useColors;

    public function __construct()
    {
        $this->useColors = $this->detectColorSupport();
    }

    /**
     * Write a line to the output
     *
     * @param  string  $message  The message to output
     * @param  bool  $error  Whether to write to stderr instead of stdout
     */
    public function writeln(string $message = '', bool $error = false): void
    {
        if ($error) {
            fwrite(STDERR, $this->formatMessage($message).PHP_EOL);
        } else {
            fwrite(STDOUT, $this->formatMessage($message).PHP_EOL);
        }
    }

    /**
     * Write an error message
     *
     * @param  string  $message  The error message
     */
    public function error(string $message): void
    {
        $this->writeln($this->color('error', "❌ $message"), true);
    }

    /**
     * Write a success message
     *
     * @param  string  $message  The success message
     */
    public function success(string $message): void
    {
        $this->writeln($this->color('success', "✅ $message"));
    }

    /**
     * Write an info message
     *
     * @param  string  $message  The info message
     */
    public function info(string $message): void
    {
        $this->writeln($this->color('info', $message));
    }

    /**
     * Write a warning message
     *
     * @param  string  $message  The warning message
     */
    public function warning(string $message): void
    {
        $this->writeln($this->color('warning', "⚠️ $message"));
    }

    /**
     * Apply color to a string if supported
     *
     * @param  string  $type  The type of formatting (error|success|info|warning)
     * @param  string  $text  The text to format
     * @return string The formatted string
     */
    private function color(string $type, string $text): string
    {
        if (! $this->useColors) {
            return $text;
        }

        $colors = [
            'error' => "\033[31m", // Red
            'success' => "\033[32m", // Green
            'info' => "\033[36m", // Cyan
            'warning' => "\033[33m", // Yellow
            'reset' => "\033[0m",
        ];

        return $colors[$type].$text.$colors['reset'];
    }

    /**
     * Format a message with simple tags like <info>text</info>
     *
     * @param  string  $message  The message with tags
     * @return string The formatted message
     */
    private function formatMessage(string $message): string
    {
        $patterns = [
            '/<error>(.*?)<\/error>/s' => 'error',
            '/<success>(.*?)<\/success>/s' => 'success',
            '/<info>(.*?)<\/info>/s' => 'info',
            '/<warning>(.*?)<\/warning>/s' => 'warning',
        ];

        foreach ($patterns as $pattern => $type) {
            $message = preg_replace_callback($pattern, function ($matches) use ($type) {
                return $this->color($type, $matches[1]);
            }, $message);
        }

        return $message;
    }

    /**
     * Detect if the terminal supports colors
     *
     * @return bool True if colors are supported
     */
    private function detectColorSupport(): bool
    {
        // Check if we're in a terminal
        if (! defined('STDOUT') || ! function_exists('posix_isatty') || ! @posix_isatty(STDOUT)) {
            return false;
        }

        // Check for NO_COLOR environment variable
        if (getenv('NO_COLOR') !== false) {
            return false;
        }

        // Check TERM environment variable
        $term = getenv('TERM');
        if ($term === 'dumb') {
            return false;
        }

        return true;
    }

    /**
     * Ask the user a yes/no question
     *
     * @param  string  $question  The question to ask
     * @param  bool  $default  The default answer
     * @return bool The user's answer
     */
    public function confirm(string $question, bool $default = false): bool
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        $response = $this->readInput("$question [$defaultText] ");

        if ($response === '') {
            return $default;
        }

        return strtolower($response[0]) === 'y';
    }

    /**
     * Read input from the user
     *
     * @param  string  $prompt  The prompt to display
     * @return string The user input
     */
    public function readInput(string $prompt): string
    {
        $this->writeln($prompt);

        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        fclose($handle);

        return trim($line);
    }
}
