<?php

namespace App\Domains\Selection\Events;

use App\Domains\Selection\Models\Application;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Application $application,
        public string $oldStatus,
        public string $newStatus
    ) {}
}
