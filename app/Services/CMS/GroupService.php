<?php

namespace App\Services\CMS;

use App\Entity\GroupResponseEntity;
use App\Repositories\GroupRepo;
use App\Repositories\QuestionRepo;
use App\Services\Contracts\CMS\GroupServiceInterface;
use Illuminate\Database\Eloquent\Model;

class GroupService implements GroupServiceInterface
{
    // Your service class code here
	public function __construct(
        private readonly GroupRepo $groupRepo,
        private readonly QuestionRepo $questionRepo
    ){}

	/**
	 * @return array
	 */
	public function get(): array
    {
        $data = $this->groupRepo->getGroups();
        return $data;
    }

	public function getGroupById(int $id)
    {
        $data = $this->response($this->groupRepo->getGroupById($id));
        return $data;
    }

	/**
	 * @param array $request
	 *
	 * @return bool
	 */
	public function store(array $request) : bool
    {
        $storeData = $this->groupRepo->storeGroup($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

	/**
	 * @param array $request
	 * @param int $id
	 *
	 * @return bool
	 */
	public function update(array $request, int $id) : bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->groupRepo->updateGroupById("id", $id, $fillableData);
        if ($updateData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function fillableData(array $request): array{
        $data = [];
        $fillable = ['user_name', 'id', 'name', 'description', 'status', 'type', 'nps_ques_id', 'promoter_range'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

	/**
	 * @param int $id
	 * @param array $request
	 *
	 * @return bool
	 */
	public function delete(int $id, array $request) : bool
    {
        return $this->groupRepo->deleteGroupById($id, $request);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getQuestionsByGroupId(int $id): array
    {
        return $this->questionRepo->getQuestionListForCMSByGroupId($id);
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function attachQuestions(array $request): bool
    {
        $syncChanges = $this->groupRepo->attachQuestions($request['group_id'], $request['questions'], $request['user_name'], "write");
        if(!empty($syncChanges)) {
            return true;
        }
        return false;
    }

    /**
     * @param Model|null $group
     *
     * @return array
     */
    private function response(Model|null $group): array
    {
        $data = [];
        if ($group) {
            $data = (new GroupResponseEntity())
                ->setName($group->name)
                ->setDescription($group->description)
                ->setStatus($group->status)
                ->setType($group->type ?? null)
                ->npsQuestionId($group->nps_ques_id ?? null)
                ->promoterRange($group->promoter_range ?? null)
                ->build();
        }

        return $data;
    }

}
