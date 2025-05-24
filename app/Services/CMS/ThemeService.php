<?php

namespace App\Services\CMS;

use App\Entity\ThemeResponseEntityForCMS;
use App\Models\Channel;
use App\Repositories\ChannelRepo;
use App\Repositories\ThemeRepo;
use App\Services\Contracts\CMS\ThemeServiceInterface;
use Illuminate\Database\Eloquent\Model;

class ThemeService implements ThemeServiceInterface
{
    // Your service class code here
	public function __construct(
        private readonly ThemeRepo $themeRepo
    ){}

	/**
	 * @param string|null $columnName
	 *
	 * @return array
	 */
	public function get(string|null $columnName) : array
    {
        if($columnName) {
            $data = $this->response($this->themeRepo->getThemeByColumn("name", $columnName));
        } else {
            $data = $this->themeRepo->getThemes();
        }
        return $data;
    }

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function getThemeById(int $id) : array
    {
        $data = $this->response($this->themeRepo->getThemeByColumn("id", $id));
        return $data;
    }

	/**
	 * @param array $request
	 *
	 * @return bool
	 */
	public function store(array $request) : bool
    {
        if(isset($request['value'])) {
            $request['value'] = json_encode($request['value']);
        }
        $storeData = $this->themeRepo->storeTheme($request);
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

        $updateData = $this->themeRepo->updateThemeById("id", $id, $fillableData);
        if ($updateData) {
            return true;
        }
        return false;
    }

	/**
	 * @param int $id
	 * @param array $request
	 *
	 * @return bool
	 */
	public function delete(int $id, array $request) : bool
    {
        return $this->themeRepo->deleteThemeById($id, $request);
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function fillableData(array $request): array{
        $data = [];
        $fillable = ['name', 'value', 'user_name'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = ($key == "value" ? json_encode($value) : $value);
            }
        }
        return $data;
    }

        /**
     * @param Model|null $theme
     *
     * @return array
     */
    private function response(Model|null $theme): array
    {
        $data = [];
        if ($theme) {
            $data = (new ThemeResponseEntityForCMS())
                ->setName($theme->name)
                ->setValue($theme->value)
                ->setChannels($theme->channels)
                ->build();
        }

        return $data;
    }

    /**
     * @param string $channel
     *
     * @return array
     */
    public static function theme(string $channel = "") : array
    {
        $channelRepo = new ChannelRepo(new Channel());
        $theme = config('app.default_theme');
        $channelRecord = $channelRepo->getInfoByChannelTag($channel);
        if(!empty($channelRecord) && $channelRecord['themes']) {
            $theme = json_decode($channelRecord['themes']['value'], true);
            $theme['name'] = $channelRecord['themes']['name'];
        }
        return $theme;
    }


}
