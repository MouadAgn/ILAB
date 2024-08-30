<?php
// src/Doctrine/SoftDeleteFilter.php
namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        // Check if the entity has the deletedAt field
        if (!$targetEntity->hasField('deleted_at')) {
            return '';
        }

        // Return the SQL condition to exclude soft-deleted entities
        return sprintf('%s.deleted_at IS NULL', $targetTableAlias);
    }
}

