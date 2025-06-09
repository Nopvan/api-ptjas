<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioVisitor extends Model
{
    protected $fillable = ['portfolio_id', 'visitor_ip'];
    public $timestamps = true;
}
