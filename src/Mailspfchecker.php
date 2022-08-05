<?php

namespace Dietercoopman\Mailspfchecker;

use IPLib\Address\IPv4;
use SPFLib\Decoder;
use SPFLib\Term\Mechanism;


class Mailspfchecker
{

    private array  $spfRecords = [];
    private string $server     = "";
    private string $sendingMailserver;

    public function __construct()
    {
        $this->setSendingMailServer();
    }

    public function canISendAs(string $emailOrDomain): bool
    {
        return ($this->check($emailOrDomain, true) === "pass");
    }

    public function using(string $server): self
    {
        $this->server = $server;
        $this->setSendingMailServer();
        return $this;
    }

    private function getDomain(string $emailOrDomain): string
    {
        if (filter_var($emailOrDomain, FILTER_VALIDATE_EMAIL)) {
            $domain = substr($emailOrDomain, strpos($emailOrDomain, '@') + 1);
        } else {
            $domain = $emailOrDomain;
        }
        return $domain;
    }

    public function check(string $emailOrDomain, $returnCode = false): mixed
    {
        $domain  = $this->getDomain($emailOrDomain);
        $decoder = new \SPFLib\Decoder();

        $code  = "error";
        $codes = [];


        foreach ($this->spfRecords as $spfValueToCheck) {
            $record = $decoder->getRecordFromDomain($domain);
            if ($record) {
                foreach ($record->getTerms() as $term) {
                    $codes[$spfValueToCheck] = "error";
                    if (strstr(strtolower($term), strtolower($spfValueToCheck))) {
                        $codes[$spfValueToCheck] = "pass";
                        break;
                    }
                }
            } else {
                $codes[$spfValueToCheck] = "error";
            }
        }

        if (in_array("pass", $codes)) {
            $code = "pass";
        }

        return ($returnCode) ? $code : $codes;

    }

    private function setSendingMailServer(): void
    {
        if (!blank($this->server)) {
            $sendingMailserver = $this->server;
        } else {
            $sendingMailserver = config('mail.mailers.smtp.host');
        }

        //if the address is localhost, then check wan address via icanhazip
        if ($sendingMailserver == "127.0.0.1" || $sendingMailserver == "localhost") {
            $sendingMailserver = trim(file_get_contents("https://icanhazip.com/"));
        }

        $this->sendingMailserver = $sendingMailserver;
        $this->spfRecords        = $this->retreiveSpfRecordsFromSendingServer();


    }

    private function retreiveSpfRecordsFromSendingServer(): array
    {
        $spfRecords = [];
        $server     = $this->sendingMailserver;

        if (!filter_var($server, FILTER_VALIDATE_IP)) {

            $checker           = new Decoder();
            $explodedServerUrl = explode('.', $server);
            array_shift($explodedServerUrl);
            $domain = implode(".", $explodedServerUrl);
            if ($domain) {
                $record = $checker->getRecordFromDomain($domain);
                foreach ($record->getTerms() as $term) {
                    if ($term instanceof Mechanism\IncludeMechanism || $term instanceof Mechanism\AMechanism) {
                        $domainSpec = (string)$term->getDomainSpec();
                        if (strstr($domainSpec, $domain)) {
                            $spfRecords[] = $domainSpec;
                        }
                    }
                }
            }
        }
        return array_unique($spfRecords);
    }

    public function howCanISendAs(string $emailOrDomain, string $overRuleMessage = null): string
    {
        list($name, $value) = array_values($this->buildDnsString($emailOrDomain));
        if ($overRuleMessage) {
            return str_replace([':name', ':value'], [$name, $value], $overRuleMessage);
        }
        return "Generate a txt-record with a name of <strong>{$name}</strong> and the value <strong>{$value}</strong>";
    }

    public function buildDnsString(string $emailOrDomain): array
    {
        $domain = $this->getDomain($emailOrDomain);
        $record = new \SPFLib\Record();
        if (!empty($this->spfRecords)) {
            foreach ($this->spfRecords as $server) {
                if (filter_var($server, FILTER_VALIDATE_IP)) {
                    $record->addTerm(new Mechanism\Ip4Mechanism(Mechanism::QUALIFIER_PASS, IPv4::parseString($server)));
                } else {
                    $record->addTerm(new Mechanism\IncludeMechanism(Mechanism::QUALIFIER_PASS, $server));
                }
            }
        } else {
            $record->addTerm(new Mechanism\Ip4Mechanism(Mechanism::QUALIFIER_PASS, IPv4::parseString(gethostbyname($this->sendingMailserver))));
        }

        $record->addTerm(new Mechanism\AllMechanism(Mechanism::QUALIFIER_FAIL));

        return ["name" => $domain, "value" => (string)$record];
    }
}
