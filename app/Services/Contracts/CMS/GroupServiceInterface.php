<?php

namespace App\Services\Contracts\CMS;

interface GroupServiceInterface
{
    // Your repository interface code here
	public function get();

	public function getGroupById(int $id);

	public function store(array $request);

	public function update(array $request, int $id);

	public function delete(int $id, array $request);

	public function getQuestionsByGroupId(int $id);

	public function attachQuestions(array $request);

}
