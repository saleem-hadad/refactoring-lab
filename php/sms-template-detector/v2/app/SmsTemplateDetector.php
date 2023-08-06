<?php

namespace App;

class SmsTemplateDetector
{
    /**
     * @var array The templates to detect sms pattern.
     */
    protected $templates = [
        'Purchase of {amount} with {card} at {brand},',
        'Payment of {amount} to {brand} with {card}.',
    ];

    /**
     * @var array The template replacements to be extracted form sms.
     */
    protected $templateReplacemnts = [
        '{amount}' => '(.*?)',
        '{brand}' => '(.*?)',
        '{card}' => '(.*?)',
        '{account}' => '(.*?)',
        '{datetime}' => '(.*?(?=\.))',
    ];

    /**
     * Retrieves the templates and it can be overidden with custom logic.
     * 
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Retrieves the template replacements and it can be overidden with custom logic.
     *
     * @return array
     */
    public function getTemplateReplacements()
    {
        return $this->templateReplacemnts;
    }

    /**
     * Detects SMS and extract information from sms once including brand name, amount and other templates replacements.
     *
     * @param $sms The SMS to be detected.
     *
     * @return array|null 
     */
    public function detect($sms)
    {
        foreach ($this->getTemplates() as $template) {
            if ($this->isSmsMatchTemplate($template, $sms, $matchedParts)) {
                return [$template, $this->getSmsInformation($matchedParts, $template)];
            }
        }
    }

    /**
     * Get the parts with their corresponding values.
     *
     * @param array $matchedParts The matched parts.
     * @param string $templateBody The template body.
     *
     * @return array
     */
    protected function getSmsInformation($matchedParts, $templateBody)
    {
        return $this->getPartsWithValues($this->partsPositionsInTemplate($templateBody), $matchedParts);
    }

    /**
     * Checks if the given template matches the provided SMS.
     *
     * @param string $template The template to match against.
     * @param string $sms The SMS to compare with the template.
     * @param array $matchedParts (out) An array to store the matched parts of the SMS.
     *
     * @return int
     */
    private function isSmsMatchTemplate($template, $sms, &$matchedParts)
    {
        foreach ($this->getTemplateReplacements() as $placeholder => $pattern) {
            $template = str_replace($placeholder, $pattern, $template);
        }

        return preg_match("/{$template}/", $sms, $matchedParts);
    }

    /**
     * Returns an array of the positions of the placeholders in the template body.
     *
     * @param string $templateBody The body of the template.
     *
     * @return array
     */
    private function partsPositionsInTemplate($templateBody)
    {
        return array_reduce(
            array_keys($this->getTemplateReplacements()),
            function ($output, $placeholder) use ($templateBody) {
                $position = strpos($templateBody, $placeholder);
                if ($position !== false) {
                    $output[trim($placeholder, '{}')] = $position;
                }
                return $output;
            }
        );
    }

    /**
     * Retrieves the parts of the template that have matching values.
     *
     * @param array $partsPositionsInTemplate The positions of the parts in the template.
     * @param array $matchedParts The parts that have matching values.
     *
     * @return array
     */
    private function getPartsWithValues($partsPositionsInTemplate, $matchedParts)
    {
        return array_reduce(
            array_keys($partsPositionsInTemplate),
            function ($output, $part) use ($matchedParts, $partsPositionsInTemplate) {
                $index = array_search($part, array_keys($partsPositionsInTemplate)) + 1;
                if (!empty($matchedParts[$index])) {
                    $output[$part] = $matchedParts[$index];
                }
                return $output;
            }
        );
    }
}
