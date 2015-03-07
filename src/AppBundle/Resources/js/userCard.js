$(document).ready(function () {
    //var route = Routing.generate('seen_userCardication', { id: {{ userCard.id }} });

    var $content = $('.userCardContent');
    var array = {};

    $('.userCardBlock').hide();

    $('.userCardButton').click(function(e){
        e.preventDefault();
        $('.userCardBlock').toggle("ease-in");
    });


});
