<?php
//print "@@@1=".$argc."\n";
//print "@@@2=".$argv[0]."\n";
//find ./ -maxdepth 2 -type d -print >_3.txt    -- find directory name for zip

function readText($filename){
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
$path_list="/MainYagpoOCR/Font_YagpoUni_2017/__SCRIPT_FONTFORGE/_namelist.txt";
$path_list_name=str_replace(".txt","",$path_list);
$uni=Array();
$ligaSort=Array(100);
$ligaTable="";

//читаем список имен букв которые необходимо построить и их кодировку
//каждое имя преобразуем в последовательность набора

if(!is_file($path_list_name."_uni.liga")){

		$text=readText($path_list);
		$text=str_replace("   ","\t",$text);
		$text=str_replace(" \n","\n",$text);
		
		$ligaArray=explode("\n",$text);
		for($i=0;$i<count($ligaArray);$i++){
			if($i%1000==0)print ".$i";
			$line=explode("\t",$ligaArray[$i]); 
			$uni=$line[0];
			$name=$line[1];
			$t=$line[1];
			$t="^".$t.";";
			$t=str_replace("^gter_yig","/0f01/",$t);
			$t=str_replace("^mchu_can","/0f89/",$t);
			$t=str_replace("^gru_can_rgyings","/0f8a/",$t);
			$t=str_replace("^a.chung","/0f60/",$t);
			$t=str_replace("o_i","/0f7c/0f0b/0f60/0f72/",$t);
			$t=str_replace("^rdo_rje","/0f62/0fa1/0f7c/0f0b/0f62/0f97/0f7a/",$t);
			$t=str_replace("a.halanta",".halanta",$t);
			$t=str_replace("a.naa.da",".naa.da",$t);
			$t=str_replace("naa.da","naa_da",$t);
			$t=str_replace("Anaa_da","/0f71/0f82/",$t);
			$t=str_replace("A.naa_da","/0f71/0f82/",$t);
			$t=str_replace("U.naa_da","/0f71/0f74/0f82/",$t);
			$t=str_replace("Unaa_da","/0f71/0f74/0f82/",$t);
			$t=str_replace("unaa_da","/0f74/0f82/",$t);
			$t=str_replace("u.naa_da","/0f74/0f82/",$t);
			$t=str_replace("^_oM","/0f7c/0f7e/",$t);
			
			
			$t=str_replace(".naa_da","/0f82/",$t);
			$t=str_replace("naa_da","/0f82/",$t);
			$t=str_replace("o.lce_tsa_can","/0f7c/0f88/",$t);
			$t=str_replace("u.lce_tsa_can","/0f74/0f88/",$t);
			$t=str_replace("e.lce_tsa_can","/0f7a/0f88/",$t);
			$t=str_replace("i.lce_tsa_can","/0f72/0f88/",$t);
			$t=str_replace("a.lce_tsa_can","/0f88/",$t);
			$t=str_replace("A.lce_tsa_can","/0f71/0f88/",$t);
			$t=str_replace("I.lce_tsa_can","/0f71/0f72/0f88/",$t);
			$t=str_replace("U.lce_tsa_can","/0f71/0f74/0f88/",$t);
			$t=str_replace("aM.lce_tsa_can","/0f7e/0f88/",$t);
			$t=str_replace("iM.lce_tsa_can","/0f72/0f7e/0f88/",$t);
			$t=str_replace("^tib_oM","/0f68/0f7c/0f7e/",$t);
			$t=str_replace("//","/",$t);
			
			$t=uniReplace("^kh","ཁ",$t);
			$t=uniReplace("^k","ཀ",$t);
			$t=uniReplace("^g","ག",$t);
			$t=uniReplace("^ng","ང",$t);
			$t=uniReplace("^ny","ཉ",$t);
			$t=uniReplace("^n","ན",$t);
			$t=uniReplace("^ch","ཆ",$t);
			$t=uniReplace("^c","ཅ",$t);
			$t=uniReplace("^dz","ཛ",$t);
			$t=uniReplace("^th","ཐ",$t);
			$t=uniReplace("^tsh","ཚ",$t);
			$t=uniReplace("^ts","ཙ",$t);
			$t=uniReplace("^t","ཏ",$t);
			$t=uniReplace("^d","ད",$t);
			$t=uniReplace("^sh","ཤ",$t);
			$t=uniReplace("^s","ས",$t);
			$t=uniReplace("^j","ཇ",$t);
			$t=uniReplace("^zh","ཞ",$t);
			$t=uniReplace("^z","ཟ",$t);
			$t=uniReplace("^m","མ",$t);
			$t=uniReplace("^ph","ཕ",$t);
			$t=uniReplace("^p","པ",$t);
			$t=uniReplace("^b","བ",$t);
			$t=uniReplace("^h","ཧ",$t);
			$t=uniReplace("^achung","འ",$t);
			$t=uniReplace("^R","ཪ",$t);
			$t=uniReplace("^r","ར",$t);
			$t=uniReplace("^Th","ཋ",$t);
			$t=uniReplace("^T","ཊ",$t);
			$t=uniReplace("^D","ཌ",$t);
			$t=uniReplace("^N","ཎ",$t);
			$t=uniReplace("^Sh","ཥ",$t);
			$t=uniReplace("^w","ཝ",$t);
			$t=uniReplace("^l","ལ",$t);
			$t=uniReplace("^y","ཡ",$t);
			$t=uniReplace("^Y","ཡ",$t);
			$t=uniReplace("^aa","ཨ",$t);
			$t=uniReplace("^a","ཨ",$t);
			
			$t=uniReplace("^e",array("ཨ","ེ"),$t);
			$t=uniReplace("^o",array("ཨ","ོ"),$t);
			$t=uniReplace("^i",array("ཨ","ོ"),$t);
			$t=uniReplace("^u",array("ཨ","ུ"),$t);
			$t=uniReplace("^A",array("ཨ","ཱ"),$t);
			$t=uniReplace("^E",array("ཨ","ཱ","ེ"),$t);
			$t=uniReplace("^O",array("ཨ","ཱ","ོ"),$t);
			$t=uniReplace("^I",array("ཨ","ཱ","ི"),$t);
			$t=uniReplace("^U",array("ཨ","ཱ","ུ"),$t);

			$t=uniReplace(".kh","ྑ",$t);
			$t=uniReplace(".k","ྐ",$t);
			$t=uniReplace(".g","ྒ",$t);
			$t=uniReplace(".ng","ྔ",$t);
			$t=uniReplace(".ny","ྙ",$t);
			$t=uniReplace(".naa.da","ྂ",$t);
			$t=uniReplace(".n","ྣ",$t);
			$t=uniReplace(".ch","ྖ",$t);
			$t=uniReplace(".c","ྕ",$t);
			$t=uniReplace(".dz","ྫ",$t);
			$t=uniReplace(".th","ྠ",$t);
			$t=uniReplace(".tsh","ྪ",$t);
			$t=uniReplace(".ts","ྩ",$t);
			$t=uniReplace(".t","ྟ",$t);
			$t=uniReplace(".d","ྡ",$t);
			$t=uniReplace(".sh","ྴ",$t);
			$t=uniReplace(".s","ྶ",$t);
			$t=uniReplace(".j","ྗ",$t);
			$t=uniReplace(".z","ྯ",$t);
			$t=uniReplace(".m","ྨ",$t);
			$t=uniReplace(".ph","ྥ",$t);
			$t=uniReplace(".p","ྤ",$t);
			$t=uniReplace(".b","ྦ",$t);
			$t=uniReplace(".halanta","྄",$t);
			$t=uniReplace(".h","ྷ",$t);
			$t=uniReplace(".achung","ྰ",$t);
			$t=uniReplace(".R","ྼ",$t);
			$t=uniReplace(".r","ྲ",$t);
			$t=uniReplace(".Th","ྛ",$t);
			$t=uniReplace(".T","ྚ",$t);
			$t=uniReplace(".D","ྜ",$t);
			$t=uniReplace(".N","ྞ",$t);
			$t=uniReplace(".Sh","ྵ",$t);
			$t=uniReplace(".w","ྭ",$t);
			$t=uniReplace(".W","ྺ",$t);
			$t=uniReplace(".x","༹༷",$t);
			$t=uniReplace(".lce_tsa_can","ྈ",$t);
			$t=uniReplace(".l","ླ",$t);
			$t=uniReplace(".y","ྱ",$t);
			$t=uniReplace(".Y","ྻ",$t);
			$t=uniReplace(".a","ྸ",$t);
			$t=uniReplace(".A",array("ྸ","ཱ"),$t);
			
			$t=str_replace("/a;","/",$t);
			$t=uniReplace("/e;","ེ",$t);
			$t=uniReplace("/i;","ི",$t);
			$t=uniReplace("/o;","ོ",$t);
			$t=uniReplace("/u;","ུ",$t);
			$t=uniReplace("/A;","ཱ",$t);
			$t=uniReplace("/MM;","ྃ",$t);
			$t=uniReplace("/M;","ཾ",$t);
			$t=uniReplace("/-i;","ྀ",$t);
			$t=uniReplace("/a-i;","ྀ",$t);
			$t=uniReplace("/aai;","ཻ",$t);
			$t=uniReplace("/aau;","ཽ",$t);
			$t=uniReplace("/au;","ཽ",$t);
			
			$t=uniReplace("/E;",array("ཱ","ེ"),$t);
			$t=uniReplace("/I;",array("ཱ","ི"),$t);
			$t=uniReplace("/O;",array("ཱ","ོ"),$t);
			$t=uniReplace("/U;",array("ཱ","ུ"),$t);
			$t=uniReplace("/A-i;",array("ཱ","ྀ"),$t);
			$t=uniReplace("/A-iM;",array("ཱ","ྀ","ཾ"),$t);
			$t=uniReplace("/A-iMM;",array("ཱ","ྀ","ྃ"),$t);
			$t=uniReplace("/Aaai;",array("ཱ","ཻ"),$t);
			$t=uniReplace("/Anaa_da;",array("ཱ","ྂ"),$t);
			$t=uniReplace("/Aaau;",array("ཱ","ཽ"),$t);
			$t=uniReplace("/Aaai;",array("ཱ","ཻ"),$t);
			$t=uniReplace("/eaai;",array("ེ","ཻ"),$t);

			$t=uniReplace("/AM;",array("ཱ","ཾ"),$t);
			$t=uniReplace("/AMM;",array("ཱ","ྃ"),$t);
			$t=uniReplace("/IM;",array("ཱ","ི","ཾ"),$t);
			$t=uniReplace("/IMM;",array("ཱ","ི","ྃ"),$t);
			$t=uniReplace("/EM;",array("ཱ","ེ","ཾ"),$t);
			$t=uniReplace("/EMM;",array("ཱ","ེ","ྃ"),$t);
			$t=uniReplace("/OM;",array("ཱ","ོ","ཾ"),$t);
			$t=uniReplace("/OMM;",array("ཱ","ོ","ྃ"),$t);
			$t=uniReplace("/UM;",array("ཱ","ུ","ཾ"),$t);
			$t=uniReplace("/UMM;",array("ཱ","ུ","ྃ"),$t);
			
			$t=uniReplace("/aM;",array("ཾ"),$t);
			$t=uniReplace("/aMM;",array("ྃ"),$t);
			$t=uniReplace("/iM;",array("ི","ཾ"),$t);
			$t=uniReplace("/iMM;",array("ི","ྃ"),$t);
			$t=uniReplace("/eM;",array("ེ","ཾ"),$t);
			$t=uniReplace("/eMM;",array("ེ","ྃ"),$t);
			$t=uniReplace("/oM;",array("ོ","ཾ"),$t);
			$t=uniReplace("/oMM;",array("ོ","ྃ"),$t);
			$t=uniReplace("/uM;",array("ུ","ཾ"),$t);
			$t=uniReplace("/uMM;",array("ུ","ྃ"),$t);
			
			
			$t=uniReplace("/-iMM;",array("ྀ","ྃ"),$t);
			$t=uniReplace("/-iM;",array("ྀ","ཾ"),$t);
			$t=uniReplace("/a-iMM;",array("ྀ","ྃ"),$t);
			$t=uniReplace("/a-iM;",array("ྀ","ཾ"),$t);
			$t=uniReplace("/aaiM;",array("ཻ","ཾ"),$t);
			$t=uniReplace("/aauM;",array("ཽ","ཾ"),$t);
			$t=uniReplace("/auM;",array("ཽ","ཾ"),$t);
			$t=uniReplace("/aaiMM;",array("ཻ","ྃ"),$t);
			$t=uniReplace("/aauMM;",array("ཽ","ྃ"),$t);
			$t=uniReplace("/MM-i;",array("ྃ","ྀ"),$t);
			$t=uniReplace("/aM-i;",array("ཾ","ྀ"),$t);
			$t=uniReplace("/a__ii;",array("ྀ","ི"),$t);
			
			$ligaTable.="$uni\t$name\t$t\n";
			//$ligaSort[$ligaLength].="$uni\t$name\t$t\n";
		}
		writeText($ligaTable,$path_list_name."_uni.liga");
		exit(0);
}		
		
//читаем таблицу последовательности набора каждой буквы	

		$text=readText($path_list_name."_uni.liga");
		$ligaTable=explode("\n",$text);
		for($i=0;$i<count($ligaTable);$i++){
			
			$line=explode("\t",$ligaTable[$i]);
			$uni=$line[0];
			$name=$line[1];
			$t=$line[2];

			$t=str_replace("//","/",$t);
			$t=str_replace("/;","/",$t);
			$ligaLength=count(explode("/",$t)); 
			
			$ligaSort[$ligaLength].=$uni."\t".$name."\t".$t."\t\n";
		    	
		}
		
		
print "\nstart build openType unicode script\n";	

for($index=100;$index>=0;$index--){
					if(count($ligaSort[$index])!=0){
					    $ligaText=$ligaSort[$index];
					    $liga=explode("\n",$ligaText);
						print "index:$index count:".count($liga)."\n";
					}
}
//exit(0);					
					


//создаем скрипт для программы FontForge создающий в шрифте инструкции для набора лигатур букв в lookup OpenType 
if(!is_file($path_list_name."_uni.pe")){

	$uni=Array();
	$uni["0f68"]="tib.a";
	$uni["0f00"]="aoM";
	$uni["0f01"]="gter.yig";
	$uni["0f02"]="gter.yig.mgo.um.rnam.bcad.ma";
	$uni["0f03"]="gter.yig.mgo.um.gter.tsheg.ma";
	$uni["0f04"]="ini.yig.mgo.mdun.ma";
	$uni["0f05"]="close.yig.mgo.sgab.ma";
	$uni["0f06"]="caret.yig.mgo.phur.shad.ma";
	$uni["0f07"]="yig.mgo.tsheg.shad.ma";
	$uni["0f08"]="sbrul.shad";
	$uni["0f09"]="bskur.yig.mgo";
	$uni["0f0a"]="bka.shog.yig.mgo";
	$uni["0f0b"]="inter.tsheg";
	$uni["0f0d"]="shad";
	$uni["0f0e"]="nyis.shad";
	$uni["0f0f"]="tsheg.shad";
	$uni["0f10"]="nyis.tsheg.shad";
	$uni["0f11"]="rin.chen.spungs.shad";
	$uni["0f12"]="rgya.gram.shad";
	$uni["0f13"]="caret.dzud.rtags.me.long.can";
	$uni["0f14"]="gter.tsheg";
	$uni["0f15"]="chad.rtags";
	$uni["0f16"]="lhag.rtags";
	$uni["0f17"]="sgra.gcan.-char.rtags";
	$uni["0f18"]="khyud.pa";
	$uni["0f19"]="sdong.tshugs_";
	$uni["0f1a"]="rdel.dkar.gcig";
	$uni["0f1b"]="rdel.dkar.gnyis";
	$uni["0f1c"]="rdel.dkar.gsum";
	$uni["0f1d"]="rdel.nag.gcig";
	$uni["0f1e"]="rdel.nag.gnyis";
	$uni["0f1f"]="rdel.dkar.rdel.nag";
	$uni["0f20"]="tib.zero";
	$uni["0f21"]="tib.one";
	$uni["0f22"]="tib.two";
	$uni["0f23"]="tib.three";
	$uni["0f24"]="tib.four";
	$uni["0f25"]="tib.five";
	$uni["0f26"]="tib.six";
	$uni["0f27"]="tib.seven";
	$uni["0f28"]="tib.eight";
	$uni["0f29"]="tib.nine";
	$uni["0f2a"]="tib.half.one";
	$uni["0f2b"]="tib.half.two";
	$uni["0f2c"]="tib.half.three";
	$uni["0f2d"]="tib.half.four";
	$uni["0f2e"]="tib.half.five";
	$uni["0f2f"]="tib.half.six";
	$uni["0f30"]="tib.half.seven";
	$uni["0f31"]="tib.half.eight";
	$uni["0f32"]="tib.half.nine";
	$uni["0f33"]="tib.half.zero";
	$uni["0f34"]="bsdus.rtags";
	$uni["0f35"]="uni0F35";
	$uni["0f36"]="caret.dzud.rtags.bzhi.mig.can";
	$uni["0f37"]="ngas.bzung.sgor.rtags";
	$uni["0f38"]="che.mgo";
	$uni["0f7f"]="rnam.bchad";
	$uni["0f3a"]="gug.rtags.gyon";
	$uni["0f3b"]="gug.rtags.gyas";
	$uni["0f3c"]="ang.khang.gyon";
	$uni["0f3d"]="ang.khang.gyas";
	$uni["0f3e"]="yar.tshes";
	$uni["0f3f"]="mar.tshes";
	$uni["0f40"]="tib.ka";
	$uni["0f41"]="tib.kha";
	$uni["0f42"]="tib.ga";
	$uni["0f43"]="tib.gha";
	$uni["0f44"]="tib.nga";
	$uni["0f45"]="tib.ca";
	$uni["0f46"]="tib.cha";
	$uni["0f47"]="tib.ja";
	$uni["0f49"]="tib.nya";
	$uni["0f4a"]="tib.Ta";
	$uni["0f4b"]="tib.Tha";
	$uni["0f4c"]="tib.Da";
	$uni["0f4e"]="tib.Na";
	$uni["0f4f"]="tib.ta";
	$uni["0f50"]="tib.tha";
	$uni["0f51"]="tib.da";
	$uni["0f53"]="tib.na";
	$uni["0f54"]="tib.pa";
	$uni["0f55"]="tib.pha";
	$uni["0f56"]="tib.ba";
	$uni["0f58"]="tib.ma";
	$uni["0f59"]="tib.tsa";
	$uni["0f5a"]="tib.tsha";
	$uni["0f5b"]="tib.dza";
	$uni["0f5c"]="tib.dzha";
	$uni["0f5d"]="tib.wa";
	$uni["0f5e"]="tib.zha";
	$uni["0f5f"]="tib.za";
	$uni["0f60"]="a.chung";
	$uni["0f61"]="tib.ya";
	$uni["0f62"]="tib.ra";
	$uni["0f63"]="tib.la";
	$uni["0f64"]="tib.sha";
	$uni["0f65"]="tib.Sha";
	$uni["0f66"]="tib.sa";
	$uni["0f67"]="tib.ha";
	$uni["0f6a"]="tib.Ra";
	$uni["0f71"]="tib.A.chung";
	$uni["0f72"]="tib.i";
	$uni["0f73"]="tib.I";
	$uni["0f74"]="tib.u";
	$uni["0f75"]="tib.U";
	$uni["0f76"]="tib.vocalic.r";
	$uni["0f77"]="tib.vocalic.rr";
	$uni["0f78"]="tib.vocalic.l";
	$uni["0f79"]="tib.vocalic.ll";
	$uni["0f7a"]="tib.e";
	$uni["0f7b"]="tib.1f.aai";
	$uni["0f7c"]="tib.o";
	$uni["0f7d"]="tib.1f.aau";
	$uni["0f7e"]="rjes.su.nga.ro";
	$uni["0f80"]="tib.1f._i";
	$uni["0f81"]="tib.A1f.i";
	$uni["0f84"]="halanta";
	$uni["0f85"]="paluta";
	$uni["0f88"]="lce.tsa.can";
	$uni["0f89"]="mchu.can";
	$uni["0f8a"]="gru.can.rgyings";
	$uni["0f8b"]="gru.med.rgyings";
	$uni["0f8d"]="uni0F8D";
	$uni["0f90"]="tib.1f.ka";
	$uni["0f91"]="tib.1f.kha";
	$uni["0f92"]="tib.1f.ga";
	$uni["0f93"]="tib.1f.gha";
	$uni["0f94"]="tib.1f.nga";
	$uni["0f95"]="tib.1f.ca";
	$uni["0f96"]="tib.1f.cha";
	$uni["0f97"]="tib.1f.ja";
	$uni["0f99"]="tib.1f.nya";
	$uni["0f9a"]="tib.1f.Ta";
	$uni["0f9b"]="tib.1f.Tha";
	$uni["0f9c"]="tib.1f.Da";
	$uni["0f9d"]="tib.1f.Dha";
	$uni["0f9e"]="tib.1f.Na";
	$uni["0f9f"]="tib.1f.ta";
	$uni["0fa0"]="tib.1f.tha";
	$uni["0fa1"]="tib.1f.da";
	$uni["0fa2"]="tib.1f.dha";
	$uni["0fa3"]="tib.1f.na";
	$uni["0fa4"]="tib.1f.pa";
	$uni["0fa5"]="tib.1f.pha";
	$uni["0fa6"]="tib.1f.ba";
	$uni["0fa7"]="tib.1f.bha";
	$uni["0fa8"]="tib.1f.ma";
	$uni["0fa9"]="tib.1f.tsa";
	$uni["0faa"]="tib.1f.tsha";
	$uni["0fab"]="tib.1f.dza";
	$uni["0fac"]="tib.1f.dzha";
	$uni["0fad"]="tib.1f.wa";
	$uni["0fae"]="tib.1f.zha";
	$uni["0faf"]="tib.1f.za";
	$uni["0fb0"]="tib.1f.a.chung";
	$uni["0fb3"]="tib.1f.la";
	$uni["0fb4"]="tib.1f.sha";
	$uni["0fb5"]="tib.1f.Sha";
	$uni["0fb6"]="tib.1f.sa";
	$uni["0fb7"]="tib.1f.ha";
	$uni["0fb8"]="tib.1f.a";
	$uni["0fba"]="tib.1f.Wa";
	$uni["0fbb"]="tib.1f.Ya";
	$uni["0fbc"]="tib.1f.Ra";
	$uni["0fbe"]="ku.ru.kha";
	$uni["0fbf"]="ku.ru.kha.bzhi.mig.can";
	$uni["0fc0"]="heavy.beat";
	$uni["0fc3"]="sbub.chal";
	$uni["0fc4"]="dril.bu";
	$uni["0fc5"]="rdo.rje";
	$uni["0fc6"]="padma.gdan";
	$uni["0fc7"]="rdo.rje.rgya.gram";
	$uni["0fc8"]="phur.pa";
	$uni["0fc9"]="nor.bu";
	$uni["0fca"]="nor.bu.nyis.khyil";
	$uni["0fcb"]="nor.bu.gsum.khyil";
	$uni["0fcc"]="nor.bu.bzhi.khyil";
	$uni["0fcf"]="rdel.nag.gsum";
	$uni["0fd0"]="bska.shog.gi.mgo.rgyan";
	$uni["0fd1"]="mnyam.yig.gi.mgo.rgyan";
	$uni["0f70"]="uni0F70";
	$uni["0f6f"]="uni0F6F";
	$uni["0fb1"]="tib.1f.ya";
	$uni["0fb2"]="tib.1f.ra";
	$uni["0fb9"]="tib.1f.kSha";
	$uni["0fbd"]="u0x0FBD";
	$uni["0fc1"]="light.beat";
	$uni["0fc2"]="cang.te.u";
	$uni["0fce"]="tib.1f.oM";
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
	$uni["0feb"]="tib.1f.aauM";
	$uni["0fec"]="uni0FEC";
	$uni["0fed"]="uni0FED";
	$uni["0fee"]="uni0FEE";
	$uni["0fef"]="tib.1f.M";
	$uni["0ff0"]="uni0FF0";
	$uni["0ff1"]="uni0FF1";
	$uni["0ff2"]="uni0FF2";
	$uni["0ff3"]="uni0FF3";
	$uni["0ff5"]="tib.1f.iM";
	$uni["0ff6"]="uni0FF6";
	$uni["0ff7"]="uni0FF7";
	$uni["0ff8"]="tib.1f.M_i1";
	$uni["0ff9"]="uni0FF9";
	$uni["0ffa"]="uni0FFA";
	$uni["0ffc"]="uni0FFC";
	$uni["0ffd"]="uni0FFD";
	$uni["0ffe"]="uni0FFE";
	$uni["0fff"]="tib.1f.oMM";
	$uni["0f52"]="tib.dha";
	$uni["0f39"]="tsa.phru";
	$uni["0f83"]="sna_ldan";
	$uni["0f82"]="nyi.zla.naa.da";
	$uni["0f57"]="tib.bha";
	$uni["0f4d"]="tib.Dha";

	print "ligaSort count:".count($ligaSort)."\n";
	$ligaTextMain="";
	$linesInTable=1000;
	$lookup=0;

				for($index=100;$index>=0;$index--){
					if(count($ligaSort[$index])!=0){
					
						$ligaText=$ligaSort[$index];
						$liga=explode("\n",$ligaText);
						print "index: $index liga count:".count($liga)."\n";
						
						$splitCount=count($liga)/$linesInTable;
						if(count($liga)<$linesInTable)$splitCount=1;
					  	for($c=0;$c<$splitCount;$c++){
					  	
							$ligaTable="";
							$table=$index;
							
							$ligaTextMain.="\n\n\n\nAddLookupSubtable(\"tibetanLigatures".$lookup."\",\"ligaData".$table."_".$c."\");\n"; 	
							$lookup++;
							$position=$c*$linesInTable;
							
							
							for($i=$position;$i<count($liga)&&$i<$position+$linesInTable;$i++){		    		
								$l=str_replace("/","\t",$liga[$i]);
								$l=str_replace("\t/","\t",$l);
								$l.=":|:";
								$l=str_replace("\t:|:","",$l);
								$l=str_replace(":|:","",$l);
								$line=explode("\t",$l);     	
								$ligaTextMain.="Select(\"".$line[1]."\"); AddPosSub(\"ligaData".$table."_".$c."\", \"";
								$data="";
								for($n=2;$n<count($line);$n++){
									if(strlen($line[$n])){
										$glyphName=$uni[$line[$n]];
										if($glyphName==""){
											print "Alert: glyphName ".$line[$n]." in line $l// not exist in font!\n";
											continue;
										}
										$data.=$glyphName." ";
									}	
								}
								$data.=":|:";
								$data=str_replace(" :|:","",$data);
								$data=str_replace(":|:","",$data);
								$ligaTextMain.="$data\");\n";
							}
						}	
					
					}
				
				}
			$text="";
			for($n=$lookup;$n>=0;$n--){
				$text.="AddLookup(\"tibetanLigatures$n\", \"gsub_ligature\", 0, [[\"liga\",[ [\"DFLT\",[\"dflt\"]],[\"tibt\",[\"dflt\"]] ] ]])\n";	
			}
			$text.=$ligaTextMain;
			
 		 
			writeText($text,$path_list_name."_uni.pe");
			
	
}

print "\nstart build FontForge glyph composer script\n";
//создаем скрипт для программы FontForge, создающий в шрифте буквы расположенные по адресу юникода указанному в таблице кодировки
$codeUni=Array(); 

//сначала читаем кабицу кодировки шрифта в соответствии с стандартом GB18030
		$text=readText("/Volumes/WORK_DHARMA_3TB/SAMSUNG/MainYagpoOCR/Font/__SCRIPT_FONTFORGE/GB18030.tab");
		$text=str_replace("\r","\n",$text);
		$codeTable=explode("\n",$text);
		for($i=0;$i<count($codeTable);$i++){
			
			$line=explode("\t",$codeTable[$i]);
			$uni=$line[0];
			$name=$line[1];
			$codeUni[$name]=$uni;
			
			
		    	
		}
		
print "start build script codeUni count:".count($codeUni)."\n";

$glyphIndex=4189;  //количество базовых букв в шрифте

if(!is_file($path_list_name."_glyph_uni.pe")||1){
	$codeText="#clear encoding from all letters in GB18030 table\n";
	foreach($codeUni as $name => $uni){
		if($name==""&&$uni=="")continue;
		$uni=str_replace("0x","",$uni);
		$fHex="U+".$uni;
		$nameUni="uni$uni";
		$codeText.="
				if(SelectIf(\"$fHex\")>0)
					Clear();
				endif;	
				if(SelectIf(\"$nameUni\")>0)
					Clear();
				endif;	
		";
	}
	$codeText.="\n#set encoding from all letters in GB18030 table\n";
	
	
	
	foreach($codeUni as $name => $uni){
		if($name==""&&$uni=="")continue;
		$codeText.="		
			Select($glyphIndex)
			SetGlyphName(\"$name\");
			Select(\".notdef\")
			CopyReference();
			Select($glyphIndex);
			Paste();
			SetUnicodeValue($uni,0);
		";		
		$glyphIndex++;
	}
	
	//расширение кодировки GB18030 буквы вносятся в дополнительную кодировку 
	//на основании тибетского корпуса текстов и результатов работы программы OCR
	//Открытой Буддийской Библиотеки www.buddism.ru
	
	print "start build GB18030 addition table\n";
	
	$ligaArray=Array();
	
	for($index=100;$index>=0;$index--){
					if(count($ligaSort[$index])!=0){
						$ligaText.=$ligaSort[$index];
						$liga=explode("\n",$ligaText);
						print "index: $index liga count:".count($liga)."\n";
					  						
					
						for($i=0;$i<count($liga);$i++){		    		
							$l=str_replace("/","\t",$liga[$i]);
							$l=str_replace("\t\t","\t",$l);
							$l.=":|:";
							$l=str_replace("\t:|:","",$l);
							$l=str_replace(":|:","",$l);
							$line=explode("\t",$l);
							$name=$line[1];
							if($codeUni[$name]==""){
								$ligaArray[$name]=Array(); 
								for($n=2;$n<count($line);$n++){
									if($codeUni[$name]==""){
										$ligaArray[$name][]=$line[$n];
									}	
								}	
							}
						}
					}		  	
	}						
	
	print "count ligaArray:".count($ligaArray)."\n";
	
	$codeText.="\n#clear encoding from all letters in GB18030 addition table\n";
	
	$f=0xf1680;	
		
	foreach($ligaArray as $name=>$uniArray){
		if($name=="")continue;
		$fHex="U+".dechex($f);
		$nameUni="uni".dechex($f);
		$codeText.="
				if(SelectIf(\"$fHex\")>0)
					Clear();
				endif;	
				if(SelectIf(\"$nameUni\")>0)
					Clear();
				endif;	
		";
		$f++;
	}
	$codeText.="\n#set encoding from all letters in GB18030 addition table\n";
	
	$f=0xf1680;	

	foreach($ligaArray as $name=>$uniArray){
		if($name=="")continue;
		$codeText.="		
			Select($glyphIndex)
			SetGlyphName(\"$name\");
			Select(\".notdef\")
			CopyReference();
			Select($glyphIndex);
			Paste();
			SetUnicodeValue($f,0)
		";		
		$f++;
		$glyphIndex++;
	}

	writeText($codeText,$path_list_name."_glyph_uni.pe");
}

		
?>
