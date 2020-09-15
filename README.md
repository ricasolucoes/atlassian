# PHP ATLASSIAN INTEGRATION - JIRA and Confluence Rest Client

Atlassian's Jira, Confluence & Confluence Question REST API Client for PHP Users.

[![Latest Stable Version](https://poser.pugx.org/ricasolucoes/atlassian/v/stable)](https://packagist.org/packages/ricasolucoes/atlassian)
[![Latest Unstable Version](https://poser.pugx.org/ricasolucoes/atlassian/v/unstable)](https://packagist.org/packages/ricasolucoes/atlassian)
[![Build Status](https://travis-ci.org/ricasolucoes/atlassian.svg?branch=master)](https://travis-ci.org/ricasolucoes/atlassian)
[![StyleCI](https://styleci.io/repos/30015369/shield?branch=master&style=flat)](https://styleci.io/repos/30015369)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/ricasolucoes/atlassian/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ricasolucoes/atlassian/)
[![Coverage Status](https://coveralls.io/repos/github/ricasolucoes/atlassian/badge.svg?branch=master)](https://coveralls.io/github/ricasolucoes/atlassian?branch=master)
[![License](https://poser.pugx.org/ricasolucoes/atlassian/license)](https://packagist.org/packages/ricasolucoes/atlassian)
[![Total Downloads](https://poser.pugx.org/ricasolucoes/atlassian/downloads)](https://packagist.org/packages/ricasolucoes/atlassian)
[![Monthly Downloads](https://poser.pugx.org/ricasolucoes/atlassian/d/monthly)](https://packagist.org/packages/ricasolucoes/atlassian)
[![Daily Downloads](https://poser.pugx.org/ricasolucoes/atlassian/d/daily)](https://packagist.org/packages/ricasolucoes/atlassian)

# Requirements

- PHP >= 7.1
- [php JsonMapper](https://github.com/netresearch/jsonmapper)
- [phpdotenv](https://github.com/vlucas/phpdotenv)
- [php-jira-rest-client](https://github.com/lesstif/php-jira-rest-client)

# Inspiring

- [confluence-rest-api](https://github.com/ogilviec/confluence-rest-api)
- [confluence-php-client](https://github.com/rainflute/confluence-php-client)
- [Amice/atlassian](https://github.com/Amice/atlassian

# JIRA Rest API Documents
* 6.4 - https://docs.atlassian.com/jira/REST/6.4/
* Jira Server latest - https://docs.atlassian.com/jira/REST/server/
* Jira Cloud latest - https://docs.atlassian.com/jira/REST/latest/
# Introduction

# Getting started

  * Requirements
    - composer
    - curl module for php
  * Git
    - https://github.com/ricasolucoes/atlassian.git
  * Run: composer install

# Settings

  * All settings can be found in settings.php.
    - Connections
      The program will always use the default connection.
  * Filters
    Filters for JQL queries are defined in src/Filter/Filter.php


# Installation

1. Download and Install PHP Composer.

   ``` sh
   curl -sS https://getcomposer.org/installer | php
   ```

2. Next, run the Composer command to install the latest version of php jira rest client.
   ``` sh
   php composer.phar require ricasolucoes/atlassian
   ```
    or add the following to your composer.json file.
   ```json
   {
       "require": {
           "ricasolucoes/atlassian": "^0.1.0"
       }
   }
   ```

3. Then run Composer's install or update commands to complete installation. 

   ```sh
   php composer.phar install
   ```

4. After installing, you need to require Composer's autoloader:

   ```php
   require 'vendor/autoload.php';
   ```

# Configuration

you can choose loads environment variables either 'dotenv' or 'array'.


### use dotenv

copy .env.example file to .env on your project root directory.

```
CONFLUENCE_HOST="https://your-confluence.host.com"
CONFLUENCE_USER="confluence-username"
CONFLUENCE_PASS="confluence-password"
```

### use array

create Service class with ArrayConfiguration parameter.

```php
use Atlassian\Question\QuestionService;

$qs = new QuestionService(new \Atlassian\Configuration\ArrayConfiguration(
          [
              'host' => 'https://your-confluence.host.com',
              'user' => 'confluence-username',
              'password' => 'confluence-password',
          ]
   ));
```

copy .env.example file to .env on your project root.	

```sh
JIRA_HOST="https://your-jira.host.com"
JIRA_USER="jira-username"
JIRA_PASS="jira-password-OR-api-token"
# to enable session cookie authorization
# COOKIE_AUTH_ENABLED=true
# COOKIE_FILE=storage/jira-cookie.txt
# if you are behind a proxy, add proxy settings
PROXY_SERVER="your-proxy-server"
PROXY_PORT="proxy-port"
PROXY_USER="proxy-username"
PROXY_PASSWORD="proxy-password"
JIRA_REST_API_V3=false
```


**Laravel Users:** This package working standarlone and not is needed load laravel. If you are developing with laravel framework(8.x), you must append above configuration to your application .env file. Once installed, if you are not using automatic package discovery, then you need to register the `Atlassian\AtlassianProvider` service provider in your `config/app.php`.

**Important Note:**
As of March 15, 2018, in accordance to the [Atlassian REST API Policy](https://developer.atlassian.com/platform/marketplace/atlassian-rest-api-policy/), Basic auth with password to be deprecated.
Instead of password, you should using [API token](https://confluence.atlassian.com/cloud/api-tokens-938839638.html).

**REST API V3 Note:**
In accordance to the [Atlassian's deprecation notice](https://developer.atlassian.com/cloud/jira/platform/deprecation-notice-user-privacy-api-migration-guide/), After the 29th of april 2019, REST API no longer supported username and userKey, 
and instead use the account ID.
if you are JIRA Cloud users, you need to set *JIRA_REST_API_V3=true* in the .env file.

**CAUTION**
this library not fully supported JIRA REST API V3 yet. 

## use array

create Service class with ArrayConfiguration parameter.

```php
use Atlassian\Configuration\ArrayConfiguration;
use Atlassian\Issue\IssueService;

$iss = new IssueService(new ArrayConfiguration(
          array(
               'jiraHost' => 'https://your-jira.host.com',
               // for basic authorization:
               'jiraUser' => 'jira-username',
               'jiraPassword' => 'jira-password-OR-api-token',
               // to enable session cookie authorization (with basic authorization only)
               'cookieAuthEnabled' => true,
               'cookieFile' => storage_path('jira-cookie.txt'),
               // if you are behind a proxy, add proxy settings
               "proxyServer" => 'your-proxy-server',
               "proxyPort" => 'proxy-port',
               "proxyUser" => 'proxy-username',
               "proxyPassword" => 'proxy-password',
          )
   ));
```

## Usage

```php
<?php

use Atlassian/Client;
use Atlassian/Curl;
use Atlassian/Entity/ConfluencePage;

//Create and configure a curl web client
$curl = new Curl('confluence_host_url,'username','password');

//Create the Confluence Client
$client = new Client($curl);

//Create a confluence page
$page = new ConfluencePage();

//Configure your page
$page->setSpace('testSpaceKey')->setTitle('Test')->setContent('<p>test page</p>');

//Create the page in confluence in the test space
$client->createPage($page);

//Get the page we created
echo $client->selectPageBy([
    'spaceKey' => 'testSpaceKey',
    'title' => 'Test'
]);

```

## CQL

```php
$cql = [
    'SPACE' => 'LAR',
    'type' => 'page',
    ];

try {
    $s = new CQLService();

    $ret = $s->search($cql);

    dump($ret);

} catch (\Atlassian\ConfluenceException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

## Question

### getting Question list

```php

$queryParam = [
    // the number of questions needed (10 by default)
    'limit' => 10,

    //the start index (0 by default)
    'start' => 0,

    // The optional filter string which value is one of "unanswered", "popular", "my", "recent"
    // (default value 'recent')
    'filter' => 'unanswered',
];

try {
    $qs = new QuestionService();

    $questions = $qs->getQuestion($queryParam);

    foreach($questions as $q) {
        echo sprintf("<a href=\"%s\">%s</a><p/>\n", $q->url, $q->title);
    }

} catch (\Atlassian\ConfluenceException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

### getting Question's detail info.

```php
try {
    $qs = new QuestionService();

    $q = $qs->getQuestionDetail($questionId);

    foreach($q->answers as $a)
    {
        // print accepted answer
        if ($a->accepted === true) {
            dump($a);
        }
    }

} catch (\Atlassian\ConfluenceException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

### getting accepted answer

```php
try {
    $qs = new QuestionService();

    $q = $qs->getAcceptedAnswer($questionId);
    dump($q);

} catch (\Atlassian\ConfluenceException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

## Answer

### getting user's answer list

```php
try {
    $username = 'ricasolucoes';

    $as = new AnswerService();

    $ans = $as->getAnswers($username);

    foreach($ans as $a) {
        dump($a);
    }

} catch (\Atlassian\ConfluenceException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

### getting related question.

```php
try {
    $answerId = '123456';

    $as = new AnswerService();

    $q = $as->getQuestion($answerId);

    dump($q);
} catch (\Atlassian\ConfluenceException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

# Confluence Rest API Documents
* Confluence Server REST API - https://developer.atlassian.com/confdev/confluence-server-rest-api
* latest server - https://docs.atlassian.com/atlassian-confluence/REST/latest-server/
* Confluence Question REST API - https://docs.atlassian.com/confluence-questions/rest/index.html


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](https://bit.ly/sierratecnologia-slack)
- [Help on Email](mailto:help@sierratecnologia.com.br)
- [Follow on Twitter](https://twitter.com/sierratecnologia)


## Contributing & Protocols

Thank you for considering contributing to this project! The contribution guide can be found in [CONTRIBUTING.md](CONTRIBUTING.md).

Bug reports, feature requests, and pull requests are very welcome.

- [Versioning](CONTRIBUTING.md#versioning)
- [Pull Requests](CONTRIBUTING.md#pull-requests)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Feature Requests](CONTRIBUTING.md#feature-requests)
- [Git Flow](CONTRIBUTING.md#git-flow)


## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to [help@sierratecnologia.com.br](help@sierratecnologia.com.br). All security vulnerabilities will be promptly addressed.


## About SierraTecnologia

SierraTecnologia is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Rio de Janeiro, Brazil since June 2008. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.


## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2008-2020 SierraTecnologia, This package no has rights reserved.
