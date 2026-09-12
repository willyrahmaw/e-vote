<?php

namespace App\Exceptions\Voting;

final class VoterNotEligibleException extends VotingException
{
    public function __construct(string $message = 'Akun Anda tidak terdaftar sebagai pemilih sah dalam pemilihan ini.')
    {
        parent::__construct($message, 403);
    }
}
