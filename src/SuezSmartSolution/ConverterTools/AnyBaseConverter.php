<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.suezsmartsolutions.com>.
 */

namespace SuezSmartSolution\ConverterTools;

/**
 * based and converted from 'any-base' javascript library (https://www.npmjs.com/package/any-base).
 * @see https://github.com/HarasimowiczKamil/any-base/blob/7595a7398959618a93cb4edef0bae3e035eb3ce0/index.js
 */
class AnyBaseConverter implements ConverterInterface
{

    const BIN = '01';
    const OCT = '01234567';
    const DEC = '0123456789';
    const HEX = '0123456789abcdef';


    private string $srcAlphabet;
    private string $dstAlphabet;

    private string $invalidList='';

    /**
     * create object with joice of source base and destination base
     */
    public function __construct(string $srcAlphabet, string $dstAlphabet)
    {

        if (empty($srcAlphabet) || empty($dstAlphabet)) {
            throw new \Exception('Bad alphabet');
        }

        if(!$this->isUniqueChar($srcAlphabet))
        {
            throw new \Exception('Chars in $srcAlphabet are not unique');
        }
        if(!$this->isUniqueChar($dstAlphabet))
        {
            throw new \Exception('Chars in $dstAlphabet are not unique');
        }

        $this->srcAlphabet = $srcAlphabet;
        $this->dstAlphabet = $dstAlphabet;
    }

    /**
    * 
    * Implement an algorithm to determine if a string has all unique characters. 
    * What if you can not use additional data structures?
    * @see https://github.com/hrishikesh-mishra/algorithm/blob/82c389cf43b25e6547c49090f67c386fa68a4353/unique-character-in-string.php
    * 
    */
    protected function isUniqueChar ($str) { 
    
        assert(is_string($str)) ; 
        $unqiueChar  = array () ; 
        $len = strlen($str) ; 
        
        for($i = 0 ; $i < $len ;  $i++) { 
            $char = $str[$i];
            if(in_array($char, $unqiueChar)) { 
                return false;
            }else { 
                $unqiueChar [$char] = $char; 
            }
        }
        
        return true;
        
    }


    /**
     * convert number from source base to destination base
     */
    public function convert ($number) {
        
        $i=null;
        $divide=null;
        $newlen=null;
        $numberMap = [];
        $fromBase = strlen($this->srcAlphabet);
        $toBase = strlen($this->dstAlphabet);
        $length = strlen($number);
        $result =  '';
    
        if (!$this->isValid($number)) {
            throw new \Exception('Number "' . $number . '" contains "'.$this->invalidList.'" not included in digits (' . $this->srcAlphabet . ')');
        }
    
        if ($this->srcAlphabet === $this->dstAlphabet) {
            return $number;
        }
    
        for ($i = 0; $i < $length; $i++) {
            $numberMap[$i] = strpos($this->srcAlphabet,str_split($number)[$i]);
        }
   
        do {
            $divide = 0;
            $newlen = 0;
            for ($i = 0; $i < $length; $i++) {
                $divide = $divide * $fromBase + $numberMap[$i];
                if ($divide >= $toBase) {
                    $numberMap[$newlen++] = intval($divide / $toBase, 10);
                    $divide = $divide % $toBase;
                } else if ($newlen > 0) {
                    $numberMap[$newlen++] = 0;
                }
            }
            $length = $newlen;

            $result = substr($this->dstAlphabet,$divide, 1) . $result;
            
            
        } while ($newlen !== 0);
    


        return $result;
    }

    /***
     * validate alphabet list against input number
     */
    protected function isValid($number) {
        $invalidChar = [];
        $isValid = true;
        $i = 0;
        $numberLength = count(str_split($number));
        for (; $i < $numberLength; ++$i) {
            $testChar= str_split($number)[$i];
            if (strpos($this->srcAlphabet,$testChar) === false) {
                $invalidChar[$testChar] = $testChar;
                $isValid = false;
            }
        }
        $this->invalidList = implode('',$invalidChar);
        return $isValid;
    }

}
