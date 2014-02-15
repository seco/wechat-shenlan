$(function(){
	var lw=$(window).width();
	var yb=lw-260;
   $('.main').css('width',yb);
   var mheight=$('.main').height();
   var oheight=$('.sidebar').height();
   if(oheight<mheight){
     $('.sidebar').css('height',mheight);
   }
    //初始菜单效果
    if($(".children").hasClass("left_children_select")){
        $('.left_children_select').removeClass("none");
        $('.left_children_select').siblings(".children").removeClass("none");
    }
    //菜单效果
    $(".left_menu").click(function(){
        if($(this).children(".children").hasClass("none")){
        $(this).children(".children").removeClass("none");
        $(this).addClass("select");
        $(this).siblings().removeClass("select");
        $(this).siblings().children(".children").removeClass("left_children_select");
        $(this).siblings().children(".children").addClass("none");
        }else{
            $(this).children(".children").addClass("none");
        }
        })
    //子菜单效果
    $(".children").click(function(){
        $(this).addClass("left_children_select");
        $(this).siblings(".children").removeClass("left_children_select");
        $(this).parent(".left_menu").children(".children").removeClass("left_children_select");
    })
 
	
 });
