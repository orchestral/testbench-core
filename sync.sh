#!/bin/bash

composer create-project "laravel/laravel:10.x-dev" skeleton --no-scripts --no-plugins --quiet

cp -f skeleton/.env.example laravel/
cp -rf skeleton/config/*.php laravel/config/
cp -rf skeleton/database/.gitignore laravel/database/.gitignore
cp -rf skeleton/database/migrations/2014_10_12_000000_create_users_table.php laravel/migrations/2014_10_12_000000_testbench_create_users_table.php
cp -rf skeleton/database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php laravel/migrations/2014_10_12_100000_testbench_create_password_reset_tokens_table.php
cp -rf skeleton/database/migrations/2019_08_19_000000_create_failed_jobs_table.php laravel/migrations/2019_08_19_000000_testbench_create_failed_jobs_table.php
# cp -rf skeleton/lang/en/*.php laravel/lang/en/
# cp -f skeleton/lang/*.json laravel/lang/
cp -rf skeleton/resources/views/* laravel/resources/views/
cp -rf skeleton/database/factories/*.php src/Factories/
cp -rf vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php laravel/server.php
cp -rf skeleton/public/index.php laravel/public/index.php
cp -rf skeleton/tests/CreatesApplication.php laravel/tests/CreatesApplication.php
rm laravel/config/sanctum.php

rm -Rf skeleton

awk '{sub(/getcwd\(\)/,"__DIR__.'\''/public'\''")}1' laravel/server.php > laravel/server.stub && mv laravel/server.stub laravel/server.php
awk '{sub(/DB_CONNECTION=mysql/,"DB_CONNECTION=sqlite")}1' laravel/.env.example > laravel/.env.example.stub && mv laravel/.env.example.stub laravel/.env.example
awk '{sub(/DB_HOST=/,"\# DB_HOST=")}1' laravel/.env.example > laravel/.env.example.stub && mv laravel/.env.example.stub laravel/.env.example
awk '{sub(/DB_PORT=/,"\# DB_PORT=")}1' laravel/.env.example > laravel/.env.example.stub && mv laravel/.env.example.stub laravel/.env.example
awk '{sub(/DB_DATABASE=/,"\# DB_DATABASE=")}1' laravel/.env.example > laravel/.env.example.stub && mv laravel/.env.example.stub laravel/.env.example
awk '{sub(/DB_USERNAME=/,"\# DB_USERNAME=")}1' laravel/.env.example > laravel/.env.example.stub && mv laravel/.env.example.stub laravel/.env.example
awk '{sub(/DB_PASSWORD=/,"\# DB_PASSWORD=")}1' laravel/.env.example > laravel/.env.example.stub && mv laravel/.env.example.stub laravel/.env.example
awk '{sub(/production/,"testing")}1' laravel/config/app.php > laravel/config/app.stub && mv laravel/config/app.stub laravel/config/app.php
awk '{sub(/App\\Providers/,"// App\\Providers")}1' laravel/config/app.php > laravel/config/app.stub && mv laravel/config/app.stub laravel/config/app.php
# awk '{sub(/\x27Redis\x27/,"'\''RedisManager'\''")}1' laravel/config/app.php > laravel/config/temp.stub && mv laravel/config/temp.stub laravel/config/app.php
awk '{sub(/\x27model\x27 => App\\Models\\User/,"'\''model'\'' => Illuminate\\Foundation\\Auth\\User")}1' laravel/config/auth.php > laravel/config/auth.stub && mv laravel/config/auth.stub laravel/config/auth.php
awk '{sub(/\x27CACHE_DRIVER\x27, \x27file\x27/,"'\''CACHE_DRIVER'\'', '\''array'\''")}1' laravel/config/cache.php > laravel/config/cache.stub && mv laravel/config/cache.stub laravel/config/cache.php
awk '{sub(/\x27SESSION_DRIVER\x27, \x27file\x27/,"'\''SESSION_DRIVER'\'', '\''array'\''")}1' laravel/config/session.php > laravel/config/session.stub && mv laravel/config/session.stub laravel/config/session.php
awk '{sub(/use App\\Models\\User/,"use Illuminate\\Foundation\\Auth\\User")}1' src/Factories/UserFactory.php > src/Factories/UserFactory.stub && mv src/Factories/UserFactory.stub src/Factories/UserFactory.php
awk '{sub(/namespace Database\\Factories/,"namespace Orchestra\\Testbench\\Factories")}1' src/Factories/UserFactory.php > src/Factories/UserFactory.stub && mv src/Factories/UserFactory.stub src/Factories/UserFactory.php
