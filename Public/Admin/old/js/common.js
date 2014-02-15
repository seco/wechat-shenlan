
	var ui = window.ui || {
	  getarea:function(prefix,init_style,init_p,init_c){
	     if(!init_p) init_p = default_p;
		 if(!init_c) init_c = default_c;
		var style = (init_style)?'class="'+init_style+'"':'';
		var html = '<select name="'+prefix+'_province" '+style+'><option value="">省/直辖市</option></select> '+
				'<select name="'+prefix+'_city" '+style+' style="width:180px"><option value="">不限</option></select>';
		document.write(html);
		// _PUBLIC_+'/js/area.js'
		$.getJSON(U('home/Public/getArea'), function(json){
			json = json.provinces;
			var province ='<option value="">省/直辖市</option>';
			$.each(json,function(i,n){
				var pselected='';
				var cselected='';
				var city='<option value="">不限</option>';
				if(n.id==init_p){
					 pselected = 'selected="true"';
					 $.each(n.citys,function(j,m){
							for(var p in m){
								cselected = (p==init_c)?'selected="true"':'';
								city+='<option value="'+p+'" '+cselected+'>'+m[p]+'</option>';
							};
					 });
					 $("select[name='"+prefix+"_city']").html(city);
				}
				province+='<option value="'+n.id+'" rel="'+i+'" '+pselected+'>'+n.name+'</option>';
			});
			
			$("select[name='"+prefix+"_province']").live('change',function(){
				var city='<option value="">不限</option>';
				var handle =  $(this).find('option:selected').attr('rel');
				if( handle ){
					var t =  json[handle].citys;
					$.each(t,function(j,m){
						for(var p in m){
							city+='<option value='+p+'>'+m[p]+'</option>';
						};
					});
				};
				$("select[name='"+prefix+"_city']").html(city);
			});
			$("select[name='"+prefix+"_province']").html(province);
		}); 
	}
	
}
function setFundStatus(id,v,h){
     $.post(U('user/Fund/setStatus'),{id:id,v:v},function(res){
	         if(res =='1'){
			       $('#'+id).html(h);
			 }
	 });
}
function setMessageStatus(id, v, h){
	$.post(U('user/Message/setMessageOver'),{id:id,v:v},function(res){
		if(res == '1'){
			var src = $('#img_'+id).attr('src');
			var src_ = src.split('.');
			    src_[0] = src_[0].substr(0,src_[0].length-1);
			var newSrc = src_[0]+'2.'+src_[1];
			$('#img_'+id).attr('src',newSrc);
			}
		});
}
//模拟ts U函数
function U(url,params){
	var website = _ROOT_+'/index.php';
	url = url.split('/');
	if(url[0]=='' || url[0]=='@')
		url[0] = APPNAME;
	if (!url[1])
		url[1] = 'Index';
	if (!url[2])
		url[2] = 'index';
	website = website+'?g='+url[0]+'&m='+url[1]+'&a='+url[2];
	if(params){
		params = params.join('&');
		website = website + '&' + params;
	}
	return website;
}
//退出ajax
 function quit(){
    $.post(U('home/Public/quit'),function(){
	            window.location.href= U('home/Index/index');
	})
 }
 //更换注册码
function changeverify(){
    var date = new Date();
    var ttime = date.getTime();
    var url = _PUBLIC_+"/captcha.php";
    $('#verifyimg').attr('src',url+'?'+ttime);
}
 function getData(){
	      var myDate = new Date();
		    
			var y = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
			 var m = myDate.getMonth()+1;       //获取当前月份(0-11,0代表1月)
			var d = myDate.getDate();        //获取当前日(1-31)
			var h = myDate.getHours();       //获取当前小时数(0-23)
            var min = myDate.getMinutes();     //获取当前分钟数(0-59)
			if(parseInt(m) < 10){
			   m = '0'+m;
			}
			d = parseInt(d) <10? '0'+d:d;
			min = parseInt(min)<10? '0'+min:min;
			
			if(parseInt(h)<10){
			  h = '0'+h;
			}
			return y+'-'+m+'-'+d+' '+h+':'+min;
	}
//设置cookie
function setCookie(name,value,time){
var strsec = getsec(time);
var exp = new Date();
exp.setTime(exp.getTime() + strsec*1);
document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getsec(str){
	
	var str1=str.substring(1,str.length)*1; 
	var str2=str.substring(0,1); 
	if (str2=="s"){
	return str1*1000;
	}else if (str2=="h"){
	return str1*60*60*1000;
	}else if (str2=="d"){
	return str1*24*60*60*1000;
	}
}
function GetCookie(name)
//获得Cookie的原始值
{
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen)
	{
	var j = i + alen;
	if (document.cookie.substring(i, j) == arg)
	return GetCookieVal (j);
	i = document.cookie.indexOf(" ", i) + 1;
	if (i == 0) break;
	}
	return null;
}
function GetCookieVal(offset)
//获得Cookie解码后的值
{
var endstr = document.cookie.indexOf (";", offset);
if (endstr == -1)
endstr = document.cookie.length;
return unescape(document.cookie.substring(offset, endstr));
}

function DelCookie(name)
//删除Cookie
{
var exp = new Date();
exp.setTime (exp.getTime() - 1);
var cval = GetCookie (name);
document.cookie = name + "=" + cval + "; expires="+ exp.toGMTString();
}




