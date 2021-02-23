<?php
/**
 *
 * ToDo: Fade-Out von Nachrichten nach x Sekunden -> Besserer Überblick bei wenig Nachrichten
 *
 * @author    Jonas Berner <admin@jonas-berner.de>
 * @copyright 23.02.2021 Jonas Berner
 */
error_reporting(0);
ini_set("display_errors", 0);
require_once "config.php";

$twitchApi = new \TwitchApi\TwitchApi($options);
checkLogin($twitchApi);
$user = $twitchApi->getAuthenticatedUser($_SESSION['access_token']);
$token = "oauth:" . $_SESSION['access_token'];
$channels = empty($_GET['channels']) ? ['echtkpvl', 'joeel561'] : explode(',', $_GET['channels']);

?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>Twitch</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta charset="utf-8">
        <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <style>
            html, body {
                height: 100%;
                background: rgb(55 55 65);
                color: rgb(255 255 240);
            }
            
            input[type=text] {
                background-color: rgb(65 70 75);
                color: rgb(255 255 240);
            }

            .emoticon {
                height: 20px;
                width: 20px;
            }

            li {
                list-style-type: none; /* decimal-leading-zero; */
                border-bottom: 1px solid rgb(45 45 55);
                padding: 2px 0 2px 0;
                font-size: small;
            }

            li > a { display: none; }
            li.text-muted > a, li.text-white > a { display: unset; }

            #left:after {
                content: "";
                background-color: rgb(45 45 55);
                position: absolute;
                width: 5px;
                height: 100%;
                left: 99%;
            }

            div.chat {
                flex-shrink: 1;
                flex-grow: 1;
                flex-direction: column;
                display: flex;
                overflow: hidden;
                position: relative;
                z-index: 0;
                height: 100%;
                flex-wrap: nowrap!important;
                bottom: 0;
            }

            .chat > ul {
                box-sizing: content-box;
                min-width: 100%;
                overflow-x: hidden;
                overflow-y: scroll;
            }

            ul { padding-inline-start: 0px; }

            i { font-size: 75%; }

            ::-webkit-scrollbar {
                width: 8px;
                height: 4px;
                transition: all .1s linear;
                border-radius: 50%;
            }

            ::-webkit-scrollbar-track {
              background: transparent;
            }

            ::-webkit-scrollbar-track:hover {
                background: rgba(0,0,0,.2)
            }

            ::-webkit-scrollbar-thumb {
                background: #a5abb1;
            }
        </style>

        <script src="//code.jquery.com/jquery-3.5.0.min.js"></script>
        <script src="//gitcdn.xyz/repo/tmijs/cdn/master/latest/tmi.min.js"></script>
        <script>
        $( document ).ready(function() {
            console.log( "document loaded" );
        });

        $( window ).on( "load", function() {
            console.log( "window loaded" );
        });
        </script>
    </head>
    <body>
        <div class="container" style="max-width:100%;margin-right:unset;margin-left:unset;">

            <div class="row">
                <div class="col-12">
                    <br>
                </div>
            </div>

            <div class="row" id="head">
                <div class="col-3">
                    <input class="form-control" type="text" id="channel" name="channel" value="EchtkPvL"></input>
                </div>
                <div class="col-6">
                    <input class="form-control" type="text" id="msg" name="msg" value="Hi Chat HeyGuys bleedPurple"></input>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-success" id="chat">&lt; send &gt;</button>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <br>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="raubzug">Raubzug</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="gaehn">Gähn</button>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col-6 chat hoch" id="left"><ul id="main"></ul></div>
                <div class="col-6 chat hoch"><ul id="primar"></ul></div>
            </div>

        </div>
        <script>
            function formatEmotes(text, emotes) {
                var splitText = text.split('');
                for(var i in emotes) {
                    var e = emotes[i];
                    for(var j in e) {
                        var mote = e[j];
                        if(typeof mote == 'string') {
                            mote = mote.split('-');
                            mote = [parseInt(mote[0]), parseInt(mote[1])];
                            var length =  mote[1] - mote[0],
                                empty = Array.apply(null, new Array(length + 1)).map(function() { return '' });
                            splitText = splitText.slice(0, mote[0]).concat(empty).concat(splitText.slice(mote[1] + 1, splitText.length));
                            splitText.splice(mote[0], 1, '<img class="emoticon" src="//static-cdn.jtvnw.net/emoticons/v1/' + i + '/3.0">');
                        }
                    }
                }
                return splitText.join('');
            }

            function genLi(text, channel='', user='', highlight='', color='') {
                // https://getbootstrap.com/docs/4.0/utilities/colors/
                if(highlight != '') highlight = " class='" + highlight + "'";
                if(color == '' || color === null) color = "#856404";
                if(channel != '') channel = "<a onclick='javascript:$(\"#channel\").val(\"" + channel.slice(1)
                    + "\");'>[" + channel + "] </a>";
                if(user != '') user = "<b><a onclick='javascript:$(\"#msg\").val(\"@" + user
                    + "\");' style='color: " + color + "'>" + user + "</a></b>: ";

                return "<li" + highlight + "><i>"
                    + (new Date()).toLocaleString().slice(10)
                    + "</i> " + channel
                    + user
                    + text
                    + "</li>\n"
                ;
            }

            const options = {
                options: {
                    clientId: '<?php echo $options['client_id']; ?>',
                    debug: true,
                },
                connection: {
                    cluster: 'aws',
                    reconnect: true,
                    secure: true,
                },
                identity: {
                    username: '<?php echo $user['name']; ?>',
                    password: '<?php echo $token; ?>',
                },
                channels: [
<?php foreach($channels as $channel){ printf("                    '%s',\n", $channel); }?>
                ],
            };

            const client = new tmi.client(options);
            const botname = options.identity.username.toLowerCase();
            client.connect();

            client.on("message", (channel, user, message, self) => {
                //console.log(user);
                if (user["message-type"] == "whisper") return;

                while($('#main li').length >= 800) $('#main li').last().remove();
                while($('#primar li').length >= 800) $('#main li').last().remove();

                highlight = '';
                if(user["mod"]) highlight = "text-success";
                if(message.toLowerCase().includes(botname)) highlight = "bg-success text-white";
                if(user["badges-raw"] !== null && user["badges-raw"].includes("broadcaster")) highlight = "text-danger";

                if(
                    message.toLowerCase().includes(botname)
                    || message.toLowerCase().includes(channel.slice(1))
                    || user["mod"]
                    || (user["badges-raw"] !== null && user["badges-raw"].includes("broadcaster"))
                ) {
                    $("#primar").prepend(genLi(
                        formatEmotes(message, user['emotes']), channel, user['display-name'], highlight, user['color']
                    ));
                } else {
                    $("#main").prepend(genLi(
                        formatEmotes(message, user['emotes']), channel, user['display-name'], highlight, user['color']
                    ));
                }
            });

            client.on("notice", (channel, msgid, message) => {
                // https://github.com/tmijs/docs/blob/gh-pages/_posts/v1.4.2/2019-03-03-Events.md#notice
                console.log(channel);
                console.log(msgid);
                console.log(message);
                $("#main").prepend(genLi(message, channel, "", "text-muted"));
            });

            // ---------------------------
            // User dis-/connect
            // ---------------------------
            client.on('join', (channel, username, self) => {
                if (username.includes(botname) || username == channel.substr(1)) $("#main").prepend(genLi("<i>joined</i>", channel, username, "text-muted"));
            });

            client.on('part', (channel, username, self) => {
                if (username.includes(botname) || username == channel.substr(1)) $("#main").prepend(genLi("<i>left</i>", channel, username, "text-muted"));
            });

            client.on("connected", (address, port) => {
                $("#main").prepend(genLi("connected", address, port, "bg-success text-white"));
            });

            client.on("connecting", (address, port) => {
                $("#main").prepend(genLi("connecting", address, port, "bg-danger text-white"));
            });

            client.on("logon", () => {
                $("#main").prepend(genLi("logon", "", "", "bg-danger text-white"));
            });

            client.on("reconnect", () => {
                $("#main").prepend(genLi("reconnect", "", "", "bg-danger text-white"));
            });

            client.on("raw_message", (messageCloned, message) => {
                //console.log(message.raw);
            });

            // ---------------------------
            // Message deleted
            // ---------------------------
            client.on('messagedeleted', (channel, username, deletedMessage) => {
                $("#main").prepend(genLi(deletedMessage, channel, username, "bg-warning text-dark"));
            });

            $("#msg").on("keypress", function(event) {
                if (event.which == 13 && !event.shiftKey) {
                    event.preventDefault();
                    client.say($("#channel").val(), $("#msg").val());
                    $("#msg").val('');
                }
            });

            $("#chat").on("click", function(){
                client.say($("#channel").val(), $("#msg").val());
                $("#msg").val('');
            });

            $("#raubzug").on("click", function(){ client.say("JosyMovieS", "!raubzug 248"); });
            $("#gaehn").on("click", function(){ client.say("einfacheva", "!gähn"); });
        </script>
    </body>
</html>
