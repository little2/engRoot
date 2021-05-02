var _sec_=0;
var timeoutID;

function start_loading()
{
  if(typeof(timeoutID)==undefined){
    console.log('clear:'+typeof(timeoutID))
    clearTimeout(timeoutID) ;
  }
  $('#loading').modal('show');
  _sec_=1;
  clearTimeout(timeoutID);
  timeoutID=setInterval("countSecond()", 1000);
}

function countSecond()
{
  _sec_=_sec_+1;
  $('#result').text(_sec_);
  if(_sec_>=10)
  {
    complete_loading();
    clearTimeout(timeoutID);
    show_message("执行失败","alert-warning");
  }

  console.log(typeof(timeoutID)+' '+timeoutID+' '+_sec_+' sec');
}

function complete_loading()
{
 
  clearTimeout(timeoutID);
 
  $('#loading').modal('hide');
  
}

function show_message(message,property)
{
  $('#message_zone').addClass('fadeOutback'); //警告信息消失
  var msgbox=$('#msgbox').clone();

  if(property!=undefined)
  {
      //alert-success,alert-info,alert-warning,alert-danger
      console.log(property+' '+typeof(property));
      msgbox.find('#alert_property').addClass(property);
  }
  else {
    msgbox.find('#alert_property').addClass("alert-info");
  }

  msgbox.removeClass('hide');
  msgbox.find('#msgtext').html(message);
  $('#message_zone').html(msgbox);
  $('#message_zone').removeClass('fadeOutback');


}

function onKeyDown(event)
{
    var e = event || window.event || arguments.callee.caller.arguments[0];
    if(e && e.keyCode==27){ // 按 Esc
        //要做的事情
    }
    if(e && e.keyCode==113){ // 按 F2
         //要做的事情
    }
    if(e && e.keyCode==13){ // enter 键

      new_search();
    }
}
