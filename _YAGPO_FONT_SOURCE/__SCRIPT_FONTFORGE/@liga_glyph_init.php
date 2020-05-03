<?php

//базовые функции инициализации данных

function readText($filename){
		print $filename;
		$fp = fopen("$filename", 'a+')or die("... no open $filename");
		$fx=fread($fp,filesize($filename));
		fclose($fp);
        return $fx;  
};
function writeText($text,$filename){
		$fp = fopen("$filename", 'w+')or die("... no open $filename");
		if (flock($fp, LOCK_EX)) { // do an exclusive lock
			fwrite($fp,$text); flock($fp, LOCK_UN); // release the lock
  	    } else {echo "Couldn't write temp file $filename"; exit(0); }
  	    fclose($fp);	
};  	    


//переводит значение Юникода в последовательность UTF8
function utf8($num)
{
    if($num<=0x7F)       return chr($num);
    if($num<=0x7FF)      return chr(($num>>6)+192).chr(($num&63)+128);
    if($num<=0xFFFF)     return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
    if($num<=0x1FFFFF)   return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
    return '';
}

/*
function uniord($c)
{
    $ord0 = ord($c{0}); if ($ord0>=0   && $ord0<=127) return $ord0;
    $ord1 = ord($c{1}); if ($ord0>=192 && $ord0<=223) return ($ord0-192)*64 + ($ord1-128);
    $ord2 = ord($c{2}); if ($ord0>=224 && $ord0<=239) return ($ord0-224)*4096 + ($ord1-128)*64 + ($ord2-128);
    $ord3 = ord($c{3}); if ($ord0>=240 && $ord0<=247) return ($ord0-240)*262144 + ($ord1-128)*4096 + ($ord2-128)*64 + ($ord3-128);
    return false;
}
*/

function uniord($s) {
    return unpack('V', iconv('UTF-8', 'UCS-4LE', $s))[1];
}

// code point to UTF-8 string
function unichr($i) {
    return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
}

function hUni($dec){
	$hex=dechex(uniord($dec));
	while(strlen($hex)<4)$hex="0".$hex;
	return $hex;
}

function uniReplace($in,$out,$t){
	if(strlen($out[0])>1){ //if $out is array of string
		for($i=0;$i<count($out);$i++){
			$uni.="/".hUni($out[$i]);
		}
		$uni.="/";
		//print $in." -> ".$out[0]." ".$uni."\n";
		$t=str_replace($in,$uni,$t);
		$t=str_replace("//","/",$t);
	}else{
		$t=str_replace($in,"/".hUni($out)."/",$t);
		$t=str_replace("//","/",$t);
	}
	
	return $t;

}



//print "@@@3=".$argv[1]."\n";
$path_list="/MainYagpoOCR/Font_YagpoUni_2017/SAMBHOTA/SAMBHOTA_SCRIPT/YagpoSambhotaUni_OT_uni.liga";
//$path_listReady="/MainYagpoOCR/Font_YagpoUni_2017/SAMBHOTA/SAMBHOTA_SCRIPT/SambhotaUni_vowel.nam";

//$path_list="/MainYagpoOCR/Font_YagpoUni_2017/Yagpo1_uni.liga";
$path_listReady="/MainYagpoOCR/Font_YagpoUni_2017/Yagpo1.nam";


$path_list_name=str_replace(".txt","",$path_list);
$uni=Array();
$ligaSort=Array(100);
$ligaTable="";

//читаем список имен букв которые необходимо построить и их порядок набора

if(!is_file($path_list)){
	print "run init script @liga_uni.php first\n";
	exit(0);
}	

	$text=readText($path_list);
	$text=str_replace("\r","\n",$text);
	$codeTable=explode("\n",$text);
	$ligaArray=Array();
	$ligaUniArray=Array();
	
	for($i=0;$i<count($codeTable);$i++){
		//
		$line=explode("\t",$codeTable[$i]);
		if(count($line)<3){  
			print $i." no line \n";
			continue;
		}
		$name=$line[1];
		$uni=$line[2];
		$uniName=$line[0];
		$l=str_replace("/","\t",$uni);
		$l=str_replace("\t\t","\t",$l);
		$l.=":|:";
		$l=str_replace("\t:|:","",$l);
		$l=str_replace(":|:","",$l);
		$line=explode("\t",$l);

		$ligaUni=Array();
		
		for($n=0;$n<count($line);$n++){
			if($line[$n]!="")$ligaUni[]=$line[$n];
		}
		$ligaArray[$name]=$ligaUni;
		$ligaUniArray[$name]=substr($uniName,2,10);

	}
	
 	print "ligaArray count:".count($ligaArray)."\n";
 	
 	$text=readText($path_listReady);
	$text=str_replace("\r","\n",$text);
	$text=str_replace(" ","\t",$text);
	$codeTable=explode("\n",$text);
	$ligaReadyArray=Array();
	
	for($i=0;$i<count($codeTable);$i++){
		//
		$line=explode("\t",$codeTable[$i]);
		if(count($line)<2){  
			print $i." no line \n";
			continue;
		}
		$name=$line[1];
		$uni=$line[0];
		$ligaReadyArray[$name]=$uni;
		
	}
 	 	print "ligaArray count:".count($ligaArray)."\n";
 	 	print "ligaReadyArray count:".count($ligaReadyArray)."\n";
 	
 	$codeText="";
if($needClean){
 	$codeText="#first clean all glyph we need to build";
 	foreach($ligaArray as $name=>$uniArray){
 		if($name=="")continue;
 		$codeText.="SelectMore(\"$name\");\n";
 	}
	$codeText.="\nClear();\n";
	
}	
 	$codeText.="#start build all glyphs for GB18030 and aditional table";

	$uni=Array();
	$uni["0f68"]="aa";
	$uni["0f00"]="oM";
	$uni["0f01"]="gter_yig";
	$uni["0f02"]="gter_yig_mgo_um_rnam_bcad_ma";
	$uni["0f03"]="gter_yig_mgo_um_gter_tsheg_ma";
	$uni["0f04"]="ini_yig_mgo_mdun_ma";
	$uni["0f05"]="close_yig_mgo_sgab_ma";
	$uni["0f06"]="caret_yig_mgo_phur_shad_ma";
	$uni["0f07"]="yig_mgo_tsheg_shad_ma";
	$uni["0f08"]="sbrul_shad";
	$uni["0f09"]="bskur_yig_mgo";
	$uni["0f0a"]="bka_shog_yig_mgo";
	$uni["0f0b"]="inter_tsheg";
	$uni["0f0d"]="shad";
	$uni["0f0e"]="nyis_shad";
	$uni["0f0f"]="tsheg_shad";
	$uni["0f10"]="nyis_tsheg_shad";
	$uni["0f11"]="rin_chen_spungs_shad";
	$uni["0f12"]="rgya_gram_shad";
	$uni["0f13"]="caret_dzud_rtags_me_long_can";
	$uni["0f14"]="gter_tsheg";
	$uni["0f15"]="chad_rtags";
	$uni["0f16"]="lhag_rtags";
	$uni["0f17"]="sgra_gcan_char_rtags";
	$uni["0f18"]="khyud_pa";
	$uni["0f19"]="sdong_tshugs_";
	$uni["0f1a"]="rdel_dkar_gcig";
	$uni["0f1b"]="rdel_dkar_gnyis";
	$uni["0f1c"]="rdel_dkar_gsum";
	$uni["0f1d"]="rdel_nag_gcig";
	$uni["0f1e"]="rdel_nag_gnyis";
	$uni["0f1f"]="rdel_dkar_rdel_nag";
	$uni["0f20"]="zero";
	$uni["0f21"]="one";
	$uni["0f22"]="two";
	$uni["0f23"]="three";
	$uni["0f24"]="four";
	$uni["0f25"]="five";
	$uni["0f26"]="six";
	$uni["0f27"]="seven";
	$uni["0f28"]="eight";
	$uni["0f29"]="nine";
	$uni["0f2a"]="half_one";
	$uni["0f2b"]="half_two";
	$uni["0f2c"]="half_three";
	$uni["0f2d"]="half_four";
	$uni["0f2e"]="half_five";
	$uni["0f2f"]="half_six";
	$uni["0f30"]="half_seven";
	$uni["0f31"]="half_eight";
	$uni["0f32"]="half_nine";
	$uni["0f33"]="half_zero";
	$uni["0f34"]="bsdus_rtags";
	$uni["0f35"]="uni0F35";
	$uni["0f36"]="caret_dzud_rtags_bzhi_mig_can";
	$uni["0f37"]="_xa";
	$uni["0f38"]="che_mgo";
	$uni["0f7f"]="rnam_bchad";
	$uni["0f3a"]="gug_rtags_gyon";
	$uni["0f3b"]="gug_rtags_gyas";
	$uni["0f3c"]="ang_khang_gyon";
	$uni["0f3d"]="ang_khang_gyas";
	$uni["0f3e"]="yar_tshes";
	$uni["0f3f"]="mar_tshes";
	$uni["0f40"]="ka";
	$uni["0f41"]="kha";
	$uni["0f42"]="ga";
	$uni["0f43"]="gha";
	$uni["0f44"]="nga";
	$uni["0f45"]="ca";
	$uni["0f46"]="cha";
	$uni["0f47"]="ja";
	$uni["0f49"]="nya";
	$uni["0f4a"]="Ta";
	$uni["0f4b"]="Tha";
	$uni["0f4c"]="Da";
	$uni["0f4e"]="Na";
	$uni["0f4f"]="ta";
	$uni["0f50"]="tha";
	$uni["0f51"]="da";
	$uni["0f53"]="na";
	$uni["0f54"]="pa";
	$uni["0f55"]="pha";
	$uni["0f56"]="ba";
	$uni["0f58"]="ma";
	$uni["0f59"]="tsa";
	$uni["0f5a"]="tsha";
	$uni["0f5b"]="dza";
	$uni["0f5c"]="dzha";
	$uni["0f5d"]="wa";
	$uni["0f5e"]="zha";
	$uni["0f5f"]="za";
	$uni["0f60"]="a_chung";
	$uni["0f61"]="ya";
	$uni["0f62"]="ra";
	$uni["0f63"]="la";
	$uni["0f64"]="sha";
	$uni["0f65"]="Sha";
	$uni["0f66"]="sa";
	$uni["0f67"]="ha";
	$uni["0f6a"]="Ra";
	$uni["0f71"]="_A";
	$uni["0f72"]="_i";
	$uni["0f73"]="_I";
	$uni["0f74"]="_u";
	$uni["0f75"]="_U";
	$uni["0f76"]="vocalic_r";
	$uni["0f77"]="vocalic_rr";
	$uni["0f78"]="vocalic_l";
	$uni["0f79"]="vocalic_ll";
	$uni["0f7a"]="_e";
	$uni["0f7b"]="_ai";
	$uni["0f7c"]="_o";
	$uni["0f7d"]="_au";
	$uni["0f7e"]="_M";
	$uni["0f80"]="__i";
	$uni["0f81"]="__I";
	$uni["0f84"]="halanta";
	$uni["0f85"]="paluta";
	$uni["0f88"]="lce_tsa_can";
	$uni["0f89"]="mchu_can";
	$uni["0f8a"]="gru_can_rgyings";
	$uni["0f8b"]="gru_med_rgyings";
	$uni["0f8d"]="uni0F8D";
	$uni["0f90"]="ka";
	$uni["0f91"]="kha";
	$uni["0f92"]="ga";
	$uni["0f93"]="gha";
	$uni["0f94"]="nga";
	$uni["0f95"]="ca";
	$uni["0f96"]="cha";
	$uni["0f97"]="ja";
	$uni["0f99"]="nya";
	$uni["0f9a"]="Ta";
	$uni["0f9b"]="Tha";
	$uni["0f9c"]="Da";
	$uni["0f9d"]="Dha";
	$uni["0f9e"]="Na";
	$uni["0f9f"]="ta";
	$uni["0fa0"]="tha";
	$uni["0fa1"]="da";
	$uni["0fa2"]="dha";
	$uni["0fa3"]="na";
	$uni["0fa4"]="pa";
	$uni["0fa5"]="pha";
	$uni["0fa6"]="ba";
	$uni["0fa7"]="bha";
	$uni["0fa8"]="ma";
	$uni["0fa9"]="tsa";
	$uni["0faa"]="tsha";
	$uni["0fab"]="dza";
	$uni["0fac"]="dzha";
	$uni["0fad"]="vasur";
	$uni["0fae"]="zha";
	$uni["0faf"]="za";
	$uni["0fb0"]="a_chung";
	$uni["0fb3"]="la";
	$uni["0fb4"]="sha";
	$uni["0fb5"]="Sha";
	$uni["0fb6"]="sa";
	$uni["0fb7"]="ha";
	$uni["0fb8"]="aa";
	$uni["0fba"]="wa";
	$uni["0fbb"]="ya";
	$uni["0fbc"]="Ra";
	$uni["0fbe"]="ku_ru_kha";
	$uni["0fbf"]="ku_ru_kha_bzhi_mig_can";
	$uni["0fc0"]="heavy_beat";
	$uni["0fc3"]="sbub_chal";
	$uni["0fc4"]="dril_bu";
	$uni["0fc5"]="rdo_rje";
	$uni["0fc6"]="padma_gdan";
	$uni["0fc7"]="rdo_rje_rgya_gram";
	$uni["0fc8"]="phur_pa";
	$uni["0fc9"]="nor_bu";
	$uni["0fca"]="nor_bu_nyis_khyil";
	$uni["0fcb"]="nor_bu_gsum_khyil";
	$uni["0fcc"]="nor_bu_bzhi_khyil";
	$uni["0fcf"]="rdel_nag_gsum";
	$uni["0fd0"]="bska_shog_gi_mgo_rgyan";
	$uni["0fd1"]="mnyam_yig_gi_mgo_rgyan";
	$uni["0f70"]="uni0F70";
	$uni["0f6f"]="uni0F6F";
	$uni["0fb1"]="yatag";
	$uni["0fb2"]="ratag";
	$uni["0fb9"]="kSha";
	$uni["0fbd"]="u0x0FBD";
	$uni["0fc1"]="light_beat";
	$uni["0fc2"]="cang_te_u";
	$uni["0fce"]="_oM";
	$uni["0fd4"]="uni0FD4";
	$uni["0fd5"]="uni0FD5";
	$uni["0fd6"]="uni0FD6";
	$uni["0fd7"]="uni0FD7";
	$uni["0fd8"]="uni0FD8";
	$uni["0fd9"]="uni0FD9";
	$uni["0fda"]="uni0FDA";
	$uni["0fdb"]="uni0FDB";
	$uni["0fdc"]="uni0FDC";
	$uni["0fdd"]="uni0FDD";
	$uni["0fdf"]="u0x0FDF";
	$uni["0fe0"]="uni0FE0";
	$uni["0fe1"]="uni0FE1";
	$uni["0fe2"]="uni0FE2";
	$uni["0fe3"]="uni0FE3";
	$uni["0fe4"]="uni0FE4";
	$uni["0fe5"]="uni0FE5";
	$uni["0fe6"]="uni0FE6";
	$uni["0fe7"]="uni0FE7";
	$uni["0fe8"]="uni0FE8";
	$uni["0fe9"]="uni0FE9";
	$uni["0fea"]="uni0FEA";
	$uni["0feb"]="_aauM";
	$uni["0fec"]="uni0FEC";
	$uni["0fed"]="uni0FED";
	$uni["0fee"]="uni0FEE";
	$uni["0fef"]="_M1";
	$uni["0ff0"]="uni0FF0";
	$uni["0ff1"]="uni0FF1";
	$uni["0ff2"]="uni0FF2";
	$uni["0ff3"]="uni0FF3";
	$uni["0ff5"]="_iM1";
	$uni["0ff6"]="uni0FF6";
	$uni["0ff7"]="uni0FF7";
	$uni["0ff8"]="M_i1";
	$uni["0ff9"]="a_ii";
	$uni["0ffa"]="uni0FFA";
	$uni["0ffc"]="uni0FFC";
	$uni["0ffd"]="uni0FFD";
	$uni["0ffe"]="uni0FFE";
	$uni["0fff"]="_oMM";
	$uni["0f52"]="dha";
	$uni["0f39"]="tsa_phru_";
	$uni["0f83"]="_MM";
	$uni["0f82"]="_naa_da";
	$uni["0f57"]="bha";
	$uni["0f4d"]="Dha";
	



//читаем из файла _anchorList.txt координаты контрольных установочных точек Anchor Points для всех букв образующих лигатуры
	print "start ";
	$anchorArray=Array();
	$path="/MainYagpoOCR/Font_YagpoUni_2017/__SCRIPT_FONTFORGE/_anchorList.txt";
	$text=readText($path);
	$text=str_replace("\r","\n",$text);
	$text=str_replace("   ","\t",$text);
	$text=str_replace(" \n","\n",$text);
	$anchorTable=explode("\n",$text);
	for($i=0;$i<count($anchorTable);$i++){
		if($anchorTable[$i]=="")continue;
		$line=explode("\t",$anchorTable[$i]);
		$name=$line[0]; 
		if(!isset($anchorArray[$name]))$anchorArray[$name]=Array();
		$anchorArray[$name][]=$line[1];
		$anchorArray[$name][]=$line[2];
		$anchorArray[$name][]=$line[3];
	}
	
	//print $anchorArray["c3.ma"][0]."\n"; exit(0);
	//print "count1:".count($anchorArray["c2.aa"])."\n";

function isStopLetter($n){
	$n=str_replace("c0.","",$n);
	$n=str_replace("c1.","",$n);
	$n=str_replace("c2.","",$n);
	$n=str_replace("c3.","",$n);
	if($n[0]=="_"||$n=="ratag"||$n=="ratag_1"||$n=="yatag"||$n=="yatag1"||$n=="vasur"||$n=="halanta")return 1;
	return 0;
}	
function isTopLetter($n){
	$n=str_replace("c_.","",$n);
	$n=str_replace("c0.","",$n);
	$n=str_replace("c1.","",$n);
	$n=str_replace("c2.","",$n);
	$n=str_replace("c3.","",$n);
	//print "@@@ $n \n";
	if($n[0]=="_"&&$n!="_A"&&$n!="_u")return 1;
	return 0;
}	

function isJoined($b,$n,$nextNext){
	$b=str_replace("c_.","",$b);
	$b=str_replace("c0.","",$b);
	$b=str_replace("c1.","",$b);
	$b=str_replace("c2.","",$b);
	$b=str_replace("c3.","",$b);
	
	$n=str_replace("c_.","",$n);
	$n=str_replace("c0.","",$n);
	$n=str_replace("c1.","",$n);
	$n=str_replace("c2.","",$n);
	$n=str_replace("c3.","",$n);
	
	global $joinStackLength;
	
	//print "@@@ join  b:$b n:$n nextNext:$nextNext\n";
	
	if($nextNext!=""){
		if($b=="ra"&&$n=="dza"&&$nextNext=="ratag"){
			$joinStackLength=2;
			return "rdzra";
		}	
		if(($b=="ra"||$b=="Ra")&&$n=="dza"&&$nextNext=="yatag"){
			$joinStackLength=2;
			return "rdzya";
		}	
	}
	

	if($b=="ka"||$b=="ga"||$b=="ha"||$b=="ta"||$b=="Sha"||$b=="na"||$b=="nya"||$b=="sha"||$b=="Na"||$b=="Tha"||$b=="Da"
		||$b=="Ta"||$b=="da"||$b=="ja"||$b=="dza"||$b=="nga"||$b=="ra"||$b=="Ra"||$b=="kha"||$b==""||$b=="rdza"){
		if($n=="yatag"){
			if($b!="a_chung")$b=substr($b,0,strlen($b)-1);
			$b.=".ya";
			$b=str_replace("R","r",$b);
			$joinStackLength=1;
			return $b;
		}	
	}
	if($b=="ka"||$b=="ga"||$b=="kha"||$b=="da"||$b=="Da"||$b=="zha"||$b=="na"||$b=="nya"||$b=="ha"||$b=="ta"||$b=="ja"||$b=="dza"||$b=="Tha"
	||$b=="ra"||$b=="a_chung"||$b=="nga"||$b=="Ta"||$b=="Na"||$b=="Sha"||$b=="rdza"){
		if($n=="ratag"){
			if($b!="a_chung")$b=substr($b,0,strlen($b)-1);
			$b.=".ra";
			$joinStackLength=1;
			return $b;
		}	
	}
	if($b=="_i"||$b=="__i"||$b=="_ai"||$b=="_au"||$b=="_e"||$b=="_o"){
		if($n=="_M"||$n=="_MM"||$n=="_naa_Da"||$n=="_i"){
			$n=str_replace("_","",$n);
			$b.=$n;
			$joinStackLength=1;
			return $b;
		}	
	}
	if($b=="ra"){
		if($n=="tsa"){$joinStackLength=1; return "rtsa";}
		if($n=="dza"){$joinStackLength=1; return "rdza";}
	}
	if($b=="_MM"&&$n=="__i"){$joinStackLength=1; return "_MM_i";}
	if($b=="_M"&&$n=="__i"){$joinStackLength=1; return "_M_i";}

	return "";
}	

function setLetter($name,$glyph){
	global $codeText;
	$codeText.="
		Select(\"$glyph\");
		CopyReference();
		Select(\"$name\");
		Paste();";
}


?>
