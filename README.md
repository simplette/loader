Simplette / Loader
==================

An implementation of simple assets loader - for building your styles and javascripts on the fly. 
Thank to this library, you can simplify your workflow with [Nette Framework][1]'s frontend.


Requirements
------------
This library requires PHP 7.1 or higher. [Simplette Loader][2] library is designed
for [Nette Framework][1] version 3.0 and higher.


Installation
------------
The best way to install this library is using [Composer](http://getcomposer.org/):

```sh
$ composer require simplette/loader
```


Documentation
-------------
Firstly, register extensions. For more information
about configuration see the class definition. This library (as all libraries from Simplette) 
is meant to be as simple as possible.

```yaml
extensions:
	style: Simplette\Loader\Style\StyleLoaderExtension
	script: Simplette\Loader\Script\ScriptLoaderExtension
```

Now you can define your own list of styles, scripts, configure each part of loader, etc.

See example configuration:

```yaml
style:
	debugger: %debugMode% # or just set true/false
	genDir: assets/gen
	files:
		admin-sb: # you can combine scss and css files
			- %appDir%/Modules/AdminModule/styles/admin-sb.scss
			- %appDir%/Modules/AdminModule/styles/daterangepicker.css
		admin-editor:
			- %appDir%/Modules/AdminModule/styles/codemirror.css
			- %appDir%/Modules/AdminModule/scripts/vendor/codemirror/addon/display/fullscreen.css
			- %appDir%/Modules/AdminModule/styles/codemirror.scss
		# ...

script:
	debugger: %debugMode%
	compiler:
		minify: FALSE # turn off minification / there can be possible to set other compiler parameters
	genDir: assets/gen
	files:
		admin-sb:
			- %appDir%/Modules/AdminModule/scripts/vendor/bootstrap.bundle.min.js # files *.min.* would not be minified again 
			# ...
			- %appDir%/Modules/AdminModule/scripts/sb-admin-2.js
			# ...
		admin-search:
			# ...
			- %appDir%/Modules/AdminModule/scripts/search.js
		admin-nette:
			- %appDir%/../vendor/nette/forms/src/assets/netteForms.min.js
			- %appDir%/Modules/AdminModule/scripts/vendor/nette.ajax.js
			# ...
			- %appDir%/Modules/AdminModule/scripts/init-nette.js
		admin-editor:
			- %appDir%/Modules/AdminModule/scripts/vendor/codemirror/codemirror.js
			# ...
			- %appDir%/Modules/AdminModule/scripts/init-editor.js
		# ...
```

Then you can use it in your latte templates like this:

```html
<link rel="stylesheet" n:style="admin-sb"/>

<script n:script="admin-sb"></script>
```

[1]: https://github.com/nette/nette
[2]: https://github.com/simplette/loader
