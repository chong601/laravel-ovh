<?php
namespace App\Services\SoYouStart;

use Ovh\Api;

class SoYouStartMe
{
    protected $ovh_api;

    public $accessRestriction;
    public $agreements;
    public $api;
    public $autorenew;
    public $availableAutomaticPaymentMeans;
    public $bill;
    public $certifiates;
    public $changeEmail;
    public $changePassword;
    public $consent;
    public $contact;
    public $creditBalance;
    public $creditCode;
    public $debtAccount;
    public $deposit;
    public $document;
    public $fidelityAccount;
    public $geolocation;
    public $identity;
    public $installationTemplate;
    public $ipOrganisation;
    public $ipxeScript;
    public $mailingList;
    public $notification;
    public $order;
    public $ovhAccount;
    public $passwordRecover;
    public $payment;
    public $paymentMean;
    public $refund;
    public $sla;
    public $sshKey;
    public $subAccount;
    public $subscription;
    public $task;
    public $voucher;
    public $withdrawal;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }
}
