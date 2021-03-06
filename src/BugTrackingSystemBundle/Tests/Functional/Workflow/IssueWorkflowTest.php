<?php

namespace Oro\Bundle\BugTrackingSystemBundle\Tests\Functional\Workflow;

use Oro\Bundle\BugTrackingSystemBundle\Tests\Functional\Controller\IssueControllerTest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 * @dbReindex
 */
class IssueWorkflowTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testWorkflow()
    {
        $issueController = new IssueControllerTest();
        $issueController->setUp();
        $issueController->testCreate();

        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $workflowManager = static::$kernel->getContainer()->get('oro_workflow.manager');

        $response = $this->client->requestGrid(
            'issues-grid',
            ['issues-grid[_filter][summary][value]' => 'New issue']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = array_shift($result['data']);

        $issue = $em->getRepository('OroBugTrackingSystemBundle:Issue')->find($result['id']);

        /*start workflow test*/
        $workflowItem = $workflowManager->getFirstWorkflowItemByEntity($issue);
        $this->assertEquals('open', $workflowItem->getCurrentStep()->getName());
        $this->assertCount(3, $workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowManager->transit($workflowItem, 'start_progress');
        $this->assertEquals('in_progress', $workflowItem->getCurrentStep()->getName());
        $this->assertCount(3, $workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowManager->transit($workflowItem, 'resolve');
        $this->assertEquals('resolved', $workflowItem->getCurrentStep()->getName());
        $this->assertCount(2, $workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowManager->transit($workflowItem, 'close');
        $this->assertEquals('closed', $workflowItem->getCurrentStep()->getName());
        $this->assertCount(1, $workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowManager->transit($workflowItem, 'reopen');
        $this->assertEquals('open', $workflowItem->getCurrentStep()->getName());
        $this->assertCount(3, $workflowManager->getTransitionsByWorkflowItem($workflowItem));
    }
}
