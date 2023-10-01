<?php

namespace App\Models\Traits;

use App\Enums\ReportStatus;
use App\Enums\UserType;

Trait ApprovalStatus
{
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, [
            'rejected',
            'permitted',
            'editable',
            'layer_status',
        ]));

        return parent::getArrayableAppends();
    }

    public function getUpdateStatus(int $layer, int $status)
    {
        $layerStatus = $this->layer_status;
        $layerStatus[$layer] = $status;

        $updateStatus = 0;
        foreach ($layerStatus as $key => $value) {
            $updateStatus += pow(10, ($key - 1)) * $value;
        }

        return $updateStatus;
    }

    public function getRejectedAttribute()
    {
        $rejected = false;
        foreach($this->layer_status as $status) {
            if ($status == ReportStatus::Rejected) {
                $rejected = true;
            }
        }

        return $rejected;
    }

    public function getPermittedAttribute()
    {
        $permitted = true;
        foreach($this->layer_status as $key => $value) {
            if ($value != ReportStatus::Permitted) {
                $permitted = false;
            }
        }

        return $permitted;
    }

    public function getEditableAttribute()
    {
        return $this->rejected;
    }

    public function getLayerStatusAttribute()
    {
        $status = $this->status;
        $type   = $this->user->is_manager
                ? UserType::Manager
                : UserType::Employee;

        $layerStatus = [];
        for ($layer = 1; $layer <= self::Layer[$type]; $layer ++) {
            $layerStatus[$layer] = $status % 10;
            $status = (int) ($status / 10);
        }

        return $layerStatus;
    }
}