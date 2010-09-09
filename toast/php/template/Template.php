<?php
/**
 * Basic template engine. Support nest and block.
 * @todo Add Exception
 */
class Myngle_Template
{
    /**
     * Support segment types
     */
    const SEGMENT_TYPE_SELF = 0;
    const SEGMENT_TYPE_FILE = 1;
    const SEGMENT_TYPE_BLOCK = 2;
    const SEGMENT_TYPE_VAR = 3;

    /**
     * Use to specify the template script path
     */
    const FILE_PATH_BASE = 0;
    const FILE_PATH_THIS = 1;

    /**
     * Variable to hold segments of template
     *
     * @var array
     */
    protected $_segments;

    /**
     * Variable to hold parsed content of template
     *
     * @var string
     */
    protected $_mainContent;

    /**
     * Variable to hold a table for variables in template
     *
     * @var array
     */
    protected $_varContent = array();

    /**
     * Variable to hold all tags found in template script
     *
     * @var array
     */
    protected $_templateTags = array();

    /**
     * Base path for template files
     *
     * @var string
     */
    protected $_tplBasePath = '';

    /**
     * Concrete path for template files
     *
     * @var string
     */
    protected $_tplScriptPath = '';

    /*
     * Template file's suffix
     *
     * @var string
     */
    protected $_tplSuffix = 'phtml';

    /**
     * Template file's encoding
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Variable to hold all the assigning value
     *
     * @var string
     */
    protected $_t;


    /**
     * Set the encoding for template file
     *
     * @param string $encoding
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
    }

    /**
     * Set the script path for template
     *
     * @param string $path
     * @return void
     */
    public function setScriptPath($path)
    {
        $this->_tplScriptPath= $path;
    }

    /**
     * Set the base path for template
     *
     * @param string $path
     * @return void
     */
    public function setBasePath($path)
    {
        $this->_tplBasePath = $path;
    }

    /**
     * Set the suffix name for template file
     *
     * @param string $suffix
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->_tplSuffix = $suffix;
    }

    /**
     * Get the suffix name for template file
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->_tplSuffix;
    }

    /**
     * Assign value
     *
     * @param string $name name of the variable
     * @param mixed $value
     * @return void
     */
    public function assign($name, $value)
    {
        $this->_t["$name"] = $value;
    }

    public function parse($file)
    {
        $this->_segments = $this->_preParse($file, $this->_mainContent);

        if (is_array($this->_segments)) {
            foreach ($this->_segments as $idx => $segment) {
                if ($segment['type'] === self::SEGMENT_TYPE_FILE) {
                    if ($segment['path'] === self::FILE_PATH_BASE) {
                        $fileToParse = $this->_tplBasePath
                        . $segment['name'] . '.' . $this->_tplSuffix;
                    } else {
                        $fileToParse = $this->_tplBasePath . $this->_tplScriptPath
                        . $segment['name'] . '.' . $this->_tplSuffix;
                    }
                    $segment['content'] = $this->_fileParse($fileToParse);
                    $this->_saveTags($segment['real_name']);
                } elseif ($segment['type'] === self::SEGMENT_TYPE_VAR) {
                    $this->_varParse($segment);
                } elseif ($segment['type'] === self::SEGMENT_TYPE_BLOCK) {
                    $replace = array_key_exists($segment['name'],$this->_t) ? $this->_t[$segment['name']] : null;
                    $this->_mainContent = $this->_blockParse($segment, $replace, $this->_mainContent);
                }
                $this->_segments[$idx] = $segment;
            }
            $this->_postParse();
        }
        //return $this->_mainContent . htmlentities(var_dump($this->_segments)) . htmlentities(var_dump($this->_varContent));
        return $this->_mainContent;
    }

    private function _fileParse($name)
    {
        $segments = $this->_preParse($name, $content);

        if(is_array($segments)) {
            foreach ($segments as $idx => $segment) {
                if ($segment['type'] === self::SEGMENT_TYPE_VAR) {
                    $this->_varParse($segment);
                } elseif ($segment['type'] === self::SEGMENT_TYPE_BLOCK) {
                    $replace = array_key_exists($segment['name'],$this->_t) ? $this->_t[$segment['name']] : null;
                    $content = $this->_blockParse($segment, $replace, $content);
                }
            }
        }
        return $content;
    }

    private function _varParse($segment)
    {
        if (isset($this->_t[$segment['name']]) && !is_null($this->_t[$segment['name']])) {
            if (!array_key_exists($segment['real_name'], $this->_varContent)) {
                $this->_varContent[$segment['real_name']] = $this->_t[$segment['name']];
            }
        }
        $this->_saveTags($segment['real_name']);
    }

    private function _preParse($name, & $file)
    {
        $file = file_get_contents($name, true);

        preg_match_all('/{[$_\[\]a-zA-Z0-9]+}/', $file, $j);

        if (is_array($j[0]) && !empty($j[0])) {
            for ($i = 0 ; $i < count($j[0]) ; $i++) {
                if (preg_match('/^{[a-z]+}$/', $j[0][$i])) {
                    $segments[$i]['name'] = preg_replace('/(^{)|(}$)/','',$j[0][$i]);
                    $segments[$i]['type'] = self::SEGMENT_TYPE_FILE;
                    $segments[$i]['path'] = self::FILE_PATH_BASE;
                    $segments[$i]['real_name'] = $j[0][$i];
                }

                if (preg_match('/^{_[a-z]+}$/', $j[0][$i])) {
                    $segments[$i]['name'] = preg_replace('/(^{)|(}$)/','',$j[0][$i]);
                    $segments[$i]['type'] = self::SEGMENT_TYPE_FILE;
                    $segments[$i]['path'] = self::FILE_PATH_THIS;
                    $segments[$i]['real_name'] = $j[0][$i];
                }

                if (preg_match('/^{\[_[a-zA-Z]+\]}$/', $j[0][$i])) {
                    $segments[$i] = $this->_preParseBlock($j[0][$i], $j[0], $i);
                }

                if (preg_match('/^{\$[a-zA-Z0-9]+}$/', $j[0][$i])) {
                    $segments[$i]['name'] = preg_replace('/(^{\$)|(}$)/','',$j[0][$i]);
                    $segments[$i]['type'] = self::SEGMENT_TYPE_VAR;
                    $segments[$i]['real_name'] = $j[0][$i];
                }
            }
            return $segments;
        }
    }

    private function _preParseBlock($openTag, $segments, & $offset)
    {
        $tmp['name'] = preg_replace('/(^{\[_)|(\]}$)/','',$openTag);
        $closeTag = '{[' . $tmp['name'] . '_]}';
        $tmp['open'] = $openTag;
        $tmp['close'] = $closeTag;
        $tmp['type'] = self::SEGMENT_TYPE_BLOCK;
        $segmentsTmp = array_slice($segments, $offset, count($segments), true);

        for ($i = $offset+1; $i < array_search($closeTag, $segmentsTmp); $i++) {
            if (preg_match('/^{\$[a-zA-Z0-9]+}$/', $segments[$i])) {
                $tmp['keys'][] = preg_replace('/(^{\$)|(}$)/','',$segments[$i]);
            }

            if (preg_match('/^{\$_[a-zA-Z0-9]+}$/', $segments[$i])) {
                $var['real_name'] = $segments[$i];
                $var['name'] = preg_replace('/(^{\$_)|(}$)/','',$segments[$i]);
                $this->_varParse($var);
            }

            if (preg_match('/^{\[_[a-zA-Z]+\]}$/', $segments[$i])) {
                $tmp['child'][] = $this->_preParseBlock($segments[$i], $segments, $i);
            }
        }
        $offset = $i;
        return $tmp;
    }

    private function _saveTags($tag)
    {
        if (!in_array($tag, $this->_templateTags)) {
            $this->_templateTags[] = $tag;
        }
    }

    /**
     * Function to parse a block
     *
     * @param array $pattern
     * @param array $replace
     * @param string $subject
     * @return string
     */
    private function _blockParse($pattern, $replace, & $subject)
    {
        $open = strpos($subject, $pattern['open']);
        $close = strpos($subject, $pattern['close']);
        $tagLen = strlen($pattern['open']);
        $blockPatternWithoutTag = substr($subject, $open + $tagLen, $close - $open - $tagLen);
        $replacement = '';

        if (is_array($replace)) {
            foreach ($replace as $block) {
                $tmp = $blockPatternWithoutTag;
                if (array_key_exists('child', $pattern)) {
                    $subjectTmp = $subject;
                    foreach ($pattern['child'] as $childPattern) {
                        $childReplace = '';
                        if (array_key_exists($childPattern['name'], $block)) {
                            $childReplace = $block[$childPattern['name']];
                        } elseif (array_key_exists($childPattern['name'], $this->_t)) {
                            $childReplace = $this->_t[$childPattern['name']];
                        }

                        $tmp = $this->_blockParse($childPattern, $childReplace, $subjectTmp);
                        $subjectTmp = $tmp;
                    }
                    $open = strpos($tmp, $pattern['open']);
                    $close = strpos($tmp, $pattern['close']);
                    $tmp = substr($tmp, $open + $tagLen, $close - $open - $tagLen);
                }

                if (isset($pattern['keys']) && is_array($pattern['keys'])) {
                    foreach ($pattern['keys'] as $key) {
                        if (is_array($block) && array_key_exists($key, $block)) {
                            $tmp = $this->_replace('{$' . $key . '}', $block["$key"], $tmp);
                        } else {
                            $tmp = str_replace('{$' . $key . '}', '', $tmp);
                        }
                    }
                }
                $replacement .= $tmp;
            }
        }
        $this->_saveTags($pattern['open']);
        $this->_saveTags($pattern['close']);
        $open = strpos($subject, $pattern['open']);
        $close = strpos($subject, $pattern['close']);
        return substr_replace($subject, $replacement, $open, $close - $open + $tagLen);
    }

    private function _postParse()
    {
        foreach ($this->_segments as $segment) {//parsing block
            if (isset($segment['content'])) {
                $this->_mainContent = str_replace($segment['real_name'], $segment['content'], $this->_mainContent);
            }
        }

        foreach ($this->_varContent as $tag => $val) {//parsing var
            $this->_mainContent = $this->_replace($tag, $val, $this->_mainContent);
        }

        foreach ($this->_templateTags as $tag) {//replace not assigned
            $this->_mainContent = str_replace($tag, '', $this->_mainContent);
        }
    }

    private function _replace($search, $replace, $subject)
    {
        return str_replace($search, htmlentities($replace, ENT_COMPAT, $this->_encoding), $subject);
    }
}
?>