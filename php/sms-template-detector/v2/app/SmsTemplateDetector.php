<?php

namespace App;

class SmsTemplateDetector
{
    private $templates = [
            'Purchase of {amount} with {card} at {brand},',
            'Payment of {amount} to {brand} with {card}.',
    ];
    private $keywords = ['amount', 'brand', 'card', 'account', 'datetime'];

    public function detect($sms)
    {
        foreach($this->templates as $template) {
            $re_pattern = $this->getRePattern($template);

            if(preg_match($re_pattern, $sms, $matched_parts)) {
                $part_with_values = $this->getPartsWithValues($matched_parts, $template);
                return [$template, $part_with_values];
            }
        }
        return null;
    }

    private function getRePattern($template){
        foreach($this->keywords as $word){
            $template = str_replace("{". $word . "}", "(.*?)", $template);
        }
        return "/{$template}/";
    }

    private function getPartsWithValues($matched_parts, $templateBody)
    {
        preg_match_all("/\{(\w+)\}/", $templateBody, $this->keywords);
        $patterns = $this->keywords[1];
        $part_with_values = array_combine($patterns, array_slice($matched_parts, 1));
        return $part_with_values;
    }
}


