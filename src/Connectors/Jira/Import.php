<?php

namespace Atlassian\Connectors\Jira;

use App\Models\User;
use Casa\Models\Calendar\Estimate;

use Casa\Models\Calendar\Event;
use Casa\Models\Registers\Spent;
use Fabrica\Models\Code\CodeIssueLink;
use Fabrica\Models\Code\Field as FieldModel;

use Fabrica\Models\Code\Issue;

use Fabrica\Models\Code\Project as ProjectModel;
use Fabrica\Models\Code\Release;
use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Version;
use JiraRestApi\JiraException;
use JiraRestApi\Project\ProjectService;
use Log;
use Transmissor\Models\Comment;

class Import extends Jira
{
    public function handle()
    {
        $this->info('Importando Jira...');
        $this->getFields();
        $this->getProjects();
        $this->getInfoFromIssues();
    }

    public function getFields()
    {
        try {
            $fieldService = new FieldService($this->getConfig($this->_token));
        
            // $fieldService->getAllFields(Field::CUSTOM),
            $fields = $fieldService->getAllFields();
            foreach ($fields as $field) {
                $this->info('Registrando FieldModel ...');
                FieldModel::registerFieldForProject($field, $this->_token->account->customize_url);
            }
        } catch (JiraException $e) {
            $this->setError('testSearch Failed : '.$e->getMessage());
        }
    }

    public function getProjects()
    {
        $this->info('Importando Projetos do Jira...');
        try {
            $proj = new ProjectService($this->getConfig($this->_token));
        
            $prjs = $proj->getAllProjects();
            // $this->info(print_r($prjs, true));
        
            foreach ($prjs as $p) {
                // $this->info(print_r($p, true));
                // dd(
                //     $p
                // );
                // Project Key:USS, Id:10021, Name:User Shipping Service, projectCategory: Desenvolvimento
                if (!$projModel = ProjectModel::where('projectPathKey', $p->key)->first()) {
                    if (!$projModel && !$projModel = ProjectModel::where('projectPath', $p->name)->first()) {
                        $this->info('Registrando Projeto: '.$p->key);
                        $projModel = ProjectModel::create(
                            [
                            'name' => $p->name,
                            'projectPathKey' => $p->key,
                            // 'created_at' => $p->created,
                            // 'updated_at' => $p->updated
                            ]
                        );
                    } else {
                        $projModel->projectPathKey = $p->key;
                        $projModel->save();
                    }
                }
                $this->projectVersions($projModel);

                $this->getIssuesFromProject($projModel);
                // echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
                //     $p->key, $p->id, $p->name, $p->projectCategory['name']
                // );
            }
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function getInfoFromIssues()
    {
        $chunkNumber = 10;
        $object = $this;
        // Trata os Outros Dados dos Usuários
        Issue::chunk(
            $chunkNumber, function ($issues) use ($object, $chunkNumber) {
                foreach ($issues as $issue) {
                    // @todo
                    // if ($this->output && isset($this->output->returnOutput())) {
                    //     $this->output->returnOutput()->progressAdvance($chunkNumber);
                    // }
                    // $object->issueTimeTracking($issue->key_name); // @todo Retirar Depois
                    // $object->issueWorklog($issue->key_name);
                    $object->comment($issue->key_name);
                    $object->getIssueRemoteLink($issue->key_name);
                }
            }
        );
    }

    /**
     * JiraRestApi\Issue\Issue^ {#10833  +expand: "operations,versionedRepresentations,editmeta,changelog,renderedFields"                                                                                                                                                            
     *       +self: "https://sitec.atlassian.net/rest/api/2/issue/11646"                                                                                                                                                                                 
     *       +id: "11646"                                                                                                                                                                                                                                
     *       +key: "CLP-37"                                                                                                                                                                                                                              
     *       +fields: JiraRestApi\Issue\IssueField^ {#10841                                                                                                                                                                                              
     *            +summary: "Formulario Representante"                                                                                                                                                                                                      
     *            +progress: array:2 [                                                                                                                                                                                                                      
     *              "progress" => 0                                                                                                                                                                                                                         
     *              "total" => 0                                                                                                                                                                                                                            
     *            ]                                                                                                                                                                                                                                         
     *            +timeTracking: null                                                                                                                                                                                                                       
     *            +issuetype: JiraRestApi\Issue\IssueType^ {#10909                                                                                                                                                                                          
     *              +self: "https://sitec.atlassian.net/rest/api/2/issuetype/10039"                                                                                                                                                                         
     *              +id: "10039"                                                                                                                                                                                                                            
     *              +description: "Uma parte pequena e distinta do trabalho."                                                                                                                                                                               
     *              +iconUrl: "https://sitec.atlassian.net/secure/viewavatar?size=medium&avatarId=10318&avatarType=issuetype"                                                                                                                               
     *              +name: "Tarefa"                                                                                                                                                                                                                         
     *              +subtask: false                                                                                                                                                                                                                         
     *              +statuses: null                                                                                                                                                                                                                         
     *              +avatarId: 10318                                                                                                                                                                                                                        
     *            }                                                                                                                                                                                                                                         
     *            +reporter: JiraRestApi\Issue\Reporter^ {#10914         
     *              +self: "https://sitec.atlassian.net/rest/api/2/user?accountId=5de8d6473384720d187a0e83"                          
     *              +name: null                                          
     *              +emailAddress: "sitec@sierratecnologia.com.br"       
     *              +avatarUrls: array:4 [                               
     *                  "48x48" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"
     *                  "24x24" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"
     *                  "16x16" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"
     *                  "32x32" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"
     *              ]                                                    
     *              +displayName: "Ricardo Sierra"                       
     *              +active: "1"                                         
     *              -wantUnassigned: false                               
     *              +accountId: "5de8d6473384720d187a0e83"               
     *              +"timeZone": "America/Sao_Paulo"                     
     *              +"accountType": "atlassian"                          
     *            }                                                      
     *            +created: DateTime @1606796447 {#10859                 
     *               date: 2020-12-01 01:20:47.840 -03:00                 
     *            }                                                      
     *            +updated: DateTime @1606796447 {#10942                 
     *               date: 2020-12-01 01:20:47.840 -03:00                 
     *            }                                                      
     *            +description: null                                     
     *            +priority: JiraRestApi\Issue\Priority^ {#10879         
     *              +self: "https://sitec.atlassian.net/rest/api/2/priority/3"                                                       
     *              +iconUrl: "https://sitec.atlassian.net/images/icons/priorities/medium.svg"                                       
     *              +name: "Fazer assim que der "                        
     *              +id: "3"                                             
     *              +statusColor: null                                   
     *              +description: null                                   
     *            }                                                      
     *            +status: JiraRestApi\Issue\IssueStatus^ {#10865        
     *              +self: "https://sitec.atlassian.net/rest/api/2/status/10054"                                                     
     *              +id: "10054"
     *                                                                                                                                                                                                                
     *      +description: "Tarefas abertas para fazer"                                                                                                                                                                                              
     *      +iconUrl: "https://sitec.atlassian.net/images/icons/statuses/generic.png"                                                                                                                                                               
     *      +name: "A Fazer"                                                                                                                                                                                                                        
     *      +statuscategory: {#6231                                                                                                                                                                                                                 
       *      +"self": "https://sitec.atlassian.net/rest/api/2/statuscategory/2"                                                                                                                                                                    
       *      +"id": 2                                                                                                                                                                                                                              
       *      +"key": "new"                                                                                                                                                                                                                         
       *      +"colorName": "blue-gray"                                                                                                                                                                                                             
       *      +"name": "Itens Pendentes"                                                                                                                                                                                                            
      }                                                                                                                                                                                                                                       
    }                                                                                                                                                                                                                                         
    +labels: []                                                                                                                                                                                                                               
    +project: JiraRestApi\Project\Project^ {#10901                                                                                                                                                                                            
     *      +expand: null                                                                                                                                                                                                                           
     *      +self: "https://sitec.atlassian.net/rest/api/2/project/10233"                                                                                                                                                                           
     *      +id: "10233"                                                                                                                                                                                                                            
     *      +key: "CLP"                                                                                                                                                                                                                             
     *      +name: "Cliente  Local Power"                                                                                                                                                                                                           
     *      +avatarUrls: array:4 [                                                                                                                                                                                                                  
        "48x48" => "https://sitec.atlassian.net/secure/projectavatar?pid=10233&avatarId=10407"                                                                                                                                                
        "24x24" => "https://sitec.atlassian.net/secure/projectavatar?size=small&s=small&pid=10233&avatarId=10407"                                                                                                                             
        "16x16" => "https://sitec.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10233&avatarId=10407"                                                                                                                           
        "32x32" => "https://sitec.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10233&avatarId=10407"                                                                                                                           
      ]                                                                                                                                                                                                                                       
     *      +projectCategory: array:4 [                                                                                                                                                                                                             
        "self" => "https://sitec.atlassian.net/rest/api/2/projectCategory/10017"                                                                                                                                                              
        "id" => "10017"                                                                                                                                                                                                                       
        "description" => "Clientes ativos, com desenvolvimento em andamento"                                                                                                                                                                  
        "name" => "Clientes"                                                                                                                                                                                                                  
      ]                                                                                                                                                                                                                                       
     *      +description: null                                                                                                                                                                                                                      
     *      +lead: null                                                                                                                                                                                                                             
     *      +leadAccountId: null                                                                                                                                                                                                                    
     *      +components: null                                                                                                                                                                                                                       
     *      +issueTypes: null                                                                                                                                                                                                                       
     *      +assigneeType: null                                                                                                                                                                                                                     
     *      +versions: null                                                                                                                                                                                                                         
     *      +roles: null                                                                                                                                                                                                                            
     *      +url: null                                                                                                                                                                                                                              
     *      +projectTypeKey: "software"                                                                                                                                                                                                             
     *      +projectTemplateKey: null                                                                                                                                                                                                               
     *      +avatarId: null                                                                                                                                                                                                                         
     *      +issueSecurityScheme: null                                                                                                                                                                                                              
     *      +permissionScheme: null                                                                                                                                                                                                                 
     *      +notificationScheme: null                                                                                                                                                                                                               
     *      +categoryId: null                                                                                                                                                                                                                       
     *      +"simplified": false                                                                                                                                                                                                                    
    }     
    +environment: null                                                                                                                                                                                                                        
    +components: []                                                                                                                                                                                                                           
    +comment: null                                                                                                                                                                                                                            
    +votes: {#6203                                                                                                                                                                                                                            
     *      +"self": "https://sitec.atlassian.net/rest/api/2/issue/CLP-37/votes"                                                                                                                                                                    
     *      +"votes": 0                                                                                                                                                                                                                             
     *      +"hasVoted": false                                                                                                                                                                                                                      
    }                                                                                                                                                                                                                                         
    +resolution: null                                                                                                                                                                                                                         
    +fixVersions: []                                                                                                                                                                                                                          
    +creator: JiraRestApi\Issue\Reporter^ {#10911                                                                                                                                                                                             
     *      +self: "https://sitec.atlassian.net/rest/api/2/user?accountId=5de8d6473384720d187a0e83"                                                                                                                                                 
     *      +name: null                                                                                                                                                                                                                             
     *      +emailAddress: "sitec@sierratecnologia.com.br"                                                                                                                                                                                          
     *      +avatarUrls: array:4 [                                                                                                                                                                                                                  
        "48x48" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
        "24x24" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
        "16x16" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
        "32x32" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
      ]                                                                                                                                                                                                                                       
     *      +displayName: "Ricardo Sierra"                                                                                                                                                                                                          
     *      +active: "1"                                                                                                                                                                                                                            
      -wantUnassigned: false                                                                                                                                                                                                                  
     *      +accountId: "5de8d6473384720d187a0e83"                                                                                                                                                                                                  
     *      +"timeZone": "America/Sao_Paulo"                                                                                                                                                                                                        
     *      +"accountType": "atlassian"                                                                                                                                                                                                             
    }                                                                                                                                                                                                                                         
    +watches: {#6220                                                                                                                                                                                                                          
     *      +"self": "https://sitec.atlassian.net/rest/api/2/issue/CLP-37/watchers"                                                                                                                                                                 
     *      +"watchCount": 1                                                                                                                                                                                                                        
     *      +"isWatching": true                                                                                                                                                                                                                     
    }                                                                                                                                                                                                                                         
    +worklog: null                                                                                                                                                                                                                            
    +assignee: JiraRestApi\Issue\Reporter^ {#10872                                                                                                                                                                                            
     *      +self: "https://sitec.atlassian.net/rest/api/2/user?accountId=5de8d6473384720d187a0e83"                                                                                                                                                 
     *      +name: null                                                                                                                                                                                                                             
     *      +emailAddress: "sitec@sierratecnologia.com.br"                                                                                                                                                                                          
     *      +avatarUrls: array:4 [                                                                                                                                                                                                                  
        "48x48" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
        "24x24" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
        "16x16" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
        "32x32" => "https://secure.gravatar.com/avatar/3764b8198eee20a62c0ec828c9c38d16?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FRS-1.png"                                                  
      ]                                                                                                                                                                                                                                       
     *      +displayName: "Ricardo Sierra" 
          *      +active: "1"
      -wantUnassigned: false
     *      +accountId: "5de8d6473384720d187a0e83"
     *      +"timeZone": "America/Sao_Paulo"
     *      +"accountType": "atlassian"
    }
    +versions: []
    +attachment: null
    +aggregatetimespent: null
    +timeestimate: null
    +aggregatetimeoriginalestimate: null
    +resolutiondate: null
    +duedate: null
    +issuelinks: []
    +subtasks: []
    +workratio: -1
    +aggregatetimeestimate: null
    +aggregateprogress: {#6204
      +"progress": 0
      +"total": 0
    }
    +lastViewed: null
    +timeoriginalestimate: null
    +parent: null
    +customFields: array:6 [
      "customfield_10031" => "1|i008xb:"
      "customfield_10034" => "3110400"
      "customfield_10018" => {#6434
        +"hasEpicLinkFieldDependency": false
        +"showField": false
        +"nonEditableReason": {#6193
          +"reason": "PLUGIN_LICENSE_ERROR"
          +"message": "O link pai só está disponível para usuários do Jira Premium."
        }
      }
      "customfield_10019" => "0|i008zb:"
      "customfield_10014" => "CLP-10"
      "customfield_10000" => "{}"
    ]
    +security: null
    +"statuscategorychangedate": "2020-12-01T01:20:48.147-0300"
    +"timespent": null
    +"customfield_10031": "1|i008xb:"
    +"customfield_10034": "3110400"
    +"customfield_10018": {#6434}
    +"customfield_10019": "0|i008zb:"
    +"customfield_10014": "CLP-10"
    +"customfield_10000": "{}"
  }
  +renderedFields: null
  +names: null
  +schema: null
  +transitions: null
  +operations: null
  +editmeta: null
  +changelog: null
}

     */
    public function getIssuesFromProject($project)
    {
        // $jql = 'status = Documentação ORDER BY created DESC';
        // $jql = 'project IN ('.$project->getSlug().')';
        $jql = 'project='.$project->getSlug();
        $paginate = $this->getPaginate(1);
        $result = $this->searchIssue($jql, $paginate);
        if (!empty($result->issues)) {
            foreach ($result->issues as $issue) {
                dd($issue);
                if (!$issueInstance = Issue::where(['key_name' => $issue->key])->first()) {
                    $this->info('Registrando Issue: '.$issue->key);
                    $issueInstance = Issue::create(
                        [
                        'key_name' => $issue->key,
                        'url' => $issue->self,
                        // 'id' => $issue->id,
                        'expand' => $issue->expand,
                        // 'created_at' => $issue->created,
                        // 'updated_at' => $issue->updated
                        // 'sumary' => '', @todo fazer aqui
                        ]
                    );
                }
                if (!empty($issue->fields)) {
                    $issueInstance->setField($issue->fields, $issue->key);
                }
            }
        }
    }

    public function searchIssue($jql = false, $paginate = false)
    {
        if (!$jql) {
            $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        }

        // $jql = str_replace(" ", "%20", $jql);
        $jql = str_replace(" ", "+", $jql);
        $jql = str_replace(" ", "\\u002f", $jql);

        try {
            return (new IssueService($this->getConfig($this->_token)))->search($jql); //, $paginate);
            // return (new IssueService($this->getConfig($this->_token)))->search($jql);
        } catch (JiraException $e) {
            $this->setError('testSearch Failed : '.$e->getMessage());
        }
    }

    /**     
     * @todo
     * @param  [type] $project
     * @return void
     */
    public function project($project)
    {
        $this->info('Importando Projeto do Jira...');
        try {
            $proj = new ProjectService($this->getConfig($this->_token));
        
            $p = $proj->get($project->getSlug());
            
            var_dump($p);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function issueTimeTracking($issueKey = 'TEST-961')
    {
        try {
            $issueService = new IssueService($this->getConfig($this->_token));
            
            // get issue's time tracking info
            $rets = $issueService->getTimeTracking($issueKey);
            $this->info('Registrando timetracking');
            Spent::registerSpentForIssue($rets, $this->_token->account->customize_url);

            
            // $timeTracking = new TimeTracking;

            // $timeTracking->setOriginalEstimate('3w 4d 6h');
            // $timeTracking->setRemainingEstimate('1w 2d 3h');
            
            // // add time tracking
            // $ret = $issueService->timeTracking($issueKey, $timeTracking);
            // var_dump($ret);
        } catch (JiraException $e) {
            $this->setError('testSearch Failed : '.$e->getMessage());
        }
    }

    public function issueWorklog($issueKey = 'TEST-961')
    {
        try {
            $issueService = new IssueService($this->getConfig($this->_token));
            
            // get issue's all worklog
            $worklogs = $issueService->getWorklog($issueKey)->getWorklogs();
            $this->info('Registrando worklogs');
            Spent::registerSpentForIssue($worklogs, $this->_token->account->customize_url);
            
            // // get worklog by id
            // $wlId = 12345;
            // $wl = $issueService->getWorklogById($issueKey, $wlId);
            // var_dump($wl);
        } catch (JiraException $e) {
            $this->setError('testSearch Failed : '.$e->getMessage());
        }
    }

    public function issueLinkType()
    {
        try {
            $ils = new IssueLinkService($this->getConfig($this->_token));
        
            $rets = $ils->getIssueLinkTypes();
            foreach ($rets as $ret) {
                $this->info('Criando CodeIssueLinkType: '.$ret->name); // @todo
                var_dump($ret);
                CodeIssueLink::firstOrCreate(
                    [
                    'name' => $ret->name
                    ]
                );
            }
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function getIssueRemoteLink($issueKey = 'TEST-316')
    {
        try {
            $issueService = new IssueService($this->getConfig($this->_token));

            $rils = $issueService->getRemoteIssueLink($issueKey);
            foreach ($rils as $ril) {
                $this->info('Criando CodeIssueLink: '.$ril->name); // @todo
                var_dump($ril);
                CodeIssueLink::firstOrCreate(
                    [
                    'name' => $ril->name
                    ]
                );
            }
        } catch (JiraException $e) {
            $this->setError($e->getMessage());
        }
    }

    public function comment($issueKey = "TEST-879")
    {
        try {
            $issueService = new IssueService($this->getConfig($this->_token));
        
            $comments = $issueService->getComments($issueKey);
            $this->info('Criando comentários p/ Issue: '.$issueKey);
            Comment::registerComents(
                $comments,
                $issueKey,
                Issue::class,
                $this->_token->account->customize_url
            );
        } catch (JiraException $e) {
            $this->setError('get Comment Failed : '.$e->getMessage());
        }
    }

    public function getFieldInfo($issueKey = "TEST-879")
    {
        try {
            $issueService = new IssueService($this->getConfig($this->_token));
            
            $queryParam = [
                'fields' => [  // default: '*all'
                    'summary',
                    'comment',
                ],
                'expand' => [
                    'renderedFields',
                    'names',
                    'schema',
                    'transitions',
                    'operations',
                    'editmeta',
                    'changelog',
                ]
            ];
                    
            $issue = $issueService->get($issueKey, $queryParam);
            
            var_dump($issue->fields);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function projectVersions($projInstance)
    {
        try {
            $proj = new ProjectService($this->getConfig($this->_token));
        
            $vers = $proj->getVersions($projInstance->getSlug());
        
            foreach ($vers as $v) {
                /**
 * @todo Usar id e url
                 * JiraRestApi\Issue\Version^ {#5807
                 * +self: "https://sitec.atlassian.net/rest/api/2/version/10207"
                 * +id: "10207"
                 * +name: "v0.1.0"
                 * +description: null
                 * +archived: false
                 * +released: false
                 * +releaseDate: null
                 * +overdue: null
                 * +userReleaseDate: null
                 * +projectId: 10215
                 * }
                  */
                // $v is  JiraRestApi\Issue\Version
                if (!Release::where(
                    [
                        'name' => $v->name,
                        'code_project_id' => $projInstance->id
                    ]
                )->first()
                ) {
                    $this->info('Criando Versão (Proj '.$projInstance->id.'): '.$v->name);
                    Release::create(
                        [
                        'name' => $v->name,
                        // 'start' => $v->startDate,
                        'release' => $v->releaseDate,
                        'code_project_id' => $projInstance->id,
                        // 'created_at' => $v->created,
                        // 'updated_at' => $v->updated
                        ]
                    );
                }
            }
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }

    public function projectType()
    {
        try {
            $proj = new ProjectService($this->getConfig($this->_token));
        
            // get all project type
            $prjtyps = $proj->getProjectTypes();
        
            foreach ($prjtyps as $pt) {
                var_dump($pt);
            }
        
            // get specific project type.
            $pt = $proj->getProjectType('software');
            var_dump($pt);
        } catch (JiraException $e) {
            $this->setError("Error Occured! " . $e->getMessage());
        }
    }
}
