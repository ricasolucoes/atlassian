<?php

namespace Atlassian\Tests;

use Atlassian\AtlassianProvider;
use Atlassian\Models\Token;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Telefonica\TelefonicaProvider;

class TokenModelTest extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            AtlassianProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('sitec.core.models.user', \Illuminate\Foundation\Auth\User::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function test_token_creation_and_attributes()
    {
        $token = Token::create([
            'account_id' => 1,
            'token' => 'atlassian_pat_sample_token_xyz',
            'obs' => 'Personal access token for Jira integration',
            'scopes' => ['read:jira-work', 'write:jira-work'],
            'status' => 1,
        ]);

        $this->assertNotNull($token->id);
        $this->assertEquals('atlassian_pat_sample_token_xyz', $token->token);
        $this->assertEquals(1, $token->account_id);
        $this->assertEquals(1, $token->status);
        $this->assertIsArray($token->scopes);
        $this->assertContains('read:jira-work', $token->scopes);
        $this->assertContains('write:jira-work', $token->scopes);

        $found = Token::where('token', 'atlassian_pat_sample_token_xyz')->first();
        $this->assertNotNull($found);
        $this->assertEquals('Personal access token for Jira integration', $found->obs);
    }
}
