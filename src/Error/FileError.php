<?php

namespace Blackdoor\Util\Error;

use Blackdoor\Util\Json;

class FileError extends \Error
{
    public const NOT_VALID_PATH = [
        'message' => 'The provided path is not valid! Provided: ',
        'code' => 999,
    ];

    public const NOT_A_DIRECTORY = [
        'message' => 'The provided path is not a directory! Provided: ',
        'code' => 888,
    ];

    public const NOT_A_FILE = [
        'message' => 'The provided path is not a file! Provided: ',
        'code' => 850,
    ];

    public const IDENTIFIER_ALREADY_IN_USE = [
        'message' => 'The provided identifier is already in use! Provided: ',
        'code' => 777,
    ];

    public const UNKNOWN_DIRECTORY_IDENTIFIER = [
        'message' => 'The provided identifier is unknown! Provided: ',
        'code' => 770,
    ];

    public const NEITHER_FILE_NOR_DIRECTORY = [
        'message' => 'The provided path leads to neither a file nor a directory! Provided: ',
        'code' => 550,
    ];

    public const COULD_NOT_DELETE = [
        'message' => 'Could not delete the resource in the provided path! Provided: ',
        'code' => 660,
    ];

    /**
     * @param array $error
     * @param $extra
     */
    public function __construct(array $error, $extra = null)
    {
        extract($error);

        if (!empty($extra)) {
            $message .= "\n" . Json::prettyEncode($extra);
        }

        parent::__construct($message, $code);
    }
}
