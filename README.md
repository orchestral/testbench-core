Testing Helper for Laravel Development
==============

Testbench Component is a simple package that has been designed to help you write tests for your Laravel package.

[![Build Status](https://travis-ci.org/orchestral/testbench-core.svg?branch=5.x)](https://travis-ci.org/orchestral/testbench-core)
[![Latest Stable Version](https://poser.pugx.org/orchestra/testbench-core/v/stable)](https://packagist.org/packages/orchestra/testbench-core)
[![Total Downloads](https://poser.pugx.org/orchestra/testbench-core/downloads)](https://packagist.org/packages/orchestra/testbench-core)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/testbench-core/v/unstable)](https://packagist.org/packages/orchestra/testbench-core)
[![License](https://poser.pugx.org/orchestra/testbench-core/license)](https://packagist.org/packages/orchestra/testbench-core)

* [Version Compatibility](#version-compatibility)

## Version Compatibility

 Laravel  | Testbench Core
:---------|:----------
 5.4.x    | 3.4.x
 5.5.x    | 3.5.x
 5.6.x    | 3.6.x
 5.7.x    | 3.7.x
 5.8.x    | 3.8.x
 6.x      | 4.x
 7.x      | 5.x
 
## Usage

**Testbench Core** is being built to enable [Laravel Framework](https://github.com/laravel/framework) to build and run integration tests for the framework itself. For package developers please use any of the following testbench projects:

### [Testbench](https://github.com/orchestral/testbench)

It loads Laravel apps and enable you to run artisan commands, migrations, factories and basic routing from within your tests.

[![Latest Stable Version](https://poser.pugx.org/orchestra/testbench/v/stable)](https://packagist.org/packages/orchestra/testbench)
[![Total Downloads](https://poser.pugx.org/orchestra/testbench/downloads)](https://packagist.org/packages/orchestra/testbench)

### [Testbench BrowserKit](https://github.com/orchestral/testbench-browser-kit)

It extends **Testbench** and allows you to interact with views using CSS selectors (interacting with form, button, link etc) but without JavaScript being loaded.

[![Latest Stable Version](https://poser.pugx.org/orchestra/testbench-browser-kit/v/stable)](https://packagist.org/packages/orchestra/testbench-browser-kit)
[![Total Downloads](https://poser.pugx.org/orchestra/testbench-browser-kit/downloads)](https://packagist.org/packages/orchestra/testbench-browser-kit)

### [Testbench Dusk](https://github.com/orchestral/testbench-dusk)

It extends **Testbench** and allows you to interact with views using CSS selectors (interacting with form, button, link etc). By loading the pages using Google Chrome it enable you to interacts with JavaScript powered content.

[![Latest Stable Version](https://poser.pugx.org/orchestra/testbench-dusk/v/stable)](https://packagist.org/packages/orchestra/testbench-dusk)
[![Total Downloads](https://poser.pugx.org/orchestra/testbench-dusk/downloads)](https://packagist.org/packages/orchestra/testbench-dusk)
