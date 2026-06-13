<?php
/*
 * ============================================================
 *  HOW TO USE:
 *  In package-details.php, just before the closing </body> tag, add:
 *      <?php include('includes/ai-chatbot.php'); ?>
 * ============================================================
 */
?>

<!-- ============================================================
     AI CHATBOT WIDGET  (add just before </body> in package-details.php)
     Does NOT touch any existing code.
     ============================================================ -->
<style>
#tms-chat-bubble {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
}
#tms-chat-toggle {
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    border: none; cursor: pointer; box-shadow: 0 4px 18px rgba(14,165,233,.45);
    display: flex; align-items: center; justify-content: center;
    transition: transform .2s;
}
#tms-chat-toggle:hover { transform: scale(1.08); }
#tms-chat-toggle i { color: #fff; font-size: 22px; }

#tms-chat-box {
    display: none;
    position: fixed; bottom: 96px; right: 28px; z-index: 9999;
    width: 320px; background: #fff;
    border-radius: 16px; box-shadow: 0 8px 40px rgba(15,23,42,.18);
    overflow: hidden; font-family: 'DM Sans', sans-serif;
}
#tms-chat-header {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    padding: 14px 18px; color: #fff;
    display: flex; align-items: center; gap: 10px;
}
#tms-chat-header i { font-size: 18px; }
#tms-chat-header span { font-weight: 600; font-size: 15px; }
#tms-chat-header small { font-size: 11px; opacity: .8; display:block; }

#tms-chat-messages {
    height: 240px; overflow-y: auto; padding: 14px;
    display: flex; flex-direction: column; gap: 10px;
    background: #f8fafc;
}
.tms-msg {
    max-width: 82%; padding: 9px 13px; border-radius: 12px;
    font-size: 13px; line-height: 1.55;
}
.tms-msg.bot  { background: #e0f2fe; color: #0c4a6e; align-self: flex-start; border-bottom-left-radius: 4px; }
.tms-msg.user { background: #0ea5e9; color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
.tms-msg.typing { background: #e0f2fe; color: #94a3b8; font-style: italic; align-self: flex-start; }

#tms-chat-input-row {
    display: flex; gap: 8px; padding: 12px 14px;
    border-top: 1px solid #e2e8f0; background: #fff;
}
#tms-chat-input {
    flex: 1; border: 1.5px solid #e2e8f0; border-radius: 999px;
    padding: 9px 14px; font-size: 13px; font-family: 'DM Sans', sans-serif;
    outline: none; transition: border-color .2s;
}
#tms-chat-input:focus { border-color: #0ea5e9; }
#tms-chat-send {
    width: 36px; height: 36px; border-radius: 50%;
    background: #0ea5e9; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
#tms-chat-send:hover { background: #0369a1; }
#tms-chat-send i { color: #fff; font-size: 14px; }
</style>

<!-- Floating button -->
<div id="tms-chat-bubble">
    <button id="tms-chat-toggle" title="Ask our AI Assistant">
        <i class="fa fa-comments"></i>
    </button>
</div>

<!-- Chat box -->
<div id="tms-chat-box">
    <div id="tms-chat-header">
        <i class="fa fa-robot"></i>
        <div>
            <span>TravelMate AI</span>
            <small>Ask me anything about this trip!</small>
        </div>
    </div>
    <div id="tms-chat-messages">
        <div class="tms-msg bot">Hi! I'm your travel assistant. Ask me about packages, destinations, or anything travel-related!</div>
    </div>
    <div id="tms-chat-input-row">
        <input type="text" id="tms-chat-input" placeholder="Type your question…" maxlength="300">
        <button id="tms-chat-send"><i class="fa fa-paper-plane"></i></button>
    </div>
</div>

<script>
(function(){
    var toggle  = document.getElementById('tms-chat-toggle');
    var box     = document.getElementById('tms-chat-box');
    var input   = document.getElementById('tms-chat-input');
    var send    = document.getElementById('tms-chat-send');
    var msgs    = document.getElementById('tms-chat-messages');
    var open    = false;

    toggle.addEventListener('click', function(){
        open = !open;
        box.style.display = open ? 'block' : 'none';
        if(open) input.focus();
    });

    function addMsg(text, type){
        var d = document.createElement('div');
        d.className = 'tms-msg ' + type;
        d.textContent = text;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
        return d;
    }

    function sendMessage(){
        var msg = input.value.trim();
        if(!msg) return;
        addMsg(msg, 'user');
        input.value = '';
        var typing = addMsg('Typing…', 'typing');

        fetch('http://localhost:5000/chat', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({message: msg})
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            msgs.removeChild(typing);
            addMsg(data.reply || 'Sorry, I could not understand that.', 'bot');
        })
        .catch(function(){
            msgs.removeChild(typing);
            addMsg('Connection error. Make sure the AI service is running.', 'bot');
        });
    }

    send.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function(e){
        if(e.key === 'Enter') sendMessage();
    });
})();
</script>