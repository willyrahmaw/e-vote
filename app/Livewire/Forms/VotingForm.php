<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VotingForm extends Form
{
    #[Validate('required|array|min:1')]
    public array $choices = [];

    #[Validate('accepted', message: 'Anda harus menyetujui pernyataan konfirmasi pilihan sebelum mengirim suara.')]
    public bool $confirmed = false;
}
