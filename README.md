# Write a PHP extension in Rust

This is a test project, mainly used to explore how to write PHP extensions using Rust and 
install them through Composer directly without PECL and PIE.

## Goals

```
composer require <vendor>/<package>
```

- [x] Write a simple PHP extension in Rust
- [ ] Build and publish the pre-compiled extension to GitHub releases by using GitHub Actions
- [ ] Install the extension through Composer
  - [ ] Add `post-install` script in `composer.json` to download the pre-compiled extension
  - [ ] Make sure the pre-compiled extension is matched with user's OS and PHP version
  - [ ] Exit and show error message if the extension is not available for the user's OS and PHP version
  - [ ] Investigate is it possible to build the extension on the user's machine
  - [ ] Using composer-plugin for the installation script
- [ ] Test the extension in a PHP script
- [ ] These all operations should be integrated with GitHub Actions
