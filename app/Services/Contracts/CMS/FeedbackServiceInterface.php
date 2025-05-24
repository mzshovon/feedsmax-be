<?php

namespace App\Services\Contracts\CMS;

interface FeedbackServiceInterface
{
    // Your repository interface code here
	public function get();

	public function getFeedbackById(int $id);

	public function store(array $request);

	public function update(array $request, int $id);

	public function delete(int $id);

}
