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

## Risks

1. Package maintainers need to provide pre-compiled binaries for different platforms and PHP versions.
2. These pre-compiled binary files need to be saved in a certain location, currently GitHub seems to be the most
   suitable option that can be published with the code repository when new releases are created.
3. The operation of installing the extension is divided into two steps, which may cause confusion for some users.
