"use strict";$(document).ready(function(){function t(){var t=r+"ApiKeyManagement/getCharacters/";$.ajax({dataType:"json",url:t,success:function(t){if($(".table-character-list tbody tr").empty(),0==t.length){var a="<tr><td colspan='3' class='text-center'>No characters found</td></tr>";$(".table-character-list tbody tr").prepend(a)}else $.each(t,function(a,e){var r=t[a].charid,c="<img src='https://image.eveonline.com/Character/"+r+"_32.jpg'></img>",n="<tr><td>"+c+" "+t[a].name+"</td><td>"+t[a].api+"</td><td><button class='btn btn-danger btn-delete' data-iddel="+r+" data-toggle='modal' data-target='#delete'>Remove</button></tr></tr>";$(".table-character-list").prepend(n)})}})}$(".api-insert-2").hide();var a,e,r=$(".navbar").data("url");t(),$("table").on("click","button",function(){var t=$(this).data("iddel"),a=$("#delete").data("url"),e=a+"/"+t;$(".btn-delete-confirm").attr("data-url",e)}),$(".btn-delete-confirm").on("click",function(){var a=$(this).attr("data-url");$.ajax({dataType:"json",url:a,success:function(a){$("#delete").modal("toggle"),toastr[a.notice](a.message),t()}})}),$(".submit-add").on("click",function(t){a=$("#keyid").val(),e=$("#vcode").val(),t.preventDefault();var c=r+"ApiKeyManagement/addCharacters/",n=$(".add-apikey").serialize();$.ajax({dataType:"json",url:c,data:n,type:"POST",success:function(t){if("undefined"!=typeof t.notice)toastr[t.notice](t.message);else{$(".api-insert-1").toggle(),$(".api-insert-2").toggle();var a=1;$.each(t,function(e,r){var c=t[e][1].id,n=t[e][0].name,d="https://image.eveonline.com/Character/"+c+"_32.jpg",i="character"+a,s="<tr><td><img src='"+d+"'alt='icon'></img> "+n+"</td><td><input type='checkbox' class='"+i+"' data-id='"+c+"'></td></tr>";a++,$(".table-character-selection tbody").append(s)})}}})}),$(".submit-add-2").on("click",function(t){t.preventDefault();var c=[];$(".character1").is(":checked")?c.push($(".character1").attr("data-id")):"",$(".character2").is(":checked")?c.push($(".character2").attr("data-id")):"",$(".character3").is(":checked")?c.push($(".character3").attr("data-id")):"";var n=c.join("/"),d=r+"ApiKeyManagement/addCharactersStep/"+a+"/"+e+"/"+n;$.ajax({dataType:"json",url:d,success:function(t){"error"===t.notice&&toastr[t.notice](t.message)}})})});