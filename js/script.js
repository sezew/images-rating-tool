function get_wokrs(){
    if (arr.length > 1) {
        jQuery('.rating--img1').css('background-image', 'url('+arr[0]+')');
        jQuery('.rating--img1').parent().attr('href', arr[0]);
        jQuery('.rating--img1').parent().parent().find('button').attr('data-rival', ids[0]);
        jQuery('.rating--img1').parent().parent().find('button').attr('data-rival-url', arr[0]);
        jQuery('.rating--img2').css('background-image', 'url('+arr[1]+')');
        jQuery('.rating--img2').parent().attr('href', arr[1]);
        jQuery('.rating--img2').parent().parent().find('button').attr('data-rival', ids[1]);
        jQuery('.rating--img2').parent().parent().find('button').attr('data-rival-url', arr[1]);
        ids.shift();
        arr.shift();
        ids.shift();
        arr.shift();
    } else {
        jQuery('.description').text('Голосование завершено.');
        jQuery('.rating__item').css('display', 'none');
        save_work(ids[0]);
    }
}
function set_wokr(a, b){
    ids.push(a);
    arr.push(b);
    get_wokrs();
}
function save_work(a){
    if (a != '') {
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: 'action=save_work&work='+a+'&slug='+slug,
            success: function( data ) {

            }
        });
    }
}
get_wokrs();
