<?php
declare(strict_types = 1);
namespace Bitmotion\Mautic\Domain\Repository;

use Bitmotion\Mautic\Service\MauticSendFormService;
use Mautic\Api\Forms;
use Mautic\Exception\ContextNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FormRepository extends AbstractRepository
{
    /**
     * @var Forms
     */
    protected $formsApi;

    /**
     * @throws ContextNotFoundException
     */
    protected function injectApis(): void
    {
        $this->formsApi = $this->getApi('forms');
    }

    public function getForm(int $identifier): array
    {
        $form = $this->formsApi->get($identifier);

        if (isset($form['errors'])) {
            foreach ($form['errors'] as $error) {
                $this->logger->critical(sprintf('%s: %s', $error['code'], $error['message']));
            }

            return [];
        }

        return $form['form'];
    }

    public function getAllForms(): array
    {
        return $this->formsApi->getList('', 0, 999)['forms'] ?: [];
    }

    public function createForm(array $parameters): array
    {
        return $this->formsApi->create($parameters) ?: [];
    }

    public function editForm(int $id, array $parameters, bool $createIfNotExists = false): array
    {
        // Unset cachedHtml to not exceed request header field server limit
        $parameters['cachedHtml'] = '';

        return $this->formsApi->edit($id, $parameters, $createIfNotExists) ?: [];
    }

    public function deleteForm(int $id): array
    {
        return $this->formsApi->delete($id) ?: [];
    }

    public function submitForm(int $id, array $data)
    {
        $data['formId'] = $id;
        $url = rtrim(trim($this->authorization->getBaseUrl()), '/') . '/form/submit?formId=' . $id;

        $mauticSendFormService = GeneralUtility::makeInstance(ObjectManager::class)->get(MauticSendFormService::class);
        $code = $mauticSendFormService->submitForm($url, $data);

        if ($code < 200 || $code >= 400) {
            $this->logger->critical(
                sprintf(
                    'An error occured submitting the form with the Mautic id %d to Mautic. Status code %d returned by Mautic.',
                    $id,
                    $code
                )
            );
        }
    }

    public function formExists(int $id): bool
    {
        return !empty($this->getForm($id));
    }
}
