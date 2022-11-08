<?php

namespace Solrad\Helpers;

use Parsedown;

class Markdown
{
    /**
     * Strips all markdown syntax characters from text
     *
     * @param string $text          The Markdown text
     * @param array $options        An array of options
     * @return string|null          The plain text result, or null on error
     */
    public static function strip(string $text, array $options = [])
    {
        $options = self::prepareStripOptions($options);

        try {
            // Remove horizontal rules (stripListHeaders conflict with this rule, which is why it has been moved to the top)
            $output = preg_replace('/^(-\s*?|\*\s*?|_\s*?){3,}\s*$/m', '', $text);

            if ($options['stripListLeaders']) {
                $output = preg_replace('/^([\s\t]*)([\*\-\+]|\d+\.)\s+/m', trim(($options['listUnicodeChar'] ?? '') . ' $1'), $output);
            }

            if ($options['gfm']) {
                /* Header */
                $output = preg_replace('/\n={2,}/', "\n", $output);
                /* Fenced Code blocks */
                $output = preg_replace('/~{3}.*\n/', '', $output);
                /* Strike through */
                $output = preg_replace('/~~/', '', $output);
                /* Fenced codeblocks */
                $output = preg_replace('/`{3}.*\n/', '', $output);
            }

            /* Remove HTML tags */
            $output = preg_replace('/<[^>]*>/', '', $output);
            /* Remove setext-style headers */
            $output = preg_replace('/^[=\-]{2,}\s*$/', '', $output);
            /* Remove footnotes */
            $output = preg_replace('/\[\^.+?\](\: .*?$)?/', '', $output);
            $output = preg_replace('/\s{0,2}\[.*?\]: .*?$/', '', $output);
            /* Remove images */
            $output = preg_replace('/\!\[(.*?)\][\[\(].*?[\]\)]/', $options['useImgAltText'] ? '$1' : '', $output);
            /* Remove inline links */
            $output = preg_replace('/\[(.*?)\][\[\(].*?[\]\)]/', '$1', $output);
            /* Remove blockquotes */
            $output = preg_replace('/^\s{0,3}>\s?/', '', $output);
            /* Remove blockquotes */
            $output = preg_replace('/^\s{1,2}\[(.*?)\]: (\S+)( ".*?")?\s*$/', '', $output);
            /* Remove blockquotes */
            $output = preg_replace('/^(\n)?\s{0,}#{1,6}\s+| {0,}(\n)?\s{0,}#{0,} {0,}(\n)?\s{0,}$/m', '$1$2$3', $output);
            /* Remove emphasis (repeat the line to remove double emphasis) */
            $output = preg_replace('/([\*_]{1,3})(\S.*?\S{0,1})\1/', '$2', $output);
            $output = preg_replace('/([\*_]{1,3})(\S.*?\S{0,1})\1/', '$2', $output);
            /* Remove code blocks */
            $output = preg_replace('/(`{3,})(.*?)\1/m', '$2', $output);
            /* Remove inline code */
            $output = preg_replace('/`(.+?)`/', '$1', $output);
            /* Replace two or more newlines with exactly two? Not entirely sure this belongs here... */
            $output = preg_replace('/\n{2,}/', "\n\n", $output);
        } catch (\Throwable $th) {
            return null;
        }

        return $output;
    }

    /**
     * Parses the provided markdown text to html using Parsedown
     *
     * @param string $text      The markdown text
     * @return string|null      The html string
     */
    public static function parse(string $text, bool $safe = true)
    {
        $parser = new Parsedown();
        $parser->setSafeMode($safe);
        return $parser->text($text);
    }

    public static function prepareStripOptions(array $options)
    {
        $defaultValues = [
            'stripListLeaders' => true ,    // strip list leaders (default: true)
            'listUnicodeChar' => '-',     // char to insert instead of stripped list leaders (default: '')
            'gfm' => true,                // support GitHub-Flavored Markdown (default: true)
            'useImgAltText' => true      // replace images with alt-text, if present (default: true)
        ];

        return array_merge($defaultValues, $options);
    }
}
