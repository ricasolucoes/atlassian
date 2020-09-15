<?php

namespace Atlassian\Console\Commands;

use Atlassian\Http\Controllers\Api\Controller;
use Atlassian\Routines\Globals\ImportTokens;
use Illuminate\Console\Command;
use Population\Models\Company;
use Population\Models\MediaEmail;
use Population\Models\MediaPush;
use Population\Models\MediaSend;
use Population\Models\User;
use SendGrid;

class TokensSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atlassian:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync with yours atlassian tokens !';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // @todo
    }
}
