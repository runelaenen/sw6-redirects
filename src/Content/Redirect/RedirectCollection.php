<?php declare(strict_types=1);

namespace RuneLaenen\Redirects\Content\Redirect;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(RedirectEntity $entity)
 * @method void                set(string $key, RedirectEntity $entity)
 * @method RedirectEntity[]    getIterator()
 * @method RedirectEntity[]    getElements()
 * @method RedirectEntity|null get(string $key)
 * @method RedirectEntity|null first()
 * @method RedirectEntity|null last()
 */
class RedirectCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return RedirectEntity::class;
    }
}
