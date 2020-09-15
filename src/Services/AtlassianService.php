<?php

namespace Atlassian\Services;

/**
 *
 */
class AtlassianService
{
    protected $config;

    public function __construct($config = false)
    {
        if (!$this->config = $config) {
            $this->config = \Illuminate\Support\Facades\Config::get('atlassian');
        }
    }
}
