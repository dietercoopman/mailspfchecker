<?php

namespace Dietercoopman\Mailspfchecker;

use Dietercoopman\Mailspfchecker\Commands\MailspfcheckerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailspfcheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mailspfchecker')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_mailspfchecker_table')
            ->hasCommand(MailspfcheckerCommand::class);
    }
}
