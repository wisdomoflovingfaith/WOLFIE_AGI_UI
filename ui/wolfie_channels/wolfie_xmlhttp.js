/**
 * WOLFIE AGI UI - XMLHttpRequest Client (Based on SalesSyntax 3.7.0)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Real XMLHttpRequest client for channels like salessyntax3.7.0
 * WHERE: C:\START\WOLFIE_AGI_UI\ui\wolfie_channels\
 * WHEN: 2025-09-26 15:10:00 CDT
 * WHY: To implement proper channel communication like salessyntax3.7.0
 * HOW: JavaScript-based XMLHttpRequest client with 2.1 second polling
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of real channel communication
 * MD: Markdown documentation with .js implementation
 * 
 * FILE IDS: [WOLFIE_XMLHTTP_JS_001, WOLFIE_AGI_UI_022]
 * 
 * VERSION: 1.0.0 - The Captain's Real XMLHttpRequest Client
 * STATUS: Active - Based on SalesSyntax 3.7.0
 */

var xmlhttp = false;
var XMLHTTP_supported = false;
var oXMLHTTP = false;

// Get XMLHttpRequest object (like salessyntax3.7.0)
function gettHTTPreqobj() {
    try {
        xmlhttp = new XMLHttpRequest();
    } catch (e1) {
        try {
            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e2) {
            try {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e3) {
                xmlhttp = false;
            }
        }
    }
    return xmlhttp;
}

// Load XMLHTTP (like salessyntax3.7.0)
function loadXMLHTTP() {
    // account for cache..
    randu = Math.round(Math.random() * 99);
    // load a test page:
    loadOK('wolfie_xmlhttp.php?whattodo=ping&rand=' + randu);
}

function loadOK(fragment_url) {
    xmlhttp = gettHTTPreqobj();
    xmlhttp.open("GET", fragment_url, true);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            isok = xmlhttp.responseText;
            if (isok == "OK")
                XMLHTTP_supported = true;
            checkXMLHTTP();
        }
    }
    try { xmlhttp.send(null); } catch (whocares) {}
}

// XMLHTTP State Handler (like salessyntax3.7.0)
function oXMLHTTPStateHandler() {
    // only if req shows "loaded"
    if (typeof oXMLHTTP != 'undefined') {
        if (oXMLHTTP.readyState == 4) {         // 4="completed"
            if (oXMLHTTP.status == 200) {         // 'OK Operation successful
                try {
                    resultingtext = oXMLHTTP.responseText;
                } catch (e) {
                    resultingtext = "error=1;";
                }
                ExecRes(unescape(resultingtext));
                delete oXMLHTTP;
                oXMLHTTP = false;
            } else {
                return false;
            }
        }
    }
}

// Submit POST data to server and retrieve results (like salessyntax3.7.0)
function PostForm(sURL, sPostData) {
    oXMLHTTP = gettHTTPreqobj();
    if (typeof (oXMLHTTP) != "object") return false;

    oXMLHTTP.onreadystatechange = oXMLHTTPStateHandler;
    try {
        oXMLHTTP.open("POST", sURL, true);
    } catch (er) {
        return false;
    }
    oXMLHTTP.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    try { oXMLHTTP.send(sPostData); } catch (whocares) {}
    return true;
}

// Submit GET data to server and retrieve results (like salessyntax3.7.0)
function GETForm(sURL) {
    oXMLHTTP = gettHTTPreqobj();
    if (typeof (oXMLHTTP) != "object") return false;
    oXMLHTTP.onreadystatechange = oXMLHTTPStateHandler;
    try {
        oXMLHTTP.open("GET", sURL, true);
    } catch (er) {
        return false;
    }
    try { oXMLHTTP.send(null); } catch (whocares) {}
    return true;
}

// WOLFIE Channel System
var WolfieChannelSystem = {
    currentChannel: null,
    currentUser: 'captain_wolfie',
    HTMLtimeof: 0,
    LAYERtimeof: 0,
    whatissaid: new Array(100),
    
    // Initialize channel system
    init: function() {
        console.log('🛸 WOLFIE Channel System Initialized');
        this.loadXMLHTTP();
        this.startPolling();
    },
    
    // Load XMLHTTP (like salessyntax3.7.0)
    loadXMLHTTP: function() {
        // account for cache..
        randu = Math.round(Math.random() * 9999);
        // load a test page:
        this.loadOK('wolfie_xmlhttp.php?whattodo=ping&rand=' + randu);
    },
    
    loadOK: function(fragment_url) {
        xmlhttp = gettHTTPreqobj();
        xmlhttp.open("GET", fragment_url, true);
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                isok = xmlhttp.responseText;
                if (isok == "OK")
                    XMLHTTP_supported = true;
                console.log('✅ XMLHTTP Supported: ' + XMLHTTP_supported);
            }
        }
        try { xmlhttp.send(null); } catch (whocares) {}
    },
    
    // Start polling like salessyntax3.7.0
    startPolling: function() {
        if (this.currentChannel) {
            this.update_xmlhttp();
        }
    },
    
    // Update XMLHTTP (like salessyntax3.7.0)
    update_xmlhttp: function() {
        setTimeout(() => this.update_xmlhttp(), 2100);
        // account for cache..
        randu = Math.round(Math.random() * 9999);
        sURL = 'wolfie_xmlhttp.php';
        extra = "";
        
        sPostData = 'whattodo=messages&channel_id=' + this.currentChannel + '&user_id=' + this.currentUser + '&rand=' + randu + '&HTML=' + this.HTMLtimeof + '&LAYER=' + this.LAYERtimeof + extra;
        fullurl = 'wolfie_xmlhttp.php?' + sPostData;
        GETForm(fullurl);
    },
    
    // Parse messages (like salessyntax3.7.0)
    ExecRes: function(textstring) {
        var chatelement = document.getElementById('currentchat');
        
        var messages = new Array();
        
        // prevent Null textstrings:
        textstring = textstring + " ok =1; ";
        try { eval(textstring); } catch (error4) {}
        
        for (var i = 0; i < messages.length; i++) {
            res_timeof = messages[i][0];
            res_jsrn = messages[i][1];
            res_typeof = messages[i][2];
            res_message = messages[i][3];
            res_javascript = messages[i][4];
            
            // is it defined:
            if (typeof chatelement != 'undefined') {
                
                if (res_typeof == "HTML") {
                    if (res_timeof > this.HTMLtimeof) {
                        try { chatelement.innerHTML = chatelement.innerHTML + unescape(res_message); }
                        catch (fucken_IE) { }
                        this.HTMLtimeof = res_timeof;
                        this.whatissaid[res_jsrn] = 'nullstring';
                        if (res_javascript != "")
                            eval(res_javascript);
                        this.update_typing();
                        this.up();
                    }
                }
                if (res_typeof == "LAYER") {
                    if (res_timeof > this.LAYERtimeof) {
                        this.whatissaid[res_jsrn] = unescape(res_message);
                        this.LAYERtimeof = res_timeof;
                        this.update_typing();
                    }
                }
            }
        }
    },
    
    // Create channel
    createChannel: function(name, type = 'general') {
        randu = Math.round(Math.random() * 9999);
        sURL = 'wolfie_xmlhttp.php';
        sPostData = 'whattodo=create_channel&user_id=' + this.currentUser + '&rand=' + randu;
        fullurl = 'wolfie_xmlhttp.php?' + sPostData;
        
        oXMLHTTP = gettHTTPreqobj();
        if (typeof (oXMLHTTP) != "object") return false;
        
        oXMLHTTP.onreadystatechange = function() {
            if (oXMLHTTP.readyState == 4 && oXMLHTTP.status == 200) {
                try {
                    var result = JSON.parse(oXMLHTTP.responseText);
                    WolfieChannelSystem.currentChannel = result.channel_id;
                    console.log('✅ Channel created: ' + result.channel_id);
                    WolfieChannelSystem.startPolling();
                } catch (e) {
                    console.error('Error creating channel:', e);
                }
            }
        }
        
        try {
            oXMLHTTP.open("GET", fullurl, true);
        } catch (er) {
            return false;
        }
        try { oXMLHTTP.send(null); } catch (whocares) {}
        return true;
    },
    
    // Send message
    sendMessage: function(message) {
        if (!this.currentChannel) {
            console.error('No channel selected');
            return;
        }
        
        randu = Math.round(Math.random() * 9999);
        sURL = 'wolfie_xmlhttp.php';
        sPostData = 'whattodo=send&channel_id=' + this.currentChannel + '&user_id=' + this.currentUser + '&message=' + encodeURIComponent(message) + '&rand=' + randu;
        fullurl = 'wolfie_xmlhttp.php?' + sPostData;
        
        oXMLHTTP = gettHTTPreqobj();
        if (typeof (oXMLHTTP) != "object") return false;
        
        oXMLHTTP.onreadystatechange = function() {
            if (oXMLHTTP.readyState == 4 && oXMLHTTP.status == 200) {
                console.log('Message sent: ' + oXMLHTTP.responseText);
            }
        }
        
        try {
            oXMLHTTP.open("GET", fullurl, true);
        } catch (er) {
            return false;
        }
        try { oXMLHTTP.send(null); } catch (whocares) {}
        return true;
    },
    
    // Update typing indicator (like salessyntax3.7.0)
    update_typing: function() {
        // Implementation for typing indicators
        console.log('Typing indicator updated');
    },
    
    // Scroll to bottom (like salessyntax3.7.0)
    up: function() {
        scroll(1, 10000000);
    }
};

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    WolfieChannelSystem.init();
});

// Make it global
window.WolfieChannelSystem = WolfieChannelSystem;
