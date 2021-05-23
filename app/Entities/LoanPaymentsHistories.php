<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LoanPaymentsHistories
 */
class LoanPaymentsHistories extends Model
{

    use SoftDeletes;

    protected $table = 'loan_payments_histories';

    protected $primaryKey = 'loan_payments_histories_id';

	public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'users_id',
        'loans_id',
        'amount_paid',
        'paid_at',
        'payment_status'
    ];

    protected $guarded = [];

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function loans()
    {
        return $this->hasOne('App\Entities\Loan', 'loans_id', 'loans_id');
    }
}

