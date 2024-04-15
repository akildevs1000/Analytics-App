<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchShiftManagers\StoreRequest;
use App\Http\Requests\BranchShiftManagers\UpdateRequest;
use App\Models\BranchShiftManagers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchShiftManagersController extends Controller
{
    public function getDefaultModelSettings($request)
    {
        $model = BranchShiftManagers::query();
        $model->where('company_id', $request->company_id);
        $model->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id',  $request->branch_id));
        $model->when($request->filled('serach_name'), fn ($q) => $q->where('name', 'ILIKE', "{$request->serach_name}%"));
        $model->when($request->filled('search_short_name'), fn ($q) => $q->where('short_name', 'ILIKE', "{$request->search_short_name}%"));
        $model->with(["branch", "employee"]);
        $model->orderByDesc("id");
        return $model;
    }

    public function index(Request $request)
    {
        return $this->getDefaultModelSettings($request)->paginate($request->per_page ?? 100);
    }

    function list(Request $request)
    {
        return $this->getDefaultModelSettings($request)->paginate($request->per_page ?? 100);
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        try {

            $isExist = BranchShiftManagers::where('company_id', '=', $request->company_id)->where('name', '=', $request->name)->first();
            if ($isExist == null) {

                $record = BranchShiftManagers::create($request->validated());
                DB::commit();

                if ($record) {

                    return $this->response('Leave Type  Successfully created.', $record, true);
                } else {
                    return $this->response('Leave Type  cannot be created.', null, false);
                }
            } else {
                return $this->response('Leave Type "' . $request->name . '" already exist', null, false);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
    public function update(UpdateRequest $request, $id)
    {

        try {
            $isExist = BranchShiftManagers::where('company_id', '=', $request->company_id)
                ->where('id', '!=', $id)
                ->where('name', '=', $request->name)->first();
            if ($isExist == null) {

                $record = BranchShiftManagers::find($id)->update($request->validated());

                if ($record) {

                    return $this->response('Leave Type  successfully updated.', $record, true);
                } else {
                    return $this->response('Leave Type  cannot update.', null, false);
                }
            } else {
                return $this->response('Leave Type "' . $request->name . '" already exist', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function destroy($id)
    {

        if (BranchShiftManagers::find($id)->delete()) {

            return $this->response('LeaveType successfully deleted.', null, true);
        } else {
            return $this->response('LeaveType cannot delete.', null, false);
        }
    }
    public function search(Request $request, $key)
    {
        return $this->getDefaultModelSettings($request)->where('title', 'LIKE', "%$key%")->paginate($request->per_page ?? 100);
    }
    public function deleteSelected(Request $request)
    {
        $record = BranchShiftManagers::whereIn('id', $request->ids)->delete();
        if ($record) {

            return $this->response('LeaveType Successfully delete.', $record, true);
        } else {
            return $this->response('LeaveType cannot delete.', null, false);
        }
    }
}
