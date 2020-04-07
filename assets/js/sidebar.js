import '../css/sidebar.scss';

$(function () {
    $("#sidebar-toggler").click(function () {
        var target = $(this).data("target");
        $(target).toggleClass("sidebar-collapsed", 400);
        $("#wrapper").toggleClass("sidebar-collapsed", 400);
    });
})();
