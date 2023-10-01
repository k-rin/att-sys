<?php

namespace App\Repositories;

use App\Models\ReportApprover;

class ReportApproverRepository
{
    /**
     * Get report approver list.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList()
    {
        return ReportApprover::all();
    }

    /**
     * Update report approver by id.
     *
     * @param  int   $id
     * @param  array $values
     * @return \App\Models\ReportApprover
     */
    public function update(int $id, array $values)
    {
        return ReportApprover::find($id)->update($values);
    }
}