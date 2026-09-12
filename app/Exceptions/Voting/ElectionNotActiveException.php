<?php

namespace App\Exceptions\Voting;

final class ElectionNotActiveException extends VotingException
{
    public function __construct(string $message = 'Sesi pemilihan ini belum dibuka atau saat ini sedang tidak aktif.')
    {
        parent::__construct($message, 400);
    }
}
