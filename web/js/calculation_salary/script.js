$(document).on('click', '.ajax-link', function (e){
    e.preventDefault();

    let url = $(this).attr('href');

    return getContent(url);
});

function getContent(url, params = {}){

    $.ajax({
        url: url,
        type: 'POST',
        data: params,
        beforeSend: function (){
          $('.content').html('<div class="wrapper-loading">\n' +
              '    <span class="spinner"></span>\n' +
              '</div>');
        },
        success: function(content){
            $('.content').html($(content).find('.content').html());
        },
        error: function(){
            alert('Error!');
        }
    });

    return true;
}


$(document).on('submit', '#filter-form', function (e){
    e.preventDefault();

    let data = $(this).serialize();
    let url = $(this).attr('action');

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        beforeSend: function (){
            $('.wrapper-block').remove();
            $('.content').prepend('<div class="wrapper-loading">\n' +
                '    <span class="spinner"></span>\n' +
                '</div>');
        },
        success: function(content){
            $('.content').html($(content).find('.content').html());
        },
        error: function(content){
            alert('Error!');
        }
    });
})

$(document).on('click', '.download-report', function (e){
    e.preventDefault();

    let data = $("#filter-form").serialize();

    $.ajax({
        url: '/calculation-salary/main/create-excel',
        type: 'POST',
        data: data,
        beforeSend: function (){
            $('.download-report').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Формирую..');
        },
        success: function(result){
            $('.download-report').html('Скачать');
            window.open('https://zarplata.173evakuator.ru/' + result.result, '_blank');
        },
        error: function(content){
            alert('Error!');
        }
    });
})

$(document).on('click', '.download-trascript', function (e){
    e.preventDefault();

    var btn = $(this);
    let data = $("#filter-form").serialize();

    $.ajax({
        url: '/calculation-salary/main/create-transcript?indexReport=' + btn.attr('aria-label'),
        type: 'POST',
        data: data,
        beforeSend: function (){
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Формирую..');
        },
        success: function(result){
            btn.html('Скачать');
            window.open('https://zarplata.173evakuator.ru/' + result.result, '_blank');
        },
        error: function(content){
            alert('Error!');
        }
    });
})

$(document).on('submit', '#settings', function (e){
    e.preventDefault();

    let data = $(this).serialize();
    let url = $(this).attr('action');

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        beforeSend: function (){
            $('.profile-form-block').remove();
            $('.content').prepend('<div class="wrapper-loading">\n' +
                '    <span class="spinner"></span>\n' +
                '</div>');
        },
        success: function(content){
            $('.content').html($(content).find('.content').html());
        },
        error: function(){
            alert('Error!');
        }
    });
})

$(document).on('change', '.fine-input', function (e){
    var element = $(this).closest('div').find('.wrapper-spinner');

    element.toggleClass('hide-block');

    BX24.callMethod('crm.contact.update', {'ID': $(this).attr('driver-id'), 'fields': {'UF_CRM_1691405841467': $(this).val()}}, function (res){
        element.toggleClass('hide-block');
    });
})

$(document).on('click', '.open-details', function (){
    let detailsRowId = $(this).attr('aria-label');
    $('.data-' + detailsRowId).toggleClass('hide-block');
})