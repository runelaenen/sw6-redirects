# Redirects for Shopware 6

[![Latest Stable Version](https://img.shields.io/github/v/release/runelaenen/sw6-redirects?color=lightblue&label=stable&logo=github)](//packagist.org/packages/runelaenen/sw6-redirects)
[![Download plugin zip](https://img.shields.io/github/v/release/runelaenen/sw6-redirects.svg?label=.zip%20download&logo=github)](https://github.com/runelaenen/sw6-redirects/releases/latest)
[![Total Downloads](https://img.shields.io/packagist/dt/runelaenen/sw6-redirects?label=packagist%20downloads&logo=composer)](//packagist.org/packages/runelaenen/sw6-redirects)
[![GitHub Issues](https://img.shields.io/github/issues/runelaenen/sw6-redirects?logo=github)](https://github.com/runelaenen/sw6-redirects/issues)
[![GitHub Stars](https://img.shields.io/github/stars/runelaenen/sw6-redirects?logo=github)](https://github.com/runelaenen/sw6-redirects/stargazers)
[![License](https://poser.pugx.org/runelaenen/sw6-redirects/license)](//packagist.org/packages/runelaenen/sw6-redirects)

![Redirects for Shopware 6](https://user-images.githubusercontent.com/3930922/110204224-41c91200-7e72-11eb-9e6e-49509fa5e47a.png)

Manage 301 and 302 redirects in your Shopware 6 shop.
The plugin is compatible with PHP version 7.4 and higher.
The plugin has been forked from [ScopPlatformRedirector](https://github.com/scope01-GmbH/ScopPlatformRedirecter).

Are you a happy user of the Redirects plugin? Please consider giving our project a ⭐️ star on Github, or [buying the maintainer a cup of ☕️ coffee](https://www.buymeacoffee.com/runelaenen).

## ✔️ Features
- 301 redirects
- 302 (temporary) redirects

## 🚀 How to install
### Composer install (recommended)
```
composer require runelaenen/sw6-redirects
bin/console plugin:refresh
bin/console plugin:install --activate RuneLaenenRedirects
```
#### 🔨 Building
The composer install does not come with compiled javascript. You will have to build/compile your administration and storefront javascript.

In case you are using the production template, the command below should do the trick.
```
bin/build.sh
```
#### Plugin update (composer)
```
composer update runelaenen/sw6-redirects
bin/console plugin:update RuneLaenenRedirects
```
Builing the javascript & css will still be needed.
```
bin/build.sh
```

### .zip install
1. Download the latest RuneLaenenRedirects.zip from the [latest release](https://github.com/runelaenen/sw6-redirects/releases/latest).
2. Upload the zip in the Shopware Administration
3. Install & Activate the plugin

#### Plugin update (zip)
1. Download the latest RuneLaenenRedirects.zip from the [latest release](https://github.com/runelaenen/sw6-redirects/releases/latest).
2. Upload the zip in the Shopware Administration
3. Update the plugin


## 👷‍ Contribution
Please help with code, love, shares, feedback and bug reporting.

## ⚖️ Licence
This plugin is licensed under the MIT licence.

