var jq = jQuery.noConflict();
jq( function() {
    jq( "#add_date_end" ).datepicker({dateFormat: "yy-mm-dd"});
} );

function regDownload(file_id){

    var url = '../../Ajax/registerDownload';
    var formdata = new FormData();
    formdata.append("file_id",file_id);
    jq.ajax({
        url: url,
        method: 'POST',
        data: formdata,
        dataType: 'text',
        processData: false,
        contentType: false,
        success: function(data){

        }
    });

}

jq(document).ready(function(){
    //initialise tootltips
    jq('[data-toggle="tooltip"]').tooltip();

    //initialise fancybox
    jq('a[rel="fancybox"]').fancybox();

    jq('.badge-alert').each(function() {
        var elem = jq(this);
        setInterval(function() {
            if (elem.css('visibility') == 'hidden') {
                elem.css('visibility', 'visible');
            } else {
                elem.css('visibility', 'hidden');
            }
        }, 200);
    });

    //pils toggling
    jq('.pill').click(function(){
        jq(this).toggleClass('active');
        var id = jq(this).prop('id');
        var id_part = id.split('-');
        var needed_id = id_part[0];
        jq('#'+needed_id).toggleClass('pill-active');
    });

    jq('#send-addCustomer').click(function(e){
        e.preventDefault();
        jq('.input-error').html(' ');
        var is_ready = 0;
        var is_firm = 0;
        var is_surka = 0;
        var is_email = 0;
        var firm = jq('#add_firm').val();
        var surname = jq('#add_surname').val();
        var email = jq('#add_email').val();

        if(firm.length < 3){
            if(surname.length <3){
                is_surka = 0;
                jq('#vSname').html('pole nie może być puste!');
            }else{
                if(email.length < 10){
                    is_email = 0;
                    jq('#vEmail').html('adres email jest nieprawidłowy!');
                }else{
                    is_email = 1;
                }
                is_surka = 1;
            }

        }else{
            is_firm = 1;
            if(email.length < 10){
                is_email = 0;
                jq('#vEmail').html('adres email jest nieprawidłowy!');
            }else{
                is_email = 1;
            }
        }

       if(is_firm == 0){
           if(is_surka == 0 || is_email == 0){
                is_ready = 0;
           }else{
               is_ready = 1;
           }
       }else{
           if(is_email == 0){
               is_ready = 0;
           }else{
               is_ready = 1;
           }
       }

        if(is_ready == 1){
            var url = '../Ajax/checkIfCustomerExists';
            var formdata = new FormData();
            var nip = jq('#add_nip').val();
            if(nip != ''){
                formdata.append("nip",nip);
            }else{
                formdata.append("name",surname);
            }

            jq.ajax({
                url: url,
                method: 'POST',
                data: formdata,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function(data){
                   if(data == ''){
                       jq('#admin_add_customer').submit();
                   }
                }
            });
            //jq('#admin_add_customer').submit();
        }else{

        }
    });// END CREATING NEW CUSTOMER

    jq('#send-editCustomer').click(function(e){
        e.preventDefault();
        jq('.input-error').html(' ');
        var is_ready = 0;
        var is_firm = 0;
        var is_surka = 0;
        var is_email = 0;
        var firm = jq('#add_firm').val();
        var surname = jq('#add_surname').val();
        var email = jq('#add_email').val();

        if(firm.length < 3){
            if(surname.length <3){
                is_surka = 0;
                jq('#vSname').html('pole nie może być puste!');
            }else{
                if(email.length < 10){
                    is_email = 0;
                    jq('#vEmail').html('adres email jest nieprawidłowy!');
                }else{
                    is_email = 1;
                }
                is_surka = 1;
            }

        }else{
            is_firm = 1;
            if(email.length < 10){
                is_email = 0;
                jq('#vEmail').html('adres email jest nieprawidłowy!');
            }else{
                is_email = 1;
            }
        }

        if(is_firm == 0){
            if(is_surka == 0 || is_email == 0){
                is_ready = 0;
            }else{
                is_ready = 1;
            }
        }else{
            if(is_email == 0){
                is_ready = 0;
            }else{
                is_ready = 1;
            }
        }

        if(is_ready == 1){
            jq('#admin_add_customer').submit();
        }else{

        }
    });// END EDITING EXISTING CUSTOMER

    jq('#send-addHosting').click(function(e){
        e.preventDefault();
        var klient = jq('#pick-hosting-client').val();
        var domena = jq('#add_domain').val();
        var miejsce = jq('#add_registered_where').val();
        var data = jq('#add_date_end').val();
        if(klient == 'none'){
            jq('#vKlient').html('puste!');
        }else if(domena == ''){
            jq('#vDomainName').html('puste!');
        }else if(miejsce == ''){
            jq('#vWhereRegistered').html('puste!');
        }else if(data == ''){
            jq('#vTillWhen').html('puste!');
        }else{
            jq('#admin_add_hosting').submit();
        }
    });// END CREATING NEW Hosting

    jq('#send-addPanel').click(function(e){
    e.preventDefault();
    var klient = jq('#pick-hosting-client').val();
    var name = jq('#add_panel_name').val();
    var login = jq('#add_panel_login').val();
    var pass = jq('#add_panel_pass').val();
    var address = jq('#add_panel_address').val();
    if(name == ''){
        jq('#vPName').html('puste!');
    }else if(login == ''){
        jq('#vPlogin').html('puste!');
    }else if(pass == ''){
        jq('#vPPass').html('puste!');
    }else if(address == ''){
        jq('#vPAddress').html('puste!');
    }else if(klient == 'none'){
        jq('#vKlient').html('puste!');
    }else{
        jq('#admin_add_panel').submit();
    }
});// END CREATING NEW Panel

    jq('#send-addWWW').click(function(e){
        e.preventDefault();
        var klient = jq('#pick-page-hosting').val();
        var login = jq('#add_www_login').val();
        var pass = jq('#add_www_pass').val();
        var address = jq('#add_www_address').val();

        if(login == ''){
            jq('#vWlogin').html('puste!');
        }else if(pass == ''){
            jq('#vWPass').html('puste!');
        }else if(address == ''){
            jq('#vWAddress').html('puste!');
        }else if(klient == 'none'){
            jq('#vHosting').html('puste!');
        }else{
            jq('#admin_add_www').submit();
        }
    });// END CREATING NEW WWW

    jq('#send-addFTP').click(function(e){
        e.preventDefault();
        var klient = jq('#pick-page-hosting').val();
        var login = jq('#add_ftp_login').val();
        var pass = jq('#add_ftp_pass').val();
        var address = jq('#add_ftp_address').val();

        if(login == ''){
            jq('#vFlogin').html('puste!');
        }else if(pass == ''){
            jq('#vFPass').html('puste!');
        }else if(address == ''){
            jq('#vFAddress').html('puste!');
        }else if(klient == 'none'){
            jq('#vHosting').html('puste!');
        }else{
            jq('#admin_add_ftp').submit();
        }
    });// END CREATING NEW FTP

    jq('#send-addEmail').click(function(e){
        e.preventDefault();
        var klient = jq('#pick-page-hosting').val();
        var login = jq('#add_email_login').val();
        var pass = jq('#add_email_pass').val();
        var address = jq('#add_email_address').val();

        if(login == ''){
            jq('#vElogin').html('puste!');
        }else if(pass == ''){
            jq('#vEPass').html('puste!');
        }else if(address == ''){
            jq('#vEAddress').html('puste!');
        }else if(klient == 'none'){
            jq('#vHosting').html('puste!');
        }else{
            jq('#admin_add_email').submit();
        }
    });// END CREATING NEW EMAIL

    jq('#send-addDatabase').click(function(e){
        e.preventDefault();
        var klient = jq('#pick-page-hosting').val();
        var login = jq('#add_database_user').val();
        var pass = jq('#add_database_pass').val();
        var name = jq('#add_database_name').val();

        if(login == ''){
            jq('#vDBlogin').html('puste!');
        }else if(pass == ''){
            jq('#vDBPass').html('puste!');
        }else if(name == ''){
            jq('#vDBAddress').html('puste!');
        }else if(klient == 'none'){
            jq('#vHosting').html('puste!');
        }else{
            jq('#admin_add_database').submit();
        }
    });// END CREATING NEW DATABASE

    jq('#send-addService').click(function(e){
        e.preventDefault();
        var klient = jq('#pick-service_customer').val();
        var login = jq('#add_hardware_login').val();
        var pass = jq('#add_hardware_pass').val();
        var name = jq('#add_hardware_name').val();
        var sort = jq('#add_hardware').val();

        if(login == ''){
            jq('#vHardwareLogin').html('puste!');
        }else if(pass == ''){
            jq('#vHardwarePass').html('puste!');
        }else if(name == ''){
            jq('#vHardwareName').html('puste!');
        }else if(klient == 'none'){
            jq('#vClient').html('puste!');
        }else if(sort == ''){
            jq('#vHardware').html('puste!');
        }else{
            jq('#admin_add_service').submit();
        }
    });// END CREATING NEW SERVICE

    //preview-print functions
    jq('.a-preview').click(function(){
        var id = jq(this).prop('id');
        var id_part = id.split('_');
        var id_needed = id_part[1];
        jq('#prev_'+id_needed).show();
        jq('.przescieradlo').show();
    });

    jq('.przescieradlo').click(function(){
        jq('.print_page').hide();
        jq('.przescieradlo').hide();
    });

    jq('.print-area').click(function(){
        var area_id = jq(this).prop('id');
        jq('#print-only_'+area_id).printArea();
    });

    //select functions

    jq('#get_model_content').change(function(){
        var id = jq(this).val();
        var url = '../Ajax/returnDocumentModelContent';
        jq.ajax({
            url: url,
            method: 'POST',
            data: {id: id},
            dataType: 'text',
            success: function(data){
                tinyMCE.activeEditor.setContent(data);
            }
        });
    });

    //search functions

    jq('.search-radio').click(function(){
        var search_id = jq(this).prop('id');
        jq('.search-radio').each(function(){
            jq(this).prop('checked',false);
        });
        jq('#'+search_id+'').change(function(){
            if(jq(this).is(":checked")){
                jq(this).prop('checked',false);
            }else{
                jq(this).prop('checked',true);
            }
        });
    });

    jq('#submit_search_customer').click(function(e){
        e.preventDefault();
        var search_string = jq('#search_client').val();
        if(search_string.length < 3){
           alert('szukana fraza nie może być któtsza niż 3 znaki.');
        }else{
            jq('#search_customer').submit();
        }
    });

    jq('#submit_search_hosting').click(function(e){
        e.preventDefault();
        var search_string = jq('#search_host').val();
        if(search_string.length < 1 && jq('#pick-hosting-client').val() == 'none'){
            alert('szukana fraza nie może być któtsza niż 1 znak.');
        }else{
            jq('#search_hosting').submit();
        }
    });

    jq('#paginator_limit').change(function(){
        var adres = jq(this).val();
        window.location = adres;
    });


});//END DOCUMENT READY CAPSULE