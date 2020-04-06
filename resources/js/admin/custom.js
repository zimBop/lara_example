$(function(){
    let sidebarToggleBtn = $('#leftSidebarToggle'),
        sidebar = $(sidebarToggleBtn).data('target');

    $(sidebarToggleBtn).on('click', function () {
        $(sidebar).toggleClass('active');
    });

    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
