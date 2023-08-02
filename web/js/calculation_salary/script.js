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
        error: function(){
            alert('Error!');
        }
    });
})

$(document).on('click', '.open-details', function (){
    let detailsRowId = $(this).attr('aria-label');
    $('.data-' + detailsRowId).toggleClass('hide-block');
})