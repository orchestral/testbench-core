#!/bin/bash

cp -rf vendor/laravel/laravel/config/*.php laravel/config/
cp -rf vendor/laravel/laravel/database/migrations/*.php laravel/migrations/
cp -rf vendor/laravel/laravel/resources/lang/en/*.php laravel/resources/lang/en/

awk '{sub(/production/,"testing")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
awk '{sub(/App\\Providers/,"// App\\Providers")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
awk '{sub(/\x27Redis\x27/,"'\''RedisManager'\''")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
awk '{sub(/\x27CACHE_DRIVER\x27, \x27file\x27/,"'\''CACHE_DRIVER'\'', '\''array'\''")}1' laravel/config/cache.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/cache.php
awk '{sub(/\x27SESSION_DRIVER\x27, \x27file\x27/,"'\''SESSION_DRIVER'\'', '\''array'\''")}1' laravel/config/session.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/session.php
