<?php

namespace App\Services;

use App\Repositories\HitRepo;

class HitService
{
    public function __construct(
        private HitRepo $repo
    ) {
    }

    /**
     * @param string $msisdn
     * @param string $event
     * @param string $channel
     * @param int|null $attempt_id
     * @return void
     */
    public function store(string $msisdn, string $event, string $channel, int|null $attempt_id): void
    {
        $this->repo->save($msisdn, $event, $channel, $attempt_id);
    }
}
