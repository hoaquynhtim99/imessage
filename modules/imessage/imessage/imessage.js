// Post noi dung chat
function post(a){
    $('#q').val('');
    $.getJSON(dataurl + 'getjson=1&data=' + a + '&token=' + token,function(data){
        writechat(data);
    });
}

// Xuat noi dung chat
function writechat(data){
    if(data[0] == 1) dingsound(); else if( data[0] == 2 ) messagesound();
    if( data[1] != "" ){
        alert( data[1] );
    }else{
        var data_length = data[2].length;
        if( data_length % 2 == 0 ) itemgb ++;

        var a = "";
        var tmplastitem = lastitem;

        $.each(data[2], function(entryIndex, entry){
            itemgb ++;
            tmplastitem ++;
            a += '<div class="item clearfix' + ( ((itemgb % 2 ) == 0 ) ? ' bg' : '' ) +  '" id="item' + tmplastitem +'">';
            a += '<img class="avatar fl" src="' + entry[0] + '" width="30" height="30" alt="' + entry[1] +'" />';
            a += '<span title="' + textquoteuser  + '" onclick="quoteuser($(this));" class="a">' + entry[1] + '</span> - <span id="' + entry[4] + '" class="time">' + entry[2] + '</span><br />';
            a += entry[3];
            a += '</div>';
        });


        if(lastitem==0){
            $('#overview').append(a);
        }else{
            $(a).insertBefore('#item'+lastitem); // Chen noi dung chat moi vao
        }
        $('#content').tinyscrollbar(); // Tao thanh cuon
        isprosess = 0; // Trang thai san sang
        lastitem ++;
        if( data_length % 2 == 0 ) itemgb ++;
    }
}

//	Cap nhat noi dung chat
function updatechat(){
    $.getJSON(dataurl + 'updatechat=1&maxtime=' + maxtime + '&token=' + token,function(data){
        maxtime = parseInt( data[0] );
        if( data[1][0] != 0 ){
            writechat(data[1]);
        }
    });
}

// Cap nhat thoi gian
function updatetime(){
    var i = new Date();
    var _out, _time;
    i = i.getTime();
    i = parseInt( i / 1000 );

    $.each($('.time'), function(){
        _out = i - parseInt( $(this).attr('id') );

        if ( _out > 86400 ){
            var j = new Date( parseInt( $(this).attr('id') ) * 1000 );
            $(this).text( fixmindate(j.getHours()) + ":" + fixmindate(j.getMinutes()) + " " + fixmindate(j.getDate()) + "/" + fixmindate( j.getMonth() + 1 ) + "/" + fixmindate( j.getFullYear() ) );
        }else if ( _out > 3600 ){
            _out = parseInt ( _out / 3600 );
            $(this).text( texttime[3].replace('%s', _out) );
        }else if ( _out > 60 ){
            _out = parseInt ( _out / 60 );
            $(this).text( texttime[2].replace('%s', _out) );
        }else if ( _out > 10 ){
            $(this).text( texttime[1].replace('%s', _out) );
        }else{
            $(this).text( texttime[0] );
        }
    });
}

// Fix thoi gian tu 0 => 9
function fixmindate(a){
    if( a <= 9 ) return ("0" + a);
    return a;
}

// Tra loi ban chat
function quoteuser(a){
    nvm_insert_text( "@" + a.html(), true );
}
