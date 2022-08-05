![showsql](https://banners.beyondco.de/mailspfchecker.png?theme=light&packageManager=composer+require&packageName=dietercoopman%2Fmailspfchecker&pattern=architect&style=style_1&description=A+Laravel+package+to+check+if+your+application+can+send+e-mail+in+name+of+a+given+address.&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

# A package to check if you can send e-mail through a given mailserver in name of a given e-mail address

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dietercoopman/mailspfchecker.svg?style=flat-square)](https://packagist.org/packages/dietercoopman/mailspfchecker)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dietercoopman/mailspfchecker/run-tests?label=tests)](https://github.com/dietercoopman/mailspfchecker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dietercoopman/mailspfchecker/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/dietercoopman/mailspfchecker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dietercoopman/mailspfchecker.svg?style=flat-square)](https://packagist.org/packages/dietercoopman/mailspfchecker)

# Mail spf checker

A Laravel package to check if your application can send e-mail in name of a given address.

## Use case

Most of the web applications are sending mails.  Mostly through a local mail server or an external mailing service.  
When sending in name of a domain without using the legitimate mailserver of the domain it can get tricky.  
Most of the time your mail ends up in a spam folder.  This can be solved by configuring a correct SPF record for the domain you are sending with.  This package
gives you the possibility to check if you can send with a given from address using the mailserver specified in your mail config
or a given mailserver.  It also gives the possibility to retrieve a dns txt record to configure your dns. 

## Compatibility

This package can be installed in Laravel 6,7,8 and 9

## Installation

```shell
composer require dietercoopman/mailspfchecker
```

## Examples

### Using the mailserver used by your application

```php 

    if ($mailSpfChecker->canISendAs("hello@dietse.dev")) {
        // the happy path
    } else {
        // you can not send e-mail in name of hello@dietse.dev, but I can tell you what to do  
        echo $mailSpfChecker->howCanISendAs("hello@dietse.be");
        // Generate a txt-record with a name of dietse.dev and the value v=spf1 ip4:#.#.#.# -all
    }
```

### Using a given mailserver

```php 

    if ($mailSpfChecker->using('smtp.mandrill.com')->canISendAs("hello@dietse.dev")) {
        // the happy path
    } else {
        // you can not send e-mail in name of hello@dietse.dev, but I can tell you what to do  
        echo $mailSpfChecker->using('smtp.mandrill.com')->howCanISendAs("hello@dietse.be");
        // Generate a txt-record with a name of dietse.dev and the value v=spf1 ip4:spf.mandrill.com -all
    }
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dieter Coopman](https://github.com/dietercoopman)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
