<?php declare(strict_types=1);

namespace RuneLaenen\Redirects\Migration;

use Doctrine\DBAL\Connection;
use RuneLaenen\Redirects\Content\Redirect\RedirectDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1615031757ImportExportProfile extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1615031757;
    }

    public function update(Connection $connection): void
    {
        foreach ($this->getProfiles() as $profile) {
            $profile['id'] = Uuid::randomBytes();
            $profile['system_default'] = 1;
            $profile['file_type'] = 'text/csv';
            $profile['delimiter'] = ';';
            $profile['enclosure'] = '"';
            $profile['mapping'] = json_encode($profile['mapping']);
            $profile['created_at'] = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

            $connection->insert('import_export_profile', $profile);

            $translation = [
                'import_export_profile_id' => $profile['id'],
                'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
                'label' => $profile['name'],
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ];
            $connection->insert('import_export_profile_translation', $translation);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function getProfiles(): array
    {
        return [
            [
                'name' => 'Default redirect',
                'source_entity' => RedirectDefinition::ENTITY_NAME,
                'mapping' => [
                    ['key' => 'id', 'mappedKey' => 'id'],
                    ['key' => 'httpCode', 'mappedKey' => 'http_code'],
                    ['key' => 'source', 'mappedKey' => 'source'],
                    ['key' => 'target', 'mappedKey' => 'target'],
                ],
            ],
        ];
    }
}
