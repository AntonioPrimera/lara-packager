<?php
//#!/usr/bin/env php

$packagerPath = __DIR__ . DIRECTORY_SEPARATOR . "packager";
$targetPath = getcwd() . DIRECTORY_SEPARATOR . "packager";

echo "Trying to link {$packagerPath} to {$targetPath} \n";

symlink($packagerPath, $targetPath);