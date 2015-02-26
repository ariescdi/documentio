$(document).ready(function () {
    var route = Routing.generate('seen_notification', , { id: {{ notif.id }} })

    $('.notifSeen').click(function(){
        $.ajax({
            type: "POST",
            url: route,
        }).done(function() {
            alert( "Notification deleted");
        });
    })
});
