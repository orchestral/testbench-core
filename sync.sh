#!/bin/bash

cp -rf vendor/laravel/laravel/config/*.php laravel/config/
cp -rf vendor/laravel/laravel/database/migrations/2014_10_12_000000_create_users_table.php laravel/migrations/2014_10_12_000000_testbench_create_users_table.php
cp -rf vendor/laravel/laravel/database/migrations/2014_10_12_100000_create_password_resets_table.php laravel/migrations/2014_10_12_100000_testbench_create_password_resets_table.php
cp -rf vendor/laravel/laravel/database/migrations/2019_08_19_000000_create_failed_jobs_table.php laravel/migrations/2019_08_19_000000_testbench_create_failed_jobs_table.php
cp -rf vendor/laravel/laravel/resources/lang/en/*.php laravel/resources/lang/en/
cp -rf vendor/laravel/laravel/database/factories/*.php src/Factories/
cp -rf vendor/laravel/laravel/server.php laravel/server.php
cp -rf vendor/laravel/laravel/public/index.php laravel/public/index.php

awk '{sub(/production/,"testing")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
awk '{sub(/App\\Providers/,"// App\\Providers")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
# awk '{sub(/\x27Redis\x27/,"'\''RedisManager'\''")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
awk '{sub(/\x27model\x27 => App\\Models\\User/,"'\''model'\'' => Illuminate\\Foundation\\Auth\\User")}1' laravel/config/auth.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/auth.php
awk '{sub(/\x27CACHE_DRIVER\x27, \x27file\x27/,"'\''CACHE_DRIVER'\'', '\''array'\''")}1' laravel/config/cache.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/cache.php
awk '{sub(/\x27SESSION_DRIVER\x27, \x27file\x27/,"'\''SESSION_DRIVER'\'', '\''array'\''")}1' laravel/config/session.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/session.php
awk '{sub(/class Create/,"class TestbenchCreate")}1' laravel/migrations/2014_10_12_000000_testbench_create_users_table.php > laravel/migrations/temp.stub && mv laravel/migrations/temp.stub laravel/migrations/2014_10_12_000000_testbench_create_users_table.php
awk '{sub(/class Create/,"class TestbenchCreate")}1' laravel/migrations/2014_10_12_100000_testbench_create_password_resets_table.php > laravel/migrations/temp.stub && mv laravel/migrations/temp.stub laravel/migrations/2014_10_12_100000_testbench_create_password_resets_table.php
awk '{sub(/class Create/,"class TestbenchCreate")}1' laravel/migrations/2019_08_19_000000_testbench_create_failed_jobs_table.php > laravel/migrations/temp.stub && mv laravel/migrations/temp.stub laravel/migrations/2019_08_19_000000_testbench_create_failed_jobs_table.php
awk '{sub(/use App\\Models\\User/,"use Illuminate\\Foundation\\Auth\\User")}1' src/Factories/UserFactory.php > src/Factories/temp.stub && mv src/Factories/temp.stub src/Factories/UserFactory.php
awk '{sub(/namespace Database\\Factories/,"namespace Orchestra\\Testbench\\Factories")}1' src/Factories/UserFactory.php > src/Factories/temp.stub && mv src/Factories/temp.stub src/Factories/UserFactory.php
