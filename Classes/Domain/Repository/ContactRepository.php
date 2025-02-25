<?php
declare(strict_types = 1);
namespace Bitmotion\Mautic\Domain\Repository;

use Mautic\Api\Contacts;
use Mautic\Exception\ContextNotFoundException;

class ContactRepository extends AbstractRepository
{
    /**
     * @var Contacts
     */
    protected $contactsApi;

    /**
     * @throws ContextNotFoundException
     */
    protected function injectApis(): void
    {
        $this->contactsApi = $this->getApi('contacts');
    }

    public function findContactSegments(int $id): array
    {
        $segments = $this->contactsApi->getContactSegments($id);

        return $segments['lists'] ?? [];
    }

    public function createContact(array $parameters): array
    {
        return $this->contactsApi->create($parameters) ?: [];
    }

    public function editContact(int $id, array $data): array
    {
        return $this->contactsApi->edit($id, $data, false);
    }

    public function getContact(int $id): array
    {
        $contact = $this->contactsApi->get($id);

        return $contact['contact'] ?? [];
    }

    public function modifyContactPoints(int $id, int $modifier, array $data = [])
    {
        if ($modifier > 0) {
            $this->contactsApi->addPoints($id, $modifier, $data);
        } else {
            $this->contactsApi->subtractPoints($id, abs($modifier), $data);
        }
    }
}
