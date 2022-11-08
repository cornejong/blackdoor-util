<?php

require 'vendor/autoload.php';

use Tests\InputValidationTest;
use Blackdoor\Testing\TestCollection;
use Tests\Trait\AccessObjectDefaultPropertiesTraitTest;

$tests = new TestCollection();

$tests->register(new InputValidationTest);
$tests->register(new AccessObjectDefaultPropertiesTraitTest);

$tests->runInConsole();