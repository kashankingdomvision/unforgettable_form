import select2 from 'select2';
import $ from 'jquery';
var BASEURL = 'http://localhost/unforgettable_form/public/json/';



$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
    });
});

$(document).on('change', '.getBrandtoHoliday',function(){
        let brand_id = $(this).val();
        var options = '';
        var url = BASEURL+'holiday-types'
    $.ajax({
        type: 'get',
        url: url,
        data: { 'brand_id': brand_id },
        success: function(response) {
            options += '<option value="">Select Holiday Type</option>';
            $.each(response,function(key,value){
                options += '<option value="'+value.id+'">'+value.name+'</option>';
            });
            $('.appendHolidayType').html(options);
        }
    });
});

$(document).on('change', '.changeRole', function() {
    var role = $(this).find('option:selected').data('role');
    var supervisor = $('.userSupervisor');
    if (role == 'Sales Agent' || role == 2) {
        supervisor.show();
        $('#selectSupervisor').removeAttr('disabled');
    } else {
        supervisor.hide();
        $('#selectSupervisor').attr('disabled', 'disabled');
    }
});


    
$('.currencyImage').select2({
    templateResult: currencyImageFormate,
    templateSelection: currencyImageFormate
  });

function currencyImageFormate(opt) {
    var optimage = $(opt.element).attr('data-image');
    if (!optimage) {
        return opt.text ;
    }
        var $opt = $(
            '<span><img height="20" width="20" src="' + optimage + '" width="60px" /> ' + opt.text + '</span>'
        );
        return $opt;    
}
