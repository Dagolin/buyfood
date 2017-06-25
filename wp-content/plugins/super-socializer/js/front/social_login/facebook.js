function theChampAuthUserFB() {
    FB.getLoginStatus(theChampFBCheckLoginStatus)
}

function theChampFBCheckLoginStatus(a) {
    if (location.href.indexOf('code') !== -1 && location.href.indexOf('token') !== -1 ) {
        if (a && "connected" == a.status) {
            theChampLoadingIcon();
            theChampFBLoginUser();
        } else {
            var params = location.href.split('&');
            params = params[0].split('=');
            
            theChampAjaxUserAuth(params , "facebook");
        }
    }
}

function theChampFBLoginUser() {
    FB.api("/me?fields=id,name,about,link,email,first_name,last_name", function(a) {
        a.id && ("undefined" != typeof heateorCslmi ? FB.api("/me/friends", {
            fields: "name,id,location,birthday"
        }, function(b) {
            b.summary && b.summary.total_count && (a.friends_count = b.summary.total_count), theChampCallAjax(function() {
                1 == heateorMSEnabled && (a.mc_subscribe = 1), theChampAjaxUserAuth(a, "facebook")
            })
        }) : theChampCallAjax(function() {
            1 == heateorMSEnabled && (a.mc_subscribe = 1), theChampAjaxUserAuth(a, "facebook")
        }))
    })
}