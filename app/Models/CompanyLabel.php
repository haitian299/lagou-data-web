<?php

namespace App\Models;

class CompanyLabel extends ImprovedModel
{
    protected $table   = 'company_labels';
    protected $guarded = ['id'];
    public $timestamps = false;
}
