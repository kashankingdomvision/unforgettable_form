require('./scripts.js');

$(document).ready(function(){
    $(document).funfon('change', 'select[name="role"]', function() {
        var role = $(this).find('option:selected').data('role');
        if (role == 'Sales Agent' || role == 2) {
            $('#supervisor').show();
            $('#selectSupervisor').removeAttr('disabled');
        } else {
            $('#supervisor').hide();
            $('#selectSupervisor').attr('disabled', 'disabled');
        }
    });
});