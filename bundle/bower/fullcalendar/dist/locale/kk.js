/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

!function(e){"function"==typeof define&&define.amd?define(["jquery","moment"],e):"object"==typeof exports?module.exports=e(require("jquery"),require("moment")):e(jQuery,moment)}(function(e,t){!function(){var e={0:"-ші",1:"-ші",2:"-ші",3:"-ші",4:"-ші",5:"-ші",6:"-шы",7:"-ші",8:"-ші",9:"-шы",10:"-шы",20:"-шы",30:"-шы",40:"-шы",50:"-ші",60:"-шы",70:"-ші",80:"-ші",90:"-шы",100:"-ші"};t.defineLocale("kk",{months:"қаңтар_ақпан_наурыз_сәуір_мамыр_маусым_шілде_тамыз_қыркүйек_қазан_қараша_желтоқсан".split("_"),monthsShort:"қаң_ақп_нау_сәу_мам_мау_шіл_там_қыр_қаз_қар_жел".split("_"),weekdays:"жексенбі_дүйсенбі_сейсенбі_сәрсенбі_бейсенбі_жұма_сенбі".split("_"),weekdaysShort:"жек_дүй_сей_сәр_бей_жұм_сен".split("_"),weekdaysMin:"жк_дй_сй_ср_бй_жм_сн".split("_"),longDateFormat:{LT:"HH:mm",LTS:"HH:mm:ss",L:"DD.MM.YYYY",LL:"D MMMM YYYY",LLL:"D MMMM YYYY HH:mm",LLLL:"dddd, D MMMM YYYY HH:mm"},calendar:{sameDay:"[Бүгін сағат] LT",nextDay:"[Ертең сағат] LT",nextWeek:"dddd [сағат] LT",lastDay:"[Кеше сағат] LT",lastWeek:"[Өткен аптаның] dddd [сағат] LT",sameElse:"L"},relativeTime:{future:"%s ішінде",past:"%s бұрын",s:"бірнеше секунд",m:"бір минут",mm:"%d минут",h:"бір сағат",hh:"%d сағат",d:"бір күн",dd:"%d күн",M:"бір ай",MM:"%d ай",y:"бір жыл",yy:"%d жыл"},dayOfMonthOrdinalParse:/\d{1,2}-(ші|шы)/,ordinal:function(t){var a=t%10,d=t>=100?100:null;return t+(e[t]||e[a]||e[d])},week:{dow:1,doy:7}})}(),e.fullCalendar.datepickerLocale("kk","kk",{closeText:"Жабу",prevText:"&#x3C;Алдыңғы",nextText:"Келесі&#x3E;",currentText:"Бүгін",monthNames:["Қаңтар","Ақпан","Наурыз","Сәуір","Мамыр","Маусым","Шілде","Тамыз","Қыркүйек","Қазан","Қараша","Желтоқсан"],monthNamesShort:["Қаң","Ақп","Нау","Сәу","Мам","Мау","Шіл","Там","Қыр","Қаз","Қар","Жел"],dayNames:["Жексенбі","Дүйсенбі","Сейсенбі","Сәрсенбі","Бейсенбі","Жұма","Сенбі"],dayNamesShort:["жкс","дсн","ссн","срс","бсн","жма","снб"],dayNamesMin:["Жк","Дс","Сс","Ср","Бс","Жм","Сн"],weekHeader:"Не",dateFormat:"dd.mm.yy",firstDay:1,isRTL:!1,showMonthAfterYear:!1,yearSuffix:""}),e.fullCalendar.locale("kk",{buttonText:{month:"Ай",week:"Апта",day:"Күн",list:"Күн тәртібі"},allDayText:"Күні бойы",eventLimitText:function(e){return"+ тағы "+e},noEventsMessage:"Көрсету үшін оқиғалар жоқ"})});