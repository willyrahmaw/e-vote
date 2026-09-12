<?php

namespace App\Exceptions\Voting;

final class AlreadyVotedException extends VotingException
{
    public function __construct(string $message = 'Anda telah menggunakan hak suara Anda untuk pemilihan ini. Suara tidak dapat dikirim ulang.')
    {
        parent::__construct($message, 403);
    }
}
