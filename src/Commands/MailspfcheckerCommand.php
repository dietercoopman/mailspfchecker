<?php

namespace Dietercoopman\Mailspfchecker\Commands;

use Illuminate\Console\Command;

class MailspfcheckerCommand extends Command
{
    public $signature = 'mailspfchecker';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
