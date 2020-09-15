<?php

namespace Atlassian\Connectors\Jira;

use Log;
use App\Models\Project as ProjectModel;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Project;
use JiraRestApi\JiraException;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;

class Update extends Jira
{
    public function issueWorklog()
    {
        $issueKey = 'TEST-961';
        $workLogid = '12345';

        try {
            $workLog = new Worklog();

            $workLog->setComment('I did edit previous worklog here.')
                ->setStarted("2016-05-29 13:15:34")
                ->setTimeSpent('3d 4h 5m');

            $issueService = new IssueService();

            $ret = $issueService->editWorklog($issueKey, $workLog, $workLogid);

            var_dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Edit worklog Failed : '.$e->getMessage());
        }
    }

    /**
     * Perform a transition on an issue
     * Note: this library uses goal status names instead of transition names. So, if you want to change issue status to 'Some Status', you should pass that status name to setTransitionName
     * i.e. $transition->setTransitionName('Some Status')
     * See Jira API reference
     */
    public function issueStatus()
    {
        $issueKey = "TEST-879";

        try {            
            $transition = new Transition();
            $transition->setTransitionName('Resolved');
            $transition->setCommentBody('performing the transition via REST API.');

            $issueService = new IssueService();

            $issueService->transition($issueKey, $transition);
        } catch (JiraException $e) {
            $this->assertTrue(false, "add Comment Failed : " . $e->getMessage());
        }
    }

    public function comment()
    {
        $issueKey = "TEST-879";

        try {
            $commentId = 12345;

            $issueService = new IssueService();
                
            $comment = new Comment();
            $comment->setBody('Updated comments');
            
            $issueService->updateComment($issueKey, $commentId, $comment);

        } catch (JiraException $e) {
            $this->assertTrue(false, 'Delete comment Failed : '.$e->getMessage());
        }
    }

    public function changeAssignee()
    {

        $issueKey = "TEST-879";

        try {
            $issueService = new IssueService();

            // if assignee is -1, automatic assignee used.
            // A null assignee will remove the assignee.
            $assignee = 'newAssigneeName';

            $ret = $issueService->changeAssignee($issueKey, $assignee);

            var_dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, "Change Assignee Failed : " . $e->getMessage());
        }
    }

    public function issue()
    {
        $issueKey = "TEST-879";

        try {            
            $issueField = new IssueField(true);
        
            $issueField->setAssigneeName("admin")
                ->setPriorityName("Blocker")
                ->setIssueType("Task")
                ->addLabel("test-label-first")
                ->addLabel("test-label-second")
                ->addVersion("1.0.1")
                ->addVersion("1.0.2")
                ->setDescription("This is a shorthand for a set operation on the summary field");
        
            // optionally set some query params
            $editParams = [
                'notifyUsers' => false,
            ];
        
            $issueService = new IssueService();
        
            // You can set the $paramArray param to disable notifications in example
            $ret = $issueService->update($issueKey, $issueField, $editParams);
        
            var_dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, "update Failed : " . $e->getMessage());
        }
    }

    public function project(ProjectModel $project)
    {
        try {
            $p = new Project();

            $p->setName('Updated Example')
                ->setProjectTypeKey('software')
                ->setProjectTemplateKey('com.atlassian.jira-software-project-templates:jira-software-project-management')
                ->setDescription('Updated Example Project description')
                ->setLead('new-leader')
                ->setUrl('http://new.example.com')
                ->setAssigneeType('UNASSIGNED');

            $proj = new ProjectService();

            $pj = $proj->updateProject($p, 'EX');
        
            var_dump($pj);
        } catch (JiraException $e) {
            print("Error Occured! " . $e->getMessage());
        }
    }
}
