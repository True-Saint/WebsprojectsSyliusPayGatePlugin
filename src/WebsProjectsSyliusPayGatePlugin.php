<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class WebsProjectsSyliusPayGatePlugin extends Bundle
{
    use SyliusPluginTrait;
}
