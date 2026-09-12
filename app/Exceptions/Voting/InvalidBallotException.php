<?php

namespace App\Exceptions\Voting;

final class InvalidBallotException extends VotingException
{
    public function __construct(string $message = 'Pilihan surat suara tidak valid atau tidak memenuhi ketentuan pemilihan.')
    {
        parent::__construct($message, 422);
    }
}
