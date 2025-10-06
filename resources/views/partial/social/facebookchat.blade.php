<style>
    .fb-livechat,
    .fb-widget {
        display: none
    }

.ctrlq.fb-button, .ctrlq.fb-close {

    position: fixed;
    left: 15px;
    cursor: pointer;

}
    .ctrlq.fb-button {
        background: #0084ff none repeat scroll 0 0;
        border: 0 none;
        border-radius: 50%;
        bottom: 10px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06), 0 2px 32px rgba(0, 0, 0, 0.16);
        height: 45px;
        outline: 0 none;
        text-align: center;
        transition: all 0.2s ease-in-out 0s;
        width: 45px;
        z-index: 999;
    }

    .ctrlq.fb-button:focus,
    .ctrlq.fb-button:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .09), 0 4px 40px rgba(0, 0, 0, .24)
    }

    .fb-widget {
        background: #fff;
        z-index: 1000;
        position: fixed;
        width: 360px;
        height: 435px;
        overflow: hidden;
        opacity: 0;
        bottom: 20px;
        right: 24px;
        border-radius: 6px;
        -o-border-radius: 6px;
        -webkit-border-radius: 6px;
        box-shadow: 0 5px 40px rgba(0, 0, 0, .16);
        -webkit-box-shadow: 0 5px 40px rgba(0, 0, 0, .16);
        -moz-box-shadow: 0 5px 40px rgba(0, 0, 0, .16);
        -o-box-shadow: 0 5px 40px rgba(0, 0, 0, .16)
    }

    .fb-credit {
        text-align: center;
        margin-top: 8px
    }

    .fb-credit a {
        transition: none;
        color: #bec2c9;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 12px;
        text-decoration: none;
        border: 0;
        font-weight: 400
    }

    .ctrlq.fb-overlay {
        z-index: 0;
        position: fixed;
        height: 100vh;
        width: 100vw;
        -webkit-transition: opacity .4s, visibility .4s;
        transition: opacity .4s, visibility .4s;
        top: 0;
        left: 0;
        background: rgba(0, 0, 0, .05);
        display: none
    }

    .ctrlq.fb-close {
        z-index: 4;
        padding: 0 6px;
        background: #365899;
        font-weight: 700;
        font-size: 11px;
        color: #fff;
        margin: 8px;
        border-radius: 3px;
        display:none;
    }

    .ctrlq.fb-close::after {
        content: "X";
        font-family: sans-serif
    }

    .bubble {
        width: 20px;
        height: 20px;
        background: #c00;
        color: #fff;
        position: absolute;
        z-index: 999999999;
        text-align: center;
        vertical-align: middle;
        top: -2px;
        left: -5px;
        border-radius: 50%;
    }

    .bubble-msg {
        width: 120px;
        left: -140px;
        top: 5px;
        position: relative;
        background: rgba(59, 89, 152, .8);
        color: #fff;
        padding: 5px 8px;
        border-radius: 8px;
        text-align: center;
        font-size: 13px;
    }

    .wh-messenger-svg-close.wh-svg-close {
        display: none;
    }

    .wh-messenger-svg-facebook.wh-svg-icon {
        fill: #ffffff;
        height: 42px;
        width: 40px;
    }

    .ctrlq.fb-button>div {
        height: 100%;
        transition: transform 0.4s ease 0s;
        width: 100%;
    }

    .ctrlq.fb-button:hover .wh- {}

    .button-active .wh-messenger-svg-facebook.wh-svg-icon {
        display: none;
    }

    .button-active .wh-messenger-svg-close.wh-svg-close {
        color: #ffffff;
        display: block;
    }

    .button-active .wh-messenger-svg-close.wh-svg-close path {
        fill: #ffffff;
    }

    .ctrlq.fb-button.button-active>div {
        transform: rotate(270deg);
    }

    .ctrlq.fb-button.button-active {
        display: block !important;
        height: 40px;
        width: 40px;
    }

</style>
<div class="fb-livechat">
    <div class="ctrlq fb-overlay"></div>
    <div class="fb-widget">
        <div class="ctrlq fb-close"></div>
        <div class="fb-page" data-href="{{ $config->facebook }}" data-tabs="messages" data-width="360" data-height="400" data-small-header="true" data-hide-cover="true" data-show-facepile="false"> </div>
        <!--<div class="fb-credit"> <a href="https://www.facebook.com/namnguyen1986" target="_blank">Powered by NamDang</a> </div>-->
        <div id="fb-root"></div>
    </div>
    <a href="{{ $config->facebook }}" title="Gửi tin nhắn cho chúng tôi qua Facebook" class="ctrlq fb-button">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-9 -10 41 44" class="wh-messenger-svg-close wh-svg-close">
                <path d=" M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill-rule="evenodd"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" class="wh-messenger-svg-facebook wh-svg-icon">
                <path d=" M16 6C9.925 6 5 10.56 5 16.185c0 3.205 1.6 6.065 4.1 7.932V28l3.745-2.056c1 .277 2.058.426 3.155.426 6.075 0 11-4.56 11-10.185C27 10.56 22.075 6 16 6zm1.093 13.716l-2.8-2.988-5.467 2.988 6.013-6.383 2.868 2.988 5.398-2.987-6.013 6.383z" fill-rule="evenodd"/>
            </svg>
        </div>
        <!--<div class="bubble-msg">Bạn cần hỗ trợ?</div>-->
    </a>
</div>

<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v13.0&appId=514608355727133&autoLogAppEvents=1" nonce="vhGXjf4P"></script>

<script>
    $(document).ready(function() {
        // $('.ctrlq.fb-button').hover(function() {
        //     $(this).toggleClass('button-active');
        // });
        function detectmob() {
            if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i)) {
                return true;
            } else {
                return false;
            }
        }
        var t = {
            delay: 125,
            overlay: $(".fb-overlay"),
            widget: $(".fb-widget"),
            button: $(".fb-button")
        };
        setTimeout(function() {
            $("div.fb-livechat").fadeIn()
        }, 8 * t.delay);
        if (!detectmob()) {
            $(".ctrlq").on("click", function(e) {
                $(this).toggleClass('button-active');
                e.preventDefault(), t.overlay.is(":visible") ? (t.overlay.fadeOut(t.delay), t.widget.stop().animate({
                    bottom: 0,
                    opacity: 0
                }, 2 * t.delay, function() {
                    $(this).hide("slow"), t.button.show()
                })) : t.button.fadeOut("medium", function() {
                    t.widget.stop().show().animate({
                        bottom: "60px",
                        opacity: 1
                    }, 2 * t.delay), t.overlay.fadeIn(t.delay)
                })
            })
            $(".ctrlq").on("hover", function(e) {
                e.preventDefault(), t.overlay.is(":visible") ? (t.overlay.fadeOut(t.delay), t.widget.stop().animate({
                    bottom: 0,
                    opacity: 0
                }, 2 * t.delay, function() {
                    $(this).hide("slow"), t.button.show()
                })) : t.button.fadeOut("medium", function() {
                    t.widget.stop().show().animate({
                        bottom: "30px",
                        opacity: 1
                    }, 2 * t.delay), t.overlay.fadeIn(t.delay)
                })
            })
        }
    });

</script>
