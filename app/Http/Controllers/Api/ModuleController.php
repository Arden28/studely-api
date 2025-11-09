<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\StoreModuleRequest;
use App\Http\Requests\Modules\UpdateModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $tid = app('tenant.id');
        $q = Module::where('tenant_id',$tid)->latest()->paginate(20);
        return ModuleResource::collection($q);
    }

    public function store(StoreModuleRequest $req)
    {
        $tid = app('tenant.id');
        $module = Module::create($req->validated() + ['tenant_id'=>$tid]);
        return new ModuleResource($module);
    }

    public function show($id)
    {
        $tid = app('tenant.id');
        $m = Module::where('tenant_id',$tid)->findOrFail($id);
        return new ModuleResource($m);
    }

    public function update(UpdateModuleRequest $req, $id)
    {
        $tid = app('tenant.id');
        $m = Module::where('tenant_id',$tid)->findOrFail($id);
        $m->update($req->validated());
        return new ModuleResource($m);
    }

    public function destroy($id)
    {
        $tid = app('tenant.id');
        $m = Module::where('tenant_id',$tid)->findOrFail($id);
        $m->delete();
        return response()->json(['message'=>'deleted']);
    }
}
