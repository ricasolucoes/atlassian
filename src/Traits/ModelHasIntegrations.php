<?php

namespace Atlassian\Traits;

trait ModelHasIntegrations
{
    /**
     * Join a integration
     *
     * @param  integer $integrationId
     * @param  integer $userId
     * @return void
     */
    public function joinIntegration($integrationId, $userId)
    {
        $integration = $this->integration->find($integrationId);
        $user = $this->model->find($userId);

        $user->integrations()->attach($integration);
    }

    /**
     * Leave a integration
     *
     * @param  integer $integrationId
     * @param  integer $userId
     * @return void
     */
    public function leaveIntegration($integrationId, $userId)
    {
        $integration = $this->integration->find($integrationId);
        $user = $this->model->find($userId);

        $user->integrations()->detach($integration);
    }

    /**
     * Leave all integrations
     *
     * @param  integer $integrationId
     * @param  integer $userId
     * @return void
     */
    public function leaveAllIntegrations($userId)
    {
        $user = $this->model->find($userId);
        $user->integrations()->detach();
    }
}
