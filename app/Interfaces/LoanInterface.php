<?php

namespace App\Interfaces;

interface LoanInterface
{
    /**
     * Create loan for the particular
     *
     * @param $mixed
     *
     * return User Loan Approval Or rejection Based on Given Input
     *
     * @throws Exception
     */
    public function createUserLoan($data);

    /**
     * Get loan Details for the particular User
     *
     * @param $users_id
     *
     * return User Loan Data
     *
     * @throws Exception
     */
    public function getLoanDetails($users_id);

    /**
     * Approve loan for the particular user
     *
     * @param $loan_id
     *
     * return message
     *
     * @throws Exception
     */
    public function approveUserLoan($data);

    /**
     * Reject loan for the particular user
     *
     * @param $loan_id
     *
     * return message
     *
     * @throws Exception
     */
    public function rejectUserLoan($data);

    /**
     * Loan Payment for the given loan_id
     *
     * @param $loan_id,$amount
     *
     * return message
     *
     * @throws Exception
     */
    public function processLoanPayment($data);

}
