<?php

namespace Atlassian\Connectors\Jira;

use App\Models\Project as ProjectModel;
use JiraRestApi\JiraException;
use JiraRestApi\Project\ProjectService;
use Log;

class Delete extends Jira
{
    public function watchFromIssue()
    {
        $issueKey = 'TEST-961';

        try {
            $issueService = new IssueService();
            
            // watcher's id
            $watcher = 'atlassian';
            
            $issueService->removeWatcher($issueKey, $watcher);
        } catch (JiraException $e) {
            $this->setError('add watcher Failed : '.$e->getMessage());
        }
    }

    public function comment($issueKey = "TEST-879")
    {
        try {
            $commentId = 12345;

            $issueService = new IssueService();

            $ret = $issueService->deleteComment($issueKey, $commentId);
        } catch (JiraException $e) {
            $this->setError('Delete comment Failed : '.$e->getMessage());
        }
    }

    public function issue($issueKey = "TEST-879")
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->deleteIssue($issueKey);
            // if you want to delete issues with sub-tasks
            //$ret = $issueService->deleteIssue($issueKey, array('deleteSubtasks' => 'true'));

            var_dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, "Change Assignee Failed : " . $e->getMessage());
        }
    }

    public function project(ProjectModel $project)
    {
        try {
            $proj = new ProjectService();
        
            $pj = $proj->deleteProject('EX');
           
            var_dump($pj);
        } catch (JiraException $e) {
            print("Error Occured! " . $e->getMessage());
        }
    }
}
