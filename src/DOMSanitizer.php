<?php
namespace Rhukster\DomSanitizer;

class DOMSanitizer
{
    const HTML = 1;
    const SVG = 2;
    const MATHML = 3;

    const EXTERNAL_URL = "/url\(\s*('|\")\s*(ftp:\/\/|http:\/\/|https:\/\/|\/\/)/i";
    const JAVASCRIPT_ATTR = "/javascript\:/i";
    const SNEAKY_ONLOAD = "/data:.*onload=/i";

    private static $root = ['html', 'body'];
    private static $html = ['a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'big', 'blink', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'content', 'data', 'datalist', 'dd', 'decorator', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'element', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meter', 'nav', 'nobr', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'select', 'shadow', 'small', 'source', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr'];
    private static $svg = ['svg', 'a', 'altglyph', 'altglyphdef', 'altglyphitem', 'animatecolor', 'animatemotion', 'animatetransform', 'circle', 'clippath', 'defs', 'desc', 'ellipse', 'filter', 'font', 'g', 'glyph', 'glyphref', 'hkern', 'image', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'mpath', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'style', 'switch', 'symbol', 'text', 'textpath', 'title', 'tref', 'tspan', 'view', 'vkern'];
    private static $svg_filters = ['feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix', 'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feFlood', 'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feMerge', 'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting', 'feSpotLight', 'feTile', 'feTurbulence'];
    private static $svg_disallowed = ['animate', 'color-profile', 'cursor', 'discard', 'fedropshadow', 'feimage', 'font-face', 'font-face-format', 'font-face-name', 'font-face-src', 'font-face-uri', 'foreignobject', 'hatch', 'hatchpath', 'mesh', 'meshgradient', 'meshpatch', 'meshrow', 'missing-glyph', 'script', 'set', 'solidcolor', 'unknown', 'use'];
    private static $math_ml = ['math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover'];
    private static $math_ml_disallowed = ['maction', 'maligngroup', 'malignmark', 'mlongdiv', 'mscarries', 'mscarry', 'msgroup', 'mstack', 'msline', 'msrow', 'semantics', 'annotation', 'annotation-xml', 'mprescripts', 'none'];
    private static $html_attr = ['accept', 'action', 'align', 'alt', 'autocapitalize', 'autocomplete', 'autopictureinpicture', 'autoplay', 'background', 'bgcolor', 'border', 'capture', 'cellpadding', 'cellspacing', 'checked', 'cite', 'class', 'clear', 'color', 'cols', 'colspan', 'controls', 'controlslist', 'coords', 'crossorigin', 'datetime', 'decoding', 'default', 'dir', 'disabled', 'disablepictureinpicture', 'disableremoteplayback', 'download', 'draggable', 'enctype', 'enterkeyhint', 'face', 'for', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'id', 'inputmode', 'integrity', 'ismap', 'kind', 'label', 'lang', 'list', 'loading', 'loop', 'low', 'max', 'maxlength', 'media', 'method', 'min', 'minlength', 'multiple', 'muted', 'name', 'noshade', 'novalidate', 'nowrap', 'open', 'optimum', 'pattern', 'placeholder', 'playsinline', 'poster', 'preload', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'rev', 'reversed', 'role', 'rows', 'rowspan', 'spellcheck', 'scope', 'selected', 'shape', 'size', 'sizes', 'span', 'srclang', 'start', 'src', 'srcset', 'step', 'style', 'summary', 'tabindex', 'title', 'translate', 'type', 'usemap', 'valign', 'value', 'width', 'xmlns', 'slot'];
    private static $svg_attr = ['accent-height', 'accumulate', 'additive', 'alignment-baseline', 'ascent', 'attributename', 'attributetype', 'azimuth', 'basefrequency', 'baseline-shift', 'begin', 'bias', 'by', 'class', 'clip', 'clippathunits', 'clip-path', 'clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cx', 'cy', 'd', 'dx', 'dy', 'diffuseconstant', 'direction', 'display', 'divisor', 'dur', 'edgemode', 'elevation', 'end', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'filterunits', 'flood-color', 'flood-opacity', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'fx', 'fy', 'g1', 'g2', 'glyph-name', 'glyphref', 'gradientunits', 'gradienttransform', 'height', 'href', 'id', 'image-rendering', 'in', 'in2', 'k', 'k1', 'k2', 'k3', 'k4', 'kerning', 'keypoints', 'keysplines', 'keytimes', 'lang', 'lengthadjust', 'letter-spacing', 'kernelmatrix', 'kernelunitlength', 'lighting-color', 'local', 'marker-end', 'marker-mid', 'marker-start', 'markerheight', 'markerunits', 'markerwidth', 'maskcontentunits', 'maskunits', 'max', 'mask', 'media', 'method', 'mode', 'min', 'name', 'numoctaves', 'offset', 'operator', 'opacity', 'order', 'orient', 'orientation', 'origin', 'overflow', 'paint-order', 'path', 'pathlength', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'preservealpha', 'preserveaspectratio', 'primitiveunits', 'r', 'rx', 'ry', 'radius', 'refx', 'refy', 'repeatcount', 'repeatdur', 'restart', 'result', 'rotate', 'scale', 'seed', 'shape-rendering', 'specularconstant', 'specularexponent', 'spreadmethod', 'startoffset', 'stddeviation', 'stitchtiles', 'stop-color', 'stop-opacity', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke', 'stroke-width', 'style', 'surfacescale', 'systemlanguage', 'tabindex', 'targetx', 'targety', 'transform', 'text-anchor', 'text-decoration', 'text-rendering', 'textlength', 'type', 'u1', 'u2', 'unicode', 'values', 'viewbox', 'visibility', 'version', 'vert-adv-y', 'vert-origin-x', 'vert-origin-y', 'width', 'word-spacing', 'wrap', 'writing-mode', 'xchannelselector', 'ychannelselector', 'x', 'x1', 'x2', 'xmlns', 'xlink:href', 'y', 'y1', 'y2', 'z', 'zoomandpan'];
    private static $math_ml_attr = ['accent', 'accentunder', 'align', 'bevelled', 'close', 'columnsalign', 'columnlines', 'columnspan', 'denomalign', 'depth', 'dir', 'display', 'displaystyle', 'encoding', 'fence', 'frame', 'height', 'href', 'id', 'largeop', 'length', 'linethickness', 'lspace', 'lquote', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'movablelimits', 'notation', 'numalign', 'open', 'rowalign', 'rowlines', 'rowspacing', 'rowspan', 'rspace', 'rquote', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier', 'selection', 'separator', 'separators', 'stretchy', 'subscriptshift', 'supscriptshift', 'symmetric', 'voffset', 'width', 'xmlns'];
    private static $special_cases = ['data-', 'aria-'];

    protected $allowed_tags = [];
    protected $allowed_attributes = [];
    protected $disallowed_tags = [];
    protected $disallowed_attributes = [];

    public function __construct(int $type = self::HTML)
    {
        switch ($type) {
            case self::SVG:
                $this->allowed_tags = array_unique(array_merge(self::$root, self::$svg, self::$svg_filters));
                $this->allowed_attributes = self::$svg_attr;
                $this->disallowed_tags = self::$svg_disallowed;
                break;
            case self::MATHML:
                $this->allowed_tags = array_unique(array_merge(self::$root, self::$math_ml));
                $this->allowed_attributes = self::$math_ml_attr;
                $this->disallowed_tags = self::$math_ml_disallowed;
                break;
            default:
                $this->allowed_tags = array_unique(array_merge(self::$root, self::$html, self::$svg, self::$svg_filters, self::$math_ml));
                $this->allowed_attributes = array_unique(array_merge(self::$html_attr, self::$svg_attr, self::$math_ml_attr));
                $this->disallowed_tags = array_unique(array_merge(self::$svg_disallowed, self::$math_ml_disallowed));
        }
    }

    public function sanitize(string $dom_content, bool $remove_namespaces = false): string
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors();

        if ($remove_namespaces) {
            $dom_content = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $dom_content);
        }

        $document = new \DOMDocument();
        $document->loadHTML($dom_content);

        $tags = array_diff($this->allowed_tags, $this->disallowed_tags);
        $attributes = array_diff($this->allowed_attributes, $this->disallowed_attributes);
        $elements = $document->getElementsByTagName('*');

        for($i = $elements->length; --$i >= 0;) {
            $element = $elements->item($i);
            $tag_name = $element->tagName;
            if(in_array(strtolower($tag_name), $tags)) {
                for($j = $element->attributes->length; --$j >= 0;) {
                    $attr_name = $element->attributes->item($j)->name;
                    $attr_value = $element->attributes->item($j)->textContent;
                    if((!in_array(strtolower($attr_name), $attributes) && !$this->isSpecialCase($attr_name)) ||
                        $this->isExternalUrl($attr_value) ||
                        $this->isJavascriptAttribute($attr_name, $attr_value) ||
                        $this->isSneakyOnload($attr_name, $attr_value)) {
                        $element->removeAttribute($attr_name);
                    }
                }
            } else {
                $element->parentNode->removeChild($element);
            }
        }

        return preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $document->saveHTML());
    }

    protected function isSpecialCase($attr_name)
    {
        return $this->startsWith($attr_name, self::$special_cases);
    }

    protected function isExternalUrl($attr_value)
    {
        return preg_match(self::EXTERNAL_URL, $attr_value);
    }

    protected function isJavascriptAttribute($attr_name, $attr_value)
    {
        return in_array($attr_name, ['href','xlink:href']) && preg_match(self::JAVASCRIPT_ATTR, $attr_value);
    }

    protected function isSneakyOnload($attr_name, $attr_value)
    {
        return in_array($attr_name, ['href','xlink:href']) && preg_match(self::SNEAKY_ONLOAD, $attr_value);
    }

    public function addAllowedTags(array $allowed_tags)
    {
        $this->allowed_tags = array_unique(array_merge(array_map('strtolower', $allowed_tags), $this->allowed_tags));
    }

    public function addAllowedAttributes(array $allowed_attributes)
    {
        $this->allowed_attributes = array_unique(array_merge(array_map('strtolower', $allowed_attributes), $this->allowed_attributes));
    }

    public function addDisallowedTags(array $disallowed_tags)
    {
        $this->disallowed_tags = array_unique(array_merge(array_map('strtolower', $disallowed_tags), $this->disallowed_tags));
    }

    public function addDisallowedAttributes(array $disallowed_attributes)
    {
        $this->disallowed_attributes = array_unique(array_merge(array_map('strtolower', $disallowed_attributes), $this->disallowed_attributes));
    }

    /**
     * @return array
     */
    public function getAllowedTags(): array
    {
        return $this->allowed_tags;
    }

    /**
     * @param array $allowed_tags
     */
    public function setAllowedTags(array $allowed_tags): void
    {
        $this->allowed_tags = $allowed_tags;
    }

    /**
     * @return array|string[]
     */
    public function getAllowedAttributes(): array
    {
        return $this->allowed_attributes;
    }

    /**
     * @param array|string[] $allowed_attributes
     */
    public function setAllowedAttributes(array $allowed_attributes): void
    {
        $this->allowed_attributes = $allowed_attributes;
    }

    /**
     * @return array|string[]
     */
    public function getDisallowedTags(): array
    {
        return $this->disallowed_tags;
    }

    /**
     * @param array|string[] $disallowed_tags
     */
    public function setDisallowedTags(array $disallowed_tags): void
    {
        $this->disallowed_tags = $disallowed_tags;
    }

    /**
     * @return mixed
     */
    public function getDisallowedAttributes()
    {
        return $this->disallowed_attributes;
    }

    /**
     * @param mixed $disallowed_attributes
     */
    public function setDisallowedAttributes($disallowed_attributes): void
    {
        $this->disallowed_attributes = $disallowed_attributes;
    }

    private function startsWith(string $haystack, $needle, bool $case_sensitive = true): bool
    {
        $status = false;
        $compare_func = $case_sensitive ? 'mb_strpos' : 'mb_stripos';
        foreach ((array)$needle as $each_needle) {
            $status = $each_needle === '' || $compare_func($haystack, $each_needle) === 0;
            if ($status) {
                break;
            }
        }
        return $status;
    }

}
