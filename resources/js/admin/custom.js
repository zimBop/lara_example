$(function(){
    let sidebarToggleBtn = $('#leftSidebarToggle'),
        sidebar = $(sidebarToggleBtn).data('target');

    $(sidebarToggleBtn).on('click', function () {
        $(sidebar).toggleClass('active');
    });

    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Clients list
    // Toggle activity switch
    $('.client-activity-switch').on('change', function () {
        let route = $(this).data('route'),
            isChecked = $(this).attr('checked'),
            that = $(this);

        $.post(route, {}, function(data){
            if(data.status === 'success') {
                $.jGrowl("Availability successfully changed");
                $(that).attr('checked', !isChecked);
                $(that).parent('div').attr('title', !isChecked ? 'Active' : 'Inactive');
            } else {
                alert('Sorry, an issue occurred. Please, contact developers.')
            }
        }, 'json');
    });

    // Ask remove confirmation
    $('.are-you-sure').on('click', function(e){
        return confirm('Are you sure want to proceed?');
    });
});
