$(document).ready(function () {
    //var route = Routing.generate('seen_notification', { id: {{ notif.id }} });

    var $content = $('.notifContent');
    var array = {};

    var notif = Routing.generate('notifications');

    $('.notifBlock').hide();

    $('.notifButton').click(function(){
        $('.notifBlock').toggle("ease-in");
    });

    // FIRST FETCH OF EVERY NOTIFICATIONS

    $.ajax({
        type: "POST",
        url: notif,
        dataType: "json"
    }) .done(function( data ) {
        var count = data.length;
        var contentCount = $('.notifCount').html(count);
        data
            .filter(function(x){
                return !(x.id in array);
            })
            .forEach(function(x){
                var showMedia = Routing.generate('media_show', { id: x.media_id });
                array[x.id] = true;
                var $list = $('<li></li>').attr('data-notif', x.id);
                var $link = $('<a></a>').html(x.message);
                var $span = $('<span></span>');
                $span.attr({class:'glyphicon glyphicon-remove notifSeen'});
                $link.attr({href:showMedia});
                $list.append($link);
                $list.append($span);
                $content.append($list);
            })
    });

    // SET EVENT TO PASS hasSeen TO TRUE

    $('.notifContent').on('click', '.notifSeen', function(e){
        var id = $(e.target).closest('li').data('notif');
        var route = Routing.generate('seen_notification', { id: id });
        $.ajax({
            type: "POST",
            url: route,
        })
        $(e.target).parent().fadeOut("slow", function(){
            $(this).remove();
        });
        var count = parseInt($('.notifCount').html()) - 1;
        $('.notifCount').html(count);

    })

    // GET DISTINC NOTIFICATIONS EVERY 10s

    setInterval(function() {
        var notif = Routing.generate('notifications');
        $.ajax({
            type: "POST",
            url: notif,
            dataType: "json"
        }) .done(function( data ) {
            var count = data.length;
            var contentCount = $('.notifCount').html(count);
            data
                .filter(function(x){
                    return !(x.id in array);
                })
                .forEach(function(x){
                    var showMedia = Routing.generate('media_show', { id: x.media_id });
                    array[x.id] = true;
                    var $list = $('<li></li>').attr('data-notif', x.id);
                    var $link = $('<a></a>').html(x.message);
                    var $span = $('<span></span>');
                    $span.attr({class:'glyphicon glyphicon-remove notifSeen'});
                    $link.attr({href:showMedia});
                    $list.append($link);
                    $list.append($span);

                    $content.append($list);
                })
        });
    }, 10000);


});
