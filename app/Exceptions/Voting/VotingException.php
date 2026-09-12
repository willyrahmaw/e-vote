<?php

namespace App\Exceptions\Voting;

use Exception;

class VotingException extends Exception
{
    public function __construct(string $message = 'Terjadi kesalahan saat memproses suara voting.', int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
