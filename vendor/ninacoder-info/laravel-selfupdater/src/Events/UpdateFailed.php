<?php

namespace NiNaCoder\Updater\Events;

use NiNaCoder\Updater\Models\Release;

class UpdateFailed
{
    protected $release;

    public function __construct(Release $release)
    {
        $this->release = $release;
    }
}
