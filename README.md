# Laravel HTML Minify

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

1. Add `"fitztrev/laravel-html-minify": "dev-master"` to **composer.json**.
2. Run `composer update`
3. Add `Fitztrev\LaravelHtmlMinify\LaravelHtmlMinifyServiceProvider` to the list of providers in **app/config/app.php**.
4. **Important:** You won't see any changes until you edit your `*.blade.php` template files. Once Laravel detects a change, it will recompile them, which is when this package will go to work. To force all views to be recompiled, just run this command: `find . -name "*.blade.php" -exec touch {} \;`

## Disclaimers

If you use either of the following practices in your views, it will not behave as intended.

##### 1) Embedded `<script>` tags that either (a) use line breaks instead of semicolons to end a statement, or (b) use single-line comments `//`

*Example:*

	<script>
		alert(1)
		alert(2)
	</script>
	
	<script>
		// alert(3)
		alert(4)
	</script>

These blocks will be combined into one line, breaking the javascript. Alerts #1 + #2 would work if they ended with a semicolon. And alert #4 never shows because it gets appended to the comment on the previous line.

##### 2) Input fields that rely on multiple spaces in their values.

*Example:*

	<input type="submit" value="     Submit     " />
	<input type="text" value="spaces   in   the    value" />

Multiple spaces will be removed, potentially causing formatting or other issues.

However, if a view file contains a `<pre>` or `<textarea>` tag, this package ignores it and it will not be compressed. In those cases, whitespace is preserved and the view will render correctly.