String.prototype.temp=function(b){return this.replace(/\$\w+\$/gi,function(a){a=b[a.replace(/\$/g,"")];return"undefined"==a+""?"":a})};jQuery.extend({urls:[],getScriptWithCache:function(b,a){-1==$.urls.indexOf(b)?($.urls.push(b),$.ajax({url:b,type:"GET",cache:!0,async:!1,success:function(){a()},dataType:"script"})):a()},getinfo:function(){$.getScriptWithCache("Main/Views/plugins/minjs/submitData.js",function(){$.getinfo_base()})}});
$.fn.extend({submitData:function(b,a){var c=$(this);$.getScriptWithCache("Main/Views/plugins/minjs/submitData.js",function(){c.submitData_base(b,a)})},copy:function(b,a){var c=$(this);$.getScriptWithCache("Main/Views/plugins/minjs/ZeroClipboard.min.js",function(){c.copy_base(b,a)})}});