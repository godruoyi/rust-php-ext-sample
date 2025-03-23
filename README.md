# PHP Extension Installer

This is a test project, mainly used to explore how to install PHP extensions that are written in Rust. The goal is to
create a Composer package that can install the PHP extension directly without using PECL or PIE.

## Installation

First, install this package:

```bash
composer require godruoyi/rust-php-extension-sample
```

Then, install the PHP extension by running the following command:

```bash
vendor/bin/install-sample-extension
```

## Goals

- [x] Write a simple PHP extension in Rust
- [x] Build and publish the pre-compiled extension to GitHub releases by using GitHub Actions
- [x] Install the extension through Composer
- [x] Run the installation script to install PHP extension
- [x] Test the extension in a PHP script
- [x] These all operations should be integrated with GitHub Actions
