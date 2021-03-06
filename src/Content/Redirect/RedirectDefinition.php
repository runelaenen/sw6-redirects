<?php
declare(strict_types=1);

namespace RuneLaenen\Redirects\Content\Redirect;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class RedirectDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'rl_redirects_redirect';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return RedirectCollection::class;
    }

    public function getEntityClass(): string
    {
        return RedirectEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new StringField('source', 'source'),
            new StringField('target', 'target'),
            new IntField('http_code', 'httpCode'),
        ]);
    }
}
