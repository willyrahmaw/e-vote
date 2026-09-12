<?php

namespace App\Exceptions\Voting;

final class ElectionHasEndedException extends VotingException
{
    public function __construct(string $message = 'Waktu pemilihan telah berakhir. Pengiriman suara sudah ditutup.')
    {
        parent::__construct($message, 400);
    }
}
