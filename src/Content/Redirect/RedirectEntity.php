<?php declare(strict_types=1);

namespace RuneLaenen\Redirects\Content\Redirect;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class RedirectEntity extends Entity
{
    use EntityIdTrait;

    /** @var string */
    protected $source;

    /** @var string */
    protected $target;

    /** @var int */
    protected $httpCode;

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $httpCode): void
    {
        $this->httpCode = $httpCode;
    }
}
