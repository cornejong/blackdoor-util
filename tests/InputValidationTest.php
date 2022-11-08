<?php

namespace Tests;

use Blackdoor\Util\NewInputValidator;
use Blackdoor\Testing\AbstractTest;

class InputValidationTest extends AbstractTest
{
    public $input = [
        "name" => "John Doe",
        "phone" => [
            "0612345678",
            "0381234567",
        ],
        "address" => [
            "street" => "Oudestraat",
            "number" => 142,
            "numberAddition" => "01",
            "zipcode" => "8261CX",
            "city" => "Kampen",
            "country" => "The Netherlands"
        ]
    ];

    public $definition = [
        "name" => ["type" => "string", "required" => true, "allowEmpty" => false],
        "phone" => [
            "type" => "list",
            "children" => [
                ["type" => "string", "required" => true, "allowEmpty" => false],
            ]
        ],
        "address" => [
            "type" => "array",
            "required" => true,
            "allowEmpty" => false,
            "children" => [
                "street" => ["type" => "string", "required" => true, "allowEmpty" => false],
                "number" => ["type" => "int", "required" => true, "allowEmpty" => false],
                "numberAddition" => ["type" => "string", "required" => false, "allowEmpty" => false],
                "zipcode" => ["type" => "string", "required" => true, "allowEmpty" => false, "pattern" => "/[\d]{4}[\w]{2}/"],
                "city" => ["type" => "string", "required" => true, "allowEmpty" => false],
                "country" => ["type" => "string", "required" => true, "allowEmpty" => false],
            ]
        ]
    ];

    public function init()
    {
        $this->validator = new NewInputValidator();
        $this->validator->definition($this->definition);
    }

    public function testMultidimensionalInputValidation()
    {
        $this->assert($this->validator->validate($this->input), true, "InputValidatedSuccessResult");
        $this->assert($this->validator->invalid(), [], "NotInvalidAfterValidation");
        $this->assert($this->validator->getContainerPointer(), "__validated", "ContainerPointerIsUpdated");   
        $this->assert($this->validator->asArray(), $this->input, "ValidatedIsEqualToInput");   
    }
}