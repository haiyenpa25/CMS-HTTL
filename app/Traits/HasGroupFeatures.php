<?php

namespace App\Traits;

trait HasGroupFeatures
{
    /**
     * Check if the group has a specific feature enabled.
     *
     * @param string $feature
     * @return bool
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }

    /**
     * Enable a specific feature.
     *
     * @param string $feature
     * @return void
     */
    public function enableFeature(string $feature): void
    {
        $features = $this->features ?? [];
        $features[$feature] = true;
        $this->features = $features;
        $this->save();
    }

    /**
     * Disable a specific feature.
     *
     * @param string $feature
     * @return void
     */
    public function disableFeature(string $feature): void
    {
        $features = $this->features ?? [];
        if (isset($features[$feature])) {
            unset($features[$feature]);
            $this->features = $features;
            $this->save();
        }
    }
}
