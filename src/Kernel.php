<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    const WEBSITE_TITLE = "Joez Marble";
    const FROM_EMAIL = 'no-reply@joez-marble.com';
    const ADMIN_EMAIL = 'info@joez-marble.com';
}
