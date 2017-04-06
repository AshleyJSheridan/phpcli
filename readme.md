# PHP CLI

This is a small class to help with the formatting of text output in PHP CLI scripts.

Currently, it's just a simple parser, that can turn basic HTML formatting into the appropriate escape codes for the terminal. It has support for (where the terminal allows it):

* Bold, italic, and underlined text
* Foreground colours
* Background colours

The sample file `test.php` shows the typical syntax that is supported. A basic example looks like this:

```php
#!/bin/php
<?php
require_once("phpcli.php");

$cli->message('<font color="#f00">red text</font> with <b>bold</b> bit, <font background="#080" color="#000">green background text</font> here'."\n", true);
```

***Note that there is currently no support for CSS styling***

Some tags support both old and new syntax:

* Bold text can use `<b>` or `<strong>`
* Italic text can use `<i>` or `<em>`

There is one minor cheat when it comes to background colours, which HTML doesn't have a simple syntax for, so the following syntax is supported instead:

```html
<font color="#0070b0">
<font background="#999">
<font color="#0070b0" background="#999">
```

You can use 6 or 3 character hex codes for the colours, and the closest colour from the pallet of 16 will be picked.
