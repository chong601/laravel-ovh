<?php
namespace App\Services\SoYouStart\Me;

use Ovh\Api;

/**
 * SoYouStart API related to the user
 *
 * @property AccessRestriction $accessRestriction
 * @property Agreements $agreements
 * @property Api $api
 * @property AutoRenew $autorenew
 * @property AvailableAutomaticPaymentMeans
 * @property Bill $bill
 * @property Certificate $certificate
 * @property ChangeEmail $changeEmail
 * @property ChangePassword $changePassword
 * @property Consent $consent
 * @property Contact $contact
 * @property CreditBalance $creditBalance
 * @property CreditCode $creditCode
 * @property DebtAccount $debtAccount
 * @property Deposit $deposit
 * @property Document $document
 * @property FidelityAccount $fidelityAccount
 * @property Geolocation $geolocation
 * @property Identity $identity
 * @property InstallationTemplate $installationTemplate
 * @property IpOrganisation $iporganisation
 * @property IpxeScript $ipxeScript
 * @property MailingList $mailingList
 * @property Notification $notification
 * @property Order $order
 * @property OvhAccount $ovhAccount
 * @property PasswordRecover $passwordRecover
 * @property Payment $payment
 * @property PaymentMean $paymentmean
 * @property Refund $refund
 * @property Sla $sla
 * @property SshKey $sshKey
 * @property SubAccount $subaccount
 * @property Task $task
 * @property Voucher $voucher
 * @property Withdrawal $withdrawal
 */
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
        'contact' => Contact::class,
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
