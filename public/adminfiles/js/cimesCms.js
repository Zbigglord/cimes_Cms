/**
 * Created by BabooJaga on 2016-02-13.
 */
var jq = jQuery.noConflict();

function getPHPMessage(message,messagecode,where){
    var url = '../adminAjax/ajaxConvertMessage';
    var formdata = new FormData();
    formdata.append("message",message);
    formdata.append("messagecode",messagecode);
    jq.ajax({
        url: url,
        method: 'POST',
        data: formdata,
        dataType: 'text',
        processData: false,
        contentType: false,
        success: function(data){
            jq( "#loading" ).hide();
            jq(where).html(data);
        }
    });
}

function getPHPMessageStraight(message){
    var url = '../adminAjax/ajaxGetMessage';
    var formdata = new FormData();
    formdata.append("message",message);
    jq.ajax({
        url: url,
        method: 'POST',
        data: formdata,
        dataType: 'text',
        processData: false,
        contentType: false,
        success: function(data){
            jq( "#loading" ).hide();
            alert(data);
        }
    });
}

function cancel(address){
    window.location = address;
}

function sendData(address, data){

    var formdata = new FormData();
    formdata.append("data",data);

    var request = new XMLHttpRequest();
    request.open("POST", address);
    request.send(formdata);

}

function checkPassword(str)
{
    // at least one number, one lowercase and one uppercase letter
    // at least six characters that are letters, numbers or the underscore
    var re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\w{6,}$/;
    return re.test(str);
}
function randomPassword(length) {
    var chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*ABCDEFGHIJKLMNOP1234567890";
    var pass = "";
    for (var x = 0; x < length; x++) {
        var i = Math.floor(Math.random() * chars.length);
        pass += chars.charAt(i);
    }
    return pass;
}
/*BLINS ERROR MESSAGES IN ERROR SPANS*/
function blinker(where) {
    jq('.input-error').fadeOut(500);
    jq('.input-error').fadeIn(500);
    jq('#addUser-select-error').fadeOut(500);
    jq('#addUser-select-error').fadeIn(500);
}
/*CLEARS ERROR SPANS*/
function clearErrorSpan(spanID){
      jq(spanID).html('');
}
/*DISPLAYS LOADING GIF - must be closed in ajax success function*/
jq( document ).ajaxStart(function() {
    jq( "#loading" ).show();
});

jq(document).ready(function(){

    //initialise tootltips
    jq('[data-toggle="tooltip"]').tooltip();

    //initialise fancybox
    jq('a[rel="fancybox"]').fancybox();

    jq(document).on('click','.check-all',function(){

        jq('.checkbox-gallery').each(function(){
            jq(this).prop('checked',true);
        });

        jq(this).removeClass('check-all');
        jq(this).addClass('uncheck-all');

    });

    jq(document).on('click','.uncheck-all',function(){

        jq('.checkbox-gallery').each(function(){
            jq(this).prop('checked',false);
        });

        jq(this).removeClass('uncheck-all');
        jq(this).addClass('check-all');

    });

    /*SET OPTION CATEGORY AS SELECTED DEPENDING ON SESSION DATA (right after cat id submit)*/
    var selected_val = jq('#ctype-for-jquerry').val();
    jq('select[name=set_cat_type]').val(selected_val);
    jq('.selectpicker').selectpicker('refresh');

/*EDIT PAGE*/
    /*SUBMITS USER NAME SELECT VALUE ON EDIT PAGE BEFORE LOADING PROPER FORM*/
    jq('#set_uname').change(function(){
        jq('#names-picker').submit();
    });
    /*SUBMITS CATEGORY TYPE ON CREATE AND EDIT CATEGORY PAGE*/
   jq('#set_cat_type').change(function(){
       var typeid = jq(this).val();
       var url = '../adminAjax/ajaxReturnCtypeId';
       jq.ajax({
           url: url,
           method: 'POST',
           data: {typeid: typeid},
           dataType: 'text',
           success: function(data){
               jq( "#loading" ).hide();
               //jq('.selectpicker').selectpicker('refresh'); script goes wild - to check
                location.reload();
           }
       });
   });

/*END EDIT PAGE*/
/*START ADD USER SELECT CHANGE OPTIONS*/
 jq('#user_groups').change(function(){
     jq('#hidden-till-needed').show();
   var option = jq(this).val();
     jq('#addUser-select-error').html('');
     if(option === 'super' || option === 'admin'){
         jq('input[type="checkbox"]').each(function(){
             jq(this).prop('checked',true);
             jq(this).prop('disabled',false);
             jq('#cs_backend').prop('disabled',true);
             jq('#is_active').prop('disabled',true);
             jq('#cc_config').prop('disabled',true);
         });
     }else if(option === 'moderator'){
         jq('input[type="checkbox"]').each(function(){
             jq(this).prop('checked',false);
             jq(this).prop('disabled',true);
             jq('#cs_backend').prop('checked','true');
             jq('#is_active').prop('checked',true);
             jq('#is_active').prop('disabled',false);
             jq('#article_edit').prop('checked',true);
             jq('#article_edit').prop('disabled',false);
             jq('#article_delete').prop('checked',true);
             jq('#article_delete').prop('disabled',false);
             jq('#us_add').prop('checked',true);
             jq('#us_add').prop('disabled',false);
             jq('#us_edit').prop('checked',true);
             jq('#us_edit').prop('disabled',false);
         });
     }else if(option === 'editor'){
         jq('input[type="checkbox"]').each(function(){
             jq(this).prop('checked',true);
             jq(this).prop('disabled',false);
             jq('#cs_backend').prop('disabled',true);
             jq('#is_active').prop('disabled',false);
             jq('#cc_config').prop('disabled',true);
             jq('#cc_config').prop('checked',false);
             jq('#us_add').prop('disabled',true);
             jq('#us_add').prop('checked',false);
             jq('#us_edit').prop('disabled',true);
             jq('#us_edit').prop('checked',false);
             jq('#us_delete').prop('disabled',true);
             jq('#us_delete').prop('checked',false);
         });
     }else if(option === 'regular_user') {
         jq('input[type="checkbox"]').each(function(){
             jq(this).prop('checked', false);
             jq(this).prop('disabled', true);
             jq('#is_active').prop('disabled', false);
             jq('#is_active').prop('checked', false);
             jq('#article_add').prop('disabled', false);
             jq('#article_add').prop('checked', true);
             jq('#article_edit').prop('disabled', false);
             jq('#article_edit').prop('checked', true);
             jq('#article_delete').prop('disabled', false);
             jq('#article_delete').prop('checked', true);
         });
     }else if(option === 'random_role'){
         jq('input[type="checkbox"]').each(function(){
         jq(this).prop('checked', false);
         jq(this).prop('disabled', false);
         jq('#cc_config').prop('disabled',true);
         jq('#cc_config').prop('checked',false);
       });
     }else{
         jq('input[type="checkbox"]').each(function(){
             jq(this).prop('checked', false);
             jq(this).prop('disabled', true);
         });
     }
  });
 /*END ADD USER SELECT CHANGE OPTIONS*/

 /*VALIDATE ADD USER AND AJAX CALL*/
    jq('#send-addUser-admin').click(function(e){
        e.preventDefault();

        var pass = jq('#add_pass').val();
        var rpass = jq('#repeat_pass').val();
        var uLogin = jq('#add_nick').val();
        var uEmail = jq('#add_email').val();
        var email_parts = uEmail.split('@',2);
        var u_role_val = jq('#user_groups').val();
        var login_bool = 'FALSE';
        var email_bool = 'FALSE';
        var pass_bool = 'FALSE';
        var role_bool = 'FALSE';


        if(uLogin !== ''){
            if(uLogin.length < 5){
                clearErrorSpan('#vLogin');
                jq('#vLogin').html('login za krótki(5+ znaków)');
                setInterval(blinker, 500);
                login_bool = 'FALSE';
            }else{
                clearErrorSpan('#vLogin');
                login_bool = 'TRUE';
            }
        }else{
            clearErrorSpan('#vLogin');
            jq('#vLogin').html('wymagane');
            setInterval(blinker, 500);
            login_bool = 'FALSE';
        }

        if(uEmail !== ''){
            if(email_parts[0].length < 2 || email_parts[1].length < 4){
                clearErrorSpan('#vEmail');
                jq('#vEmail').html('email nieprawidłowy');
                setInterval(blinker, 500);
                email_bool = 'FALSE';
            }else{
                clearErrorSpan('#vEmail');
                email_bool = 'TRUE';
            }
        }else{
            clearErrorSpan('#vEmail');
            jq('#vEmail').html('wymagane');
            setInterval(blinker, 500);
            email_bool = 'FALSE';
        }

        if (pass !== '' && rpass !== ''){
            if (pass !== rpass) {
                clearErrorSpan('#vPass');
                clearErrorSpan('#vRpass');
                jq('#vPass').html('hasła nie pasują');
                jq('#vRpass').html('hasła nie pasują');
                setInterval(blinker, 500);
                pass_bool = 'FALSE';
            }else if(pass.length < 6){
                clearErrorSpan('#vPass');
                jq('#vPass').html('musi zawierać min. 6 znaków');
                setInterval(blinker, 500);
                pass_bool = 'FALSE';
            }else {
                clearErrorSpan('#vPass');
                clearErrorSpan('#vRpass');
                pass_bool = 'TRUE';
            }
        }else if(pass === '' && rpass === ''){
            clearErrorSpan('#vPass');
            clearErrorSpan('#vRpass');
            jq('#vPass').html('wymagane');
            jq('#vRpass').html('wymagane');
            setInterval(blinker, 500);
            pass_bool = 'FALSE';
        }else if(rpass === ''){
            clearErrorSpan('#vRpass');
            jq('#vRpass').html('wymagane');
            setInterval(blinker, 500);
            pass_bool = 'FALSE';
        }else if(pass === ''){
            clearErrorSpan('#vPass');
            jq('#vPass').html('wymagane');
            setInterval(blinker, 500);
            pass_bool = 'FALSE';
        }

        if(u_role_val === 'none'){
            clearErrorSpan('#addUser-select-error');
            jq('#addUser-select-error').html('należy wybrać grupę dla użytkownika');
            setInterval(blinker, 500);
            role_bool = 'FALSE';
        }else{
            jq('#addUser-select-error').html('');
            role_bool = 'TRUE';
        }

        if(pass_bool === 'TRUE' && role_bool ==='TRUE' && login_bool === 'TRUE' && email_bool === 'TRUE'){

            jq('input[type="checkbox"]').each(function(){
                if(jq(this).prop('checked')){
                    jq(this).val(1);
                }else{
                    jq(this).val(0);
                }
            });

            var url = '../adminAjax/ajaxAddNew';
            var data = jq('#admin_add_user').serialize();
            jq.ajax({
                url: url,
                method: 'POST',
                data: data,
                dataType: 'text',
                success: function(data){
                    jq('.message-login').html(data);
                    location.reload();
                }
            });
        }
    });
    /* END VALIDATE ADD USER AND AJAX CALL*/
    /*VALIDATE EDIT USER AND AJAX CALL*/
    jq('#send-editUser-admin').click(function(e){
        e.preventDefault();

        var pass = jq('#add_pass').val();
        var rpass = jq('#repeat_pass').val();
        var uLogin = jq('#add_nick').val();
        var uEmail = jq('#add_email').val();
        var email_parts = uEmail.split('@',2);
        var u_role_val = jq('#user_groups').val();
        var login_bool = 'FALSE';
        var email_bool = 'FALSE';
        var pass_bool = 'FALSE';
        var role_bool = 'FALSE';


        if(uLogin !== ''){
            if(uLogin.length < 5){
                clearErrorSpan('#vLogin');
                jq('#vLogin').html('login za krótki(5+ znaków)');
                setInterval(blinker, 500);
                login_bool = 'FALSE';
            }else{
                clearErrorSpan('#vLogin');
                login_bool = 'TRUE';
            }
        }else{
            clearErrorSpan('#vLogin');
            jq('#vLogin').html('wymagane');
            setInterval(blinker, 500);
            login_bool = 'FALSE';
        }

        if(uEmail !== ''){
            if(email_parts[0].length < 2 || email_parts[1].length < 4){
                clearErrorSpan('#vEmail');
                jq('#vEmail').html('email nieprawidłowy');
                setInterval(blinker, 500);
                email_bool = 'FALSE';
            }else{
                clearErrorSpan('#vEmail');
                email_bool = 'TRUE';
            }
        }else{
            clearErrorSpan('#vEmail');
            jq('#vEmail').html('wymagane');
            setInterval(blinker, 500);
            email_bool = 'FALSE';
        }

        if (pass !== '' && rpass !== ''){
            if (pass !== rpass) {
                clearErrorSpan('#vPass');
                clearErrorSpan('#vRpass');
                jq('#vPass').html('hasła nie pasują');
                jq('#vRpass').html('hasła nie pasują');
                setInterval(blinker, 500);
                pass_bool = 'FALSE';
            }else if(pass.length < 6){
                clearErrorSpan('#vPass');
                jq('#vPass').html('musi zawierać min. 6 znaków');
                setInterval(blinker, 500);
                pass_bool = 'FALSE';
            }else {
                clearErrorSpan('#vPass');
                clearErrorSpan('#vRpass');
                pass_bool = 'TRUE';
            }
        }else if(pass === '' && rpass === ''){
            clearErrorSpan('#vPass');
            clearErrorSpan('#vRpass');
            pass_bool = 'TRUE';
        }else if(pass !== '' && rpass === ''){
            clearErrorSpan('#vPass');
            clearErrorSpan('#vRpass');
            jq('#vPass').html('hasła nie pasują');
            jq('#vRpass').html('hasła nie pasują');
            setInterval(blinker, 500);
            pass_bool = 'FALSE';
        }else if(pass === '' && rpass !== ''){
            clearErrorSpan('#vPass');
            clearErrorSpan('#vRpass');
            jq('#vPass').html('hasła nie pasują');
            jq('#vRpass').html('hasła nie pasują');
            setInterval(blinker, 500);
            pass_bool = 'FALSE';
        }

        if(u_role_val === 'none'){
            clearErrorSpan('#addUser-select-error');
            role_bool = 'TRUE';
        }else{
            clearErrorSpan('#addUser-select-error');
            role_bool = 'TRUE';
        }

        if(pass_bool === 'TRUE' && role_bool ==='TRUE' && login_bool === 'TRUE' && email_bool === 'TRUE'){

            jq('input[type="checkbox"]').each(function(){
                if(jq(this).prop('checked')){
                    jq(this).val(1);
                }else{
                    jq(this).val(0);
                }
            });

            var url = '../adminAjax/ajaxEditUser';
            var data = jq('#admin_edit_user').serialize();
            jq.ajax({
                url: url,
                method: 'POST',
                data: data,
                dataType: 'text',
                success: function(data){
                    jq('.message-login').html(data);
                    jq( "#loading" ).hide();
                }
            });
        }
    });
    /* END VALIDATE EDIT USER AND AJAX CALL*/
/*START SUBMIT DELETE ACTIVATE USER FORM*/
  jq('.button-delete-user').click(function(){
      var ul_id = jq(this).attr('id');
      var ul_id_part = ul_id.split('_');
      jq('#delete_user_by_id').val(ul_id_part[1]);
      jq( '#confirm-delete-user' ).dialog({
              resizable: false,
              height:220,
              width: 420,
              modal: true,
              buttons: {
                  'Kontynuuj': function() {
                      jq( this ).dialog( 'close' );
                      jq('#user_delete_form').submit();
                  },
                        'Anuluj': function() {
                      jq( this ).dialog( 'close' );
                  }
              }
          });
      jq( "#confirm-delete-user" ).dialog( "open" );
  });
    jq('.button-deactivate-user').click(function(){
        var ul_id = jq(this).attr('id');
        var ul_id_part = ul_id.split('_');
        jq('#deactivate_user_by_id').val(ul_id_part[1]);
        jq('#user_delete_form').submit();
    });
    jq('.button-activate-user').click(function(){
        var ul_id = jq(this).attr('id');
        var ul_id_part = ul_id.split('_');
        jq('#activate_user_by_id').val(ul_id_part[1]);
        jq('#user_delete_form').submit();
    });
/*END SUBMIT DELETE ACTIVATE USER FORM*/

/*START UPLOAD CATEGORY IMAGE HELPERS */
    jq('#category_image_add').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#category_image_add')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

    jq('#category_image_edit').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#category_image_edit')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var url = '../adminAjax/ajaxUploadFile';
        var formdata = new FormData();
        formdata.append("images",img);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        // to do:: image validation
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq('#category_image').val(data);
                    jq( "#loading" ).hide();
                }
            });
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }

    });

    /*START DELETE CATEGORY*/
    jq('.btn-cat-delete').click(function(e){
        e.preventDefault();
        jq( '#confirm-delete-category' ).dialog({
            resizable: false,
            height:220,
            width: 420,
            modal: true,
            buttons: {
                'Kontynuuj': function() {
                    jq( this ).dialog( 'close' );
                    jq('#cat-delete-form').submit();
                },
                'Anuluj': function() {
                    jq( this ).dialog( 'close' );
                }
            }
        });
        jq( "#confirm-delete-category" ).dialog( "open" );
    });

    /*START EDIT CATEGORY*/
    jq('#send-editCategory-admin').click(function(event){
        event.preventDefault();
        tinyMCE.triggerSave();
        var url = '../AdminAjax/ajaxEditCategory';
        var data = jq('#admin_edit_category').serialize();
        jq.ajax({
            url: url,
            method: 'POST',
            data: data,
            dataType: 'text',
            success: function(data){
                jq( "#loading" ).hide();
                if(data === 'ok'){
                    window.location.replace("../categories/list");
                }else{
                    alert(data);
                }
            }
        });
    });

    /*START UPLOAD CLASSES IMAGE HELPERS */
    jq('#class_image_add').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#class_image_add')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

    jq('#class_image_edit').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#class_image_edit')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var url = '../adminAjax/ajaxUploadFileCourse';
        var formdata = new FormData();
        formdata.append("images",img);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        // to do:: image validation
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq('#class_image').val(data);
                    jq( "#loading" ).hide();
                }
            });
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

    /*START UPLOAD POSTS IMAGE HELPERS */
    jq('#post_image_add').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#post_image_add')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

    jq('#post_image_edit').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#post_image_edit')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var url = '../adminAjax/ajaxUploadFileCourse';
        var formdata = new FormData();
        formdata.append("images",img);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        // to do:: image validation
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq('#class_image').val(data);
                    jq( "#loading" ).hide();
                }
            });
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

//click rozwiń zwiń functio
    jq('.glyphicon').click(function(){
        var course_id = jq(this).attr('id');
        var just_id = course_id.split('_');
        var block = 'course-container_'+just_id[1];
        jq('#'+block).toggle(300);
    });
//MENU SELECT AJAX FUNCTIONS
jq('#menuitem_set_type').change(function(){
 var url = '../adminAjax/ajaxSetMenuitemType';
 var data = jq('#menuitem_set_type').val();
    jq.ajax({
        url: url,
        method: 'POST',
        data: {data: data},
        dataType: 'text',
        success: function(data){
            jq( "#loading" ).hide();
            jq('.dynamic-ajax-call').html(data);
            jq('.selectpicker').selectpicker('refresh');
            jq('#menuitem_links_to_what').change(function(){
                var url = '../adminAjax/ajaxSetMenuitemParent';
                var parent_menu_id = jq('#parent_menu_id').val();
                var menuitem_type = jq('#menuitem_set_type').val();
                var menuitem_parent_item = jq('#menuitem_links_to_what').val();
                var formdata = new FormData();
                formdata.append("parent_menu_id",parent_menu_id);
                formdata.append("menuitem_type",menuitem_type);
                formdata.append("menuitem_parent_item",menuitem_parent_item);
                jq.ajax({
                    url: url,
                    method: 'POST',
                    data: formdata,
                    dataType: 'text',
                    processData: false,
                    contentType: false,
                    success: function(data){
                        jq( "#loading" ).hide();
                        jq('.dynamic-ajax-call-2').html(data);
                        jq('.selectpicker').selectpicker('refresh');
                    }
                });
            });//end menuitem set type function

        }
    });
});//end menuitem set type function

    jq('.dynamic-ajax-call').on('click','#add_external_link',function(){
        var url = '../adminAjax/ajaxSetMenuitemParent';
        var parent_menu_id = jq('#parent_menu_id').val();
        var menuitem_type = jq('#menuitem_set_type').val();
        var menuitem_external_link = jq('#add_external_link').val();
        var formdata = new FormData();
        formdata.append("parent_menu_id",parent_menu_id);
        formdata.append("menuitem_type",menuitem_type);
        formdata.append("add_external_link",menuitem_external_link);
        jq.ajax({
            url: url,
            method: 'POST',
            data: formdata,
            dataType: 'text',
            processData: false,
            contentType: false,
            success: function(data){
                jq( "#loading" ).hide();
                jq('.dynamic-ajax-call-2').html(data);
                jq('.selectpicker').selectpicker('refresh');
            }
        });
    });

    jq('.dynamic-ajax-call').on('change','#menuitem_links_to_form',function(){
        var url = '../adminAjax/ajaxSetMenuitemParent';
        var parent_menu_id = jq('#parent_menu_id').val();
        var menuitem_type = jq('#menuitem_set_type').val();
        var menuitem_parent_item = jq('#menuitem_links_to_form').val();
        var formdata = new FormData();
        formdata.append("parent_menu_id",parent_menu_id);
        formdata.append("menuitem_type",menuitem_type);
        formdata.append("menuitem_parent_item",menuitem_parent_item);
        jq.ajax({
            url: url,
            method: 'POST',
            data: formdata,
            dataType: 'text',
            processData: false,
            contentType: false,
            success: function(data){
                jq( "#loading" ).hide();
                jq('.dynamic-ajax-call-2').html(data);
                jq('.selectpicker').selectpicker('refresh');
            }
        });
    });


    jq('.dynamic-ajax-call-2').on('change','#menuitem_setmenuposition_parent',function(){
        jq( "#add_menu" ).prop('disabled',false);
    });

    jq('#menuitem_edit_type').change(function(){
        var url = '../adminAjax/ajaxSetMenuitemType';
        var data = jq('#menuitem_edit_type').val();
        jq.ajax({
            url: url,
            method: 'POST',
            data: {data: data},
            dataType: 'text',
            success: function(data){
                jq( "#loading" ).hide();
                jq('.dynamic-ajax-call').html(data);
                jq('.selectpicker').selectpicker('refresh');

            }
        });
    });//end menuitem set type function

    /*START DELETE POST DIALOG*/
    jq('.btn-post-delete').click(function(e){
        e.preventDefault();
        var title = jq('#delete-post-confirmation').val();
        var confirm = jq('#btn-continue').val();
        var cancel = jq('#btn-cancel').val();
        var needed_id = jq(this).prop('id');
        var id = needed_id.split('_');
        jq( '#confirm-delete-post' ).dialog({
            title: title,
            resizable: false,
            height:220,
            width: 420,
            modal: true,
            buttons: {
                'Kontynuuj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                        jq('#post-delete-form_'+id[1]).submit();
                    },
                    text: confirm,
                    class: 'btn btn-default'
                },
                'Anuluj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                    },
                    text: cancel,
                    class: 'btn btn-default'
                }
            }
        });
        jq( "#confirm-delete-post" ).dialog( "open" );
    });

    /*START DELETE COURSE DIALOG*/
    jq('.btn-couse-delete').click(function(e){
        e.preventDefault();
        var $delete_id = jq(this).prop('id');
        var id_part = $delete_id.split('_');
        var title = jq('#delete-course-confirmation').val();
        var confirm = jq('#btn-continue').val();
        var cancel = jq('#btn-cancel').val();
        jq( '#confirm-delete-course' ).dialog({
            title: title,
            resizable: false,
            height:220,
            width: 420,
            modal: true,
            buttons: {
                'Kontynuuj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                        jq('#form-delete-class_'+id_part[1]).submit();
                    },
                    text: confirm,
                    class: 'btn btn-default'
                },
                'Anuluj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                    },
                    text: cancel,
                    class: 'btn btn-default'
                }
            }
        });
        jq( "#confirm-delete-course" ).dialog( "open" );
    });

    /*START DELETE STUDENT DIALOG*/
    jq('.btn-student-delete').click(function(e){
        e.preventDefault();
        var $delete_id = jq(this).prop('id');
        var id_part = $delete_id.split('_');
        var title = jq('#delete-student-confirmation').val();
        var confirm = jq('#btn-continue').val();
        var cancel = jq('#btn-cancel').val();
        jq( '#confirm-delete-student' ).dialog({
            title: title,
            resizable: false,
            height:220,
            width: 420,
            modal: true,
            buttons: {
                'Kontynuuj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                        jq('#student-delete-form_'+id_part[1]).submit();
                    },
                    text: confirm,
                    class: 'btn btn-default'
                },
                'Anuluj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                    },
                    text: cancel,
                    class: 'btn btn-default'
                }
            }
        });
        jq( "#confirm-delete-student" ).dialog( "open" );
    });

    jq('.glyphicon').click(function(){
        var id = jq(this).prop('id');
        var needed_id = id.split('_');
        jq('#'+needed_id[1]).toggle();
    });
/*CONFIG PART FUNCTIONS */
    jq('#logo_add').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#logo_add')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var url = '../adminAjax/ajaxUploadFileLogo';
        var formdata = new FormData();
        formdata.append("images",img);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        // to do:: image validation
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq('#logo').val(data);
                    jq( "#loading" ).hide();
                }
            });
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

    jq('#mail_connection').change(function(){
        var value = jq('#mail_connection').val();
        if(value == 'none'){

        }else if(value == 'smtp'){
            jq('#show-smtp').show();
        }else{
            jq('#show-smtp').hide();
        }

    });

    jq('#group-show-pass').change(function(){
         var show_it = jq('#group-show-pass').val();
        if(show_it == 'yes'){
            jq('#group-curse-pass').prop('type','text');
        }else{
            jq('#group-curse-pass').prop('type','password');
        }
    });

    jq('#pass-gen').click(function(){
        jq('#group-curse-pass').val(randomPassword(8));
    });


    /*ADD STUDENT CHECK FUNCTIONS */
    jq('#add_student').click(function(e){
        e.preventDefault();
        var email = jq('#student-email').val();
        var name = jq('#student-name').val();
        var surname = jq('#student-surname').val();
        var group = jq('#student-group-select').val();
        var can_send_bool = 'no';
        if(group == 'none'){
            group = 0;
        }
        if(email == '' || name == '' || surname == ''){
            can_send_bool = 'no';
        }else{
            can_send_bool = 'yes';
        }

        if(can_send_bool === 'yes'){
            var url = '../adminAjax/ajaxCheckStudentAvai';
            var formdata = new FormData();
            formdata.append("email",email);
            formdata.append("name",name);
            formdata.append("surname",surname);
            formdata.append("group",group);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    if(data == 'true'){
                        jq('#admin_add_student').submit();
                    }else{
                        getPHPMessage('ATXT_STUDENT_EXISTS','error','.message-login');
                    }
                    jq( "#loading" ).hide();
                }
            });
        }else{
            getPHPMessage('MENUITEM_NOT_ALL_INPUTS','error','.message-login');
        }
    });

    /*ADD STUDENT CHECK FUNCTIONS */

    /*START DELETE STUDENT DIALOG*/
    jq('.recipient-delete-button').click(function(e){
        e.preventDefault();
        var $delete_id = jq(this).prop('id');
        var id_part = $delete_id.split('_');
        var title = jq('#delete-recipient-confirmation').val();
        var confirm = jq('#btn-continue').val();
        var cancel = jq('#btn-cancel').val();
        jq( '#confirm-delete-recipient' ).dialog({
            title: title,
            resizable: false,
            height:220,
            width: 420,
            modal: true,
            buttons: {
                'Kontynuuj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                        jq('#form-delete-recipient_'+id_part[1]).submit();
                    },
                    text: confirm,
                    class: 'btn btn-default'
                },
                'Anuluj': {
                    click: function() {
                        jq( this ).dialog( 'close' );
                    },
                    text: cancel,
                    class: 'btn btn-default'
                }
            }
        });
        jq( "#confirm-delete-recipient" ).dialog( "open" );
    });

    jq('#pick-user-new').change(function(){

         var which_one = jq(this).val();
        if(which_one == 'recipient-new'){
          jq('#new-recipient-no-show').show();
            jq('#add-recipient-list-select').hide();
        }else{
            jq('#add-recipient-list-select').show();
            jq('#new-recipient-no-show').hide();
            var url = '../adminAjax/ajaxGetAllUsers';
            var data = 'get-list';
            jq.ajax({
                url: url,
                method: 'POST',
                data: {data: data},
                dataType: 'text',
                success: function(data){
                    jq( "#loading" ).hide();
                    jq('#add-recipient-list-select').html(data);
                    jq('#users-list').selectpicker('refresh');

                }
            });
        }
    });

//================================================================================== MEDIA

    jq('#select_slider_id').change(function(){
        var slider_id = jq(this).val();
        jq('#slider_id').val(slider_id);
    });

    jq('#slider_media_add').change(function(event){ // MULTIUPLOAD checking and displaing thumbnails - actual upload in next function (on submit event(click))
        jq('#add_pictures_salon').prop('disabled',false);//just in case...
        var url = '../adminAjax/ajaxUpladMultiCheck';
        var gotcha = 0;
        var lenght = this.files.length;
        for(var i = 0; i < lenght; i++){
            var img = this.files[i];
            var tmppath = URL.createObjectURL(event.target.files[i]);

            var tmplabel = '<p class="temp-label">'+img.name+'</p><p class="temp-label">'+img.type+'</p><p class="temp-label">'+((parseInt(img.size)/1024)/1024).toFixed(2)+ 'MB'+'</p>';
            jq('.thums-list').html(jq('.thums-list').html()+'<li><div class="blob-video"><img class="img-responsive thumbnail no-margin image-thumb-multiupload" src="'+tmppath+'" />'+tmplabel+'</div></li>');


        }

        jq('#slider_media_add').prop('disabled',true);
        jq('#slider_media_add').css('cursor','not-allowed');
        jq('.input-placeholder').css('background-color','#EFB2AC');
        jq('#add_pictures_slider').prop('disabled',false);
    });

    jq('#add_pictures_slider').click(function(e){//just simple submit - needed to enable disabled earlier input - otherwise files are not submited

        e.preventDefault();
        var img = jq('#slider_media_add')[0].files[0];
        if(img){
            if(jq('#select_slider_id').val() != 0){
                jq('#slider_media_add').prop('disabled',false);
                jq('#add_media_slider_form').submit();
            }else{
                alert('Nie wybrano slidera dla obrazków!');
                e.stopPropagation();
            }
        }else{
            jq('#add_pictures_slider').prop('disabled',true);
            alert('Nie wybrano plików do wysłania na serwer!');
        }

    });

    jq('.button-add').click(function(){
        var button_id = jq(this).prop('id');
        var button_sort = button_id.split('_');
        if(button_sort[0] == 'btna'){
            var gallery_add_images_id = button_sort[1];
            window.location = '/admin/media/addgalleryimage?gallery_add_images_id='+gallery_add_images_id;
        }
    });

    jq('.button-edit').click(function(){

        var button_id = jq(this).prop('id');
        var button_sort = button_id.split('_');
        if(button_sort[0] == 'btne'){
            var slider_edit_id = button_sort[1];
            window.location = '/admin/media/editslider?slider_edit_id='+slider_edit_id;
        }

        if(button_sort[0] == 'gal-btne'){
            var gallery_edit_id = button_sort[1];
            window.location = '/admin/media/editgallery?gallery_edit_id='+gallery_edit_id;
        }

        if(button_sort[0] == 'btnimge'){
            var slider_id = button_sort[1];
            var image_id = button_sort[2];
            window.location = '/admin/media/editimage?slider_id='+slider_id+'&image_id='+image_id;
        }

        if(button_sort[0] == 'gal-btnimge'){
            var gallery_id = button_sort[1];
            var gallery_image_id = button_sort[2];
            window.location = '/admin/media/editgalleryimage?gallery_id='+gallery_id+'&gallery_image_id='+gallery_image_id;
        }

    });

    jq('.button-delete').click(function(){

        var button_id = jq(this).prop('id');
        var button_sort = button_id.split('_');
        if(button_sort[0] == 'btnd'){
            var slider_delete_id = button_sort[1];
            window.location = '/admin/media/deleteslider?slider_delete_id='+slider_delete_id;
        }

        if(button_sort[0] == 'gal-btnd'){
            var gallery_delete_id = button_sort[1];
            window.location = '/admin/media/deletegallery?gallery_delete_id='+gallery_delete_id;
        }

        if(button_sort[0] == 'btnimgd'){
            var slider_id = button_sort[1];
            var image_id = button_sort[2];
            window.location = '/admin/media/sliderdeleteimage?slider_id='+slider_id+'&image_id='+image_id;
        }

        if(button_sort[0] == 'gal-btnimgd'){
            var gallery_id = button_sort[1];
            var image_gallery_id = button_sort[2];
            window.location = '/admin/media/gallerydeleteimage?gallery_id='+gallery_id+'&image_gallery_id='+image_gallery_id;
        }

    });

    jq('#slider_image_edit').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#slider_image_edit')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var slider_id = jq('#slider_id').val();
        var url = '../adminAjax/ajaxUploadFileSlider';
        var formdata = new FormData();
        formdata.append("images",img);
        formdata.append("slider_id",slider_id);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        // to do:: image validation
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq('#slider_image').val(data);
                    jq( "#loading" ).hide();
                }
            });
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }

    });

/*GALLERY*/
    jq('#gallery_media_add').change(function(event){ // MULTIUPLOAD checking and displaing thumbnails - actual upload in next function (on submit event(click))
        jq('#add_pictures_gallery').prop('disabled',false);//just in case...
        var url = '../adminAjax/ajaxUpladMultiCheck';
        var gotcha = 0;
        var lenght = this.files.length;
        for(var i = 0; i < lenght; i++){
            var img = this.files[i];
            var tmppath = URL.createObjectURL(event.target.files[i]);

            var tmplabel = '<p class="temp-label">'+img.name+'</p><p class="temp-label">'+img.type+'</p><p class="temp-label">'+((parseInt(img.size)/1024)/1024).toFixed(2)+ 'MB'+'</p>';
            jq('.thums-list').html(jq('.thums-list').html()+'<li><div class="blob-video"><img class="img-responsive thumbnail no-margin image-thumb-multiupload" src="'+tmppath+'" />'+tmplabel+'</div></li>');


        }

        jq('#gallery_media_add').prop('disabled',true);
        jq('#gallery_media_add').css('cursor','not-allowed');
        jq('.input-placeholder').css('background-color','#EFB2AC');
        jq('#add_pictures_gallery').prop('disabled',false);
    });

    jq('#add_pictures_gallery').click(function(e){//just simple submit - needed to enable disabled earlier input - otherwise files are not submited

        e.preventDefault();
        var img = jq('#gallery_media_add')[0].files[0];
        if(img){
            if(jq('#select-gallery-category').val() != 0 && jq('#gallery-name').val() != ''){
                jq('#gallery-category-id').val(jq('#select-gallery-category').val());
                jq('#gallery_media_add').prop('disabled',false);
                jq('#add_media_form').submit();
            }else{
                alert('Nie wybrano kategorii lub/i nie wpisano tytułu galerii!');
                e.stopPropagation();
            }
        }else{
            jq('#add_pictures_gallery').prop('disabled',true);
            alert('Nie wybrano plików do wysłania na serwer!');
        }

    });

    jq('#select-gallery-category').change(function(){
        var gallery_category_id = jq(this).val();
        jq('#gallery-category-id').val(gallery_category_id);
    });

    jq('#gallery_image_edit').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#gallery_image_edit')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var gallery_id = jq('#gallery-id').val();
        var gallery_category_id = jq('#gallery-category-id').val();
        var url = '../adminAjax/ajaxUploadFileGallery';
        var formdata = new FormData();
        formdata.append("images",img);
        formdata.append("gallery_id",gallery_id);
        formdata.append("gallery_category_id",gallery_category_id);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        // to do:: image validation
        if(img){
            jq('.thumbnail-placeholder').html('<img class="img-responsive thumbnail no-margin" src="'+tmppath+'">'+tmp_label);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq('#gallery_image').val(data);
                    jq( "#loading" ).hide();
                }
            });
        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }

    });

    jq('#select-gallery-action').change(function(){

        var nothing_checked = 0;

         jq('.checkbox-gallery').each(function(){

             if(jq(this).prop('checked')){

                 nothing_checked = 1;

             }

         });

        if(nothing_checked == 1){//if some is checked

            var action_value = jq('#select-gallery-action').val();
            var action = '';

            if(action_value == 0){

                getPHPMessageStraight('AMENU_GALLERIES_ACTION_NOT_PICKED');
                location.reload();

            }else if(action_value == 'delete'){

                var title = jq('#delete-galeries-confirmation').val();
                var confirm = jq('#btn-continue').val();
                var cancel = jq('#btn-cancel').val();
                jq( '#confirm-delete-galeries' ).dialog({
                    title: title,
                    resizable: false,
                    height:220,
                    width: 420,
                    modal: true,
                    buttons: {
                        'Kontynuuj': {
                            click: function() {

                                action = '/admin/media/deletegallery';
                                jq('#form-gallery-action').prop('action',action);
                                jq('#form-gallery-action').submit();

                            },
                            text: confirm,
                            class: 'btn btn-default'
                        },
                        'Anuluj': {
                            click: function() {
                                jq( this ).dialog( 'close' );
                            },
                            text: cancel,
                            class: 'btn btn-default'
                        }
                    }
                });

                jq( "#confirm-delete-galeries" ).dialog( "open" );


            }else if(action_value == 'edit'){

                action = '/admin/media/editgallery';
                jq('#form-gallery-action').prop('action',action);
                jq('#form-gallery-action').submit();

            }else{//action must be image

                action = '/admin/media/galleryimages';
                jq('#form-gallery-action').prop('action',action);
                jq('#form-gallery-action').submit();

            }

        }else{//if nothing checked

            getPHPMessageStraight('AMENU_GALLERIES_CHECKBOX_NOT_CHECKED');
            location.reload();

        }

    });

    jq('#select-gallery-image-action').change(function(){

        var nothing_checked = 0;
        var action_value = jq('#select-gallery-image-action').val();
        var action = '';

        jq('.checkbox-gallery').each(function(){

            if(jq(this).prop('checked')){

                nothing_checked = 1;

            }

        });

        if(nothing_checked == 1){//if some is checked

            if(action_value == 0){

                getPHPMessageStraight('AMENU_GALLERIES_ACTION_NOT_PICKED');
                location.reload();

            }else if(action_value == 'delete'){

                var title = jq('#delete-images-confirmation').val();
                var confirm = jq('#btn-continue').val();
                var cancel = jq('#btn-cancel').val();
                jq( '#confirm-delete-images' ).dialog({
                    title: title,
                    resizable: false,
                    height:220,
                    width: 420,
                    modal: true,
                    buttons: {
                        'Kontynuuj': {
                            click: function() {

                                action = '/admin/media/gallerydeleteimage';
                                jq('#form-gallery-image-action').prop('action',action);
                                jq('#form-gallery-image-action').submit();

                            },
                            text: confirm,
                            class: 'btn btn-default'
                        },
                        'Anuluj': {
                            click: function() {
                                jq( this ).dialog( 'close' );
                            },
                            text: cancel,
                            class: 'btn btn-default'
                        }
                    }
                });

                jq( "#confirm-delete-images" ).dialog( "open" );

            }else if(action_value == 'edit'){

                action = '/admin/media/editgalleryimage';
                jq('#form-gallery-image-action').prop('action',action);
                jq('#form-gallery-image-action').submit();

            }

        }else{//if nothing checked

            if(action_value == 'add'){

                action = '/admin/media/addgalleryimage';
                jq('#form-gallery-image-action').prop('action',action);
                jq('#form-gallery-image-action').submit();

            }else{

                alert(getPHPMessageStraight('AMENU_GALLERIES_CHECKBOX_NOT_CHECKED'));
                location.reload();

            }

        }

    });

    /*END GALLERY*/

    /*FORMS*/

    jq('#select-form-action').change(function(){

        var nothing_checked = 0;

        jq('.checkbox-forms').each(function(){

            if(jq(this).prop('checked')){

                nothing_checked = 1;

            }

        });

        if(nothing_checked == 1){//if some is checked

            var action_value = jq('#select-form-action').val();
            var action = '';

            if(action_value == 0){

                getPHPMessageStraight('AMENU_GALLERIES_ACTION_NOT_PICKED');
                location.reload();

            }else if(action_value == 'delete'){

                var title = jq('#delete-forms-confirmation').val();
                var confirm = jq('#btn-continue').val();
                var cancel = jq('#btn-cancel').val();
                jq( '#confirm-delete-forms' ).dialog({
                    title: title,
                    resizable: false,
                    height:220,
                    width: 420,
                    modal: true,
                    buttons: {
                        'Kontynuuj': {
                            click: function() {

                                action = '/admin/forms/deleteForm';
                                jq('#form-forms-action').prop('action',action);
                                jq('#form-forms-action').submit();

                            },
                            text: confirm,
                            class: 'btn btn-default'
                        },
                        'Anuluj': {
                            click: function() {
                                jq( this ).dialog( 'close' );
                            },
                            text: cancel,
                            class: 'btn btn-default'
                        }
                    }
                });

                jq( "#confirm-delete-forms" ).dialog( "open" );


            }else if(action_value == 'edit'){

                action = '/admin/forms/editForm';
                jq('#form-forms-action').prop('action',action);
                jq('#form-forms-action').submit();

            }else{//action must be inputs

                action = '/admin/forms/formInputs';
                jq('#form-forms-action').prop('action',action);
                jq('#form-forms-action').submit();

            }

        }else{//if nothing checked

            alert(getPHPMessageStraight('AMENU_FORMS_CHECKBOX_NOT_CHECKED'));
            location.reload();
        }

    });

    jq('#select-form-input-type').change(function(){

        var url = '../adminAjax/ajaxLoadInputHtml';
        var data = jq('#select-form-input-type').val();
        var inpt_nbr = jq('#input-number').val();
        var formdata = new FormData();
        formdata.append("input_number",inpt_nbr);
        formdata.append("input_type",data);
        jq('#new-form-input-type').val(data);
        if(data == 12){//if radiobutton than just need to redirect to particular controller
            var form_id = jq(".new-form-created").prop("id");
                var id =  form_id.split('_');
                url = '../Forms/addFormInputs?radio_data='+id[1];
                 cancel(url);
        }else{

            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    var remains = jq('#new-form-created-html').html();
                    jq('#new-form-created-html').html(remains+data);
                    jq( "#loading" ).hide();
                    jq('#select-form-input-type').prop('disabled',true);
                    var inp_nbr = jq('#input-number').val();
                    jq('#input-number').val(parseInt(inp_nbr)+1);
                    jq('#send-new-form-created').prop('disabled',false);
                }
            });

        }

    });

    jq('#add-new-option').click(function(){

    });


    jq('#select-input-action').change(function(){

        var nothing_checked = 0;
        var action_value = jq('#select-input-action').val();
        var action = '';

        jq('.checkbox-inputs').each(function(){

            if(jq(this).prop('checked')){

                nothing_checked = 1;

            }

        });

        if(nothing_checked == 1){//if some is checked

            if(action_value == 0){

                getPHPMessageStraight('AMENU_GALLERIES_ACTION_NOT_PICKED');
                location.reload();

            }else if(action_value == 'delete'){

                var title = jq('#delete-inputs-confirmation').val();
                var confirm = jq('#btn-continue').val();
                var cancel = jq('#btn-cancel').val();
                jq( '#confirm-delete-inputs' ).dialog({
                    title: title,
                    resizable: false,
                    height:220,
                    width: 420,
                    modal: true,
                    buttons: {
                        'Kontynuuj': {
                            click: function() {

                                action = '/admin/forms/deleteFormInput';
                                jq('#form-inputs-action').prop('action',action);
                                jq('#form-inputs-action').submit();

                            },
                            text: confirm,
                            class: 'btn btn-default'
                        },
                        'Anuluj': {
                            click: function() {
                                jq( this ).dialog( 'close' );
                            },
                            text: cancel,
                            class: 'btn btn-default'
                        }
                    }
                });

                jq( "#confirm-delete-inputs" ).dialog( "open" );


            }else if(action_value == 'edit'){

                action = '/admin/forms/editFormInput';
                jq('#form-inputs-action').prop('action',action);
                jq('#form-inputs-action').submit();

            }else if(action_value == 'add-option'){

                action = '/admin/forms/optionsList';
            jq('#form-inputs-action').prop('action',action);
            jq('#form-inputs-action').submit();

             }else{//action must be add - will be executed only if somebody will check checkbox and than pick add action

                action = '/admin/forms/addFormInputs';
                jq('#form-inputs-action').prop('action',action);
                jq('#form-inputs-action').submit();

            }

        }else{//if nothing checked

            if(action_value == 'add'){//nothing is checked if we want to add new input as well so have to check if it is just mistake or on purpose

                action = '/admin/forms/addFormInputs';
                jq('#form-inputs-action').prop('action',action);
                jq('#form-inputs-action').submit();

            }else{

                action = '/admin/forms/cancelFormInputs';
                jq('#form-inputs-action').prop('action',action);
                jq('#form-inputs-action').submit();

            }

        }

    });

    jq('#select-option-action').change(function(){

        var nothing_checked = 0;
        var action_value = jq('#select-option-action').val();
        var action = '';

        jq('.checkbox-inputs').each(function(){

            if(jq(this).prop('checked')){

                nothing_checked = 1;

            }

        });

        if(nothing_checked == 1){//if some is checked

            if(action_value == 0){

                getPHPMessageStraight('AMENU_GALLERIES_ACTION_NOT_PICKED');
                location.reload();

            }else if(action_value == 'delete'){

                var title = jq('#delete-option-confirmation').val();
                var confirm = jq('#btn-continue').val();
                var cancel = jq('#btn-cancel').val();
                jq( '#confirm-delete-options' ).dialog({
                    title: title,
                    resizable: false,
                    height:220,
                    width: 420,
                    modal: true,
                    buttons: {
                        'Kontynuuj': {
                            click: function() {

                                action = '/admin/forms/deleteSelectOption';
                                jq('#form-select-options-action').prop('action',action);
                                jq('#form-select-options-action').submit();

                            },
                            text: confirm,
                            class: 'btn btn-default'
                        },
                        'Anuluj': {
                            click: function() {
                                jq( this ).dialog( 'close' );
                            },
                            text: cancel,
                            class: 'btn btn-default'
                        }
                    }
                });

                jq( "#confirm-delete-options" ).dialog( "open" );


            }else if(action_value == 'edit'){

                action = '/admin/forms/editSelectOption';
                jq('#form-select-options-action').prop('action',action);
                jq('#form-select-options-action').submit();

            }else if(action_value == 'add'){

                action = '/admin/forms/addSelectOption';
                jq('#form-select-options-action').prop('action',action);
                jq('#form-select-options-action').submit();

            }else{//just ina case

                action = '/admin/forms/optionsList';
                jq('#form-select-options-action').prop('action',action);
                jq('#form-select-options-action').submit();

            }

        }else{//if nothing checked must be add new option

            action = '/admin/forms/addSelectOption';
            jq('#form-select-options-action').prop('action',action);
            jq('#form-select-options-action').submit();

        }

    });

    /*END FORMS*/

    /*START UPLOAD FILES */
    jq('#file_add').change(function(event){ // little trick to get temp path of file to display thumbnail - it's not real path - it's fake path - blob
        var img = jq('#file_add')[0].files[0];
        var tmppath = URL.createObjectURL(event.target.files[0]);
        var tmp_label = '<div class="thumbnail-placeholder-label-after"><p class="thumbnail-placeholder-label-after-p">'+img.name+'</p><p class="thumbnail-placeholder-label-after-p">'+img.type+'</p><p class="thumbnail-placeholder-label-after-p">'+((img.size/1024)/1024).toFixed(2)+ 'MB'+'</p></div>';
        if(img){

            //todo: if image than ajax call to check mimetype and add proper file icon
            var url = '../adminAjax/matchExt';
            var formdata = new FormData();
            formdata.append("filename",img.name);
            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                    jq( "#loading" ).hide();
                    jq('.thumbnail-placeholder').html(data+tmp_label);
                }
            });



        }else{
            jq('.thumbnail-placeholder-label').html('no file has been selected');
        }
    });

    jq('#save-file-admin').click(function(e){

        e.preventDefault();
        var is_file = jq('#file_add')[0].files[0];
        var cat = jq('#pick-category').val();
        var title = jq('#add-file-title').val();

        if(!is_file){
            clearErrorSpan('#vFile');
            jq('#vFile').html('wymagane');
            setInterval(blinker, 500);

        }else if(cat == 0){
            clearErrorSpan('#vCat');
            jq('#vCat').html('wymagane');
            setInterval(blinker, 500);

        }else if(title == ''){
            clearErrorSpan('#vTitle');
            jq('#vTitle').html('wymagane');
            setInterval(blinker, 500);

        }else{

            jq('#admin_add_file').submit();

        }

    });

    jq('#save-edit-file-admin').click(function(e){

        e.preventDefault();
        var is_file = jq('#file_add')[0].files[0];
        var cat = jq('#pick-category').val();
        var title = jq('#add-file-title').val();

        if(cat == 0){
            clearErrorSpan('#vCat');
            jq('#vCat').html('wymagane');
            setInterval(blinker, 500);

        }else if(title == ''){
            clearErrorSpan('#vTitle');
            jq('#vTitle').html('wymagane');
            setInterval(blinker, 500);

        }else{

            jq('#admin_edit_file').submit();

        }

    });

    jq('#select-file-category').change(function(){

     var cat_id = jq('#select-file-category').val();

        if(cat_id != 0){
            var address = '../files/list';
            cancel(address+'?data='+cat_id);
        }

    });

    jq('#select-file-action').change(function(){

        var nothing_checked = 0;
        var action_value = jq('#select-file-action').val();
        var action = '';

        jq('.checkbox-inputs').each(function(){

            if(jq(this).prop('checked')){

                nothing_checked = 1;

            }

        });

        if(nothing_checked == 1){//if some is checked

            if(action_value == 0){

                getPHPMessageStraight('AMENU_GALLERIES_ACTION_NOT_PICKED');
                location.reload();

            }else if(action_value == 'delete'){

                var title = jq('#delete-file-confirmation').val();
                var confirm = jq('#btn-continue').val();
                var cancel = jq('#btn-cancel').val();
                jq( '#confirm-delete-files' ).dialog({
                    title: title,
                    resizable: false,
                    height:220,
                    width: 420,
                    modal: true,
                    buttons: {
                        'Kontynuuj': {
                            click: function() {

                                action = '/admin/files/deleteFile';
                                jq('#form-files-action').prop('action',action);
                                jq('#form-files-action').submit();

                            },
                            text: confirm,
                            class: 'btn btn-default'
                        },
                        'Anuluj': {
                            click: function() {
                                jq( this ).dialog( 'close' );
                            },
                            text: cancel,
                            class: 'btn btn-default'
                        }
                    }
                });

                jq( "#confirm-delete-files" ).dialog( "open" );


            }else if(action_value == 'edit'){

                action = '/admin/files/editFile';
                jq('#form-files-action').prop('action',action);
                jq('#form-files-action').submit();

            }else{//just ina case

                action = '/admin/files/list';
                jq('#form-files-action').prop('action',action);
                jq('#form-files-action').submit();

            }

        }else{//if nothing checked must be add new option

            action = '/admin/forms/addSelectOption';
            jq('#form-select-options-action').prop('action',action);
            jq('#form-select-options-action').submit();

        }

    });

    /*END UPLOAD FILES */

});//end document ready function capsule
