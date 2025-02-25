<?php
declare(strict_types = 1);
namespace Bitmotion\Mautic\Hooks;

use Bitmotion\Mautic\Domain\Repository\ContactRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class MauticTagHook
{
    public function setTags(array $params, PageRenderer $pageRenderer)
    {
        $page = $GLOBALS['TSFE']->page;

        if ($page['tx_mautic_tags'] > 0) {
            $tags = $this->getTagsToAssign($page);
            if (!empty($tags)) {
                $contactId = (int)$_COOKIE['mtc_id'];

                if ($contactId > 0) {
                    $contactRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(ContactRepository::class);
                    $contactRepository->editContact($contactId, ['tags' => $tags]);
                }
            }
        }
    }

    protected function getTagsToAssign(array $page): array
    {
        $pageUid = $page['_PAGES_OVERLAY_UID'] ?? $page['uid'];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_mautic_page_tag_mm');
        $result = $queryBuilder
            ->select('uid_foreign')
            ->from('tx_mautic_page_tag_mm')
            ->where($queryBuilder->expr()->eq('uid_local', $pageUid))
            ->execute();

        $tags = [];

        while ($tag = $result->fetchColumn()) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_mautic_domain_model_tag');
            $tags[$tag] = $queryBuilder
                ->select('title')
                ->from('tx_mautic_domain_model_tag')
                ->where($queryBuilder->expr()->eq('uid', $tag))
                ->execute()
                ->fetchColumn();
        }

        return $tags;
    }
}
