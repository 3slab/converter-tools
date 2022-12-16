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
 * based and converted from 'short-uuid' javascript library (https://www.npmjs.com/package/short-uuid).
 * @see https://github.com/oculus42/short-uuid/blob/bdd83c4a6cae19387796ec1e8fdf36129b819b50/index.js
 */
class ShortUUID
{

    const flickrBase58 = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    const cookieBase90 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#$%&'()*+-./:<=>?@[]^_`{|}~";

    /**
     * used  alphabet
     */
    public $useAlphabet = ShortUUID::flickrBase58;

    /**
     * default Options
     */
    protected $defaultOptions = [
        'consistentLength' => true // force length of result
    ];

    /**
     * slected options. Merged from $this->defaultOptions and contructor $options
     */
    protected $selectedOptions = [];


    public $maxLength; 

    /**
     * UUID shorten manipulator
     * it's possible to change Converter with param $fromHexConverter and $toHexConverter. This converter need to implements ConverterInterface
     */
    public function __construct(
        $targetAlphabet = null, 
        $options = [],
        ConverterInterface $fromHexConverter=null,
        ConverterInterface $toHexConverter = null)
    {

        if ($targetAlphabet !== null) {
            $this->useAlphabet = $targetAlphabet;
        }

        $this->selectedOptions = array_merge($this->defaultOptions, $options);

        if($fromHexConverter !== null)
        {
            $this->fromHex = $fromHexConverter;
        }
        else
        {
            $this->fromHex = new AnyBaseConverter(AnyBaseConverter::HEX, $this->useAlphabet);
        }

        if($toHexConverter !== null)
        {
            $this->toHex = $toHexConverter;
        }
        else
        {
            $this->toHex = new AnyBaseConverter($this->useAlphabet, AnyBaseConverter::HEX);
        }
        
        

        $this->maxLength = $this->getShortIdLength(strlen($this->useAlphabet));

        $this->paddingParams = [
            'shortIdLength' => $this->maxLength,
            'consistentLength' => $this->selectedOptions['consistentLength'],
            'paddingChar' =>  substr($this->useAlphabet, 0, 1),
        ];
    }

    /**
     * internal shorten UUID function
     */
    protected function shortenUUID($longId,ConverterInterface $translator, $paddingParams)
    {
        $translated = $translator->convert(str_replace('-', '', strtolower($longId)));

        if (!empty($paddingParams) || !$paddingParams['consistentLength']) {
            return $translated;
        }

        return str_pad(
            $translated,
            $paddingParams['shortIdLength'],
            $paddingParams['paddingChar'],
            STR_PAD_LEFT
        );
    }

    /**
     * internal enlarge UUID function
     */
    protected function enlargeUUID($shortId,ConverterInterface $translator)
    {
        $uu1 = str_pad($translator->convert($shortId), 32, '0', STR_PAD_LEFT);
        $m = [];
        // Join the zero padding and the UUID and then slice it up with match
        preg_match('/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/', $uu1,$m);
        // Accumulate the matches and join them.
        return implode('-', [$m[1], $m[2], $m[3], $m[4], $m[5]]);
    }


    /**
     * reduce UUID size based on enw base.
     */
    public function fromUUID($uuid)
    {
        return $this->shortenUUID($uuid, $this->fromHex, $this->paddingParams);
    }

    /**
     * enlarge UUID previouly shorten by fromUUID
     */
    public function toUUID($shortUuid)
    {
        return $this->enlargeUUID($shortUuid, $this->toHex);
    }


    protected function getShortIdLength($alphabetLength)
    {
        return ceil(log(2 ** 128) / log($alphabetLength));
    }

    /**
     * generate UUIDV4 id
     */
    public function generate()
    {
        return $this->shortenUUID($this->uuidv4(), $this->fromHex, $this->paddingParams);
    }

    /**
     * alias  to generate
     */
    public function new()
    {
        return $this->generate();
    }

    /**
     * alias  to guidv4
     */
    public function uuidv4()
    {
        return $this->guidv4();
    }

    /**
     * @see https://www.uuidgenerator.net/dev-corner/php
     */
    public function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
