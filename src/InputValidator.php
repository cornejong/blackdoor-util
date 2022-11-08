<?php

namespace Blackdoor\Util;

use Blackdoor\Util\Traits\Searchable;
use Blackdoor\Util\Traits\ArrayAccess as ArrayAccessTrait;
use Blackdoor\Util\Str;
use Blackdoor\Util\Expression;
use Blackdoor\Util\Error\InputValidationException;
use ArrayAccess;

class InputValidator implements ArrayAccess
{
    use ArrayAccessTrait;
    // use Searchable;

    public $definition = [];

    public $invalid = [];

    public $input = [];
    public $validated = [];

    public $castToScalarType = true;
    public $allowOutsideDefinition = false;

    protected $containerPointer = 'input';

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
            $this->input = $input;
        }

        return $this->input;
    }

    public function validate(array $input = null)
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

        if (!empty($this->invalid)) {
            return false;
        }

        if ($this->allowOutsideDefinition) {
            $extraField = $this->input;
            foreach(array_keys($this->validated) as $knownKey) {
                unset($extraField[$knownKey]);
            }
            $this->validated = array_merge($this->validated, $extraField);
        }

        /* Switch the array access data container pointer to the validated data */
        $this->containerPointer = 'validated';
        return true;
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
}
