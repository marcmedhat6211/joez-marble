<?php

namespace App\Twig;

use App\ECommerceBundle\Services\CurrencyService;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getPriceWithCurrentCurrency', [$this, 'getPriceWithCurrentCurrency']),
        ];
    }

    public function getPriceWithCurrentCurrency($priceInEgp): string
    {
        return $this->currencyService->getPriceWithCurrentCurrency($priceInEgp);
    }
}