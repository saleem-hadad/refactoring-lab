<?php

use App\SmsTemplateDetector;

test('it returns correct matched template with extracted data', function () {
    $sut = new SmsTemplateDetector;

    $smsTemplate = $sut->detect("Payment of 33.3 to Apple with 4233.");

    expect($smsTemplate[0])->toBe('Payment of {amount} to {brand} with {card}.');
    expect($smsTemplate[1]['amount'])->toBe('33.3');
    expect($smsTemplate[1]['brand'])->toBe('Apple');
});
