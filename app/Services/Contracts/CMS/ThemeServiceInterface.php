<?php

namespace App\Services\Contracts\CMS;

interface ThemeServiceInterface
{
    // Your repository interface code here
	public function get(string|null $columnName);

	public function getThemeById(int $id);

	public function store(array $request);

	public function update(array $request, int $id);

	public function delete(int $id, array $request);

}
