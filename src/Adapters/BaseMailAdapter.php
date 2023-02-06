<?php

namespace Cornatul\Marketing\Base\Adapters;

use Cornatul\Marketing\Base\Interfaces\MailAdapterInterface;

abstract class BaseMailAdapter implements MailAdapterInterface
{
    /** @var array */
    protected $config;

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
