<?php

use App\SmsTemplateDetector;

test('it returns correct matched template with extracted data', function () {
    $sut = new SmsTemplateDetector;

    $smsTemplate = $sut->detect("Payment of 33.3 to Apple with 4233,");

    $smsTemplate2 = $sut->detect("Purchase of 45.9 with 4853 at Samsung,");

    expect($smsTemplate[0])->toBe('Payment of {amount} to {brand} with {card},');
    expect($smsTemplate[1]['amount'])->toBe('33.3');
    expect($smsTemplate[1]['brand'])->toBe('Apple');
    expect($smsTemplate[1]['card'])->toBe('4233');

    expect($smsTemplate2[0])->toBe("Purchase of {amount} with {card} at {brand},");
    expect($smsTemplate2[1]['amount'])->toBe('45.9');
    expect($smsTemplate2[1]['brand'])->toBe('Samsung');
    expect($smsTemplate2[1]['card'])->toBe('4853');
});
