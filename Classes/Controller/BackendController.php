<?php
declare(strict_types=1);
namespace Bitmotion\Mautic\Controller;

use Bitmotion\Mautic\Domain\Model\Dto\YamlConfiguration;
use Bitmotion\Mautic\Service\MauticAuthorizeService;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;

class BackendController extends ActionController
{
    const FLASH_MESSAGE_QUEUE = 'marketingautomation.mautic.flashMessages';

    protected $defaultViewObjectName = BackendTemplateView::class;

    public function showAction()
    {
        $emConfiguration = new YamlConfiguration();
        $authorizeService = GeneralUtility::makeInstance(MauticAuthorizeService::class);

        if ($authorizeService->validateCredentials() === true) {
            if ($emConfiguration->getAccessToken() === '' || $emConfiguration->getAccessTokenSecret() === '') {
                if ((int)GeneralUtility::_GP('tx_marketingauthorizemautic_authorize') !== 1) {
                    $this->view->assign('authorizeButton', $authorizeService->getAuthorizeButton());
                } else {
                    $authorizeService->authorize();
                }
            } else {
                $authorizeService->checkConnection();
            }
        }

        $this->view->assign('configuration', $emConfiguration);
    }

    /**
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function saveAction(array $configuration)
    {
        $emConfiguration = new YamlConfiguration();

        if (substr($configuration['baseUrl'], -1) === '/') {
            $configuration['baseUrl'] = rtrim($configuration['baseUrl'], '/');
        }

        $emConfiguration->save($configuration);
        $this->redirect('show');
    }
}
