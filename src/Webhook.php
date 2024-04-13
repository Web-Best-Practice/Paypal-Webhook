<?php

namespace Wbp\Paypal;

use Illuminate\Http\Request;
use Wbp\Paypal\Exceptions\PaypalException;

class Webhook
{

    /**
     * Verify PayPal Webhook response and response payload.
     *
     * @param Request $request
     * @return object
     * @throws PaypalException
     */
    public function verify(Request $request) : object
    {
        /**
         * The ID of the HTTP transmission.
         */
        $transmissionId = $request->server('HTTP_PAYPAL_TRANSMISSION_ID');

        /**
         * The PayPal-generated asymmetric signature.
         */
        $transmissionSig = $request->server('HTTP_PAYPAL_TRANSMISSION_SIG');

        /**
         * The date and time of the HTTP transmission, in Internet date and time format.
         */
        $transmissionTime = $request->server('HTTP_PAYPAL_TRANSMISSION_TIME');

        /**
         * he X.509 public key certificate. Download the certificate from this URL and use it to verify the signature.
         */
        $certUrl = $request->server('HTTP_PAYPAL_CERT_URL');

        /**
         * The ID of the webhook as configured in your Developer Portal account.
         */
        $webhookId = config('paypal.webhook_id');

        /**
         * Message Payload.
         */
        $payload = $request->getContent();

        // Check for PayPal headers
        if(!$transmissionId || !$transmissionSig && !$transmissionTime && !$certUrl) {
            throw new PaypalException('PayPal headers not set.');
        }

        if(!$webhookId) {
            throw new PaypalException('PayPal webhook ID not set.');
        }

        // Read the certificate
        $cert = file_get_contents($certUrl);

        $signature = base64_decode($transmissionSig);

        // Signature string
        $signatureString = implode('|', [
            $transmissionId,
            $transmissionTime,
            $webhookId,
            crc32($payload),
        ]);

        $success = openssl_verify(
            data: $signatureString,
            signature: $signature,
            public_key: $cert,
            algorithm: 'sha256WithRSAEncryption'
        );

        if(!$success) {
            throw new PaypalException('PayPal webhook verification failed.');
        }

        return json_decode($payload);
    }
}
