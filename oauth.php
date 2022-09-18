<?php
error_reporting(0);
ini_set("display_errors", 0);
//require_once "config.php";
$options = [
    'client_id' => '4zaj3kl0se6h6smuqeox5w9bsutzse',
    'oauth_uri' => 'https://twitch.echtkpvl.de/oauth',
    'scope' => [
            /* Twitch API Scope */
            'analytics:read:extensions',
            'analytics:read:games',
            'bits:read',
            'channel:edit:commercial',
            'channel:manage:broadcast',
            'channel:read:charity',
            'channel:manage:extensions',
            'channel:manage:moderators',
            'channel:manage:polls',
            'channel:manage:predictions',
            'channel:manage:raids',
            'channel:manage:redemptions',
            'channel:manage:schedule',
            'channel:manage:videos',
            'channel:read:editors',
            'channel:read:goals',
            'channel:read:hype_train',
            'channel:read:polls',
            'channel:read:predictions',
            'channel:read:redemptions',
            'channel:read:stream_key',
            'channel:read:subscriptions',
            'channel:read:vips',
            'channel:manage:vips',
            'clips:edit',
            'moderation:read',
            'moderator:manage:announcements',
            'moderator:manage:automod',
            'moderator:read:automod_settings',
            'moderator:manage:automod_settings',
            'moderator:manage:banned_users',
            'moderator:read:blocked_terms',
            'moderator:manage:blocked_terms',
            'moderator:manage:chat_messages',
            'moderator:read:chat_settings',
            'moderator:manage:chat_settings',
            'user:edit',
            'user:edit:follows',
            'user:manage:blocked_users',
            'user:read:blocked_users',
            'user:read:broadcast',
            'user:manage:chat_color',
            'user:read:email',
            'user:read:follows',
            'user:read:subscriptions',
            'user:manage:whispers',

            /* Chat and PubSub Scope */
            'channel:moderate',
            'chat:edit',
            'chat:read',
            'whispers:read',
            'whispers:edit',
    ],
];


$url = sprintf("//id.twitch.tv/oauth2/authorize?response_type=token&redirect_uri=%s&scope=%s&client_id=",
    $options['oauth_uri'],
    implode('+', $options['scope'])
);

?><html lang="en">
<head>
    <meta charset="utf-8">
    <title>Twitch Chat Token Generator</title>
    <link href="//stackpath.bootstrapcdn.com/bootswatch/2.3.2/cosmo/bootstrap.min.css" rel="stylesheet" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/2.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            var oauthHash = location.hash.substr(1);
            var accessToken = oauthHash.substr(oauthHash.indexOf('access_token=')).split('&')[0].split('=')[1];
            var scopes = oauthHash.substr(oauthHash.indexOf('scope=')).split('&')[0].split('=')[1];

            if (accessToken) {
                $("#infoTable").hide();
                $("#passwordTable").show();
                $("#tmiPasswordField").val(encodeURIComponent(accessToken));

                if (scopes) {
                    const scopeList = document.getElementById("scopes");
                    scopes = decodeURIComponent(scopes).split('+');

                    for (const scope of scopes) {
                        const li = document.createElement("li");
                        li.textContent = scope;
                        scopeList.appendChild(li);
                    };
                } else {
                    $("#scopesDiv").hide();
                }

                fetch("https://id.twitch.tv/oauth2/validate", {
                    method: 'GET',
                    headers: {
                        'Authorization': 'OAuth ' + accessToken
                    }
                }).then((response) => {
                    return response.json();
                }).then((data) => {
                    console.log(data);
                    
                    if (typeof data.expires_in === 'undefined') {
                        $("#tmiValidate").text( data );
                        $("#tmiValidate").text( JSON.stringify(data, null, 4) );
                        return;
                    }

                    $("#tmiValidate").text( JSON.stringify(data, null, 4) );

                    if (data.expires_in != 0) {
                        var date = new Date();
                        date.setSeconds(date.getSeconds() + data.expires_in);
                        var timeString = date.toISOString().substring(0, 19).replace('T', ' ');
                        console.log(timeString);

                        $("#tmiValidate").text( $("#tmiValidate").text() + "\nExpire date: " + timeString );
                    }
                }).catch(function(error) {
                    console.log("fetch error");
                    console.log(error);
                });
            }

            $("#tmiClientId").on('propertychange input', function (e) {
              var valueChanged = false;
                console.log(e);

              if (e.type=='propertychange') {
                valueChanged = e.originalEvent.propertyName=='value';
              } else {
                valueChanged = true;
              }
              if (valueChanged) {
                $("#tmiUrl").attr('href', '<?php echo $url; ?>' + $("#tmiClientId").val());
                console.log($("#tmiUrl").attr('href'));
              }
            });
        });
    </script>
</head>
<body style="padding-top: 20px;">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div style="width:600px;margin:auto;">
                    <table class="table table-bordered table-striped" id="passwordTable" style="display:none;">
                        <tr>
                            <th>Twitch Chat OAuth Token Generator</th>
                        </tr>
                        <tr>
                            <td>
                                <div style="text-align:center;padding-bottom:10px;">
                                    <div>
                                        <p>Your token:</p>
                                        <input class="span9" type="text" id="tmiPasswordField" onClick="this.select();" style="cursor:default!important;" readonly="readonly">
                                    </div>
                                    <div id="scopesDiv">
                                        <br>
                                        <br>
                                        <p>Scopes:</p>
                                        <ul id="scopes" style="text-align:left"></ul>
                                    </div>
                                    <div>
                                        <br>
                                        <br>
                                        <p>oauth2 validate:</p>
                                        <pre id="tmiValidate" style="text-align:left;"></pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class="table table-bordered table-striped" id="infoTable">
                        <tr>
                            <th>Twitch Chat OAuth Token Generator</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="well">
                                    <p>
                                    Use this tool to generate an OAuth token to authenticate with Twitch IRC. The entire presented token (including "oauth:") can be substituted for your old password in your IRC client.
                                    </p>
                                    <p>
                                    <strong>To revoke access, disconnect this App from your Twitch <a href="//www.twitch.tv/settings/connections" target="_blank">Connections-settings</a>.</strong>
                                    </p>
                                    <p>
                                    <small>(Technical: This application uses the <a href="//dev.twitch.tv/docs/authentication" target="_blank">implicit grant flow</a> for the Twitch API to retrieve your token. This means that your token is only ever visible to your browser and not our server.)</small>
                                    </p>
                                </div>

                                <div style="text-align:center;padding-bottom:10px;">
                                    App Client-ID: <input class="span9" type="text" id="tmiClientId" value="<?php echo $options['client_id']; ?>"><br>
                                    <a href="<?php echo $url . $options['client_id']; ?>" class="btn btn-large btn-primary" id="tmiUrl">Connect</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
