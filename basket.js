const STEP_LENGTH=0.1;  //шаг +- метража
const MIN_LENGTH=0.1;   //мин метраж
const MAX_LENGTH=10;    //макс метраж
const MAX_GOODS=10;    //макс кол-во товаров в корзине
const BASKET='basket';  //имя куки для корзины
function addEvent(cn,ev,f){
    //Подписывает на событие все эл-ты по имени класса
    //cn-строка имя класса,ev-строка событие,f-обработчик
    arr=document.getElementsByClassName(cn);
    for(var i=0;i<arr.length;i++){
        arr[i].addEventListener(ev,f,false);
    }
}
window.onload=function(){
    //подписать на события остальные эл-ты
    this.addEvent('btn_del','click',del);
    this.addEvent('btn_dec','click',dec);
    this.addEvent('btn_inc','click',inc);
    this.addEvent('length','change',calc);
    this.addEvent('btn_to_basket','click',to_basket);     
    //при загрузке вычислить и установить ИТОГО для всех
    this.i_d_bl=document.getElementsByClassName('inc_dec_block');   
    for(var i=0;i<i_d_bl.length;i++){
        set_length(i_d_bl[i]);
        set_sum(i_d_bl[i]);
    }
    //при загрузке установить кол-во товаров в корзине
    set_basket_count(get_goods_count());
    //и ИТОГО страницы
    set_total();
}
function del(e){
    //Вызывается при нажатии 'btn_del', удаляет
    //ел-т из DOM и куку
    e=e||event;
    var rem=e.target.parentNode.parentNode.parentNode;
    var i_d_bl=e.target.parentNode.parentNode.getElementsByClassName('inc_dec_block')[0];
    change_cookie(i_d_bl,0);
    rem.parentNode.removeChild(rem);
    renumerate();
    set_total();
    set_basket_count(get_goods_count());
}
function renumerate(){
    //Переустанавливает нумерацию блоков id_js в DOM-e
    //Нужно при удалении, т.к. удаляется из массива
    //строк корзины методом splice, чтоб сохранить
    //соответсвие индексов
    var arr_els=document.getElementsByClassName('id_js');
    for(var i=0;i<arr_els.length;i++){
        arr_els[i].firstChild.nodeValue=i;
    }
}
// function clear_basket(){
//     //Очищает корзину: удаить куку, удалить список из ДОМ
//     var c_d=new Date();
//     c_d.setTime(c_d.getTime()-1);
//     document.cookie=BASKET+"=;expires="+c_d.toGMTString()+";";
//     good_list=document.getElementsByClassName('good_list')[0];
//     good_list.parentNode.removeChild(good_list);
// }
function to_basket(){
//Добавляет товар в корзину и обновляет счетчик в значке корзины
    if(get_goods_count()<MAX_GOODS){
        var art=document.getElementsByClassName('art')[0].firstChild.nodeValue;
        var len=document.getElementsByClassName('length')[0].value;
        var price=document.getElementsByClassName('price')[0].firstChild.nodeValue;
        var b_s=getCookie(BASKET);
        if(b_s!='')b_s+='|';
        b_s+=art+':'+len+':'+price;
        document.cookie=BASKET+'='+b_s+"; path=/;";
        set_total();
        set_basket_count(get_goods_count());
    }
}
function set_basket_count(c){
//Устанавливает текст в эл-те 'basket_count'>
//и в эл-те 'basket_total'>
//в зависимости от текущей страницы
    document.getElementsByClassName('basket_count')[0].firstChild.nodeValue=c;
}
function get_goods_count(){
//Возвращает кол-во id  в cookie BASKET
    var b_s=getCookie(BASKET);
    if(b_s=="")return 0;
    var as=b_s.split('|');
    return as.length;
}
function getCookie(name) {
//Возвращает строку-значение куки по имени, или ''
    var r = document.cookie.match("(^|;) ?" + name + "=([^;]*)(;|$)");
    if (r) return r[2];
    else return "";
}
function inc(e){
    //Вызывается при нажатии на '+'    
    //-получаем inc_dec_block
    e=e||event;
    var i_d_bl=e.target.parentNode;//srcElement=target
    var id_js;
    var l=toDiapason(get_length(i_d_bl)+STEP_LENGTH);
    try{
        //-если id_js есть- мы в корзине
        isset=e.target.parentNode.parentNode.parentNode.parentNode.getElementsByClassName('id_js');
        change_cookie(i_d_bl,l);
    }catch(x){
        //-иначе- в GoodBig
    }finally{
        //-в любом случае сделать
        i_d_bl.getElementsByClassName('length')[0].value=l;
        set_sum(i_d_bl);
        set_total();
        set_basket_count(get_goods_count());
        e.preventDefault();        
    }
}
function dec(e){
    //Вызывается при нажатии на '-'
    e=e||event;
    var i_d_bl=e.target.parentNode;
    var id_js;
    var l=toDiapason(get_length(i_d_bl)-STEP_LENGTH);
    try{
        //-если id_js есть- мы в корзине
        isset=e.target.parentNode.parentNode.parentNode.parentNode.getElementsByClassName('id_js');
        change_cookie(i_d_bl,l);
    }catch(x){
        //-иначе- в GoodBig
    }finally{
        //-в любом случае сделать
        i_d_bl.getElementsByClassName('length')[0].value=l;
        set_sum(i_d_bl);
        set_total();
        set_basket_count(get_goods_count());
        e.preventDefault();        
    }
}
function toDiapason(l){
    //Ограничивает диапазон значений от MIN_LENGTH до MAX_LENGTH
    l=Math.abs(parseFloat(l));
    l=(l>MIN_LENGTH)?l:MIN_LENGTH;
    l=(l<MAX_LENGTH)?l:MAX_LENGTH;
    l=parseFloat(l.toFixed(1));
    return l;
}
function get_length(i_d_bl){
    //принимает один ел-т класса 'inc_dec_block',
    //получает,значение поля 'length'проверяет корректирует 
    //приводит к Float, проверяет допустимый диапазон,
    //ограничивает точность в 1 знак после запятой,
    //возвращает скорректированное значение
    var l=i_d_bl.getElementsByClassName('length')[0].value;
    l=toDiapason(l);
    return l;
}
function set_sum(i_d_bl){
    //принимает один ел-т класса 'inc_dec_block',
    //пересчитывает ИТОГО, устанавливает поле ИТОГО
    var p=i_d_bl.parentNode.parentNode.getElementsByClassName('price')[0].firstChild.nodeValue;
    var l=get_length(i_d_bl);
    var e_s=i_d_bl.parentNode.getElementsByClassName('sum')[0];
    i_d_bl.getElementsByClassName('length')[0].value=String(l);
    var sum=(l*parseFloat(p)).toFixed(1);
    e_s.firstChild.nodeValue=sum;
}
function set_length(i_d_bl){
    //Устанавливает значение полей 'length' по данным из Куков
    // только для корзины (не для GoodBig)
    try{
        //если id_js есть- мы в корзине,иначе- в GoodBig
        var id_js=parseInt(i_d_bl.parentNode.parentNode.parentNode.getElementsByClassName('id_js')[0].firstChild.nodeValue);
    }catch(x){return;}
    var a_a_s=getCookie(BASKET).split('|');
    i_d_bl.getElementsByClassName('length')[0].value=a_a_s[id_js].split(':')[1];
}
function change_cookie(i_d_bl,l){
    //Принимает i_d_bl эл-т и новую длину(0 для удаления)
    //Изменяет или удаляет нужную запись в куках
    //-получить id_js данного good-а, т.е. его порядковый номер, 
    try{
        //если id_js есть- мы в корзине,иначе- в GoodBig
        var id_js=parseInt(i_d_bl.parentNode.parentNode.parentNode.getElementsByClassName('id_js')[0].firstChild.nodeValue);
    }catch(x){return;}
    //-получить из куков по этому номеру строку-запись,если есть
    var b_s=getCookie(BASKET);
    var as=b_s.split('|');
    var str=as[id_js];  
    //-изменить значение длины, или удалить,если l==0
    var i_l_arr=str.split(':');
    if(l===0){//удалить
        as.splice([id_js],1);
        
    }
    else{//изменить
        i_l_arr[1]=l;
        as[id_js]=i_l_arr.join(':');        
    }
    var coocka=as.join('|');
    //меняем COOKIE
    document.cookie=BASKET+'='+coocka+';';
}
function set_total(){
    //Подсчитывает и устанавливает basket_total
    var as=getCookie(BASKET).split('|');
    var sum=0;
    if(as!=''){
        for(var i=0;i<as.length;i++){
            var item=as[i].split(':');
            var l=parseFloat(item[1]);
            var p=parseFloat(item[2]);
            sum+=l*p;
        }
    }
    document.getElementsByClassName('basket_total')[0].firstChild.nodeValue=sum;
}
function calc(e){
    //вызывается при необх. пересчитать ИТОГО, принимает событие,
    //вызывает set_sum с нужным ел-м
    //вызывает change_cookie
    //e=e||event;
    try{
        var i_d_bl=e.target.parentNode;
    }catch(x){
        return;
    }
    set_sum(i_d_bl);
    set_total();
    change_cookie(i_d_bl,get_length(i_d_bl));
    set_basket_count(get_goods_count());
    e.stopPropagation();
    e.preventDefault(); 
}