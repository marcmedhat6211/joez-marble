<?php

namespace App\Twig;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('calculateCurrency', [$this, 'calculateCurrency']),
        ];
    }

    public function calculateCurrency($priceInEgp, $egpEquivalence): float
    {
        return $priceInEgp * $egpEquivalence;
    }
}