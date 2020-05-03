<?php

//скрипт анализирует порядок набора тибетских букв, записанный  в файле SambhotaUni_uni.liga
//для каждой буквы определяется из каких частей она должна быть составлена
//каждая часть буквы устанавливается в букве в соответствии с данными о контрольных точках Anchor Point
//составных частей букв OpenType записанными в файле _anchorList.txt
//_anchorList.txt создается в программе FontForge как результат работы скрипта getAnchor.pe
//При установки каждой последующей части буквы Anchor Point совмещается с соответствующей Anchor Point предыдущей буквы
//или с Anchor Point первой буквы, в соответствии с правилами набора.
//Сарва Мангалам!

$needClean=0;  //set 1 if need remove all ligature glyphs before build font
$p=0;
$glyph="";
$collectVowels=0;    // режим сборки стеков вместе с оглассовками
// $collectVowels=1;  - режим расстановки оглассовок на коренными буквами

//global
$joinStackLength=0;

include ("/MainYagpoOCR/Font_YagpoUni_2017/__SCRIPT_FONTFORGE/@liga_glyph_init.php");
include ("/MainYagpoOCR/Font_YagpoUni_2017/__SCRIPT_FONTFORGE/@liga_glyph_setNexLetter.php");
	
	

	
	$index=0;
	$count=0;
	$codeText="";
	
	$anchor=Array();
	$anchor["_o"]=669;
	$anchor["_MM"]=477;
	$anchor["_oM"]=669;
	$anchor["_naa_da"]=574;
	$anchor["_M"]=527;
	$anchor["_au"]=610;
	$anchor["_onaa_da"]=669;
	$anchor["__ii"]=805;
	$anchor["a_ii"]=805;
	$anchor["_oMM"]=669;
	$anchor["_auM"]=610;

	
 	foreach($ligaArray as $name=>$uniArray){
		$stackL=count($uniArray);
 		if($name=="")continue;
		if($stackL==1)continue;
		if($glyph!=""&&$name!=$glyph)continue;
		
		if($p)print "\n@1@".$name."\n";
		
		if($count%1000==0)print "$count."; 
		$count++;

		//проверяем есть ли готовый стек к которому необходимо добавить надписные буквы
		//
		if(isset($ligaReadyArray[$name])){
			//$codeText.="$name exist \n"; 
			continue;
		}
		
		if(strlen($name)>1&&$collectVowels==1){
		
		
			foreach($ligaReadyArray as $nameReady=>$uniReady){
				if(strlen($nameReady)<2)continue;
				$a=substr($nameReady,strlen($nameReady)-1,1);
				$n=$nameReady;
				if($a=="a"||$a=="A"){
					$n=substr($nameReady,0,strlen($nameReady)-1);
				}	
				if(strpos ($name,$n)===0){
					$lt=substr($name,strlen($n),strlen($name));
					$dot=substr($lt,0,1);
					if($dot=="."||
					$dot=="_"||
					$dot=="y"||
					$dot=="s"||
					$dot=="h"||$dot=="c"||
					$dot=="z"||$dot=="g"||
					strpos($lt,"alant")===0||
					strpos($lt,"ter")===0||
					strpos($lt,"ru_can_rgyings")===0||
					strpos($lt,"aa_da")===0) 
					continue;
					
					$vowel=substr($lt,0,1);
					if($vowel=="A"){
						$n_=$n."A";
						if(isset($ligaReadyArray[$a])){
							$n.="A";
							$lt=str_replace("A","",$lt);
						}
					}
					if($vowel=="E"){
						$n_=$n."A";
						if(isset($ligaReadyArray[$a])){
							$n.="A";
							$lt=str_replace("E","e",$lt);
						}
					}
					if($vowel=="O"){
						$n_=$n."A";
						if(isset($ligaReadyArray[$a])){
							$n.="A";
							$lt=str_replace("O","o",$lt);
						}
					}
					if($vowel=="I"){
						$n_=$n."A";
						if(isset($ligaReadyArray[$a])){
							$n.="A";
							$lt=str_replace("I","i",$lt);
						}
					}
					if($vowel=="U"){
						$n_=$n."U";
						if(isset($ligaReadyArray[$a])){
							$n.="U";
							$lt=substr($lt,1,strlen($lt));
						}else{
							$n.="a";
						}
					}
					if($vowel=="u"){
						$n_=$n."u";
						if(isset($ligaReadyArray[$a])){
							$n.="u";
							$lt=substr($lt,1,strlen($lt));
						}else{
							$n.="a";
						}
					}
					if($vowel=="a"){
						$n.="a";
						$lt=substr($lt,1,strlen($lt));
					}
					if($vowel=="e"||$vowel=="i"||$vowel=="o")$n.="a";

					if($lt==".halanta"){
						$n.="a";
						$lt=substr($lt,1,strlen($lt));
					}
					if($lt=="a.da"){
						$lt="naa.da";
					}
					//print "@ $name => $n => $lt\n";
					if($lt=="")break;
  					$n=str_replace("a_chunga","a_chung",$n);
  					$n=str_replace("gter_yiga","gter_yig",$n);
					$lt="_".$lt;
					$codeText.="# $name => $n => $lt\n";
					$nameUni="U+".$ligaUniArray[$name];
					
					if(isset($anchor[$lt])){
						$a1=$anchor[$lt];
						$codeText.="
							Select(\"$n\");
							w=GlyphInfo(\"Xextrema\",1769);
							w_=(w[1]-w[0]);
							a1=w[0]+w_/2-w_/23;
							a1=a1+$a1;
							CopyReference();
							Select(\"$nameUni\");
							Paste();
							Select(\"$lt\");
							CopyReference();
							Select(\"$nameUni\");
							PasteWithOffset(a1,0);\n";
					
					}else{
						$codeText.="
							Select(\"$n\");
							w=GlyphInfo(\"Width\");
							CopyReference();
							Select(\"$nameUni\");
							Paste();
							Select(\"$lt\");
							CopyReference();
							Select(\"$nameUni\");
							PasteWithOffset(w,0);\n";
					}	
					break;
				}
		
			}
			continue;
		}		
		$uniCode=$ligaUniArray[$name];
		

		$codeText.="\n\n		Select(\".notdef\"); CopyReference(); Select(\"U+$uniCode\"); Paste();";   //стираем старую и создаем новую букву
		
		//проверяем сколько базовых букв в стеке.
		$l=0;
		//сначала устанавливаем порядок набора тибетских надписных согласных lce_tsa_can

/**/
		
/*		
		if($stackL>1&&($uni[$uniArray[$stackL-1]]=="lce_tsa_can"||$uni[$uniArray[$stackL-1]]=="mchu_can"||$uni[$uniArray[$stackL-1]]=="gru_can_rgyings"
		||$uni[$uniArray[$stackL-1]]=="gru_med_rgyings")){
			$t=$uniArray[$stackL-1];
			for($i=$stackL-1;$i>0;$i--){
				$uniArray[$i]=$uniArray[$i-1];
			}
			$uniArray[0]=$t;
		}
*/		
		for($i=0;$i<$stackL;$i++){
			if(!isset($uni[$uniArray[$i]])){print "  $name not set letter by code1:".$uniArray[$i]."\n"; exit(0);}
			
			if(isStopLetter($uni[$uniArray[$i]])){
				//проверяем есть ли после подписной буквы еще базовые буквы (встречается в санскрите)
				if($i+1<$stackL){
					if(!isset($uni[$uniArray[$i+1]])){print " @1 i:$i ".
						count($uniArray)." $name not set letter by code:".$uniArray[$i+1]." -> ". var_dump($uniArray)."\n"; exit(0);}
					
					if(!isStopLetter($uni[$uniArray[$i+1]])){
						$l++;
						continue;
					}
				}
				break;
			}	
 			$l++;
		}
		if($p)print "1stack base letters count: l:".$l."\n"; //exit(0);

		
		//анализируем стек
		$rootGlyph="";  //коренная буква стека
		$baseGlyph="";  //базовая буква это последняя смонтированная буква стека
		$baseGlyphX=0;  //смещение базовой буквы относительно коренной буквы стека
		$baseGlyphY=0;
		$prefix="";     //дискретный размер букв из которых собирается стек
		$stackL=$stackL;
		

 		for($i=0;$i<$stackL;$i++){
 			if($p)print "/".$uniArray[$i]."/\n";
 			
 			//установка стека с одной коренной буквой и одной базовой буквой
 			if($i==0){
 				//ставим в стек коренную букву нужного размера.
 				$b=$uni[$uniArray[0]];
 				$n=$uni[$uniArray[1]];
  				$nextNext="";
 				if($stackL>$i+2)$nextNext=$uni[$uniArray[$i+2]];
 				//проверяем меняет ли базовая буква свою форму за при присоединении следующей за ней буквы
				$newNext=isJoined($b,$uni[$uniArray[$i+1]],$nextNext);
				
				if(($newNext=="rtsa"||$newNext=="rdza")&&$l>1)$l--;
				
				if($l==1)$prefix="c0.";
 				if($l==2)$prefix="c1.";
 				if($l==3)$prefix="c2.";
 				if($l>3)$prefix="c3.";

				
				if($p)print "@1@ b:$b n:$n prefix:$prefix newNext:$newNext stackL:$stackL\n";
				//exit(0);
				if($newNext!=""){
						if($stackL==2||($stackL>2&&isTopLetter($uni[$uniArray[2]]))){
							$rootGlyph="c0_.".$newNext;
						}else{
							if($l==1)$rootGlyph="c_.".$newNext;
							if($l==2)$rootGlyph="c1.".$newNext;
							if($l==3)$rootGlyph="c2.".$newNext;
							if($l>3)$rootGlyph="c3.".$newNext;
						}	
						setLetter($name,$rootGlyph);
						$codeText.=setLetter($name,$rootGlyph);
						$baseGlyph=$rootGlyph;
						$i+=$joinStackLength;
						continue;
				}
 				$rootGlyph=$prefix.$b;
 				if($l>1&&($b=="ra"&&$n!="nya"&&$n!="la")){
					$rootGlyph="ra_short";
					if($stackL==2||($stackL>2 &&isTopLetter($uni[$uniArray[2]]) ) )$prefix="c0.";
				}

				if($l<2&&($n=="vasur"||$n=="_A"||$n=="_u"||$n=="ratag")&&($b=="ha"||$b=="ka"||$b=="ga"||$b=="ta"||$b=="da"||$b=="na"||$b=="nya"||$b=="sha"||$b=="zha")){
					if($b=="ha"&&$stackL>2&&$uni[$uniArray[2]]=="_u"){
						$rootGlyph.="2";
					}else{
						$rootGlyph.="1";
					}
				}
				
 				$codeText.=setLetter($name,$rootGlyph);
 				$baseGlyph=$rootGlyph;
 				continue;
 			}
 			
 			if($i>0){
 				//если в стеке коренная буква и базовая буква, то ставим в стек базовую букву размера $prefix.
 				$next=$uni[$uniArray[$i]];
 				if($p)print "@@@0 i:$i next:$next baseGlyph:$baseGlyph root:$rootGlyph stackL:$stackL\n";
 				
 				
 				if($stackL>$i+1){
					//проверяем меняет ли базовая буква свою форму за при присоединении следующей за ней буквы
					$b=$uni[$uniArray[$i]];
	 				$n=$uni[$uniArray[$i+1]];
	 				
	 				if($stackL>$i+2)$nextNext=$uni[$uniArray[$i+2]];
	 				
					$newNext=isJoined($next,$uni[$uniArray[$i+1]],$nextNext);
					
					if($p)print "@@@1 i:$i b:$b n:$n newNext:$newNext root:$rootGlyph stackL:$stackL\n";
					if($newNext!=""){
						if(isTopLetter($next)){
	 						setNextLetter($name,$rootGlyph,$newNext,$uniArray,$i,$l);
	 						$i+=$joinStackLength;
 							continue;
 						}
						if($i==1&&$rootGlyph=="ra_short"){
							if($stackL==3){
								$prefix="c_.";
							}
							if($stackL>3&&isTopLetter($uni[$uniArray[3]])){
								$prefix="c_.";
							}
						}	
						setNextLetter($name,$baseGlyph,$prefix.$newNext,$uniArray,$i,$l);
						$baseGlyph=$prefix.$newNext;
						$i++;
						continue;
					}
					
				}	
				//проверяем меняет ли присоединяемая буква свою форму в зависимости от базовой буквы
 				$b=$uni[$uniArray[$i-1]];
				$n=$uni[$uniArray[$i]];

			
				if($b=="ta"||$b=="sa"){
					if($n=="sa"){
						$next.="1";
					}	
				}
				if($b=="Sha"||$b=="Tha"){ 
					if($n=="ratag"){
						$next.="_";
					}	
				}
				if($b=="ratag"||$b=="ratag1"||$b=="kh.ra"||$b=="d.ra"||$b=="zh.ra"||$b=="n.ra"||$b=="ny.ra"||$b=="h.ra"||
				$b=="j.ra"||$b=="dz.ra"||$b=="r.ra"||$b=="a_chung.ra"||$b=="ng.ra"||$b=="T.ra"||$b=="g.ra"||$b=="k.ra"||$b=="vasur"||$b=="vasur1"){
					if($n=="yatag"){
						$index=$l;
						if($index>4)$index=4;
						$next="yatag3_".$index;
					}	
				}
				
				//if($l==2&&$rootGlyph!="ra_short"&&($n=="ta"||$n=="Ta")){
				//	$prefix="c2.";
				//}
		
				
				if(isTopLetter($next)){
 					setNextLetter($name,$rootGlyph,$next,$uniArray,$i,$l);
 					continue;
 				}

 				setNextLetter($name,$baseGlyph,$prefix.$next,$uniArray,$i,$l);
 				$baseGlyph=$prefix.$next;
 				
 				continue;
 			}

 			
 		}
 		//$codeText.="\n		CenterInWidth();\n";
 		$index++;
 		if($p&&$index>$p-1)break;
 	}
 print "@@@done";
 	
	$path_list_name="/MainYagpoOCR/Font_YagpoUni_2017/_build.pe";
	if($p)print $codeText."\n";
	//exit(0);

	writeText($codeText,$path_list_name);

?>
