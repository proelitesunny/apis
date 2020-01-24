<?php

namespace App\MyHealthcare\Helpers;

class PaymentHash
{
    
    function compress($n) {
        $r = '';
        for ($i = 1; $n >= 0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
            $n -= pow(26, $i);
        }
        return $r;
    }
    
    function decompress($a) {
        $r = 0;
        $l = strlen($a);
        for ($i = 0; $i < $l; $i++) {
           $r += pow(26, $i) * (ord($a[$l - $i - 1]) - 0x40);
        }
        return $r - 1;
    }
    
    /*public function compress($input, $ascii_offset = 38){
        $input = strtoupper($input);
        $output = '';
        //We can try for a 4:3 (8:6) compression (roughly), 24 bits for 4 chars
        foreach(str_split($input, 4) as $chunk) {
            $chunk = str_pad($chunk, 4, '=');

            $int_24 = 0;
            for($i=0; $i<4; $i++){
                //Shift the output to the left 6 bits
                $int_24 <<= 6;

                //Add the next 6 bits
                //Discard the leading ascii chars, i.e make
                $int_24 |= (ord($chunk[$i]) - $ascii_offset) & 0b111111;
            }

            //Here we take the 4 sets of 6 apart in 3 sets of 8
            for($i=0; $i<3; $i++) {
                $output = pack('C', $int_24) . $output;
                $int_24 >>= 8;
            }
        }
        return $output;
    }
    
    public function decompress($input, $ascii_offset = 38) {

        $output = '';
        foreach(str_split($input, 3) as $chunk) {

            //Reassemble the 24 bit ints from 3 bytes
            $int_24 = 0;
            foreach(unpack('C*', $chunk) as $char) {
                $int_24 <<= 8;
                $int_24 |= $char & 0b11111111;
            }

            //Expand the 24 bits to 4 sets of 6, and take their character values
            for($i = 0; $i < 4; $i++) {
                $output = chr($ascii_offset + ($int_24 & 0b111111)) . $output;
                $int_24 >>= 6;
            }
        }
        //Make lowercase again and trim off the padding.
        return strtolower(rtrim($output, '='));
    }*/
}