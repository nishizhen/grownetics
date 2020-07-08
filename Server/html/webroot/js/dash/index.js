var GrowServer = GrowServer || {};
GrowServer.map_data_type = 3;
GrowServer.setMapDataType = function (data_type) {
    this.socket.emit('leave', 'data.sensor.'+this.map_data_type);
    this.map_data_type = data_type;
    this.socket.emit('join', 'data.sensor.'+data_type);
}

function addNotification(message, className) {
    if (!className) {
        className = 'fa-comment';
    }
    var noticeEle;
    var template = $('#notificationTemplate');
    template.find('.message').html(message);
    template.find('abbr.loaded').attr("title", moment().toISOString());
    var target = $('#notificationsBox');
    target.prepend(template.html());
    target.animate({ scrollTop: 0 });
    noticeEle = target.find("div.desc:first");
    noticeEle.find("abbr.timeago").attr("title", moment().toISOString());
    noticeEle.find("abbr.timeago").timeago();
    noticeEle.find("i").addClass(className);
    if (target.children('div').length > 11) {
        target.children('div:last').remove();
    }
    target.fadeIn();
}

function addChat(message, avatar) {
    var noticeEle;
    var template = $('#notificationTemplate');
    template.find('.message').html(message);
    template.find('abbr.loaded').attr("title", moment().toISOString());

    var target = $('#chatBox');
        target.append(template.html());
        target.animate({ scrollTop: target.prop("scrollHeight") });
        noticeEle = target.find("div.desc:last");
    noticeEle.find("abbr.timeago").attr("title", moment().toISOString());
    noticeEle.find("abbr.timeago").timeago();
    noticeEle.find(".badge").html('<img src="'+avatar+'" width="20" />');
    if (target.children('div').length > 11) {
        target.children('div:first').remove();
    }
}

function sendAlert(title,message,sticky,time,img) {
    if (!img) {
        img = '/img/ui-sam.jpg';
    }
    if (!time) {
        time = 10000;
    }
    var unique_id = $.gritter.add({
        title: title,
        text: message,
        image: img,
        time: time,
        sticky: sticky,
        class_name: 'my-sticky-class error'
    });
}

GrowServer.charts = new GrowServer.Charts();

function addData(newData) {
    GrowServer.map.updateMap(newData.datapoints.mapData);
}

$(document).ready(function () {

    $('abbr.loaded').attr("title", moment().toISOString());

    var socket = io(GrowServer.socket_address);
    GrowServer.socket = socket;
    socket.on('connect', function() {
        
    });

    var sendMessage = function(){ //use clicks message send button  
        var message = $('#message').val();
        var user_name = $('#accountLink').text();
        var user_id = $('#accountLink').data('userid');
        
        if("" === message){
            $('#chatBox').append("<div class=\"system_msg\">Enter a message please!</div>");
            playErrorSound();
            return;
        }
        
        var msg = {
            message: message,
            name: user_name,
            user_id: user_id,
            color : '#ff0000',
            type: 'usermsg'
        };
        socket.emit('chat', JSON.stringify(msg));
    };
    
    $('#send-btn').click(sendMessage);
    $('#message').keyup(function(e) {
        if(e.keyCode==13) {
            sendMessage();
            $('#message').val(''); //reset text
        }
    });
    
    socket.on('chat', function(msg){
        var server_data = JSON.parse(msg); //PHP sends Json data
        var type = server_data.type; //message type
        var msg = server_data.message; //message text
        var uname = server_data.name; //user name
        var avatar = server_data.avatar; // avatar
        var ucolor = server_data.color; //color

        if(type == 'usermsg') 
        {
            playAlertSound();
            addChat('<b>'+uname+':</b> '+msg,avatar);
            $('#chatBox').animate({ scrollTop: $('#chatBox').prop("scrollHeight") });
        }
        else if(type == 'notice')
        {
            switch (server_data.level) {
                case 1:
                    addNotification(msg);
                break;
                case 2:
                    playAlertSound();
                    addNotification(msg,'fa-bell');
                    sendAlert('Automation Alert!',msg,false);
                break;
                case 3:
                    var mesg = new SpeechSynthesisUtterance('Grownetics Facility Alert! '+msg);
                    mesg.voice = speechSynthesis.getVoices().filter(function(voice) { return voice.name == 'Samantha'; })[0];
                    speechSynthesis.speak(mesg);
                    addNotification(msg,'fa-exclamation');
                    sendAlert('Automation Alarm!',msg,true);
                break;
            }
        }
        else if(type == 'data')
        {   
            addData(server_data);
        }
    });
    
    socket.on('data.sensor', function (msg) {
        GrowServer.map.updateMap(JSON.parse(msg))
    });

    function playAlertSound() {
        $('#beep').get(0).play();
    }

    function playErrorSound() {
        $('#error').get(0).play();
    }

    function playAlarmSound() {
        $('#alarm').get(0).play();
    }

    $( "#slider-vertical" ).slider({
      orientation: "vertical",
      range: "min",
      min: 0,
      max: 100,
      value: 60,
      stop: function( event, ui ) {
        $('audio').each(function(ii, obj){
            obj.volume = ui.value/100;
        });
        playAlertSound();
      }
    });
    $('audio').each(function(ii, obj){
        obj.volume = 0.5;
    });

    $('#notifications').animate({ scrollTop: $('#notifications').prop("scrollHeight") });
    $('#chatBox').animate({ scrollTop: $('#chatBox').prop("scrollHeight") });

    // notify-row icon drop-downs
    $(".notify-row .dropdown").on("click", function() {
        $(this).toggleClass("open");
        return false;
    })

    // map edit button
    $("button[data-target='edit-map']").on("click", function() {
        if (GrowServer.map.editing) {
            GrowServer.map.saveMap();
            $(this).html('Edit');
        } else {
            GrowServer.map.editMap();
            $(this).html('Save');
        }
    });
}); // document.ready

