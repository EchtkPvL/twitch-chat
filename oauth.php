<?php
error_reporting(0);
ini_set("display_errors", 0);
require_once "config.php";

$url = sprintf("//id.twitch.tv/oauth2/authorize?response_type=token&client_id=%s&redirect_uri=%s&scope=%s",
    $options['client_id'],
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

            if (accessToken) {
                $("#infoTable").hide();
                $("#passwordTable").show();
                $("#tmiPasswordField").val('oauth:' + encodeURIComponent(accessToken));
            }
        });
    </script>
    <style>
        #tmiPasswordField {
            cursor: default !important;
        }
    </style>
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
                                    <p>Use the following token to login to chat:</p>
                                    <input class="span9" type="text" id="tmiPasswordField" onClick="this.select();" readonly="readonly">
                                    <br />
                                    <br />
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
                                    <strong>To revoke access, disconnect "Twitch Chat OAuth Token Generator" from your Twitch <a href="//www.twitch.tv/settings/connections" target="_blank">settings</a>.</strong>
                                    </p>
                                    <p>
                                    <small>(Technical: This application uses the <a href="//dev.twitch.tv/docs/v5/guides/authentication/" target="_blank">implicit grant flow</a> for the Twitch API to retrieve your token. This means that your token is only ever visible to your browser and not our server.)</small>
                                    </p>
                                </div>

                                <div style="text-align:center;padding-bottom:10px;">
                                    <a href="<?php echo $url; ?>" class="btn btn-large btn-primary">Connect</a>
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
