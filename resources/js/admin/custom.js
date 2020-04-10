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
        let clientId = $(this).data('client-id'),
            isChecked = $(this).attr('checked'),
            that = $(this);

        $.post(ROUTES.R_ADMIN_AJAX_CLIENTS_CHANGE_ACTIVITY, {clientId : clientId}, function(data){
            if(data.status === 'success') {
                $.jGrowl("Availability successfully changed");
                $(that).attr('checked', !isChecked);
                $(that).parent('div').attr('title', !isChecked ? 'Active' : 'Inactive');
            } else {
                alert('Sorry, an issue occurred. Please, contact developers.')
            }
        }, 'json');
    });
});
