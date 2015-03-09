$(document).ready(function () {
    var $content = $('.userCardContent');
    var array = {};

    $('.userCardBlock').hide();

    $('.userCardButton').click(function (e) {
        e.preventDefault();
        $('.userCardBlock').toggle('ease-in');
    });
});
