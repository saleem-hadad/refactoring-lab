<?php

declare(strict_types=1);

namespace App;

class SmsTemplateDetector
{
    private array $templates = [
        'Payment of {amount} to {brand} with {card},',
        'Purchase of {amount} with {card} at {brand},',
    ];

    private array $patterns = [
        '/\{amount\}/' => '(.*?)',
        '/\{brand\}/' => '(.*?)',
        '/\{card\}/' => '(.*?)',
    ];


    public function detect(string $sms): ?array
    {
        foreach ($this->templates as  $template) {

            $templateCopy = $this->transformTemplateToRegex($template);

            if (preg_match("/{$templateCopy}/", $sms, $matchedParts)) {
                $partsWithValues = $this->getPartsWithValues($matchedParts, $template);
                return [$template, $partsWithValues];
            }
        }

        return null;
    }

    protected function getPartsWithValues(array $matchedParts, string $templateBody): array
    {
        $partsPositionsInTemplate = [];

        if (strpos($templateBody, "{amount}")) {
            $partsPositionsInTemplate['amount'] = strpos($templateBody, "{amount}");
        }

        if (strpos($templateBody, "{brand}")) {
            $partsPositionsInTemplate['brand'] = strpos($templateBody, "{brand}");
        }
        if (strpos($templateBody, "{card}")) {
            $partsPositionsInTemplate['card'] = strpos($templateBody, "{card}");
        }

        asort($partsPositionsInTemplate);

        $index = 1;
        $partsWithValues = [];
        foreach (array_keys($partsPositionsInTemplate) as $part) {
            if (isset($matchedParts[$index])) {
                $partsWithValues[$part] = $matchedParts[$index];
            }
            $index++;
        }

        return $partsWithValues;
    }

    private function transformTemplateToRegex($template)
    {
        return preg_replace(
            pattern: array_keys($this->patterns),
            replacement: array_values($this->patterns),
            subject: (string) $template
        );
    }
}
