<?php
/**
 * Content utils.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Preserve\Classes\Utils;

use WebSharks\WpSharks\Preserve\Classes;
use WebSharks\WpSharks\Preserve\Interfaces;
use WebSharks\WpSharks\Preserve\Traits;
#
use WebSharks\WpSharks\Preserve\Classes\AppFacades as a;
use WebSharks\WpSharks\Preserve\Classes\SCoreFacades as s;
use WebSharks\WpSharks\Preserve\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Content utils.
 *
 * @since 160722.57589 Content utils.
 */
class Content extends SCoreClasses\SCore\Base\Core
{
    /**
     * Tokenizers.
     *
     * @since 160722.57589 Content utils.
     *
     * @var array|null
     */
    protected $Tokenizers;

    /**
     * Preserve pre/code/samp.
     *
     * @since 160722.57589 Content utils.
     *
     * @param string|scalar $content Content.
     *
     * @return string $content Filtered content.
     */
    public function onTheContentPreserve($content): string
    {
        $content = (string) $content;

        if (mb_strpos($content, '[') === false) {
            $this->Tokenizers[] = null;
            return $content; // Nothing to do.
        } elseif (!preg_match('/\<(?:pre|code|samp)/ui', $content)) {
            $this->Tokenizers[] = null;
            return $content; // Nothing to do.
        }
        $Tokenizer          = c::tokenize($content, ['pre', 'code', 'samp']);
        $this->Tokenizers[] = $Tokenizer; // End of stack.
        return $content     = $Tokenizer->getString();
    }

    /**
     * Restore pre/code/samp.
     *
     * @since 160722.57589 Content utils.
     *
     * @param string|scalar $content Content.
     *
     * @return string $content Filtered content.
     */
    public function onTheContentRestore($content): string
    {
        $content = (string) $content;

        if (!$this->Tokenizers) {
            debug(0, c::issue(vars(), 'Missing tokenizers.'));
            return $content; // Nothing to do.
        } elseif (!($Tokenizer = array_pop($this->Tokenizers))) {
            return $content; // Nothing to do.
        } // Pops last tokenizer off the stack ↑ also.

        $Tokenizer->setString($content);
        return $content = $Tokenizer->restoreGetString();
    }
}
