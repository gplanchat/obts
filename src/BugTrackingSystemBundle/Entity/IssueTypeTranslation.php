<?php

namespace Oro\Bundle\BugTrackingSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Table(
 *      name="oro_bts_issue_type_trans",
 *      indexes={
 *          @ORM\Index(
 *              name="idx_oro_bts_issue_type_trans",
 *              columns={"locale", "object_class", "field", "foreign_key"}
 *          )
 *      }
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 * @codeCoverageIgnore
 */
class IssueTypeTranslation extends AbstractTranslation
{
    /**
     * @var string $foreignKey
     *
     * @ORM\Column(name="foreign_key", type="string", length=16)
     */
    protected $foreignKey;

    /**
     * @var string $content
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $content;
}
