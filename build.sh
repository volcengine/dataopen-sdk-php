#!/bin/sh

rm -rf release/dataopen-sdk-php*
mkdir release/dataopen-sdk-php

cp -rf LICENSE release/dataopen-sdk-php/
cp -rf README.md release/dataopen-sdk-php/
cp -rf ./src/Client.php release/dataopen-sdk-php/
cp -rf composer.json release/dataopen-sdk-php/
cp -rf composer.lock release/dataopen-sdk-php/

cd release
zip -r dataopen-sdk-php.zip dataopen-sdk-php/*

rm -rf dataopen-sdk-php

cd ../