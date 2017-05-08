Ohlášky.cz
=============

This repository contains source code of the website named in the heading.

Installation
------------

Point the webserver into `www/` directory
Make directories `temp/` and `log/` writable.

It is CRITICAL that whole `app/`, `log/` and `temp/` directories are not accessible directly
via a web browser.

Requirements
------------

PHP 7.0 or higher. 
To check whether server configuration meets the minimum requirements for Nette Framework, point your browser to the directory `/checker` in your project root.

Adminer
-------

[Adminer](https://www.adminer.org/) is full-featured database management tool written in PHP and it is part of this Sandbox.
To use it, browse to the subdirectory `/adminer` in your project root (i.e. `http://localhost:8000/adminer`).


License
-------
- Nette: New BSD License or GPL 2.0 or 3.0
- Adminer: Apache License 2.0 or GPL 2
- Ohlášky.cz: source-code usage ad libitum (commercial use only with author's agreement)