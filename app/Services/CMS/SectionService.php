<?php

namespace App\Services\CMS;

use App\Entity\SectionResponseEntity;
use App\Repositories\SectionRepo;
use App\Services\Contracts\CMS\SectionServiceInterface;
use Illuminate\Database\Eloquent\Model;

class SectionService implements SectionServiceInterface
{
    // Your service class code here
	public function __construct(
        private readonly SectionRepo $sectionRepo
    ){}

	/**
	 * @return array
	 */
	public function get() : array
    {
        $data = $this->sectionRepo->getSections();
        return $data;
    }

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function getSectionById(int $id) : array
    {
        $data = $this->response($this->sectionRepo->getSectionById($id));
        return $data;
    }

	/**
	 * @param array $request
	 *
	 * @return bool
	 */
	public function store(array $request) : bool
    {
        $storeData = $this->sectionRepo->storeSection($request);
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

        $updateData = $this->sectionRepo->updateSectionById("id", $id, $fillableData);
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
        $fillable = ['user_name', 'id', 'name', 'set_no'];
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
        return $this->sectionRepo->deleteSectionById($id, $request);
    }

        /**
     * @param Model|null $channel
     *
     * @return array
     */
    private function response(Model|null $section): array
    {
        $data = [];
        if ($section) {
            $data = (new SectionResponseEntity())
                ->setName($section->name)
                ->setNo($section->set_no)
                ->build();
        }

        return $data;
    }

}
