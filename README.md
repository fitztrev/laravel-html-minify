# Laravel HTML Minify

[![Build Status](https://travis-ci.org/fitztrev/laravel-html-minify.png)](https://travis-ci.org/fitztrev/laravel-html-minify)
[![Total Downloads](https://poser.pugx.org/fitztrev/laravel-html-minify/downloads.png)](https://packagist.org/packages/fitztrev/laravel-html-minify)

## About

This package compresses the HTML output from your Laravel 4 application, seamlessly reducing the overall response size of your pages.

Other scripts that I've seen will compress the HTML output on-the-fly for each request. Instead, this package extends the Blade compiler to save the compiled template files to disk in their compressed state, reducing the overhead for each request.

## Why?

Even with gzip enabled, there is still an improvement in the response size for HTML content-type documents.

Test Page | w/o Gzip | w/ Gzip | w/ Gzip + Laravel HTML Minify
--- | ---: | ---: | :---:
**#1** | 8,039 bytes | 1,944 bytes | **1,836 bytes** (5.6% improvement)
**#2** | 377,867 bytes | 5,247 bytes | **4,314 bytes** (17.8% improvement)

## Installation

1. Add `"fitztrev/laravel-html-minify": "1.*"` to **composer.json**.
2. Run `composer update`
3. Add `Fitztrev\LaravelHtmlMinify\LaravelHtmlMinifyServiceProvider` to the list of providers in **app/config/app.php**.
4. **Important:** You won't see any changes until you edit your `*.blade.php` template files. Once Laravel detects a change, it will recompile them, which is when this package will go to work. To force all views to be recompiled, just run this command: `find . -name "*.blade.php" -exec touch {} \;`

## Config

Optionally, you can choose to customize how the minifier functions for different environments. Publish the configuration file and edit accordingly.

    $ php artisan config:publish fitztrev/laravel-html-minify

### Options

- **`enabled`** - *boolean*, default **true**

If you are using a javascript framework that conflicts with Blade's tags, you can change them.

- **`blade.contentTags`** - *array*, default `{{` and `}}`
- **`blade.escapedContentTags`** - *array*, default `{{{` and `}}}`

#### Skipping minification

To prevent the minification of a view file, add `skipmin` somewhere in the view.

```
{{-- skipmin --}}
<!-- skipmin -->
```
