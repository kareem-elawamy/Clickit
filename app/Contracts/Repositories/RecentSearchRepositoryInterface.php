<?php

namespace App\Contracts\Repositories;

interface RecentSearchRepositoryInterface extends RepositoryInterface
{

    public function updateOrInsert(array $params, array $data): bool;
}
