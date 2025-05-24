<?php

namespace App\Classes;

use App\Models\SurveyQuota;
use App\Models\SurveyQuotaHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ResetSurveyQuotaAndStoreOldHistory
{

    public function reset()
    {
        return $this->exec();
    }

    private function exec()
    {
        $old_survey_data = $this->wrap_old_data();
        if(!empty($old_survey_data)) {
            if($this->store($old_survey_data)) {
                return $this->refill($old_survey_data);
            }
        }
        return false;
    }

    /**
     * @return array
     */
    private function wrap_old_data() : array
    {
        try {
            $quotaTableData = SurveyQuota::get(["type", "param", "quota", "count"]);
            return $quotaTableData->toArray();
        } catch (Exception $ex) {
            throw new Exception("Survey Quota Fetch Old Data Failed!");
        }

    }

    /**
     * @param array $old_data
     *
     * @return Model
     */
    private function store(array $old_data) : Model
    {
        try {
            $data["quota_history"] = json_encode($old_data);
            $data["quota_from"] = date('Y-m-d',strtotime("-1 days"));
            $quotaHistory = SurveyQuotaHistory::create($data);
            return $quotaHistory;
        } catch (Exception $ex) {
            throw new Exception("Survey Quota Store Failed!");
        }

    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function refill(array $data) : bool
    {
        try {
            array_walk($data, function(&$old_data) {
                $old_data['count'] = $old_data['type'] == "location" ? 0 : $old_data['quota'];
            });
            foreach ($data as $value) {
                SurveyQuota::whereType($value['type'])->whereParam($value['param'])->update(["count" => $value['count']]);
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception("Update Survey Quota Failed!");
        }
    }

}
