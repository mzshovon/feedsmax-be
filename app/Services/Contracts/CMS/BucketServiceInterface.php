<?php

namespace App\Services\Contracts\CMS;

interface BucketServiceInterface
{
    // Your repository interface code here
	public function get();

	public function getBucketById(int $id);

	public function store(array $request);

	public function update(array $request, int $id);

	public function delete(int $id, array $request);

	public function getQuestionsByBucketId(int $id);

	public function attachQuestions(array $request);

}
