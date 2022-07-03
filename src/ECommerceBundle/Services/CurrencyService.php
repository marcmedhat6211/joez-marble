<?php

namespace App\ECommerceBundle\Services;

use Symfony\Contracts\Translation\TranslatorInterface;

class CurrencyService
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getPriceWithCurrentCurrency($priceInEgp): string
    {
        $currencyCode = $this->translator->trans("egp");
        $egpEquivalence = 1;

        if (isset($_COOKIE["currencyCode"]) || isset($_COOKIE["egpEquivalence"])) {
            $currencyCode = $_COOKIE["currencyCode"];
            $egpEquivalence = $_COOKIE["egpEquivalence"];
        }

        return number_format($priceInEgp * $egpEquivalence) . " " . $currencyCode;
    }
}