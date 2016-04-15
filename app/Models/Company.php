<?php

namespace App\Models;

class Company extends ImprovedModel
{
    protected $table   = 'companies';
    protected $guarded = [];

    public function industries()
    {
        return $this->belongsToMany(
            'App\Models\CompanyIndustry',
            'company_industry_relations',
            'company_id',
            'industry_id'
        );
    }
}
