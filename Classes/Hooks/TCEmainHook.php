<?php
declare(strict_types=1);
namespace Bitmotion\Mautic\Hooks;

use Bitmotion\Mautic\Domain\Model\Dto\YamlConfiguration;
use Bitmotion\Mautic\Domain\Repository\TagRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TCEmainHook
{
    /**
     * Create tags in Mautic and synchronize them with TYPO3 to receive proper IDs
     */
    public function processDatamap_afterDatabaseOperations(string $status, string $table, string $id, array $fields, DataHandler &$dataHandler)
    {
        if ($status === 'new' && $table === 'tx_mautic_domain_model_tag' && !empty($fields['title'])) {
            // Dirty way to create tags in Mautic
            $config = new YamlConfiguration();
            $url = sprintf('%s/mtracking.gif?tags=%s', $config->getBaseUrl(), $fields['title']);
            GeneralUtility::getUrl($url);

            // Synchronize tags to receive proper ids
            $tagRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(TagRepository::class);
            $tagRepository->synchronizeTags();
        }
    }
}
