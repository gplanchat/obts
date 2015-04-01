<?php

namespace Oro\Bundle\BugTrackingSystemBundle\Migrations\Data\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @codeCoverageIgnore
 */
class OroBugTrackingSystemBundle implements Migration
{
    /**
     * @var string
     */
    private $issueTableName = 'obts_issue';

    /**
     * @var string
     */
    private $issuePriorityTableName = 'obts_issue_priority';

    /**
     * @var string
     */
    private $issuePriorityTranslationTableName = 'obts_issue_priority_trans';

    /**
     * @var string
     */
    private $issueResolutionTableName = 'obts_issue_resolution';

    /**
     * @var string
     */
    private $issueResolutionTranslationTableName = 'obts_issue_resolution_trans';

    /**
     * @var string
     */
    private $issueTypeTableName = 'obts_issue_type';

    /**
     * @var string
     */
    private $issueTypeTranslationTableName = 'obts_issue_type_trans';

    /**
     * @var string
     */
    private $issueCollaboratorsTableName = 'obts_issue_collaborators';

    /**
     * @var string
     */
    private $issueRelationsTableName = 'obts_issue_relations';

    /**
     * @var string
     */
    private $userTableName = 'oro_user';

    /**
     * @var string
     */
    private $organizationTableName = 'oro_organization';

    /**
     * @var string
     */
    private $workflowItemTableName = 'oro_workflow_item';

    /**
     * @var string
     */
    private $workflowStepTableName = 'oro_workflow_step';

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createIssueTable($schema);
        $this->createIssuePriorityTable($schema);
        $this->createIssuePriorityTranslationTable($schema);
        $this->createIssueResolutionTable($schema);
        $this->createIssueResolutionTranslationTable($schema);
        $this->createIssueTypeTable($schema);
        $this->createIssueTypeTranslationTable($schema);
        $this->createIssueCollaboratorsTable($schema);
        $this->createIssueRelationsTable($schema);

        $this->addIssueForeignKeys($schema);
        $this->addIssueCollaboratorsForeignKeys($schema);
        $this->addIssueRelationsForeignKeys($schema);
    }

    /**
     * Create Issue Table
     *
     * @param Schema $schema
     */
    private function createIssueTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueTableName)) {
            $schema->dropTable($this->issueTableName);
        }

        $table = $schema->createTable($this->issueTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('summary', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('code', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('description', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('issue_type_id', 'integer', ['notnull' => false]);
        $table->addColumn('issue_priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('issue_resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);

        $table->setPrimaryKey(['id']);

        $table->addUniqueIndex(['code'], 'uidx_obts_issue_code');
        $table->addUniqueIndex(['workflow_item_id'], 'UNIQ_6D3EA5741023C4EE');
    }

    /**
     * Create IssuePriority table
     *
     * @param Schema $schema
     */
    private function createIssuePriorityTable(Schema $schema)
    {
        if ($schema->hasTable($this->issuePriorityTableName)) {
            $schema->dropTable($this->issuePriorityTableName);
        }

        $table = $schema->createTable($this->issuePriorityTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);

        $table->setPrimaryKey(['id']);

        $table->addUniqueIndex(['label'], 'UNIQ_4FB220FDEA750E8');
    }

    /**
     * Create IssuePriorityTranslation table
     *
     * @param Schema $schema
     */
    private function createIssuePriorityTranslationTable(Schema $schema)
    {
        if ($schema->hasTable($this->issuePriorityTranslationTableName)) {
            $schema->dropTable($this->issuePriorityTranslationTableName);
        }

        $table = $schema->createTable($this->issuePriorityTranslationTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 16]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['locale', 'object_class', 'field', 'foreign_key'], 'idx_obts_issue_priority_trans');
    }

    /**
     * Create IssueResolution table
     *
     * @param Schema $schema
     */
    private function createIssueResolutionTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueResolutionTableName)) {
            $schema->dropTable($this->issueResolutionTableName);
        }

        $table = $schema->createTable($this->issueResolutionTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);

        $table->setPrimaryKey(['id']);

        $table->addUniqueIndex(['label'], 'UNIQ_320FB13CEA750E8');
    }

    /**
     * Create IssueResolutionTranslation table
     *
     * @param Schema $schema
     */
    private function createIssueResolutionTranslationTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueResolutionTranslationTableName)) {
            $schema->dropTable($this->issueResolutionTranslationTableName);
        }

        $table = $schema->createTable($this->issueResolutionTranslationTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 16]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['locale', 'object_class', 'field', 'foreign_key'], 'idx_obts_issue_resolution_trans');
    }

    /**
     * Create IssueType table
     *
     * @param Schema $schema
     */
    private function createIssueTypeTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueTypeTableName)) {
            $schema->dropTable($this->issueTypeTableName);
        }

        $table = $schema->createTable($this->issueTypeTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);

        $table->setPrimaryKey(['id']);

        $table->addUniqueIndex(['label'], 'UNIQ_59BA557AEA750E8');
    }

    /**
     * Create IssueTypeTranslation table
     *
     * @param Schema $schema
     */
    private function createIssueTypeTranslationTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueTypeTranslationTableName)) {
            $schema->dropTable($this->issueTypeTranslationTableName);
        }

        $table = $schema->createTable($this->issueTypeTranslationTableName);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 16]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['locale', 'object_class', 'field', 'foreign_key'], 'idx_obts_issue_type_trans');
    }

    /**
     * @param Schema $schema
     */
    private function addIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable($this->issueTableName);
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issuePriorityTableName),
            ['issue_priority_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issueResolutionTableName),
            ['issue_resolution_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issueTypeTableName),
            ['issue_type_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->userTableName),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->userTableName),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issueTableName),
            ['parent_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->organizationTableName),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->workflowItemTableName),
            ['workflow_item_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->workflowStepTableName),
            ['workflow_step_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
    }

    /**
     * Create IssueCollaborators table
     *
     * @param Schema $schema
     */
    private function createIssueCollaboratorsTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueCollaboratorsTableName)) {
            $schema->dropTable($this->issueCollaboratorsTableName);
        }

        $table = $schema->createTable($this->issueCollaboratorsTableName);

        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('user_id', 'integer', []);

        $table->setPrimaryKey(['issue_id', 'user_id']);
    }

    /**
     * Create IssueRelations table
     *
     * @param Schema $schema
     */
    private function createIssueRelationsTable(Schema $schema)
    {
        if ($schema->hasTable($this->issueRelationsTableName)) {
            $schema->dropTable($this->issueRelationsTableName);
        }

        $table = $schema->createTable($this->issueRelationsTableName);

        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('linked_issue_id', 'integer', []);

        $table->setPrimaryKey(['issue_id', 'linked_issue_id']);
    }

    /**
     * Add IssueCollaborators foreign keys.
     *
     * @param Schema $schema
     */
    private function addIssueCollaboratorsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable($this->issueCollaboratorsTableName);
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issueTableName),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->userTableName),
            ['user_id'],
            ['id'],
            ['onDelete' => null]
        );
    }

    /**
     * Add IssueRelations foreign keys.
     *
     * @param Schema $schema
     */
    private function addIssueRelationsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable($this->issueRelationsTableName);
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issueTableName),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable($this->issueTableName),
            ['linked_issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }
}
