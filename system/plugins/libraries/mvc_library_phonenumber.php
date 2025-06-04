<?php
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;

class MVC_Library_PhoneNumber
{
    private $phoneNumber = null;
    
    public function __construct() {}
    
    /**
     * Parse a phone number string
     * 
     * @param string $phoneNumber The phone number to parse
     * @param string $defaultRegion The default region code (e.g., 'US', 'GB')
     * @return MVC_Library_PhoneNumber
     */
    public function parse($phoneNumber, $defaultRegion = null)
    {
        try {
            $this->phoneNumber = PhoneNumber::parse($phoneNumber, $defaultRegion);
            return $this;
        } catch (PhoneNumberParseException $e) {
            // Handle parsing exception
            return $this;
        }
    }
    
    /**
     * Check if the phone number is valid
     * 
     * @return bool Whether the number is valid
     */
    public function isValid()
    {
        return $this->phoneNumber !== null && $this->phoneNumber->isValid();
    }
    
    /**
     * Format the phone number
     * 
     * @param int $format The format to use (constants from PhoneNumberFormat)
     * @return string The formatted phone number
     */
    public function format($format = PhoneNumberFormat::INTERNATIONAL)
    {
        if ($this->phoneNumber === null) {
            return '';
        }
        
        return $this->phoneNumber->format($format);
    }
    
    /**
     * Get the region code for this phone number
     * 
     * @return string|null The region code, or null if unknown
     */
    public function getRegionCode()
    {
        if ($this->phoneNumber === null) {
            return null;
        }
        
        return $this->phoneNumber->getRegionCode();
    }
    
    /**
     * Get the country code for this phone number
     * 
     * @return int|null The country code, or null if unknown
     */
    public function getCountryCode()
    {
        if ($this->phoneNumber === null) {
            return null;
        }
        
        return $this->phoneNumber->getCountryCode();
    }
    
    /**
     * Get the national number part
     * 
     * @return string The national number
     */
    public function getNationalNumber()
    {
        if ($this->phoneNumber === null) {
            return '';
        }
        
        return $this->phoneNumber->getNationalNumber();
    }
}
