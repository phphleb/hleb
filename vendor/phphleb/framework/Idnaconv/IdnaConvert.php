<?php
declare(strict_types=1);

// license MIT

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

namespace Hleb\Idnaconv;

/**
 * Encode/decode Internationalized Domain Names.
 *
 * The class allows to convert internationalized domain names
 * (see RFC 3490 for details) as they can be used with various registries worldwide
 * to be translated between their original (localized) form and their encoded form
 * as it will be used in the DNS (Domain Name System).
 *
 * The class provides two public methods, encode() and decode(), which do exactly
 * what you would expect them to do. You are allowed to use complete domain names,
 * simple strings and complete email addresses as well. That means, that you might
 * use any of the following notations:
 *
 * - www.n�rgler.com
 * - xn--nrgler-wxa
 * - xn--brse-5qa.xn--knrz-1ra.info
 *
 * Unicode input might be given as either UTF-8 string, UCS-4 string or UCS-4 array.
 * Unicode output is available in the same formats.
 * You can select your preferred format via {@link setParamter()}.
 *
 * ACE input and output is always expected to be ASCII.
 *
 * @author  Matthias Sommerfeld <mso@phlylabs.de>
 *
 * @contributor Foma Tuturov (update to PHP7+ )
 */
class IdnaConvert
{
    // NP See below

    // Internal settings, do not mess with them
    protected $punycodePrefix = 'xn--';
    protected $invalidUcs = 0x80000000;
    protected $maxUcs = 0x10FFFF;
    protected $base = 36;
    protected $tMin = 1;
    protected $tMax = 26;
    protected $skew = 38;
    protected $damp = 700;
    protected $initialBias = 72;
    protected $initialN = 0x80;
    protected $sBase = 0xAC00;
    protected $lBase = 0x1100;
    protected $vBase = 0x1161;
    protected $tBase = 0x11A7;
    protected $lCount = 19;
    protected $vCount = 21;
    protected $tCount = 28;
    protected $nCount = 588;   // vCount * tCount
    protected $sCount = 11172; // lCount * tCount * vCount
    protected $error = false;

    protected static $mbStringOverload = null;

    // See {@link setParameter()} for details of how to change the following
    // settings from within your script / application
    protected $apiEncoding = 'utf8';   // Default input charset is UTF-8
    protected $allowOverlong = false;  // Overlong UTF-8 encodings are forbidden
    protected $strictMode = false;     // Behave strict or not
    protected $idnVersion = 2003;      // Can be either 2003 (old, default) or 2008

    protected $sLast;
    protected static $safeChar = null;
    protected static $safeMode = null;

    /**
     * the constructor
     *
     * @param array $options
     * @since 0.5.2
     */
    public function __construct(array $options = ['idn_version' => 2008]) {
        $this->sLast = $this->sBase + $this->lCount * $this->vCount * $this->tCount;
        // If parameters are given, pass these to the respective method
        if (is_array($options)) {
            $this->setParameter($options);
        }

        // populate mbstring overloading cache if not set
        if (self::$mbStringOverload === null) {
            self::$mbStringOverload = (extension_loaded('mbstring')
                && (ini_get('mbstring.func_overload') & 0x02) === 0x02);
        }
    }

    /**
     * Sets a new option value. Available options and values:
     * [encoding - Use either UTF-8, UCS4 as array or UCS4 as string as input ('utf8' for UTF-8,
     *         'ucs4_string' and 'ucs4_array' respectively for UCS4); The output is always UTF-8]
     * [overlong - Unicode does not allow unnecessarily long encodings of chars,
     *             to allow this, set this parameter to true, else to false;
     *             default is false.]
     * [strict - true: strict mode, good for registration purposes - Causes errors
     *           on failures; false: loose mode, ideal for "wildlife" applications
     *           by silently ignoring errors and returning the original input instead
     *
     * @param mixed $option Parameter to set (string: single parameter)
     * @param string|bool $value Value to use (if parameter 1 is a string)
     * @return   boolean   true on success, false otherwise
     */
    public function setParameter($option, $value = false) {
        if (!is_array($option)) {
            $option = [$option => $value];
        }
        foreach ($option as $k => $v) {
            switch ($k) {
                case 'encoding':
                    switch ($v) {
                        case 'utf8':
                        case 'ucs4_string':
                        case 'ucs4_array':
                            $this->apiEncoding = $v;
                            break;
                        default:
                            $this->useError('Set Parameter: Unknown parameter ' . $v . ' for option ' . $k);
                            return false;
                    }
                    break;
                case 'overlong':
                    $this->allowOverlong = ($v) ? true : false;
                    break;
                case 'strict':
                    $this->strictMode = ($v) ? true : false;
                    break;
                case 'idn_version':
                    if (in_array($v, ['2003', '2008'])) {
                        $this->idnVersion = $v;
                    } else {
                        $this->useError('Set Parameter: Unknown parameter ' . $v . ' for option ' . $k);
                    }
                    break;
                case 'encode_german_sz': // Deprecated
                    if (!$v) {
                        self::$NP['replacemaps'][0xDF] = [0x73, 0x73];
                    } else {
                        unset(self::$NP['replacemaps'][0xDF]);
                    }
                    break;
                default:
                    $this->useError('Set Parameter: Unknown option ' . $k);
                    return false;
            }
        }
        return true;
    }

    /**
     * Decode a given ACE domain name
     * @param string $input Domain name (ACE string)
     * [@param string|boolean $oneTimeEncoding Desired output encoding, see {@link setParameter}]
     * @return   string|array|false   Decoded Domain name (UTF-8 or UCS-4)
     */
    public function decode(string $input, $oneTimeEncoding = false) {
        // Optionally set
        if ($oneTimeEncoding) {
            switch ($oneTimeEncoding) {
                case 'utf8':
                case 'ucs4_string':
                case 'ucs4_array':
                    break;
                default:
                    $this->useError('Unknown encoding ' . $oneTimeEncoding);
                    return false;
            }
        }
        // Make sure to drop any newline characters around
        $input = trim($input);

        // Negotiate input and try to determine, whether it is a plain string,
        // an email address or something like a complete URL
        if (strpos($input, '@')) { // Maybe it is an email address
            // No no in strict mode
            if ($this->strictMode) {
                $this->useError('Only simple domain name parts can be handled in strict mode');
                return false;
            }
            list ($emailPref, $input) = explode('@', $input, 2);
            $arr = explode('.', $input);
            foreach ($arr as $k => $v) {
                if (preg_match('!^' . preg_quote($this->punycodePrefix, '!') . '!', $v)) {
                    $conv = $this->useDecode($v);
                    if (!empty($conv)) $arr[$k] = $conv;
                }
            }
            $input = join('.', $arr);
            $arr = explode('.', $emailPref);
            foreach ($arr as $k => $v) {
                if (preg_match('!^' . preg_quote($this->punycodePrefix, '!') . '!', $v)) {
                    $conv = $this->useDecode($v);
                    if (!empty($conv)) $arr[$k] = $conv;
                }
            }
            $emailPref = join('.', $arr);
            $return = $emailPref . '@' . $input;
        } elseif (preg_match('![:\./]!', $input)) { // Or a complete domain name (with or without paths / parameters)
            // No no in strict mode
            if ($this->strictMode) {
                $this->useError('Only simple domain name parts can be handled in strict mode');
                return false;
            }
            $parsed = parse_url($input);
            if (isset($parsed['host'])) {
                $arr = explode('.', $parsed['host']);
                foreach ($arr as $k => $v) {
                    $conv = $this->useDecode($v);
                    if (!empty($conv)) $arr[$k] = $conv;
                }
                $parsed['host'] = join('.', $arr);
                $return =
                    (empty($parsed['scheme']) ? '' : $parsed['scheme'] . (strtolower($parsed['scheme']) == 'mailto' ? ':' : '://'))
                    . (empty($parsed['user']) ? '' : $parsed['user'] . (empty($parsed['pass']) ? '' : ':' . $parsed['pass']) . '@')
                    . $parsed['host']
                    . (empty($parsed['port']) ? '' : ':' . $parsed['port'])
                    . (empty($parsed['path']) ? '' : $parsed['path'])
                    . (empty($parsed['query']) ? '' : '?' . $parsed['query'])
                    . (empty($parsed['fragment']) ? '' : '#' . $parsed['fragment']);
            } else { // parse_url seems to have failed, try without it
                $arr = explode('.', $input);
                foreach ($arr as $k => $v) {
                    $conv = $this->useDecode($v);
                    $arr[$k] = ($conv) ? $conv : $v;
                }
                $return = join('.', $arr);
            }
        } else { // Otherwise we consider it being a pure domain name string
            $return = $this->useDecode($input);
            if (empty($return)) $return = $input;
        }
        // The output is UTF-8 by default, other output formats need conversion here
        // If one time encoding is given, use this, else the objects property
        switch (($oneTimeEncoding) ? $oneTimeEncoding : $this->apiEncoding) {
            case 'utf8':
                return $return;
                break;
            case 'ucs4_string':
                return $this->ucs4ToUcs4String($this->utf8ToUcs4($return) ?? []);
                break;
            case 'ucs4_array':
                return $this->utf8ToUcs4($return ?? '');
                break;
            default:
                $this->useError('Unsupported output format');
                return false;
        }
    }

    /**
     * Encode a given UTF-8 domain name
     * @param string $decoded Domain name (UTF-8 or UCS-4)
     * [@param string $oneTimeEncoding Desired input encoding, see {@link setParameter}]
     * @return   string|false   Encoded Domain name (ACE string)
     */
    public function encode(string $decoded, $oneTimeEncoding = '') {
        // Forcing conversion of input to UCS4 array
        // If one time encoding is given, use this, else the objects property
        switch (!empty($oneTimeEncoding) ? $oneTimeEncoding : $this->apiEncoding) {
            case 'utf8':
                $decoded = $this->utf8ToUcs4($decoded);
                break;
            case 'ucs4_string':
                // To string
                $decoded = $this->ucs4StringToUcs4($decoded);
            case 'ucs4_array':
                break;
            default:
                $this->useError('Unsupported input format: ' . ($oneTimeEncoding ? $oneTimeEncoding : $this->apiEncoding));
                return false;
        }

        // No input, no output, what else did you expect?
        if (empty($decoded)) return '';

        // Anchors for iteration
        $lastBegin = 0;
        // Output string
        $output = '';
        foreach ($decoded as $k => $v) {
            // Make sure to use just the plain dot
            switch ($v) {
                case 0x3002:
                case 0xFF0E:
                case 0xFF61:
                    $decoded[$k] = 0x2E;
                // Right, no break here, the above are converted to dots anyway
                // Stumbling across an anchoring character
                case 0x2E:
                case 0x2F:
                case 0x3A:
                case 0x3F:
                case 0x40:
                    // Neither email addresses nor URLs allowed in strict mode
                    if ($this->strictMode) {
                        $this->useError('Neither email addresses nor URLs are allowed in strict mode.');
                        return false;
                    } else {
                        // Skip first char
                        if ($k) {
                            $sliced = array_slice($decoded, $lastBegin, (($k) - $lastBegin));
                            $encoded = $this->useEncode(is_array($sliced) ? $sliced : []);
                            if (!empty($encoded)) {
                                $output .= $encoded;
                            } else {
                                $output .= $this->ucs4ToUtf8(array_slice($decoded, $lastBegin, (($k) - $lastBegin)));
                            }
                            $output .= chr($decoded[$k]);
                        }
                        $lastBegin = $k + 1;
                    }
                    break;
            }
        }
        $decoded = is_array($decoded) ? $decoded : [];
        // Catch the rest of the string
        if ($lastBegin) {
            $inp_len = sizeof($decoded);
            $encoded = $this->useEncode(array_slice($decoded, $lastBegin, (($inp_len) - $lastBegin)));
            if (!empty($encoded)) {
                $output .= $encoded;
            } else {
                $output .= $this->ucs4ToUtf8(array_slice($decoded, $lastBegin, (($inp_len) - $lastBegin)));
            }
            return $output;
        } else {
            if ($output = $this->useEncode($decoded)) {
                return $output;
            } else {
                return $this->ucs4ToUtf8($decoded);
            }
        }
    }

    /**
     * Removes a weakness of encode(), which cannot properly handle URIs but instead encodes their
     * path or query components, too.
     * @param string $uri Expects the URI as a UTF-8 (or ASCII) string
     * @return  string|bool  The URI encoded to Punycode, everything but the host component is left alone
     * @since 0.6.4
     */
    public function encodeUri(string $uri) {
        $parsed = parse_url($uri);
        if (!isset($parsed['host'])) {
            $this->useError('The given string does not look like a URI');
            return false;
        }
        $arr = explode('.', $parsed['host']);
        foreach ($arr as $k => $v) {
            $conv = $this->encode($v, 'utf8');
            if (!empty($conv)) $arr[$k] = $conv;
        }
        $parsed['host'] = join('.', $arr);
        $return =
            (empty($parsed['scheme']) ? '' : $parsed['scheme'] . (strtolower($parsed['scheme']) == 'mailto' ? ':' : '://')) .
            (empty($parsed['user']) ? '' : $parsed['user'] . (empty($parsed['pass']) ? '' : ':' . $parsed['pass']) . '@') .
            $parsed['host'] .
            (empty($parsed['port']) ? '' : ':' . $parsed['port']) .
            (empty($parsed['path']) ? '' : $parsed['path']) .
            (empty($parsed['query']) ? '' : '?' . $parsed['query']) .
            (empty($parsed['fragment']) ? '' : '#' . $parsed['fragment']);
        return $return;
    }

    /**
     * Use this method to get the last error ocurred
     * @param void
     * @return   string|boolean   The last error, that occured
     */
    public function getLastError() {
        return $this->error;
    }

    /**
     * The actual decoding algorithm
     * @param string $encoded
     * @return mixed
     */
    protected function useDecode($encoded) {
        $decoded = [];
        // find the Punycode prefix
        if (!preg_match('!^' . preg_quote($this->punycodePrefix, '!') . '!', $encoded)) {
            $this->useError('This is not a punycode string');
            return false;
        }
        $encodeTest = preg_replace('!^' . preg_quote($this->punycodePrefix, '!') . '!', '', $encoded);
        // If nothing left after removing the prefix, it is hopeless
        if (!$encodeTest) {
            $this->useError('The given encoded string was empty');
            return false;
        }
        // Find last occurence of the delimiter
        $delimPos = strrpos($encoded, '-');
        if ($delimPos > self::byteLength($this->punycodePrefix)) {
            for ($k = self::byteLength($this->punycodePrefix); $k < $delimPos; ++$k) {
                $decoded[] = ord($encoded[$k]);
            }
        }
        $decoLen = count($decoded);
        $encoLen = self::byteLength($encoded);

        // Wandering through the strings; init
        $isFirst = true;
        $bias = $this->initialBias;
        $idx = 0;
        $char = $this->initialN;

        for ($encoIdx = ($delimPos) ? ($delimPos + 1) : 0; $encoIdx < $encoLen; ++$decoLen) {
            for ($old_idx = $idx, $w = 1, $k = $this->base; 1; $k += $this->base) {
                $digit = $this->useDecodeDigit($encoded[$encoIdx++]);
                $idx += $digit * $w;
                $t = ($k <= $bias) ? $this->tMin :
                    (($k >= $bias + $this->tMax) ? $this->tMax : ($k - $bias));
                if ($digit < $t) break;
                $w = (int)($w * ($this->base - $t));
            }
            $bias = $this->useAdapt($idx - $old_idx, $decoLen + 1, intval($isFirst));
            $isFirst = false;
            $char += (int)($idx / ($decoLen + 1));
            $idx %= ($decoLen + 1);
            if ($decoLen > 0) {
                // Make room for the decoded char
                for ($i = $decoLen; $i > $idx; $i--) $decoded[$i] = $decoded[($i - 1)];
            }
            $decoded[$idx++] = $char;
        }
        return $this->ucs4ToUtf8($decoded);
    }

    /**
     * The actual encoding algorithm
     * @param string|array $decoded
     * @return mixed
     */
    protected function useEncode($decoded) {
        // We cannot encode a domain name containing the Punycode prefix
        $extract = self::byteLength($this->punycodePrefix);
        $checkPref = $this->utf8ToUcs4($this->punycodePrefix);
        $checkDeco = array_slice(is_array($decoded) ? $decoded : [], 0, $extract);

        if ($checkPref == $checkDeco) {
            $this->useError('This is already a punycode string');
            return false;
        }
        // We will not try to encode strings consisting of basic code points only
        $encodable = false;
        if (is_array($decoded)) {
            foreach ($decoded as $k => $v) {
                if ($v > 0x7a) {
                    $encodable = true;
                    break;
                }
            }
        }
        if (!$encodable || !is_array($decoded)) {
            $this->useError('The given string does not contain encodable chars');
            return false;
        }
        // Do NAMEPREP
        $decoded = $this->useNameprep($decoded);
        if (empty($decoded) || !is_array($decoded)) return false; // NAMEPREP failed
        $decoLen = count($decoded);
        if (!$decoLen) return false; // Empty array
        $codecount = 0; // How many chars have been consumed
        $encoded = '';
        // Copy all basic code points to output
        for ($i = 0; $i < $decoLen; ++$i) {
            $test = $decoded[$i];
            // Will match [-0-9a-zA-Z]
            if ((0x2F < $test && $test < 0x40) || (0x40 < $test && $test < 0x5B)
                || (0x60 < $test && $test <= 0x7B) || (0x2D == $test)) {
                $encoded .= chr($decoded[$i]);
                $codecount++;
            }
        }
        if ($codecount == $decoLen) return $encoded; // All codepoints were basic ones

        // Start with the prefix; copy it to output
        $encoded = $this->punycodePrefix . $encoded;
        // If we have basic code points in output, add an hyphen to the end
        if ($codecount) $encoded .= '-';
        // Now find and encode all non-basic code points
        $isFirst = true;
        $curCode = $this->initialN;
        $bias = $this->initialBias;
        $delta = 0;
        while ($codecount < $decoLen) {
            // Find the smallest code point >= the current code point and
            // remember the last ouccrence of it in the input
            for ($i = 0, $nextCode = $this->maxUcs; $i < $decoLen; $i++) {
                if ($decoded[$i] >= $curCode && $decoded[$i] <= $nextCode) {
                    $nextCode = $decoded[$i];
                }
            }
            $delta += ($nextCode - $curCode) * ($codecount + 1);
            $curCode = $nextCode;

            // Scan input again and encode all characters whose code point is $cur_code
            for ($i = 0; $i < $decoLen; $i++) {
                if ($decoded[$i] < $curCode) {
                    $delta++;
                } elseif ($decoded[$i] == $curCode) {
                    for ($q = $delta, $k = $this->base; 1; $k += $this->base) {
                        $t = ($k <= $bias) ? $this->tMin :
                            (($k >= $bias + $this->tMax) ? $this->tMax : $k - $bias);
                        if ($q < $t) break;
                        $encoded .= $this->useEncodeDigit(intval($t + (($q - $t) % ($this->base - $t)))); //v0.4.5 Changed from ceil() to intval()
                        $q = (int)(($q - $t) / ($this->base - $t));
                    }
                    $encoded .= $this->useEncodeDigit($q);
                    $bias = $this->useAdapt($delta, $codecount + 1, intval($isFirst));
                    $codecount++;
                    $delta = 0;
                    $isFirst = false;
                }
            }
            $delta++;
            $curCode++;
        }
        return $encoded;
    }

    /**
     * Adapt the bias according to the current code point and position
     * @param int $delta
     * @param int $npoints
     * @param int $isFirst
     * @return int
     */
    protected function useAdapt($delta, $npoints, $isFirst) {
        $delta = intval($isFirst ? ($delta / $this->damp) : ($delta / 2));
        $delta += intval($delta / $npoints);
        for ($k = 0; $delta > (($this->base - $this->tMin) * $this->tMax) / 2; $k += $this->base) {
            $delta = intval($delta / ($this->base - $this->tMin));
        }
        return intval($k + ($this->base - $this->tMin + 1) * $delta / ($delta + $this->skew));
    }

    /**
     * Encoding a certain digit
     * @param int $d
     * @return string
     */
    protected function useEncodeDigit($d) {
        return chr($d + 22 + 75 * ($d < 26));
    }

    /**
     * Decode a certain digit
     * @param int $cp
     * @return int
     */
    protected function useDecodeDigit($cp) {
        $cp = ord($cp);
        return ($cp - 48 < 10) ? $cp - 22 : (($cp - 65 < 26) ? $cp - 65 : (($cp - 97 < 26) ? $cp - 97 : $this->base));
    }

    /**
     * Internal error handling method
     * @param string $error
     */
    protected function useError(string $error = '') {
        $this->error = $error;
    }

    /**
     * Do Nameprep according to RFC3491 and RFC3454
     * @param array $input Unicode Characters
     * @return   string|array|integer[]|bool   Unicode Characters, Nameprep'd
     */
    protected function useNameprep($input) {
        $output = [];
        $error = false;
        //
        // Mapping
        // Walking through the input array, performing the required steps on each of
        // the input chars and putting the result into the output array
        // While mapping required chars we apply the cannonical ordering
        foreach ($input as $v) {
            // Map to nothing == skip that code point
            if (in_array($v, self::$NP['map_nothing'])) continue;
            // Try to find prohibited input
            if (in_array($v, self::$NP['prohibit']) || in_array($v, self::$NP['general_prohibited'])) {
                $this->useError('NAMEPREP: Prohibited input U+' . sprintf('%08X', $v));
                return false;
            }
            foreach (self::$NP['prohibit_ranges'] as $range) {
                if ($range[0] <= $v && $v <= $range[1]) {
                    $this->useError('NAMEPREP: Prohibited input U+' . sprintf('%08X', $v));
                    return false;
                }
            }

            if (0xAC00 <= $v && $v <= 0xD7AF) {
                // Hangul syllable decomposition
                foreach ($this->useHangulDecompose($v) as $out) {
                    $output[] = (int)$out;
                }
            } elseif (($this->idnVersion == '2003') && isset(self::$NP['replacemaps'][$v])) {
                // There's a decomposition mapping for that code point
                // Decompositions only in version 2003 (original) of IDNA
                foreach ($this->applyCanonicalOrdering(self::$NP['replacemaps'][$v]) as $out) {
                    $output[] = (int)$out;
                }
            } else {
                $output[] = (int)$v;
            }
        }
        // Before applying any Combining, try to rearrange any Hangul syllables
        $output = $this->useHangulCompose($output);
        //
        // Combine code points
        //
        $lastClass = 0;
        $lastStarter = 0;
        $outLen = count($output);
        for ($i = 0; $i < $outLen; ++$i) {
            $class = $this->getCombiningClass($output[$i]);
            if ((!$lastClass || $lastClass > $class) && $class) {
                // Try to match
                $seqLen = $i - $lastStarter;
                $out = $this->combine(array_slice($output, $lastStarter, $seqLen));
                // On match: Replace the last starter with the composed character and remove
                // the now redundant non-starter(s)
                if (!empty($out) && is_array($out)) {
                    $output[$lastStarter] = $out;
                    if (count($out) != $seqLen) {
                        for ($j = $i + 1; $j < $outLen; ++$j) $output[$j - 1] = $output[$j];
                        unset($output[$outLen]);
                    }
                    // Rewind the for loop by one, since there can be more possible compositions
                    $i--;
                    $outLen--;
                    $lastClass = ($i == $lastStarter) ? 0 : $this->getCombiningClass($output[$i - 1]);
                    continue;
                }
            }
            // The current class is 0
            if (!$class) $lastStarter = $i;
            $lastClass = $class;
        }
        return $output;
    }

    /**
     * Decomposes a Hangul syllable
     * (see http://www.unicode.org/unicode/reports/tr15/#Hangul
     * @param integer $char 32bit UCS4 code point
     * @return   array    Either Hangul Syllable decomposed or original 32bit value as one value array
     */
    protected function useHangulDecompose($char) {
        $sindex = (int)$char - $this->sBase;
        if ($sindex < 0 || $sindex >= $this->sCount) return [$char];
        $result = [];
        $result[] = (int)$this->lBase + $sindex / $this->nCount;
        $result[] = (int)$this->vBase + ($sindex % $this->nCount) / $this->tCount;
        $T = intval($this->tBase + $sindex % $this->tCount);
        if ($T != $this->tBase) $result[] = $T;
        return $result;
    }

    /**
     * Ccomposes a Hangul syllable
     * (see http://www.unicode.org/unicode/reports/tr15/#Hangul
     * @param array $input Decomposed UCS4 sequence
     * @return   array    UCS4 sequence with syllables composed
     */
    protected function useHangulCompose($input) {
        $inp_len = count($input);
        if (!$inp_len) return [];
        $result = [];
        $last = (int)$input[0];
        $result[] = $last; // copy first char from input to output

        for ($i = 1; $i < $inp_len; ++$i) {
            $char = (int)$input[$i];
            $sIndex = $last - $this->sBase;
            $lIndex = $last - $this->lBase;
            $vIndex = $char - $this->vBase;
            $tIndex = $char - $this->tBase;
            // Find out, whether two current characters are LV and T
            if (0 <= $sIndex && $sIndex < $this->sCount && ($sIndex % $this->tCount == 0)
                && 0 <= $tIndex && $tIndex <= $this->tCount) {
                // create syllable of form LVT
                $last += $tIndex;
                $result[(count($result) - 1)] = $last; // reset last
                continue; // discard char
            }
            // Find out, whether two current characters form L and V
            if (0 <= $lIndex && $lIndex < $this->lCount && 0 <= $vIndex && $vIndex < $this->vCount) {
                // create syllable of form LV
                $last = (int)$this->sBase + ($lIndex * $this->vCount + $vIndex) * $this->tCount;
                $result[(count($result) - 1)] = $last; // reset last
                continue; // discard char
            }
            // if neither case was true, just add the character
            $last = $char;
            $result[] = $char;
        }
        return $result;
    }

    /**
     * Returns the combining class of a certain wide char
     * @param integer $char Wide char to check (32bit integer)
     * @return   integer    Combining class if found, else 0
     */
    protected function getCombiningClass($char) {
        return isset(self::$NP['norm_combcls'][$char]) ? self::$NP['norm_combcls'][$char] : 0;
    }

    /**
     * Applies the cannonical ordering of a decomposed UCS4 sequence
     * @param array $input Decomposed UCS4 sequence
     * @return   array      Ordered USC4 sequence
     */
    protected function applyCanonicalOrdering($input) {
        $swap = true;
        $size = count($input);
        while ($swap) {
            $swap = false;
            $last = $this->getCombiningClass(intval($input[0]));
            for ($i = 0; $i < $size - 1; ++$i) {
                $next = $this->getCombiningClass(intval($input[$i + 1]));
                if ($next != 0 && $last > $next) {
                    // Move item leftward until it fits
                    for ($j = $i + 1; $j > 0; --$j) {
                        if ($this->getCombiningClass(intval($input[$j - 1])) <= $next) break;
                        $t = intval($input[$j]);
                        $input[$j] = intval($input[$j - 1]);
                        $input[$j - 1] = $t;
                        $swap = true;
                    }
                    // Reentering the loop looking at the old character again
                    $next = $last;
                }
                $last = $next;
            }
        }
        return $input;
    }

    /**
     * Do composition of a sequence of starter and non-starter
     * @param array $input UCS4 Decomposed sequence
     * @return   array|bool|int|string      Ordered USC4 sequence
     */
    protected function combine($input) {
        $inpLen = count($input);
        foreach (self::$NP['replacemaps'] as $npSrc => $npTarget) {
            if ($npTarget[0] != $input[0]) continue;
            if (count($npTarget) != $inpLen) continue;
            $hit = false;
            foreach ($input as $k2 => $v2) {
                if ($v2 == $npTarget[$k2]) {
                    $hit = true;
                } else {
                    $hit = false;
                    break;
                }
            }
            if ($hit) return $npSrc;
        }
        return false;
    }

    /**
     * This converts an UTF-8 encoded string to its UCS-4 representation
     * By talking about UCS-4 "strings" we mean arrays of 32bit integers representing
     * each of the "chars". This is due to PHP not being able to handle strings with
     * bit depth different from 8. This apllies to the reverse method _ucs4_to_utf8(), too.
     * The following UTF-8 encodings are supported:
     * bytes bits  representation
     * 1        7  0xxxxxxx
     * 2       11  110xxxxx 10xxxxxx
     * 3       16  1110xxxx 10xxxxxx 10xxxxxx
     * 4       21  11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
     * 5       26  111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
     * 6       31  1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
     * Each x represents a bit that can be used to store character data.
     * The five and six byte sequences are part of Annex D of ISO/IEC 10646-1:2000
     * @param string $input
     * @return array|bool
     */
    protected function utf8ToUcs4($input) {
        $output = [];
        $outLen = 0;
        $inpLen = self::byteLength($input);
        $mode = 'next';
        $test = 'none';
        for ($k = 0; $k < $inpLen; ++$k) {
            $v = ord($input[$k]); // Extract byte from input string
            if ($v < 128) { // We found an ASCII char - put into stirng as is
                $output[$outLen] = $v;
                ++$outLen;
                if ('add' == $mode) {
                    $this->useError('Conversion from UTF-8 to UCS-4 failed: malformed input at byte ' . $k);
                    return false;
                }
                continue;
            }
            if ('next' == $mode) { // Try to find the next start byte; determine the width of the Unicode char
                $startByte = $v;
                $mode = 'add';
                $test = 'range';
                if ($v >> 5 == 6) { // &110xxxxx 10xxxxx
                    $nextByte = 0; // Tells, how many times subsequent bitmasks must rotate 6bits to the left
                    $v = ($v - 192) << 6;
                } elseif ($v >> 4 == 14) { // &1110xxxx 10xxxxxx 10xxxxxx
                    $nextByte = 1;
                    $v = ($v - 224) << 12;
                } elseif ($v >> 3 == 30) { // &11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
                    $nextByte = 2;
                    $v = ($v - 240) << 18;
                } elseif ($v >> 2 == 62) { // &111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
                    $nextByte = 3;
                    $v = ($v - 248) << 24;
                } elseif ($v >> 1 == 126) { // &1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
                    $nextByte = 4;
                    $v = ($v - 252) << 30;
                } else {
                    $this->useError('This might be UTF-8, but I don\'t understand it at byte ' . $k);
                    return false;
                }
                if ('add' == $mode) {
                    $output[$outLen] = (int)$v;
                    ++$outLen;
                    continue;
                }
            }
            if ('add' == $mode) {
                if (!$this->allowOverlong && $test == 'range') {
                    $test = 'none';
                    if (($v < 0xA0 && $startByte == 0xE0) || ($v < 0x90 && $startByte == 0xF0) || ($v > 0x8F && $startByte == 0xF4)) {
                        $this->useError('Bogus UTF-8 character detected (out of legal range) at byte ' . $k);
                        return false;
                    }
                }
                if ($v >> 6 == 2) { // Bit mask must be 10xxxxxx
                    $v = ($v - 128) << ($nextByte * 6);
                    $output[($outLen - 1)] += $v;
                    --$nextByte;
                } else {
                    $this->useError('Conversion from UTF-8 to UCS-4 failed: malformed input at byte ' . $k);
                    return false;
                }
                if ($nextByte < 0) {
                    $mode = 'next';
                }
            }
        } // for
        return $output;
    }

    /**
     * Convert UCS-4 string into UTF-8 string
     * See _utf8_to_ucs4() for details
     * @param array $input
     * @return string|bool
     */
    protected function ucs4ToUtf8($input) {
        $output = '';
        foreach ($input as $k => $v) {
            if ($v < 128) { // 7bit are transferred literally
                $output .= chr($v);
            } elseif ($v < (1 << 11)) { // 2 bytes
                $output .= chr(192 + ($v >> 6)) . chr(128 + ($v & 63));
            } elseif ($v < (1 << 16)) { // 3 bytes
                $output .= chr(224 + ($v >> 12)) . chr(128 + (($v >> 6) & 63)) . chr(128 + ($v & 63));
            } elseif ($v < (1 << 21)) { // 4 bytes
                $output .= chr(240 + ($v >> 18)) . chr(128 + (($v >> 12) & 63)) . chr(128 + (($v >> 6) & 63)) . chr(128 + ($v & 63));
            } elseif (self::$safeMode) {
                $output .= self::$safeChar;
            } else {
                $this->useError('Conversion from UCS-4 to UTF-8 failed: malformed input at byte ' . $k);
                return false;
            }
        }
        return $output;
    }

    /**
     * Convert UCS-4 array into UCS-4 string
     *
     * @param array $input
     * @return string
     */
    protected function ucs4ToUcs4String($input) {
        $output = '';
        // Take array values and split output to 4 bytes per value
        // The bit mask is 255, which reads &11111111
        foreach ($input as $v) {
            $output .= chr(($v >> 24) & 255) . chr(($v >> 16) & 255) . chr(($v >> 8) & 255) . chr($v & 255);
        }
        return $output;
    }

    /**
     * Convert UCS-4 strin into UCS-4 array
     *
     * @param string $input
     * @return array|false
     */
    protected function ucs4StringToUcs4($input) {
        $output = [];
        $inpLen = self::byteLength($input);
        // Input length must be dividable by 4
        if ($inpLen % 4) {
            $this->useError('Input UCS4 string is broken');
            return false;
        }
        // Empty input - return empty output
        if (!$inpLen) return $output;
        for ($i = 0, $out_len = -1; $i < $inpLen; ++$i) {
            // Increment output position every 4 input bytes
            if (!($i % 4)) {
                $out_len++;
                $output[$out_len] = 0;
            }
            $output[$out_len] += ord($input[$i]) << (8 * (3 - ($i % 4)));
        }
        return $output;
    }

    /**
     * Gets the length of a string in bytes even if mbstring function
     * overloading is turned on
     *
     * @param string $string the string for which to get the length.
     * @return integer the length of the string in bytes.
     */
    protected static function byteLength($string) {
        if (self::$mbStringOverload) {
            return mb_strlen($string, '8bit');
        }
        return strlen((binary)$string);
    }

    /**
     * Attempts to return a concrete IDNA instance.
     *
     * @param array $params Set of parameters
     * @return IdnaConvert
     * @access public
     */
    public function getInstance(array $params = []) {
        return new IdnaConvert($params);
    }

    /**
     * Attempts to return a concrete IDNA instance for either php4,php5 оr php7,
     * only creating a new instance if no IDNA instance with the same
     * parameters currently exists.
     *
     * @param array $params Set of parameters
     *
     * @return object idnaConvert
     * @access public
     */
    public function singleton(array $params = []) {
        static $instances;
        if (!isset($instances)) {
            $instances = [];
        }
        $signature = serialize($params);
        if (!isset($instances[$signature])) {
            $instances[$signature] = $this->getInstance($params);
        }
        return $instances[$signature];
    }

    /**
     * Holds all relevant mapping tables
     * See RFC3454 for details
     *
     * @private array
     * @since 0.5.2
     */
    protected static $NP = array
    ('map_nothing' => [0xAD, 0x34F, 0x1806, 0x180B, 0x180C, 0x180D, 0x200B, 0x200C,
            0x200D, 0x2060, 0xFE00, 0xFE01, 0xFE02, 0xFE03, 0xFE04, 0xFE05, 0xFE06, 0xFE07,
            0xFE08, 0xFE09, 0xFE0A, 0xFE0B, 0xFE0C, 0xFE0D, 0xFE0E, 0xFE0F, 0xFEFF
        ],
        'general_prohibited' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
            20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42,
            43, 44, 47, 59, 60, 61, 62, 63, 64, 91, 92, 93, 94, 95, 96, 123, 124, 125, 126, 127, 0x3002
        ],
        'prohibit' => [0xA0, 0x340, 0x341, 0x6DD, 0x70F, 0x1680, 0x180E, 0x2000, 0x2001, 0x2002, 0x2003,
            0x2004, 0x2005, 0x2006, 0x2007, 0x2008, 0x2009, 0x200A, 0x200B, 0x200C, 0x200D, 0x200E, 0x200F,
            0x2028, 0x2029, 0x202A, 0x202B, 0x202C, 0x202D, 0x202E, 0x202F, 0x205F, 0x206A, 0x206B, 0x206C,
            0x206D, 0x206E, 0x206F, 0x3000, 0xFEFF, 0xFFF9, 0xFFFA, 0xFFFB, 0xFFFC, 0xFFFD, 0xFFFE, 0xFFFF,
            0x1FFFE, 0x1FFFF, 0x2FFFE, 0x2FFFF, 0x3FFFE, 0x3FFFF, 0x4FFFE, 0x4FFFF, 0x5FFFE, 0x5FFFF, 0x6FFFE,
            0x6FFFF, 0x7FFFE, 0x7FFFF, 0x8FFFE, 0x8FFFF, 0x9FFFE, 0x9FFFF, 0xAFFFE, 0xAFFFF, 0xBFFFE, 0xBFFFF,
            0xCFFFE, 0xCFFFF, 0xDFFFE, 0xDFFFF, 0xE0001, 0xEFFFE, 0xEFFFF, 0xFFFFE, 0xFFFFF, 0x10FFFE, 0x10FFFF,
        ],
        'prohibit_ranges' => [[0x80, 0x9F], [0x2060, 0x206F], [0x1D173, 0x1D17A],
            [0xE000, 0xF8FF], [0xF0000, 0xFFFFD], [0x100000, 0x10FFFD],
            [0xFDD0, 0xFDEF], [0xD800, 0xDFFF], [0x2FF0, 0x2FFB], [0xE0020, 0xE007F]
        ],
        'replacemaps' => [0x41 => [0x61], 0x42 => [0x62], 0x43 => [0x63],
            0x44 => [0x64], 0x45 => [0x65], 0x46 => [0x66], 0x47 => [0x67],
            0x48 => [0x68], 0x49 => [0x69], 0x4A => [0x6A], 0x4B => [0x6B],
            0x4C => [0x6C], 0x4D => [0x6D], 0x4E => [0x6E], 0x4F => [0x6F],
            0x50 => [0x70], 0x51 => [0x71], 0x52 => [0x72], 0x53 => [0x73],
            0x54 => [0x74], 0x55 => [0x75], 0x56 => [0x76], 0x57 => [0x77],
            0x58 => [0x78], 0x59 => [0x79], 0x5A => [0x7A], 0xB5 => [0x3BC],
            0xC0 => [0xE0], 0xC1 => [0xE1], 0xC2 => [0xE2], 0xC3 => [0xE3],
            0xC4 => [0xE4], 0xC5 => [0xE5], 0xC6 => [0xE6], 0xC7 => [0xE7],
            0xC8 => [0xE8], 0xC9 => [0xE9], 0xCA => [0xEA], 0xCB => [0xEB],
            0xCC => [0xEC], 0xCD => [0xED], 0xCE => [0xEE], 0xCF => [0xEF],
            0xD0 => [0xF0], 0xD1 => [0xF1], 0xD2 => [0xF2], 0xD3 => [0xF3],
            0xD4 => [0xF4], 0xD5 => [0xF5], 0xD6 => [0xF6], 0xD8 => [0xF8],
            0xD9 => [0xF9], 0xDA => [0xFA], 0xDB => [0xFB], 0xDC => [0xFC],
            0xDD => [0xFD], 0xDE => [0xFE], 0xDF => [0x73, 0x73],
            0x100 => [0x101], 0x102 => [0x103], 0x104 => [0x105],
            0x106 => [0x107], 0x108 => [0x109], 0x10A => [0x10B],
            0x10C => [0x10D], 0x10E => [0x10F], 0x110 => [0x111],
            0x112 => [0x113], 0x114 => [0x115], 0x116 => [0x117],
            0x118 => [0x119], 0x11A => [0x11B], 0x11C => [0x11D],
            0x11E => [0x11F], 0x120 => [0x121], 0x122 => [0x123],
            0x124 => [0x125], 0x126 => [0x127], 0x128 => [0x129],
            0x12A => [0x12B], 0x12C => [0x12D], 0x12E => [0x12F],
            0x130 => [0x69, 0x307], 0x132 => [0x133], 0x134 => [0x135],
            0x136 => [0x137], 0x139 => [0x13A], 0x13B => [0x13C],
            0x13D => [0x13E], 0x13F => [0x140], 0x141 => [0x142],
            0x143 => [0x144], 0x145 => [0x146], 0x147 => [0x148],
            0x149 => [0x2BC, 0x6E], 0x14A => [0x14B], 0x14C => [0x14D],
            0x14E => [0x14F], 0x150 => [0x151], 0x152 => [0x153],
            0x154 => [0x155], 0x156 => [0x157], 0x158 => [0x159],
            0x15A => [0x15B], 0x15C => [0x15D], 0x15E => [0x15F],
            0x160 => [0x161], 0x162 => [0x163], 0x164 => [0x165],
            0x166 => [0x167], 0x168 => [0x169], 0x16A => [0x16B],
            0x16C => [0x16D], 0x16E => [0x16F], 0x170 => [0x171],
            0x172 => [0x173], 0x174 => [0x175], 0x176 => [0x177],
            0x178 => [0xFF], 0x179 => [0x17A], 0x17B => [0x17C],
            0x17D => [0x17E], 0x17F => [0x73], 0x181 => [0x253],
            0x182 => [0x183], 0x184 => [0x185], 0x186 => [0x254],
            0x187 => [0x188], 0x189 => [0x256], 0x18A => [0x257],
            0x18B => [0x18C], 0x18E => [0x1DD], 0x18F => [0x259],
            0x190 => [0x25B], 0x191 => [0x192], 0x193 => [0x260],
            0x194 => [0x263], 0x196 => [0x269], 0x197 => [0x268],
            0x198 => [0x199], 0x19C => [0x26F], 0x19D => [0x272],
            0x19F => [0x275], 0x1A0 => [0x1A1], 0x1A2 => [0x1A3],
            0x1A4 => [0x1A5], 0x1A6 => [0x280], 0x1A7 => [0x1A8],
            0x1A9 => [0x283], 0x1AC => [0x1AD], 0x1AE => [0x288],
            0x1AF => [0x1B0], 0x1B1 => [0x28A], 0x1B2 => [0x28B],
            0x1B3 => [0x1B4], 0x1B5 => [0x1B6], 0x1B7 => [0x292],
            0x1B8 => [0x1B9], 0x1BC => [0x1BD], 0x1C4 => [0x1C6],
            0x1C5 => [0x1C6], 0x1C7 => [0x1C9], 0x1C8 => [0x1C9],
            0x1CA => [0x1CC], 0x1CB => [0x1CC], 0x1CD => [0x1CE],
            0x1CF => [0x1D0], 0x1D1 => [0x1D2], 0x1D3 => [0x1D4],
            0x1D5 => [0x1D6], 0x1D7 => [0x1D8], 0x1D9 => [0x1DA],
            0x1DB => [0x1DC], 0x1DE => [0x1DF], 0x1E0 => [0x1E1],
            0x1E2 => [0x1E3], 0x1E4 => [0x1E5], 0x1E6 => [0x1E7],
            0x1E8 => [0x1E9], 0x1EA => [0x1EB], 0x1EC => [0x1ED],
            0x1EE => [0x1EF], 0x1F0 => [0x6A, 0x30C], 0x1F1 => [0x1F3],
            0x1F2 => [0x1F3], 0x1F4 => [0x1F5], 0x1F6 => [0x195],
            0x1F7 => [0x1BF], 0x1F8 => [0x1F9], 0x1FA => [0x1FB],
            0x1FC => [0x1FD], 0x1FE => [0x1FF], 0x200 => [0x201],
            0x202 => [0x203], 0x204 => [0x205], 0x206 => [0x207],
            0x208 => [0x209], 0x20A => [0x20B], 0x20C => [0x20D],
            0x20E => [0x20F], 0x210 => [0x211], 0x212 => [0x213],
            0x214 => [0x215], 0x216 => [0x217], 0x218 => [0x219],
            0x21A => [0x21B], 0x21C => [0x21D], 0x21E => [0x21F],
            0x220 => [0x19E], 0x222 => [0x223], 0x224 => [0x225],
            0x226 => [0x227], 0x228 => [0x229], 0x22A => [0x22B],
            0x22C => [0x22D], 0x22E => [0x22F], 0x230 => [0x231],
            0x232 => [0x233], 0x345 => [0x3B9], 0x37A => [0x20, 0x3B9],
            0x386 => [0x3AC], 0x388 => [0x3AD], 0x389 => [0x3AE],
            0x38A => [0x3AF], 0x38C => [0x3CC], 0x38E => [0x3CD],
            0x38F => [0x3CE], 0x390 => [0x3B9, 0x308, 0x301],
            0x391 => [0x3B1], 0x392 => [0x3B2], 0x393 => [0x3B3],
            0x394 => [0x3B4], 0x395 => [0x3B5], 0x396 => [0x3B6],
            0x397 => [0x3B7], 0x398 => [0x3B8], 0x399 => [0x3B9],
            0x39A => [0x3BA], 0x39B => [0x3BB], 0x39C => [0x3BC],
            0x39D => [0x3BD], 0x39E => [0x3BE], 0x39F => [0x3BF],
            0x3A0 => [0x3C0], 0x3A1 => [0x3C1], 0x3A3 => [0x3C3],
            0x3A4 => [0x3C4], 0x3A5 => [0x3C5], 0x3A6 => [0x3C6],
            0x3A7 => [0x3C7], 0x3A8 => [0x3C8], 0x3A9 => [0x3C9],
            0x3AA => [0x3CA], 0x3AB => [0x3CB], 0x3B0 => [0x3C5, 0x308, 0x301],
            0x3C2 => [0x3C3], 0x3D0 => [0x3B2], 0x3D1 => [0x3B8],
            0x3D2 => [0x3C5], 0x3D3 => [0x3CD], 0x3D4 => [0x3CB],
            0x3D5 => [0x3C6], 0x3D6 => [0x3C0], 0x3D8 => [0x3D9],
            0x3DA => [0x3DB], 0x3DC => [0x3DD], 0x3DE => [0x3DF],
            0x3E0 => [0x3E1], 0x3E2 => [0x3E3], 0x3E4 => [0x3E5],
            0x3E6 => [0x3E7], 0x3E8 => [0x3E9], 0x3EA => [0x3EB],
            0x3EC => [0x3ED], 0x3EE => [0x3EF], 0x3F0 => [0x3BA],
            0x3F1 => [0x3C1], 0x3F2 => [0x3C3], 0x3F4 => [0x3B8],
            0x3F5 => [0x3B5], 0x400 => [0x450], 0x401 => [0x451],
            0x402 => [0x452], 0x403 => [0x453], 0x404 => [0x454],
            0x405 => [0x455], 0x406 => [0x456], 0x407 => [0x457],
            0x408 => [0x458], 0x409 => [0x459], 0x40A => [0x45A],
            0x40B => [0x45B], 0x40C => [0x45C], 0x40D => [0x45D],
            0x40E => [0x45E], 0x40F => [0x45F], 0x410 => [0x430],
            0x411 => [0x431], 0x412 => [0x432], 0x413 => [0x433],
            0x414 => [0x434], 0x415 => [0x435], 0x416 => [0x436],
            0x417 => [0x437], 0x418 => [0x438], 0x419 => [0x439],
            0x41A => [0x43A], 0x41B => [0x43B], 0x41C => [0x43C],
            0x41D => [0x43D], 0x41E => [0x43E], 0x41F => [0x43F],
            0x420 => [0x440], 0x421 => [0x441], 0x422 => [0x442],
            0x423 => [0x443], 0x424 => [0x444], 0x425 => [0x445],
            0x426 => [0x446], 0x427 => [0x447], 0x428 => [0x448],
            0x429 => [0x449], 0x42A => [0x44A], 0x42B => [0x44B],
            0x42C => [0x44C], 0x42D => [0x44D], 0x42E => [0x44E],
            0x42F => [0x44F], 0x460 => [0x461], 0x462 => [0x463],
            0x464 => [0x465], 0x466 => [0x467], 0x468 => [0x469],
            0x46A => [0x46B], 0x46C => [0x46D], 0x46E => [0x46F],
            0x470 => [0x471], 0x472 => [0x473], 0x474 => [0x475],
            0x476 => [0x477], 0x478 => [0x479], 0x47A => [0x47B],
            0x47C => [0x47D], 0x47E => [0x47F], 0x480 => [0x481],
            0x48A => [0x48B], 0x48C => [0x48D], 0x48E => [0x48F],
            0x490 => [0x491], 0x492 => [0x493], 0x494 => [0x495],
            0x496 => [0x497], 0x498 => [0x499], 0x49A => [0x49B],
            0x49C => [0x49D], 0x49E => [0x49F], 0x4A0 => [0x4A1],
            0x4A2 => [0x4A3], 0x4A4 => [0x4A5], 0x4A6 => [0x4A7],
            0x4A8 => [0x4A9], 0x4AA => [0x4AB], 0x4AC => [0x4AD],
            0x4AE => [0x4AF], 0x4B0 => [0x4B1], 0x4B2 => [0x4B3],
            0x4B4 => [0x4B5], 0x4B6 => [0x4B7], 0x4B8 => [0x4B9],
            0x4BA => [0x4BB], 0x4BC => [0x4BD], 0x4BE => [0x4BF],
            0x4C1 => [0x4C2], 0x4C3 => [0x4C4], 0x4C5 => [0x4C6],
            0x4C7 => [0x4C8], 0x4C9 => [0x4CA], 0x4CB => [0x4CC],
            0x4CD => [0x4CE], 0x4D0 => [0x4D1], 0x4D2 => [0x4D3],
            0x4D4 => [0x4D5], 0x4D6 => [0x4D7], 0x4D8 => [0x4D9],
            0x4DA => [0x4DB], 0x4DC => [0x4DD], 0x4DE => [0x4DF],
            0x4E0 => [0x4E1], 0x4E2 => [0x4E3], 0x4E4 => [0x4E5],
            0x4E6 => [0x4E7], 0x4E8 => [0x4E9], 0x4EA => [0x4EB],
            0x4EC => [0x4ED], 0x4EE => [0x4EF], 0x4F0 => [0x4F1],
            0x4F2 => [0x4F3], 0x4F4 => [0x4F5], 0x4F8 => [0x4F9],
            0x500 => [0x501], 0x502 => [0x503], 0x504 => [0x505],
            0x506 => [0x507], 0x508 => [0x509], 0x50A => [0x50B],
            0x50C => [0x50D], 0x50E => [0x50F], 0x531 => [0x561],
            0x532 => [0x562], 0x533 => [0x563], 0x534 => [0x564],
            0x535 => [0x565], 0x536 => [0x566], 0x537 => [0x567],
            0x538 => [0x568], 0x539 => [0x569], 0x53A => [0x56A],
            0x53B => [0x56B], 0x53C => [0x56C], 0x53D => [0x56D],
            0x53E => [0x56E], 0x53F => [0x56F], 0x540 => [0x570],
            0x541 => [0x571], 0x542 => [0x572], 0x543 => [0x573],
            0x544 => [0x574], 0x545 => [0x575], 0x546 => [0x576],
            0x547 => [0x577], 0x548 => [0x578], 0x549 => [0x579],
            0x54A => [0x57A], 0x54B => [0x57B], 0x54C => [0x57C],
            0x54D => [0x57D], 0x54E => [0x57E], 0x54F => [0x57F],
            0x550 => [0x580], 0x551 => [0x581], 0x552 => [0x582],
            0x553 => [0x583], 0x554 => [0x584], 0x555 => [0x585],
            0x556 => [0x586], 0x587 => [0x565, 0x582], 0xE33 => [0xE4D, 0xE32],
            0x1E00 => [0x1E01], 0x1E02 => [0x1E03], 0x1E04 => [0x1E05],
            0x1E06 => [0x1E07], 0x1E08 => [0x1E09], 0x1E0A => [0x1E0B],
            0x1E0C => [0x1E0D], 0x1E0E => [0x1E0F], 0x1E10 => [0x1E11],
            0x1E12 => [0x1E13], 0x1E14 => [0x1E15], 0x1E16 => [0x1E17],
            0x1E18 => [0x1E19], 0x1E1A => [0x1E1B], 0x1E1C => [0x1E1D],
            0x1E1E => [0x1E1F], 0x1E20 => [0x1E21], 0x1E22 => [0x1E23],
            0x1E24 => [0x1E25], 0x1E26 => [0x1E27], 0x1E28 => [0x1E29],
            0x1E2A => [0x1E2B], 0x1E2C => [0x1E2D], 0x1E2E => [0x1E2F],
            0x1E30 => [0x1E31], 0x1E32 => [0x1E33], 0x1E34 => [0x1E35],
            0x1E36 => [0x1E37], 0x1E38 => [0x1E39], 0x1E3A => [0x1E3B],
            0x1E3C => [0x1E3D], 0x1E3E => [0x1E3F], 0x1E40 => [0x1E41],
            0x1E42 => [0x1E43], 0x1E44 => [0x1E45], 0x1E46 => [0x1E47],
            0x1E48 => [0x1E49], 0x1E4A => [0x1E4B], 0x1E4C => [0x1E4D],
            0x1E4E => [0x1E4F], 0x1E50 => [0x1E51], 0x1E52 => [0x1E53],
            0x1E54 => [0x1E55], 0x1E56 => [0x1E57], 0x1E58 => [0x1E59],
            0x1E5A => [0x1E5B], 0x1E5C => [0x1E5D], 0x1E5E => [0x1E5F],
            0x1E60 => [0x1E61], 0x1E62 => [0x1E63], 0x1E64 => [0x1E65],
            0x1E66 => [0x1E67], 0x1E68 => [0x1E69], 0x1E6A => [0x1E6B],
            0x1E6C => [0x1E6D], 0x1E6E => [0x1E6F], 0x1E70 => [0x1E71],
            0x1E72 => [0x1E73], 0x1E74 => [0x1E75], 0x1E76 => [0x1E77],
            0x1E78 => [0x1E79], 0x1E7A => [0x1E7B], 0x1E7C => [0x1E7D],
            0x1E7E => [0x1E7F], 0x1E80 => [0x1E81], 0x1E82 => [0x1E83],
            0x1E84 => [0x1E85], 0x1E86 => [0x1E87], 0x1E88 => [0x1E89],
            0x1E8A => [0x1E8B], 0x1E8C => [0x1E8D], 0x1E8E => [0x1E8F],
            0x1E90 => [0x1E91], 0x1E92 => [0x1E93], 0x1E94 => [0x1E95],
            0x1E96 => [0x68, 0x331], 0x1E97 => [0x74, 0x308], 0x1E98 => [0x77, 0x30A],
            0x1E99 => [0x79, 0x30A], 0x1E9A => [0x61, 0x2BE], 0x1E9B => [0x1E61],
            0x1EA0 => [0x1EA1], 0x1EA2 => [0x1EA3], 0x1EA4 => [0x1EA5],
            0x1EA6 => [0x1EA7], 0x1EA8 => [0x1EA9], 0x1EAA => [0x1EAB],
            0x1EAC => [0x1EAD], 0x1EAE => [0x1EAF], 0x1EB0 => [0x1EB1],
            0x1EB2 => [0x1EB3], 0x1EB4 => [0x1EB5], 0x1EB6 => [0x1EB7],
            0x1EB8 => [0x1EB9], 0x1EBA => [0x1EBB], 0x1EBC => [0x1EBD],
            0x1EBE => [0x1EBF], 0x1EC0 => [0x1EC1], 0x1EC2 => [0x1EC3],
            0x1EC4 => [0x1EC5], 0x1EC6 => [0x1EC7], 0x1EC8 => [0x1EC9],
            0x1ECA => [0x1ECB], 0x1ECC => [0x1ECD], 0x1ECE => [0x1ECF],
            0x1ED0 => [0x1ED1], 0x1ED2 => [0x1ED3], 0x1ED4 => [0x1ED5],
            0x1ED6 => [0x1ED7], 0x1ED8 => [0x1ED9], 0x1EDA => [0x1EDB],
            0x1EDC => [0x1EDD], 0x1EDE => [0x1EDF], 0x1EE0 => [0x1EE1],
            0x1EE2 => [0x1EE3], 0x1EE4 => [0x1EE5], 0x1EE6 => [0x1EE7],
            0x1EE8 => [0x1EE9], 0x1EEA => [0x1EEB], 0x1EEC => [0x1EED],
            0x1EEE => [0x1EEF], 0x1EF0 => [0x1EF1], 0x1EF2 => [0x1EF3],
            0x1EF4 => [0x1EF5], 0x1EF6 => [0x1EF7], 0x1EF8 => [0x1EF9],
            0x1F08 => [0x1F00], 0x1F09 => [0x1F01], 0x1F0A => [0x1F02],
            0x1F0B => [0x1F03], 0x1F0C => [0x1F04], 0x1F0D => [0x1F05],
            0x1F0E => [0x1F06], 0x1F0F => [0x1F07], 0x1F18 => [0x1F10],
            0x1F19 => [0x1F11], 0x1F1A => [0x1F12], 0x1F1B => [0x1F13],
            0x1F1C => [0x1F14], 0x1F1D => [0x1F15], 0x1F28 => [0x1F20],
            0x1F29 => [0x1F21], 0x1F2A => [0x1F22], 0x1F2B => [0x1F23],
            0x1F2C => [0x1F24], 0x1F2D => [0x1F25], 0x1F2E => [0x1F26],
            0x1F2F => [0x1F27], 0x1F38 => [0x1F30], 0x1F39 => [0x1F31],
            0x1F3A => [0x1F32], 0x1F3B => [0x1F33], 0x1F3C => [0x1F34],
            0x1F3D => [0x1F35], 0x1F3E => [0x1F36], 0x1F3F => [0x1F37],
            0x1F48 => [0x1F40], 0x1F49 => [0x1F41], 0x1F4A => [0x1F42],
            0x1F4B => [0x1F43], 0x1F4C => [0x1F44], 0x1F4D => [0x1F45],
            0x1F50 => [0x3C5, 0x313], 0x1F52 => [0x3C5, 0x313, 0x300],
            0x1F54 => [0x3C5, 0x313, 0x301], 0x1F56 => [0x3C5, 0x313, 0x342],
            0x1F59 => [0x1F51], 0x1F5B => [0x1F53], 0x1F5D => [0x1F55],
            0x1F5F => [0x1F57], 0x1F68 => [0x1F60], 0x1F69 => [0x1F61],
            0x1F6A => [0x1F62], 0x1F6B => [0x1F63], 0x1F6C => [0x1F64],
            0x1F6D => [0x1F65], 0x1F6E => [0x1F66], 0x1F6F => [0x1F67],
            0x1F80 => [0x1F00, 0x3B9], 0x1F81 => [0x1F01, 0x3B9],
            0x1F82 => [0x1F02, 0x3B9], 0x1F83 => [0x1F03, 0x3B9],
            0x1F84 => [0x1F04, 0x3B9], 0x1F85 => [0x1F05, 0x3B9],
            0x1F86 => [0x1F06, 0x3B9], 0x1F87 => [0x1F07, 0x3B9],
            0x1F88 => [0x1F00, 0x3B9], 0x1F89 => [0x1F01, 0x3B9],
            0x1F8A => [0x1F02, 0x3B9], 0x1F8B => [0x1F03, 0x3B9],
            0x1F8C => [0x1F04, 0x3B9], 0x1F8D => [0x1F05, 0x3B9],
            0x1F8E => [0x1F06, 0x3B9], 0x1F8F => [0x1F07, 0x3B9],
            0x1F90 => [0x1F20, 0x3B9], 0x1F91 => [0x1F21, 0x3B9],
            0x1F92 => [0x1F22, 0x3B9], 0x1F93 => [0x1F23, 0x3B9],
            0x1F94 => [0x1F24, 0x3B9], 0x1F95 => [0x1F25, 0x3B9],
            0x1F96 => [0x1F26, 0x3B9], 0x1F97 => [0x1F27, 0x3B9],
            0x1F98 => [0x1F20, 0x3B9], 0x1F99 => [0x1F21, 0x3B9],
            0x1F9A => [0x1F22, 0x3B9], 0x1F9B => [0x1F23, 0x3B9],
            0x1F9C => [0x1F24, 0x3B9], 0x1F9D => [0x1F25, 0x3B9],
            0x1F9E => [0x1F26, 0x3B9], 0x1F9F => [0x1F27, 0x3B9],
            0x1FA0 => [0x1F60, 0x3B9], 0x1FA1 => [0x1F61, 0x3B9],
            0x1FA2 => [0x1F62, 0x3B9], 0x1FA3 => [0x1F63, 0x3B9],
            0x1FA4 => [0x1F64, 0x3B9], 0x1FA5 => [0x1F65, 0x3B9],
            0x1FA6 => [0x1F66, 0x3B9], 0x1FA7 => [0x1F67, 0x3B9],
            0x1FA8 => [0x1F60, 0x3B9], 0x1FA9 => [0x1F61, 0x3B9],
            0x1FAA => [0x1F62, 0x3B9], 0x1FAB => [0x1F63, 0x3B9],
            0x1FAC => [0x1F64, 0x3B9], 0x1FAD => [0x1F65, 0x3B9],
            0x1FAE => [0x1F66, 0x3B9], 0x1FAF => [0x1F67, 0x3B9],
            0x1FB2 => [0x1F70, 0x3B9], 0x1FB3 => [0x3B1, 0x3B9],
            0x1FB4 => [0x3AC, 0x3B9], 0x1FB6 => [0x3B1, 0x342],
            0x1FB7 => [0x3B1, 0x342, 0x3B9], 0x1FB8 => [0x1FB0],
            0x1FB9 => [0x1FB1], 0x1FBA => [0x1F70], 0x1FBB => [0x1F71],
            0x1FBC => [0x3B1, 0x3B9], 0x1FBE => [0x3B9],
            0x1FC2 => [0x1F74, 0x3B9], 0x1FC3 => [0x3B7, 0x3B9],
            0x1FC4 => [0x3AE, 0x3B9], 0x1FC6 => [0x3B7, 0x342],
            0x1FC7 => [0x3B7, 0x342, 0x3B9], 0x1FC8 => [0x1F72],
            0x1FC9 => [0x1F73], 0x1FCA => [0x1F74], 0x1FCB => [0x1F75],
            0x1FCC => [0x3B7, 0x3B9], 0x1FD2 => [0x3B9, 0x308, 0x300],
            0x1FD3 => [0x3B9, 0x308, 0x301], 0x1FD6 => [0x3B9, 0x342],
            0x1FD7 => [0x3B9, 0x308, 0x342], 0x1FD8 => [0x1FD0],
            0x1FD9 => [0x1FD1], 0x1FDA => [0x1F76],
            0x1FDB => [0x1F77], 0x1FE2 => [0x3C5, 0x308, 0x300],
            0x1FE3 => [0x3C5, 0x308, 0x301], 0x1FE4 => [0x3C1, 0x313],
            0x1FE6 => [0x3C5, 0x342], 0x1FE7 => [0x3C5, 0x308, 0x342],
            0x1FE8 => [0x1FE0], 0x1FE9 => [0x1FE1],
            0x1FEA => [0x1F7A], 0x1FEB => [0x1F7B],
            0x1FEC => [0x1FE5], 0x1FF2 => [0x1F7C, 0x3B9],
            0x1FF3 => [0x3C9, 0x3B9], 0x1FF4 => [0x3CE, 0x3B9],
            0x1FF6 => [0x3C9, 0x342], 0x1FF7 => [0x3C9, 0x342, 0x3B9],
            0x1FF8 => [0x1F78], 0x1FF9 => [0x1F79], 0x1FFA => [0x1F7C],
            0x1FFB => [0x1F7D], 0x1FFC => [0x3C9, 0x3B9],
            0x20A8 => [0x72, 0x73], 0x2102 => [0x63], 0x2103 => [0xB0, 0x63],
            0x2107 => [0x25B], 0x2109 => [0xB0, 0x66], 0x210B => [0x68],
            0x210C => [0x68], 0x210D => [0x68], 0x2110 => [0x69],
            0x2111 => [0x69], 0x2112 => [0x6C], 0x2115 => [0x6E],
            0x2116 => [0x6E, 0x6F], 0x2119 => [0x70], 0x211A => [0x71],
            0x211B => [0x72], 0x211C => [0x72], 0x211D => [0x72],
            0x2120 => [0x73, 0x6D], 0x2121 => [0x74, 0x65, 0x6C],
            0x2122 => [0x74, 0x6D], 0x2124 => [0x7A], 0x2126 => [0x3C9],
            0x2128 => [0x7A], 0x212A => [0x6B], 0x212B => [0xE5],
            0x212C => [0x62], 0x212D => [0x63], 0x2130 => [0x65],
            0x2131 => [0x66], 0x2133 => [0x6D], 0x213E => [0x3B3],
            0x213F => [0x3C0], 0x2145 => [0x64], 0x2160 => [0x2170],
            0x2161 => [0x2171], 0x2162 => [0x2172], 0x2163 => [0x2173],
            0x2164 => [0x2174], 0x2165 => [0x2175], 0x2166 => [0x2176],
            0x2167 => [0x2177], 0x2168 => [0x2178], 0x2169 => [0x2179],
            0x216A => [0x217A], 0x216B => [0x217B], 0x216C => [0x217C],
            0x216D => [0x217D], 0x216E => [0x217E], 0x216F => [0x217F],
            0x24B6 => [0x24D0], 0x24B7 => [0x24D1], 0x24B8 => [0x24D2],
            0x24B9 => [0x24D3], 0x24BA => [0x24D4], 0x24BB => [0x24D5],
            0x24BC => [0x24D6], 0x24BD => [0x24D7], 0x24BE => [0x24D8],
            0x24BF => [0x24D9], 0x24C0 => [0x24DA], 0x24C1 => [0x24DB],
            0x24C2 => [0x24DC], 0x24C3 => [0x24DD], 0x24C4 => [0x24DE],
            0x24C5 => [0x24DF], 0x24C6 => [0x24E0], 0x24C7 => [0x24E1],
            0x24C8 => [0x24E2], 0x24C9 => [0x24E3], 0x24CA => [0x24E4],
            0x24CB => [0x24E5], 0x24CC => [0x24E6], 0x24CD => [0x24E7],
            0x24CE => [0x24E8], 0x24CF => [0x24E9], 0x3371 => [0x68, 0x70, 0x61],
            0x3373 => [0x61, 0x75], 0x3375 => [0x6F, 0x76],
            0x3380 => [0x70, 0x61], 0x3381 => [0x6E, 0x61],
            0x3382 => [0x3BC, 0x61], 0x3383 => [0x6D, 0x61],
            0x3384 => [0x6B, 0x61], 0x3385 => [0x6B, 0x62],
            0x3386 => [0x6D, 0x62], 0x3387 => [0x67, 0x62],
            0x338A => [0x70, 0x66], 0x338B => [0x6E, 0x66],
            0x338C => [0x3BC, 0x66], 0x3390 => [0x68, 0x7A],
            0x3391 => [0x6B, 0x68, 0x7A], 0x3392 => [0x6D, 0x68, 0x7A],
            0x3393 => [0x67, 0x68, 0x7A], 0x3394 => [0x74, 0x68, 0x7A],
            0x33A9 => [0x70, 0x61], 0x33AA => [0x6B, 0x70, 0x61],
            0x33AB => [0x6D, 0x70, 0x61], 0x33AC => [0x67, 0x70, 0x61],
            0x33B4 => [0x70, 0x76], 0x33B5 => [0x6E, 0x76],
            0x33B6 => [0x3BC, 0x76], 0x33B7 => [0x6D, 0x76],
            0x33B8 => [0x6B, 0x76], 0x33B9 => [0x6D, 0x76],
            0x33BA => [0x70, 0x77], 0x33BB => [0x6E, 0x77],
            0x33BC => [0x3BC, 0x77], 0x33BD => [0x6D, 0x77],
            0x33BE => [0x6B, 0x77], 0x33BF => [0x6D, 0x77],
            0x33C0 => [0x6B, 0x3C9], 0x33C1 => [0x6D, 0x3C9], /*
                    0x33C2  => [0x61, 0x2E, 0x6D, 0x2E], */
            0x33C3 => [0x62, 0x71], 0x33C6 => [0x63, 0x2215, 0x6B, 0x67],
            0x33C7 => [0x63, 0x6F, 0x2E], 0x33C8 => [0x64, 0x62],
            0x33C9 => [0x67, 0x79], 0x33CB => [0x68, 0x70],
            0x33CD => [0x6B, 0x6B], 0x33CE => [0x6B, 0x6D],
            0x33D7 => [0x70, 0x68], 0x33D9 => [0x70, 0x70, 0x6D],
            0x33DA => [0x70, 0x72], 0x33DC => [0x73, 0x76],
            0x33DD => [0x77, 0x62], 0xFB00 => [0x66, 0x66],
            0xFB01 => [0x66, 0x69], 0xFB02 => [0x66, 0x6C],
            0xFB03 => [0x66, 0x66, 0x69], 0xFB04 => [0x66, 0x66, 0x6C],
            0xFB05 => [0x73, 0x74], 0xFB06 => [0x73, 0x74],
            0xFB13 => [0x574, 0x576], 0xFB14 => [0x574, 0x565],
            0xFB15 => [0x574, 0x56B], 0xFB16 => [0x57E, 0x576],
            0xFB17 => [0x574, 0x56D], 0xFF21 => [0xFF41],
            0xFF22 => [0xFF42], 0xFF23 => [0xFF43], 0xFF24 => [0xFF44],
            0xFF25 => [0xFF45], 0xFF26 => [0xFF46], 0xFF27 => [0xFF47],
            0xFF28 => [0xFF48], 0xFF29 => [0xFF49], 0xFF2A => [0xFF4A],
            0xFF2B => [0xFF4B], 0xFF2C => [0xFF4C], 0xFF2D => [0xFF4D],
            0xFF2E => [0xFF4E], 0xFF2F => [0xFF4F], 0xFF30 => [0xFF50],
            0xFF31 => [0xFF51], 0xFF32 => [0xFF52], 0xFF33 => [0xFF53],
            0xFF34 => [0xFF54], 0xFF35 => [0xFF55], 0xFF36 => [0xFF56],
            0xFF37 => [0xFF57], 0xFF38 => [0xFF58], 0xFF39 => [0xFF59],
            0xFF3A => [0xFF5A], 0x10400 => [0x10428], 0x10401 => [0x10429],
            0x10402 => [0x1042A], 0x10403 => [0x1042B], 0x10404 => [0x1042C],
            0x10405 => [0x1042D], 0x10406 => [0x1042E], 0x10407 => [0x1042F],
            0x10408 => [0x10430], 0x10409 => [0x10431], 0x1040A => [0x10432],
            0x1040B => [0x10433], 0x1040C => [0x10434], 0x1040D => [0x10435],
            0x1040E => [0x10436], 0x1040F => [0x10437], 0x10410 => [0x10438],
            0x10411 => [0x10439], 0x10412 => [0x1043A], 0x10413 => [0x1043B],
            0x10414 => [0x1043C], 0x10415 => [0x1043D], 0x10416 => [0x1043E],
            0x10417 => [0x1043F], 0x10418 => [0x10440], 0x10419 => [0x10441],
            0x1041A => [0x10442], 0x1041B => [0x10443], 0x1041C => [0x10444],
            0x1041D => [0x10445], 0x1041E => [0x10446], 0x1041F => [0x10447],
            0x10420 => [0x10448], 0x10421 => [0x10449], 0x10422 => [0x1044A],
            0x10423 => [0x1044B], 0x10424 => [0x1044C], 0x10425 => [0x1044D],
            0x1D400 => [0x61], 0x1D401 => [0x62], 0x1D402 => [0x63],
            0x1D403 => [0x64], 0x1D404 => [0x65], 0x1D405 => [0x66],
            0x1D406 => [0x67], 0x1D407 => [0x68], 0x1D408 => [0x69],
            0x1D409 => [0x6A], 0x1D40A => [0x6B], 0x1D40B => [0x6C],
            0x1D40C => [0x6D], 0x1D40D => [0x6E], 0x1D40E => [0x6F],
            0x1D40F => [0x70], 0x1D410 => [0x71], 0x1D411 => [0x72],
            0x1D412 => [0x73], 0x1D413 => [0x74], 0x1D414 => [0x75],
            0x1D415 => [0x76], 0x1D416 => [0x77], 0x1D417 => [0x78],
            0x1D418 => [0x79], 0x1D419 => [0x7A], 0x1D434 => [0x61],
            0x1D435 => [0x62], 0x1D436 => [0x63], 0x1D437 => [0x64],
            0x1D438 => [0x65], 0x1D439 => [0x66], 0x1D43A => [0x67],
            0x1D43B => [0x68], 0x1D43C => [0x69], 0x1D43D => [0x6A],
            0x1D43E => [0x6B], 0x1D43F => [0x6C], 0x1D440 => [0x6D],
            0x1D441 => [0x6E], 0x1D442 => [0x6F], 0x1D443 => [0x70],
            0x1D444 => [0x71], 0x1D445 => [0x72], 0x1D446 => [0x73],
            0x1D447 => [0x74], 0x1D448 => [0x75], 0x1D449 => [0x76],
            0x1D44A => [0x77], 0x1D44B => [0x78], 0x1D44C => [0x79],
            0x1D44D => [0x7A], 0x1D468 => [0x61], 0x1D469 => [0x62],
            0x1D46A => [0x63], 0x1D46B => [0x64], 0x1D46C => [0x65],
            0x1D46D => [0x66], 0x1D46E => [0x67], 0x1D46F => [0x68],
            0x1D470 => [0x69], 0x1D471 => [0x6A], 0x1D472 => [0x6B],
            0x1D473 => [0x6C], 0x1D474 => [0x6D], 0x1D475 => [0x6E],
            0x1D476 => [0x6F], 0x1D477 => [0x70], 0x1D478 => [0x71],
            0x1D479 => [0x72], 0x1D47A => [0x73], 0x1D47B => [0x74],
            0x1D47C => [0x75], 0x1D47D => [0x76], 0x1D47E => [0x77],
            0x1D47F => [0x78], 0x1D480 => [0x79], 0x1D481 => [0x7A],
            0x1D49C => [0x61], 0x1D49E => [0x63], 0x1D49F => [0x64],
            0x1D4A2 => [0x67], 0x1D4A5 => [0x6A], 0x1D4A6 => [0x6B],
            0x1D4A9 => [0x6E], 0x1D4AA => [0x6F], 0x1D4AB => [0x70],
            0x1D4AC => [0x71], 0x1D4AE => [0x73], 0x1D4AF => [0x74],
            0x1D4B0 => [0x75], 0x1D4B1 => [0x76], 0x1D4B2 => [0x77],
            0x1D4B3 => [0x78], 0x1D4B4 => [0x79], 0x1D4B5 => [0x7A],
            0x1D4D0 => [0x61], 0x1D4D1 => [0x62], 0x1D4D2 => [0x63],
            0x1D4D3 => [0x64], 0x1D4D4 => [0x65], 0x1D4D5 => [0x66],
            0x1D4D6 => [0x67], 0x1D4D7 => [0x68], 0x1D4D8 => [0x69],
            0x1D4D9 => [0x6A], 0x1D4DA => [0x6B], 0x1D4DB => [0x6C],
            0x1D4DC => [0x6D], 0x1D4DD => [0x6E], 0x1D4DE => [0x6F],
            0x1D4DF => [0x70], 0x1D4E0 => [0x71], 0x1D4E1 => [0x72],
            0x1D4E2 => [0x73], 0x1D4E3 => [0x74], 0x1D4E4 => [0x75],
            0x1D4E5 => [0x76], 0x1D4E6 => [0x77], 0x1D4E7 => [0x78],
            0x1D4E8 => [0x79], 0x1D4E9 => [0x7A], 0x1D504 => [0x61],
            0x1D505 => [0x62], 0x1D507 => [0x64], 0x1D508 => [0x65],
            0x1D509 => [0x66], 0x1D50A => [0x67], 0x1D50D => [0x6A],
            0x1D50E => [0x6B], 0x1D50F => [0x6C], 0x1D510 => [0x6D],
            0x1D511 => [0x6E], 0x1D512 => [0x6F], 0x1D513 => [0x70],
            0x1D514 => [0x71], 0x1D516 => [0x73], 0x1D517 => [0x74],
            0x1D518 => [0x75], 0x1D519 => [0x76], 0x1D51A => [0x77],
            0x1D51B => [0x78], 0x1D51C => [0x79], 0x1D538 => [0x61],
            0x1D539 => [0x62], 0x1D53B => [0x64], 0x1D53C => [0x65],
            0x1D53D => [0x66], 0x1D53E => [0x67], 0x1D540 => [0x69],
            0x1D541 => [0x6A], 0x1D542 => [0x6B], 0x1D543 => [0x6C],
            0x1D544 => [0x6D], 0x1D546 => [0x6F], 0x1D54A => [0x73],
            0x1D54B => [0x74], 0x1D54C => [0x75], 0x1D54D => [0x76],
            0x1D54E => [0x77], 0x1D54F => [0x78], 0x1D550 => [0x79],
            0x1D56C => [0x61], 0x1D56D => [0x62], 0x1D56E => [0x63],
            0x1D56F => [0x64], 0x1D570 => [0x65], 0x1D571 => [0x66],
            0x1D572 => [0x67], 0x1D573 => [0x68], 0x1D574 => [0x69],
            0x1D575 => [0x6A], 0x1D576 => [0x6B], 0x1D577 => [0x6C],
            0x1D578 => [0x6D], 0x1D579 => [0x6E], 0x1D57A => [0x6F],
            0x1D57B => [0x70], 0x1D57C => [0x71], 0x1D57D => [0x72],
            0x1D57E => [0x73], 0x1D57F => [0x74], 0x1D580 => [0x75],
            0x1D581 => [0x76], 0x1D582 => [0x77], 0x1D583 => [0x78],
            0x1D584 => [0x79], 0x1D585 => [0x7A], 0x1D5A0 => [0x61],
            0x1D5A1 => [0x62], 0x1D5A2 => [0x63], 0x1D5A3 => [0x64],
            0x1D5A4 => [0x65], 0x1D5A5 => [0x66], 0x1D5A6 => [0x67],
            0x1D5A7 => [0x68], 0x1D5A8 => [0x69], 0x1D5A9 => [0x6A],
            0x1D5AA => [0x6B], 0x1D5AB => [0x6C], 0x1D5AC => [0x6D],
            0x1D5AD => [0x6E], 0x1D5AE => [0x6F], 0x1D5AF => [0x70],
            0x1D5B0 => [0x71], 0x1D5B1 => [0x72], 0x1D5B2 => [0x73],
            0x1D5B3 => [0x74], 0x1D5B4 => [0x75], 0x1D5B5 => [0x76],
            0x1D5B6 => [0x77], 0x1D5B7 => [0x78], 0x1D5B8 => [0x79],
            0x1D5B9 => [0x7A], 0x1D5D4 => [0x61], 0x1D5D5 => [0x62],
            0x1D5D6 => [0x63], 0x1D5D7 => [0x64], 0x1D5D8 => [0x65],
            0x1D5D9 => [0x66], 0x1D5DA => [0x67], 0x1D5DB => [0x68],
            0x1D5DC => [0x69], 0x1D5DD => [0x6A], 0x1D5DE => [0x6B],
            0x1D5DF => [0x6C], 0x1D5E0 => [0x6D], 0x1D5E1 => [0x6E],
            0x1D5E2 => [0x6F], 0x1D5E3 => [0x70], 0x1D5E4 => [0x71],
            0x1D5E5 => [0x72], 0x1D5E6 => [0x73], 0x1D5E7 => [0x74],
            0x1D5E8 => [0x75], 0x1D5E9 => [0x76], 0x1D5EA => [0x77],
            0x1D5EB => [0x78], 0x1D5EC => [0x79], 0x1D5ED => [0x7A],
            0x1D608 => [0x61], 0x1D609 => [0x62], 0x1D60A => [0x63],
            0x1D60B => [0x64], 0x1D60C => [0x65], 0x1D60D => [0x66],
            0x1D60E => [0x67], 0x1D60F => [0x68], 0x1D610 => [0x69],
            0x1D611 => [0x6A], 0x1D612 => [0x6B], 0x1D613 => [0x6C],
            0x1D614 => [0x6D], 0x1D615 => [0x6E], 0x1D616 => [0x6F],
            0x1D617 => [0x70], 0x1D618 => [0x71], 0x1D619 => [0x72],
            0x1D61A => [0x73], 0x1D61B => [0x74], 0x1D61C => [0x75],
            0x1D61D => [0x76], 0x1D61E => [0x77], 0x1D61F => [0x78],
            0x1D620 => [0x79], 0x1D621 => [0x7A], 0x1D63C => [0x61],
            0x1D63D => [0x62], 0x1D63E => [0x63], 0x1D63F => [0x64],
            0x1D640 => [0x65], 0x1D641 => [0x66], 0x1D642 => [0x67],
            0x1D643 => [0x68], 0x1D644 => [0x69], 0x1D645 => [0x6A],
            0x1D646 => [0x6B], 0x1D647 => [0x6C], 0x1D648 => [0x6D],
            0x1D649 => [0x6E], 0x1D64A => [0x6F], 0x1D64B => [0x70],
            0x1D64C => [0x71], 0x1D64D => [0x72], 0x1D64E => [0x73],
            0x1D64F => [0x74], 0x1D650 => [0x75], 0x1D651 => [0x76],
            0x1D652 => [0x77], 0x1D653 => [0x78], 0x1D654 => [0x79],
            0x1D655 => [0x7A], 0x1D670 => [0x61], 0x1D671 => [0x62],
            0x1D672 => [0x63], 0x1D673 => [0x64], 0x1D674 => [0x65],
            0x1D675 => [0x66], 0x1D676 => [0x67], 0x1D677 => [0x68],
            0x1D678 => [0x69], 0x1D679 => [0x6A], 0x1D67A => [0x6B],
            0x1D67B => [0x6C], 0x1D67C => [0x6D], 0x1D67D => [0x6E],
            0x1D67E => [0x6F], 0x1D67F => [0x70], 0x1D680 => [0x71],
            0x1D681 => [0x72], 0x1D682 => [0x73], 0x1D683 => [0x74],
            0x1D684 => [0x75], 0x1D685 => [0x76], 0x1D686 => [0x77],
            0x1D687 => [0x78], 0x1D688 => [0x79], 0x1D689 => [0x7A],
            0x1D6A8 => [0x3B1], 0x1D6A9 => [0x3B2], 0x1D6AA => [0x3B3],
            0x1D6AB => [0x3B4], 0x1D6AC => [0x3B5], 0x1D6AD => [0x3B6],
            0x1D6AE => [0x3B7], 0x1D6AF => [0x3B8], 0x1D6B0 => [0x3B9],
            0x1D6B1 => [0x3BA], 0x1D6B2 => [0x3BB], 0x1D6B3 => [0x3BC],
            0x1D6B4 => [0x3BD], 0x1D6B5 => [0x3BE], 0x1D6B6 => [0x3BF],
            0x1D6B7 => [0x3C0], 0x1D6B8 => [0x3C1], 0x1D6B9 => [0x3B8],
            0x1D6BA => [0x3C3], 0x1D6BB => [0x3C4], 0x1D6BC => [0x3C5],
            0x1D6BD => [0x3C6], 0x1D6BE => [0x3C7], 0x1D6BF => [0x3C8],
            0x1D6C0 => [0x3C9], 0x1D6D3 => [0x3C3], 0x1D6E2 => [0x3B1],
            0x1D6E3 => [0x3B2], 0x1D6E4 => [0x3B3], 0x1D6E5 => [0x3B4],
            0x1D6E6 => [0x3B5], 0x1D6E7 => [0x3B6], 0x1D6E8 => [0x3B7],
            0x1D6E9 => [0x3B8], 0x1D6EA => [0x3B9], 0x1D6EB => [0x3BA],
            0x1D6EC => [0x3BB], 0x1D6ED => [0x3BC], 0x1D6EE => [0x3BD],
            0x1D6EF => [0x3BE], 0x1D6F0 => [0x3BF], 0x1D6F1 => [0x3C0],
            0x1D6F2 => [0x3C1], 0x1D6F3 => [0x3B8], 0x1D6F4 => [0x3C3],
            0x1D6F5 => [0x3C4], 0x1D6F6 => [0x3C5], 0x1D6F7 => [0x3C6],
            0x1D6F8 => [0x3C7], 0x1D6F9 => [0x3C8], 0x1D6FA => [0x3C9],
            0x1D70D => [0x3C3], 0x1D71C => [0x3B1], 0x1D71D => [0x3B2],
            0x1D71E => [0x3B3], 0x1D71F => [0x3B4], 0x1D720 => [0x3B5],
            0x1D721 => [0x3B6], 0x1D722 => [0x3B7], 0x1D723 => [0x3B8],
            0x1D724 => [0x3B9], 0x1D725 => [0x3BA], 0x1D726 => [0x3BB],
            0x1D727 => [0x3BC], 0x1D728 => [0x3BD], 0x1D729 => [0x3BE],
            0x1D72A => [0x3BF], 0x1D72B => [0x3C0], 0x1D72C => [0x3C1],
            0x1D72D => [0x3B8], 0x1D72E => [0x3C3], 0x1D72F => [0x3C4],
            0x1D730 => [0x3C5], 0x1D731 => [0x3C6], 0x1D732 => [0x3C7],
            0x1D733 => [0x3C8], 0x1D734 => [0x3C9], 0x1D747 => [0x3C3],
            0x1D756 => [0x3B1], 0x1D757 => [0x3B2], 0x1D758 => [0x3B3],
            0x1D759 => [0x3B4], 0x1D75A => [0x3B5], 0x1D75B => [0x3B6],
            0x1D75C => [0x3B7], 0x1D75D => [0x3B8], 0x1D75E => [0x3B9],
            0x1D75F => [0x3BA], 0x1D760 => [0x3BB], 0x1D761 => [0x3BC],
            0x1D762 => [0x3BD], 0x1D763 => [0x3BE], 0x1D764 => [0x3BF],
            0x1D765 => [0x3C0], 0x1D766 => [0x3C1], 0x1D767 => [0x3B8],
            0x1D768 => [0x3C3], 0x1D769 => [0x3C4], 0x1D76A => [0x3C5],
            0x1D76B => [0x3C6], 0x1D76C => [0x3C7], 0x1D76D => [0x3C8],
            0x1D76E => [0x3C9], 0x1D781 => [0x3C3], 0x1D790 => [0x3B1],
            0x1D791 => [0x3B2], 0x1D792 => [0x3B3], 0x1D793 => [0x3B4],
            0x1D794 => [0x3B5], 0x1D795 => [0x3B6], 0x1D796 => [0x3B7],
            0x1D797 => [0x3B8], 0x1D798 => [0x3B9], 0x1D799 => [0x3BA],
            0x1D79A => [0x3BB], 0x1D79B => [0x3BC], 0x1D79C => [0x3BD],
            0x1D79D => [0x3BE], 0x1D79E => [0x3BF], 0x1D79F => [0x3C0],
            0x1D7A0 => [0x3C1], 0x1D7A1 => [0x3B8], 0x1D7A2 => [0x3C3],
            0x1D7A3 => [0x3C4], 0x1D7A4 => [0x3C5], 0x1D7A5 => [0x3C6],
            0x1D7A6 => [0x3C7], 0x1D7A7 => [0x3C8], 0x1D7A8 => [0x3C9],
            0x1D7BB => [0x3C3], 0x3F9 => [0x3C3], 0x1D2C => [0x61],
            0x1D2D => [0xE6], 0x1D2E => [0x62], 0x1D30 => [0x64],
            0x1D31 => [0x65], 0x1D32 => [0x1DD], 0x1D33 => [0x67],
            0x1D34 => [0x68], 0x1D35 => [0x69], 0x1D36 => [0x6A],
            0x1D37 => [0x6B], 0x1D38 => [0x6C], 0x1D39 => [0x6D],
            0x1D3A => [0x6E], 0x1D3C => [0x6F], 0x1D3D => [0x223],
            0x1D3E => [0x70], 0x1D3F => [0x72], 0x1D40 => [0x74],
            0x1D41 => [0x75], 0x1D42 => [0x77], 0x213B => [0x66, 0x61, 0x78],
            0x3250 => [0x70, 0x74, 0x65], 0x32CC => [0x68, 0x67],
            0x32CE => [0x65, 0x76], 0x32CF => [0x6C, 0x74, 0x64],
            0x337A => [0x69, 0x75], 0x33DE => [0x76, 0x2215, 0x6D],
            0x33DF => [0x61, 0x2215, 0x6D],
        ],
        'norm_combcls' => [0x334 => 1, 0x335 => 1, 0x336 => 1, 0x337 => 1,
            0x338 => 1, 0x93C => 7, 0x9BC => 7, 0xA3C => 7, 0xABC => 7,
            0xB3C => 7, 0xCBC => 7, 0x1037 => 7, 0x3099 => 8, 0x309A => 8,
            0x94D => 9, 0x9CD => 9, 0xA4D => 9, 0xACD => 9, 0xB4D => 9,
            0xBCD => 9, 0xC4D => 9, 0xCCD => 9, 0xD4D => 9, 0xDCA => 9,
            0xE3A => 9, 0xF84 => 9, 0x1039 => 9, 0x1714 => 9, 0x1734 => 9,
            0x17D2 => 9, 0x5B0 => 10, 0x5B1 => 11, 0x5B2 => 12, 0x5B3 => 13,
            0x5B4 => 14, 0x5B5 => 15, 0x5B6 => 16, 0x5B7 => 17, 0x5B8 => 18,
            0x5B9 => 19, 0x5BB => 20, 0x5Bc => 21, 0x5BD => 22, 0x5BF => 23,
            0x5C1 => 24, 0x5C2 => 25, 0xFB1E => 26, 0x64B => 27, 0x64C => 28,
            0x64D => 29, 0x64E => 30, 0x64F => 31, 0x650 => 32, 0x651 => 33,
            0x652 => 34, 0x670 => 35, 0x711 => 36, 0xC55 => 84, 0xC56 => 91,
            0xE38 => 103, 0xE39 => 103, 0xE48 => 107, 0xE49 => 107, 0xE4A => 107,
            0xE4B => 107, 0xEB8 => 118, 0xEB9 => 118, 0xEC8 => 122, 0xEC9 => 122,
            0xECA => 122, 0xECB => 122, 0xF71 => 129, 0xF72 => 130, 0xF7A => 130,
            0xF7B => 130, 0xF7C => 130, 0xF7D => 130, 0xF80 => 130, 0xF74 => 132,
            0x321 => 202, 0x322 => 202, 0x327 => 202, 0x328 => 202, 0x31B => 216,
            0xF39 => 216, 0x1D165 => 216, 0x1D166 => 216, 0x1D16E => 216, 0x1D16F => 216,
            0x1D170 => 216, 0x1D171 => 216, 0x1D172 => 216, 0x302A => 218, 0x316 => 220,
            0x317 => 220, 0x318 => 220, 0x319 => 220, 0x31C => 220, 0x31D => 220,
            0x31E => 220, 0x31F => 220, 0x320 => 220, 0x323 => 220, 0x324 => 220,
            0x325 => 220, 0x326 => 220, 0x329 => 220, 0x32A => 220, 0x32B => 220,
            0x32C => 220, 0x32D => 220, 0x32E => 220, 0x32F => 220, 0x330 => 220,
            0x331 => 220, 0x332 => 220, 0x333 => 220, 0x339 => 220, 0x33A => 220,
            0x33B => 220, 0x33C => 220, 0x347 => 220, 0x348 => 220, 0x349 => 220,
            0x34D => 220, 0x34E => 220, 0x353 => 220, 0x354 => 220, 0x355 => 220,
            0x356 => 220, 0x591 => 220, 0x596 => 220, 0x59B => 220, 0x5A3 => 220,
            0x5A4 => 220, 0x5A5 => 220, 0x5A6 => 220, 0x5A7 => 220, 0x5AA => 220,
            0x655 => 220, 0x656 => 220, 0x6E3 => 220, 0x6EA => 220, 0x6ED => 220,
            0x731 => 220, 0x734 => 220, 0x737 => 220, 0x738 => 220, 0x739 => 220,
            0x73B => 220, 0x73C => 220, 0x73E => 220, 0x742 => 220, 0x744 => 220,
            0x746 => 220, 0x748 => 220, 0x952 => 220, 0xF18 => 220, 0xF19 => 220,
            0xF35 => 220, 0xF37 => 220, 0xFC6 => 220, 0x193B => 220, 0x20E8 => 220,
            0x1D17B => 220, 0x1D17C => 220, 0x1D17D => 220, 0x1D17E => 220, 0x1D17F => 220,
            0x1D180 => 220, 0x1D181 => 220, 0x1D182 => 220, 0x1D18A => 220, 0x1D18B => 220,
            0x59A => 222, 0x5AD => 222, 0x1929 => 222, 0x302D => 222, 0x302E => 224,
            0x302F => 224, 0x1D16D => 226, 0x5AE => 228, 0x18A9 => 228, 0x302B => 228,
            0x300 => 230, 0x301 => 230, 0x302 => 230, 0x303 => 230, 0x304 => 230,
            0x305 => 230, 0x306 => 230, 0x307 => 230, 0x308 => 230, 0x309 => 230,
            0x30A => 230, 0x30B => 230, 0x30C => 230, 0x30D => 230, 0x30E => 230,
            0x30F => 230, 0x310 => 230, 0x311 => 230, 0x312 => 230, 0x313 => 230,
            0x314 => 230, 0x33D => 230, 0x33E => 230, 0x33F => 230, 0x340 => 230,
            0x341 => 230, 0x342 => 230, 0x343 => 230, 0x344 => 230, 0x346 => 230,
            0x34A => 230, 0x34B => 230, 0x34C => 230, 0x350 => 230, 0x351 => 230,
            0x352 => 230, 0x357 => 230, 0x363 => 230, 0x364 => 230, 0x365 => 230,
            0x366 => 230, 0x367 => 230, 0x368 => 230, 0x369 => 230, 0x36A => 230,
            0x36B => 230, 0x36C => 230, 0x36D => 230, 0x36E => 230, 0x36F => 230,
            0x483 => 230, 0x484 => 230, 0x485 => 230, 0x486 => 230, 0x592 => 230,
            0x593 => 230, 0x594 => 230, 0x595 => 230, 0x597 => 230, 0x598 => 230,
            0x599 => 230, 0x59C => 230, 0x59D => 230, 0x59E => 230, 0x59F => 230,
            0x5A0 => 230, 0x5A1 => 230, 0x5A8 => 230, 0x5A9 => 230, 0x5AB => 230,
            0x5AC => 230, 0x5AF => 230, 0x5C4 => 230, 0x610 => 230, 0x611 => 230,
            0x612 => 230, 0x613 => 230, 0x614 => 230, 0x615 => 230, 0x653 => 230,
            0x654 => 230, 0x657 => 230, 0x658 => 230, 0x6D6 => 230, 0x6D7 => 230,
            0x6D8 => 230, 0x6D9 => 230, 0x6DA => 230, 0x6DB => 230, 0x6DC => 230,
            0x6DF => 230, 0x6E0 => 230, 0x6E1 => 230, 0x6E2 => 230, 0x6E4 => 230,
            0x6E7 => 230, 0x6E8 => 230, 0x6EB => 230, 0x6EC => 230, 0x730 => 230,
            0x732 => 230, 0x733 => 230, 0x735 => 230, 0x736 => 230, 0x73A => 230,
            0x73D => 230, 0x73F => 230, 0x740 => 230, 0x741 => 230, 0x743 => 230,
            0x745 => 230, 0x747 => 230, 0x749 => 230, 0x74A => 230, 0x951 => 230,
            0x953 => 230, 0x954 => 230, 0xF82 => 230, 0xF83 => 230, 0xF86 => 230,
            0xF87 => 230, 0x170D => 230, 0x193A => 230, 0x20D0 => 230, 0x20D1 => 230,
            0x20D4 => 230, 0x20D5 => 230, 0x20D6 => 230, 0x20D7 => 230, 0x20DB => 230,
            0x20DC => 230, 0x20E1 => 230, 0x20E7 => 230, 0x20E9 => 230, 0xFE20 => 230,
            0xFE21 => 230, 0xFE22 => 230, 0xFE23 => 230, 0x1D185 => 230, 0x1D186 => 230,
            0x1D187 => 230, 0x1D189 => 230, 0x1D188 => 230, 0x1D1AA => 230, 0x1D1AB => 230,
            0x1D1AC => 230, 0x1D1AD => 230, 0x315 => 232, 0x31A => 232, 0x302C => 232,
            0x35F => 233, 0x362 => 233, 0x35D => 234, 0x35E => 234, 0x360 => 234,
            0x361 => 234, 0x345 => 240
        ]
    );
}

