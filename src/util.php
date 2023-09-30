<?php

namespace App;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\JWSSerializerManager;


class Util
{
    //-----------------------------------
	//
	//-----------------------------------
    public static function logmsg($msg="", $traceUp=true, $ligne=true, $afficheDate=true)
    {
	   //return;	
	   if ($traceUp)
	   {
          $journal = fopen("logbook.txt", "a");
          $d = "";
          if ($afficheDate)
            $d = date('Y-m-d H:i:s');
  
          if ($ligne)
           fwrite($journal, "$d: $msg\n");
          else
	       fwrite($journal, "$d: $msg");
	  
	      fclose($journal);	
	   }
    }
}