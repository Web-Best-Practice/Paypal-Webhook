

### Add Custom repository:

    composer config repositories.repo-name vcs https://github.com/Web-Best-Practice/Paypal-Webhook

### Install package:

    composer require web-best-practice/paypal-webhook

### Set-up Webhook ID:

    PAYPAL_WEBHOOK_ID=123123123

### Validate Webhook request:

```php
use WebBestPractice\PaypalWebhook\Webhook;

/** ... **/

$webhook = new Webhook();
    
try {
    $response = $webhook->verify($request);
} catch (PaypalException $e) {
    echo $e->getMessage();
}
```


