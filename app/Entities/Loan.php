<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Loan
 */
class Loan extends Model
{

    use SoftDeletes;

    protected $table = 'loans';

    protected $primaryKey = 'loans_id';

	public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'users_id',
        'total_amount',
        'total_amount_due',
        'start_date',
        'last_send_at',
        'last_paid_at',
        'status'
    ];

    protected $guarded = [];

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function loan_payments_histories()
    {
        return $this->hasOne('App\Entities\LoanPaymentsHistories', 'loans_id', 'loans_id');
    }
}

