<?php
// src/EventSubscriber/DoctrineSubscriber.php
namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs; // Correct class import
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSubscriber implements EventSubscriber
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    // Correct type hint for onFlush method
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager(); // Correct method to get the EntityManager

        // Check if the soft delete filter is enabled and enable it if not
        if (!$em->getFilters()->isEnabled('soft_delete')) {
            $em->getFilters()->enable('soft_delete');
        }
    }
}
