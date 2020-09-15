<?php

namespace Atlassian\Connectors\Jira;

use Log;
use App\Models\Project as ProjectModel;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Project;
use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Comment;
use JiraRestApi\JiraException;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;

class Create extends Jira
{
    public function issueWatcher($issueKey = 'TEST-961')
    {
        try {
            $issueService = new IssueService();
            
            // watcher's id
            $watcher = 'lesstif';
            
            $issueService->addWatcher($issueKey, $watcher);
            
        } catch (JiraException $e) {
            $this->setError('add watcher Failed : '.$e->getMessage());
        }
    }

    public function issueWorklog($issueKey = 'TEST-961')
    {

        try {
            $workLog = new Worklog();
        
            $workLog->setComment('I did some work here.')
                ->setStarted("2016-05-28 12:35:54")
                ->setTimeSpent('1d 2h 3m');
        
            $issueService = new IssueService();
        
            $ret = $issueService->addWorklog($issueKey, $workLog);
        
            $workLogid = $ret->{'id'};
        
            var_dump($ret);
        } catch (JiraException $e) {
            $this->setError('Create Failed : '.$e->getMessage());
        }
        
    }


    public function issueLink()
    {
        try {
            $il = new IssueLink();

            $il->setInwardIssue('TEST-258')
                ->setOutwardIssue('TEST-249')
                ->setLinkTypeName('Relates')
                ->setComment('Linked related issue via REST API.');
                    
            $ils = new IssueLinkService();

            $ret = $ils->addIssueLink($il);

        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }


    public function issueRemoteLink()
    {
        $issueKey = 'TEST-316';

        try {
            $issueService = new IssueService();

            $ril = new RemoteIssueLink();

            $ril->setUrl('http://www.mycompany.com/support?id=1')
                ->setTitle('Remote Link Title')
                ->setRelationship('causes')
                ->setSummary('Crazy customer support issue');

            $rils = $issueService->createOrUpdateRemoteIssueLink($issueKey, $ril);

            // rils is array of RemoteIssueLink classes
            var_dump($rils);
        } catch (JiraException $e) {
            $this->setError('Create Failed : '.$e->getMessage());
        }
    }

    public function comment()
    {
        $issueKey = "TEST-879";

        try {            
            $comment = new Comment();

            $body = "<<<COMMENT
            Adds a new comment to an issue.
            * Bullet 1
            * Bullet 2
            ** sub Bullet 1
            ** sub Bullet 2
            * Bullet 3
            COMMENT;";

            $comment->setBody($body)
                ->setVisibility('role', 'Users');
            ;

            $issueService = new IssueService();
            $ret = $issueService->addComment($issueKey, $comment);
            print_r($ret);
        } catch (JiraException $e) {
            $this->setError("add Comment Failed : " . $e->getMessage());
        }
    }

    public function addAttachment()
    {
        $issueKey = "TEST-879";

        try {
            $issueService = new IssueService();
        
            // multiple file upload support.
            $ret = $issueService->addAttachments(
                $issueKey, 
                ['screen_capture.png', 'bug-description.pdf', 'README.md']
            );
        
            print_r($ret);
        } catch (JiraException $e) {
            $this->setError("Attach Failed : " . $e->getMessage());
        }
        
    }

    public function multipleIssue()
    {

        try {
            $issueFieldOne = new IssueField();

            $issueFieldOne->setProjectKey("TEST")
                ->setSummary("something's wrong")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->setDescription("Full description for issue");

            $issueFieldTwo = new IssueField();

            $issueFieldTwo->setProjectKey("TEST")
                ->setSummary("something else is wrong")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->setDescription("Full description for second issue");
            
            $issueService = new IssueService();

            $ret = $issueService->createMultiple([$issueFieldOne, $issueFieldTwo]);
            
            //If success, returns an array of the created issues
            var_dump($ret);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function issue()
    {
        try {
            $issueField = new IssueField();
        
            $issueField->setProjectKey("TEST")
                ->setSummary("something's wrong")
                ->setAssigneeName("lesstif")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->setDescription("Full description for issue")
                ->addVersion(["1.0.1", "1.0.3"])
                ->addComponents(['Component-1', 'Component-2'])
                        // set issue security if you need.
                ->setSecurityId(10001 /* security scheme id */)
                ->setDueDate('2019-06-19');
            
            $issueService = new IssueService();
        
            $ret = $issueService->create($issueField);
            
            //If success, Returns a link to the created issue.
            var_dump($ret);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function field()
    {

        try {
            $field = new Field();
            
            $field->setName("New custom field")
                ->setDescription("Custom field for picking groups")
                ->setType("com.atlassian.jira.plugin.system.customfieldtypes:grouppicker")
                ->setSearcherKey("com.atlassian.jira.plugin.system.customfieldtypes:grouppickersearcher");

            $fieldService = new FieldService();

            $ret = $fieldService->create($field);
            
            var_dump($ret);
        } catch (JiraException $e) {
            $this->setError('Field Create Failed : '.$e->getMessage());
        }
    }

    public function project(ProjectModel $project)
    {
        
        try {
            $p = new Project();
        
            $p->setKey('EX')
                ->setName('Example')
                ->setProjectTypeKey('business')
                ->setProjectTemplateKey('com.atlassian.jira-core-project-templates:jira-core-project-management')
                ->setDescription('Example Project description')
                ->setLead('lesstif')
                ->setUrl('http://example.com')
                ->setAssigneeType('PROJECT_LEAD')
                ->setAvatarId(10130)
                ->setIssueSecurityScheme(10000)
                ->setPermissionScheme(10100)
                ->setNotificationScheme(10100)
                ->setCategoryId(10100);
        
            $proj = new ProjectService();
        
            $pj = $proj->createProject($p);
           
            // "http://example.com/rest/api/2/project/10042"
            var_dump($pj->self);
            // 10042 
            var_dump($pj->id);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function user()
    {
        try {
            $us = new UserService();
        
            // create new user
            $user = $us->create(
                [
                    'name'=>'charlie',
                    'password' => 'abracadabra',
                    'emailAddress' => 'charlie@atlassian.com',
                    'displayName' => 'Charlie of Atlassian',
                ]
            );
        
            var_dump($user);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }
}
