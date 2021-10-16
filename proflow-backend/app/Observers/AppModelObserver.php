<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Company;

class AppModelObserver {

    /**
     * @param $model
     */
    public function updating($model)
    {
        $request =  app('request');
        $company = Company::where('workspace_url', $request->header('subdomain'))->first();
      //  $model->created_by = Auth::id();
        // if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'deleted_by')) {
        //     $model->deleted_by = Auth::id();
        // }
        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'company_id')) {
            $model->company_id = $company->id;
        }

    }

    /**
     * @param $model
     */
    public function creating($model)
    {
        $request =  app('request');
        $company = Company::where('workspace_url', $request->header('subdomain'))->first();
        $model->created_by = Auth::id();
        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'company_id')) {
            $model->company_id = $company->id;
        }
    }

    public function deleting($model)
    {
        $request =  app('request');
        $company = Company::where('workspace_url', $request->header('subdomain'))->first();
      //  $model->created_by = Auth::id();
        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'deleted_by')) {
            $model->deleted_by = Auth::id();
        }
       

    }
}
