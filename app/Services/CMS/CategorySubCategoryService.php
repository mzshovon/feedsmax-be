<?php

namespace App\Services\CMS;

use App\Entity\CategorySubCategoryResponseEntityForCMS;
use App\Repositories\CategorySubCategoryRepo;
use App\Services\Contracts\CMS\CategorySubCategoryServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CategorySubCategoryService implements CategorySubCategoryServiceInterface
{
    public function __construct(
        private CategorySubCategoryRepo $categorySubCategoryRepo
    ) {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->categorySubCategoryRepo->getCategorySubCategories();
        return $data;
    }

    /**
     * @param int $categorySubCategoryId
     *
     * @return array
     */
    public function getCategorySubCategoryById(int $categorySubCategoryId): array
    {
        $data = $this->response($this->categorySubCategoryRepo->getCategoryOrSubCategoryById($categorySubCategoryId));
        return $data;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function store(array $request): bool
    {
        $storeData = $this->categorySubCategoryRepo->storeCategorySubCategory($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     * @param int $categorySubCategoryId
     *
     * @return bool
     */
    public function update(array $request, int $categorySubCategoryId): bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->categorySubCategoryRepo->updateCategorySubCategoryById("id", $categorySubCategoryId, $fillableData);
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
        $fillable = ['user_name', 'id', 'name', 'parent_id'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param int $categorySubCategoryId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $categorySubCategoryId, array $request): bool
    {
        return $this->categorySubCategoryRepo->deleteCategorySubCategoryById($categorySubCategoryId, $request);
    }

    /**
     * @param Model|null $category
     *
     * @return array
     */
    private function response(Model|null $category): array
    {
        $data = [];
        if ($category) {
            $data = (new CategorySubCategoryResponseEntityForCMS())
                ->setName($category->name)
                ->setChildren($category->children)
                ->build();
        }

        return $data;
    }
}
