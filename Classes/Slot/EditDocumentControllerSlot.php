<?php
declare(strict_types = 1);
namespace Bitmotion\Mautic\Slot;

use Bitmotion\Mautic\Domain\Repository\SegmentRepository;
use Bitmotion\Mautic\Domain\Repository\TagRepository;
use TYPO3\CMS\Backend\Controller\EditDocumentController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EditDocumentControllerSlot
{
    protected $segmentRepository;

    protected $tagRepository;

    public function __construct(SegmentRepository $segmentRepository, TagRepository $tagRepository)
    {
        $this->segmentRepository = $segmentRepository;
        $this->tagRepository = $tagRepository;
    }

    public function synchronizeSegments(EditDocumentController $editDocumentController)
    {
        if (empty(GeneralUtility::_GP('tx_marketingautomation_segments')['updateSegments'])) {
            return;
        }

        $this->segmentRepository->synchronizeSegments();
    }

    public function synchronizeTags(EditDocumentController $editDocumentController)
    {
        if (empty(GeneralUtility::_GP('tx_mautic_domain_model_tag')['updateTags'])) {
            return;
        }

        $this->tagRepository->synchronizeTags();
    }
}
