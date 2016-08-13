//Реализация мини галереи для страницы Большой карты товаров
//получить эл-ты фото и минифото
var bf=document.getElementsByClassName('big_foto');          
var mfs=document.getElementsByClassName('mini_fotos');
//и подписать их на события
for(var i=0;i<mfs.length;i++){
    mfs[i].addEventListener('click',fClick,false);
}    
function fClick(e){
//Галерея- меняет путь к файлу в эл-те фото
    var a=document.createElement('a');//нужен для разбора пути
    a.href=e.target.src;
    var as_mini=a.pathname.split('/');
    var fn_mini=as_mini[as_mini.length-1];
    var bf=document.getElementsByClassName('big_foto');
    var as_bf=bf[0].src.split('/');
    var fn_big=as_bf[as_bf.length-1];
    bf[0].src=bf[0].src.replace(fn_big,fn_mini);
    }
