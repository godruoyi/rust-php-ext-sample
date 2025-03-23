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
- [ ] Install the extension through Composer
  - [ ] Add `post-install` script in `composer.json` to download the pre-compiled extension
  - [ ] Make sure the pre-compiled extension is matched with user's OS and PHP version
  - [ ] Exit and show error message if the extension is not available for the user's OS and PHP version
  - [ ] Investigate is it possible to build the extension on the user's machine
  - [ ] Using composer-plugin for the installation script
- [ ] Test the extension in a PHP script
- [ ] These all operations should be integrated with GitHub Actions
