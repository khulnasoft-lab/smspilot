<?php
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\ValueConverter;

class MVC_Library_SCSS
{
    /**
     * @var Compiler
     */
    private $compiler;
    
    public function __construct()
    {
        $this->compiler = new Compiler();
    }
    
    /**
     * Compile SCSS string to CSS
     * 
     * @param string $scss SCSS string to compile
     * @return string Compiled CSS
     */
    public function compile($scss)
    {
        return $this->compiler->compileString($scss)->getCss();
    }
    
    /**
     * Set import paths
     * 
     * @param array|string $path Import path(s)
     * @return $this
     */
    public function setImportPaths($path)
    {
        $this->compiler->setImportPaths($path);
        return $this;
    }
    
    /**
     * Set output formatting style
     * 
     * @param string $style Output style (expanded, compressed, etc.)
     * @return $this
     */
    public function setOutputStyle($style)
    {
        if ($style === 'compressed') {
            $this->compiler->setOutputStyle(OutputStyle::COMPRESSED);
        } else {
            $this->compiler->setOutputStyle(OutputStyle::EXPANDED);
        }
        return $this;
    }
    
    /**
     * Set SCSS variables
     * 
     * @param array $variables Array of variables to set
     * @return $this
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $name => $value) {
            $this->compiler->setVariables([$name => ValueConverter::parseValue($value)]);
        }
        return $this;
    }
}
