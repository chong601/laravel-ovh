<?php
namespace App\Services\SoYouStart\Me;

use Ovh\Api;

class SoYouStartMe
{
    protected $ovh_api;

    private static $mapStringToClass = [
        'accessRestriction' => AccessRestriction::class,
        'agreements' => Agreements::class,
        'api' => Api::class,
        'autorenew' => AutoRenew::class,
        'availableAutomaticPaymentMeans' => AvailableAutomaticPaymentMeans::class,
        'bill' => Bill::class,
        'certifiates' => Certificates::class,
        'changeEmail' => ChangeEmail::class,
        'changePassword' => ChangePassword::class,
        'consent' => Consent::class,
        'contact' => Contract::class,
        'creditBalance' => CreditBalance::class,
        'creditCode' => CreditCode::class,
        'debtAccount' => DebtAccount::class,
        'deposit' => Deposit::class,
        'document' => Document::class,
        'fidelityAccount' => FidelityAccount::class,  // ???
        'geolocation' => Geolocation::class,
        'identity' => Identity::class,
        'installationTemplate' => InstallationTemplate::class,
        'ipOrganisation' => IpOrganisation::class,
        'ipxeScript' => IpxeScript::class,
        'mailingList' => MailingList::class,
        'notification' => Notification::class,
        'order' => Order::class,
        'ovhAccount' => OvhAccount::class,
        'passwordRecover' => PasswordRecover::class,
        'payment' => Payment::class,
        'paymentMean' => PaymentMean::class,
        'refund' => Refund::class,
        'sla' => Sla::class,
        'sshKey' => SshKey::class,
        'subAccount' => SubAccount::class,
        'subscription' => Subscription::class,
        'task' => Task::class,
        'voucher' => Voucher:: class,
        'withdrawal' => Withdrawal::class
    ];

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    // Thanks Stripe for this design!
    public function __get($name)
    {
        return array_key_exists($name, self::$mapStringToClass) ? new self::$mapStringToClass[$name]($this->ovh_api) : null;
    }
}
