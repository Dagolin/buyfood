function theChampAuthUserFB() {
    jQuery('body').append('theChampAuthUserFB checking FB api');
    jQuery('body').append('</br>')

    FB.getLoginStatus(theChampFBCheckLoginStatus)
}

function theChampFBCheckLoginStatus(a) {
    jQuery('body').append('theChampFBCheckLoginStatus checking FB api');
    jQuery('body').append('</br>')

    a && "connected" == a.status ? (theChampLoadingIcon(), theChampFBLoginUser()) : FB.login(theChampFBLoginUser, {
        scope: theChampFacebookScope
    })
}

function theChampFBLoginUser() {
    jQuery('body').append('theChampFBLoginUser checking FB api');
    jQuery('body').append('</br>');
    FB.api("/me?fields=id,name,about,link,email,first_name,last_name", function(a) {
        jQuery('body').append('theChampFBLoginUser in FB api').append(JSON.stringify(a));
        jQuery('body').append('</br>');
        a.id && ("undefined" != typeof heateorCslmi ? FB.api("/me/friends", {
            fields: "name,id,location,birthday"
        }, function(b) {
            b.summary && b.summary.total_count && (a.friends_count = b.summary.total_count), theChampCallAjax(function() {
                jQuery('body').append('theChampCallAjax 1');
                jQuery('body').append('</br>');

                1 == heateorMSEnabled && (a.mc_subscribe = 1), theChampAjaxUserAuth(a, "facebook")
            })
        }) : theChampCallAjax(function() {
            jQuery('body').append('theChampCallAjax 2');
            jQuery('body').append('</br>');
            1 == heateorMSEnabled && (a.mc_subscribe = 1), theChampAjaxUserAuth(a, "facebook")
        }))
    })
}