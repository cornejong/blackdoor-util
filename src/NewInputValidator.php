<?php

namespace Blackdoor\Util;

use Blackdoor\Util\Traits\Searchable;
use Blackdoor\Util\Traits\MagicObjectAccess;
use Blackdoor\Util\Traits\ArrayAccess as ArrayAccessTrait;
use Blackdoor\Util\Str;
use Blackdoor\Util\Expression;
use Blackdoor\Util\Error\InputValidationException;
use ArrayAccess;

class NewInputValidator implements ArrayAccess
{
    use ArrayAccessTrait;
    // use Searchable;

    public $definition = [];

    public $__invalid = [];
    public $__input = [];
    public $__validated = [];

    public $castToScalarType = true;

    protected $containerPointer = '__input';

    public function __construct(array $definition = null, array $input = null)
    {
        $this->input($input);
        $this->definition($definition);
    }

    public function tryCastToScalarType(bool $yes)
    {
        $this->castToScalarType = $yes;
    }

    public function definition(array $definition = null)
    {
        if (!is_null($definition)) {
            $this->definition = $definition;
        }

        return $this->definition;
    }

    public function input(array $input = null)
    {
        if (!is_null($input)) {
            $this->__input = $input;
        }

        return $this->__input;
    }

    public function getContainerPointer(): string
    {
        return $this->containerPointer;
    }

    public function invalid()
    {
        return $this->__invalid;
    }

    public function validate($input): bool
    {
        if (!is_null($input)) {
            $this->input($input);
        }

        // $flatInput = Arr::flatten($this->input);
        // $flatDefinition = $this->flattenDefinition();
        $validation = $this->validateArray($this->definition, $this->__input);

        if ($validation["success"]) {
            $this->__validated = $validation["validated"];
            $this->containerPointer = "__validated";
        } else {
            $this->__invalid = $validation["invalid"];
        }

        return $validation["success"];
    }

    public function flattenDefinition(array $definition, bool $returnFlat = true): array
    {
        $tmp = [];

        foreach ($definition as $key => $rules) {
            if (is_array($rules) && isset($rules["children"]) && !empty($rules["children"])) {
                $tmp[$key] = $this->flattenDefinition($rules["children"], false);
            } else {
                $tmp[$key] = $rules;
            }
        }

        return $returnFlat ? Arr::flatten($tmp) : $tmp;
    }

    public function validateArray(array $definition, array $input)
    {
        $validated = [];
        $invalid = [];

        if (!Arr::isAssoc($definition)) {
            /* TODO: ValidateList */
            return $this->validateList($definition[0], $input);
        }

        foreach ($definition as $field => $rules) {
            if (isset($rules['empty'])) {
                $rules['allowEmpty'] = $rules['empty'];
            }

            if (!isset($rules['type'])) {
                $rules['type'] = "string";
            }

            /* Dismiss non required and missing items */
            if (!($rules['required'] ?? true) && !isset($input[$field])) {
                if (isset($rules['default'])) {
                    $validated[$field] = $rules['default'];
                }
                continue;
            }

            /* Check if it was required */
            if (($rules['required'] ?? false) && !isset($input[$field])) {
                $invalid['missing'][] = $field;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) === false && empty($input[$field])) {
                $invalid['empty'][] = $field;
                continue;
            }

            if (isset($rules['default']) && !isset($input[$field])) {
                $input[$field] = $rules['default'];
            }

            /* Check if it matches the pattern */
            if (isset($rules['pattern']) && !Expression::match($rules['pattern'], $input[$field])) {
                // if($field === 'redirect_uri') dd($this);
                $invalid['notMatchingPattern'][] = $field;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) && empty($input[$field]) && isset($rules['default'])) {
                $input[$field] = $rules['default'];
            }

            if (isset($input[$field]) && $this->castToScalarType && is_string($input[$field]) && (isset($rules["type"]) && $rules["type"] !== "string")) {
                $input[$field] = Str::getRealType($input[$field]);
            }

            /* TODO: DO TYPE CHECKING */

            if (isset($rules["children"]) && !empty($rules["children"])) {
                $subValidation = $this->validateArray($definition[$field]["children"], $input[$field]);

                if (!empty($subValidation["invalid"])) {
                    foreach ($subValidation["invalid"] as $invalidType => $fieldName) {
                        $invalid[$invalidType] = array_merge($invalid[$invalidType] ?? [], [$field => $fieldName]);
                    }
                }

                $validated[$field] = $subValidation["validated"];
            } else {
                $validated[$field] = $input[$field];
            }
        }

        return [
            "success" => empty($invalid),
            "validated" => $validated,
            "invalid" => $invalid
        ];
    }

    public function validateList(array $rules, array $input): array
    {
        $validated = [];
        $invalid = [];

        if (!isset($rules['type'])) {
            $rules['type'] = "string";
        }

        foreach ($input as $index => $value) {
            if (isset($rules['empty'])) {
                $rules['allowEmpty'] = $rules['empty'];
            }

            /* Dismiss non required and missing items */
            if (!($rules['required'] ?? true) && !isset($input)) {
                if (isset($rules['default'])) {
                    $validated[$index] = $rules['default'];
                }
                continue;
            }


            if (($rules['allowEmpty'] ?? true) === false && empty($input[$index])) {
                $invalid['empty'][] = $index;
                continue;
            }

            if (isset($rules['default']) && !isset($input[$index])) {
                $input[$index] = $rules['default'];
            }

            /* Check if it matches the pattern */
            if (isset($rules['pattern']) && !Expression::match($rules['pattern'], $input[$field])) {
                // if($field === 'redirect_uri') dd($this);
                $invalid['notMatchingPattern'][] = $index;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) && empty($input[$index]) && isset($rules['default'])) {
                $input[$index] = $rules['default'];
            }

            if (isset($input[$index]) && $this->castToScalarType && is_string($input[$index]) && (isset($rules["type"]) && $rules["type"] !== "string")) {
                $input[$index] = Str::getRealType($input[$index]);
            }

            /* TODO: DO TYPE CHECKING */

            if (isset($rules["children"]) && !empty($rules["children"])) {
                $subValidation = $this->validateArray($definition[$index]["children"], $input[$index]);

                if (!empty($subValidation["invalid"])) {
                    foreach ($subValidation["invalid"] as $invalidType => $fieldName) {
                        $invalid[$invalidType] = array_merge($invalid[$invalidType] ?? [], [$index => $fieldName]);
                    }
                }

                $validated[$index] = $subValidation["validated"];
            } else {
                $validated[$index] = $input[$index];
            }
        }


        return [
            "success" => empty($invalid),
            "validated" => $validated,
            "invalid" => $invalid
        ];
    }

    public function validate__(array $input = null)
    {
        if (!is_null($input)) {
            $this->input($input);
        }

        foreach ($this->definition as $field => $rules) {
            if (isset($rules['empty'])) {
                $rules['allowEmpty'] = $rules['empty'];
            }

            /* Dismiss non required and missing items */
            if (!($rules['required'] ?? true) && !isset($this->input[$field])) {
                if (isset($rules['default'])) {
                    $this->validated[$field] = $rules['default'];
                }
                continue;
            }

            /* Check if it was required */
            if (($rules['required'] ?? false) && !isset($this->input[$field])) {
                $this->invalid['missing'][] = $field;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) === false && empty($this->input[$field])) {
                $this->invalid['empty'][] = $field;
                continue;
            }

            if (isset($rules['default']) && !isset($this->input[$field])) {
                $this->input[$field] = $rules['default'];
            }

            /* Check if it matches the pattern */
            if (isset($rules['pattern']) && !Expression::match($rules['pattern'], $this->input[$field])) {
                // if($field === 'redirect_uri') dd($this);

                $this->invalid['notMatchingPattern'][] = $field;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) && empty($this->input[$field]) && isset($rules['default'])) {
                $this->input[$field] = $rules['default'];
            }

            if (isset($this->input[$field]) && $this->castToScalarType && is_string($this->input[$field])) {
                $this->input[$field] = Str::getRealType($this->input[$field]);
            }

            $this->validated[$field] = $this->input[$field];
        }

        if (empty($this->invalid)) {
            /* Switch the array access data container pointer to the validated data */
            $this->containerPointer = 'validated';
            return true;
        }

        return false;
    }

    public function validateField(array $rules, $fieldName, $fieldValue): array
    {
    }

    public function asArray(array $keys = null)
    {
        $array = $this->{$this->containerPointer};

        if (!is_null($keys)) {
            $array = array_filter($array, function ($key) use ($keys) {
                return in_array($key, $keys, true);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $array;
    }

    /**
     * validates the input. if failed throws InputValidationException
     *
     * @throws InputValidationException
     * @param array|null $input
     * @return void
     */
    public function validateOrThrow(array $input = null): void
    {
        if (!$this->validate($input)) {
            $this->throwValidationErrors();
        }
    }

        /**
         * throws the current validation errors
     *
     * @throws InputValidationException
     * @param string|null $domain
     * @return void
     */
    public function throwValidationErrors(string $domain = null): void
    {
        foreach ($this->invalid as $type => $parameters) {
            foreach ($parameters as $parameter) {
                $failed[$parameter]['issues'][] = $type;
                $message = 'Failed the' . ($domain ? ' ' . $domain : '') . ' input validation! "' . $parameter . '" is "' . $type . '".';

                if ($type === 'notMatchingPattern') {
                    $message .= ' Pattern: ' . $this->definition[$parameter]['pattern'];
                }

                throw new InputValidationException($message);
            }
        }
    }

    public function validationErrorAsProblemJsonArray(string $domain = null)
    {
        $failed = [];

        foreach ($this->invalid as $type => $parameters) {
            foreach ($parameters as $parameter) {
                $tmp = [
                    'name' => $parameter,
                    'issue' => $type,
                ];

                if ($type === 'notMatchingPattern') {
                    //$failed['patterns'][$parameter] = $this->definition[$parameter]['pattern'];
                    $tmp['pattern'] = $this->definition[$parameter]['pattern'];
                }

                $failed[] = $tmp;
            }
        }

        if ($domain) {
            $failed = [Str::camelize($domain) => $failed];
        }

        /* return new ProblemJson(412, 'The request did not pass the' . ($domain ? ' ' . $domain : '') . ' input validation. Please check "failed" for more info.', [
            'failed' => $failed,
        ]); */
    }
}
