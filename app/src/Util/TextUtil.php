<?php

namespace App\Util;

class TextUtil
{
    /**
     * Replaces all special emphasis characters with appropriate HTML tags
     */
    public static function emphasize(string $text, string $tagName = 'span')
    {
        $startTag = "<{$tagName}>";
        $endTag = "</{$tagName}>";

        $formatted = str_replace('[', $startTag, trim($text ?? ''));
        $formatted = str_replace(']', $endTag, trim($formatted));
        $formatted = str_replace('|', '<br>', trim($formatted));

        return trim($formatted);
    }

    /**
     * Removes all special emphasis characters
     */
    public static function deemphasize(?string $text)
    {
        $formatted = str_replace('[', '', trim($text ?? ''));
        $formatted = str_replace(']', '', trim($formatted));
        $formatted = str_replace('|', '', trim($formatted));

        return trim($formatted);
    }

    /**
     * Surrounds $numWords from the start of the $text with appropriate HTML tags
     */
    public static function emphasizeFromStart(string $text, int $numWords = 1)
    {
        $text = trim($text ?? '');
        $output = '';
        $words = preg_split('/\s+/', $text);
        $chunks = array_chunk($words, $numWords);

        $wrapped = join(' ', $chunks[0]);
        $chunks[0] = ["<strong>{$wrapped}</strong>"];

        $output = call_user_func_array('array_merge', $chunks);
        $output = join(' ', $output);

        return $output;
    }

    /**
     * Surrounds $numWords from the end of the $text with appropriate HTML tags
     */
    public static function emphasizeFromEnd(string $text, int $numWords = 1)
    {
        $text = trim($text ?? '');
        $output = '';
        $words = preg_split('/\s+/', $text);
        $chunks = array_chunk($words, $numWords);

        $wrapped = join(' ', $chunks[count($chunks) - 1]);
        $chunks[count($chunks) - 1] = ["<strong>{$wrapped}</strong>"];

        $output = call_user_func_array('array_merge', $chunks);
        $output = join(' ', $output);

        return $output;
    }

    /**
     * Truncates a string of text, tries to avoid splitting words
     */
    public static function truncate(string $text, int $maxChars = 60, string $append = '…')
    {
        $text = trim($text ?? '');

        if (strlen($text) > $maxChars) {
            $text = wordwrap($text, $maxChars);
            $text = explode("\n", $text, 2);
            $text = $text[0] . $append;
        }

        return $text;
    }
}
