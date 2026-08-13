<!-- CHATBOT DE AYUDA EN LÍNEA -->
<div class="chatbot-container">
    <div class="chatbot-button" onclick="toggleChatbot()">
        <svg class="chat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="chat-notification">1</span>
    </div>

    <div class="chatbot-panel" id="chatbotPanel">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <h3>Asistente MindKind</h3>
                <p>¡Hola! ¿En qué puedo ayudarte?</p>
            </div>
            <button class="chatbot-close" onclick="toggleChatbot()">×</button>
        </div>

        <div class="chatbot-messages" id="chatMessages">
            <div class="message bot-message">
                <div class="message-avatar">M</div>
                <div class="message-content">
                    <p>¡Bienvenido al centro de ayuda! Soy tu asistente virtual. ¿Qué necesitas saber hoy?</p>
                    <span class="message-time">Justo ahora</span>
                </div>
            </div>
        </div>

        <div class="chatbot-options" id="chatOptions">
            <button class="option-btn" onclick="sendQuickMessage('app')"> Usar la app</button>
            <button class="option-btn" onclick="sendQuickMessage('comprar')"> Comprar contenido</button>
            <button class="option-btn" onclick="sendQuickMessage('pictogramas')"> Pictogramas</button>
            <button class="option-btn" onclick="sendQuickMessage('problemas')"> Problemas técnicos</button>
        </div>

        <div class="chatbot-input-area">
            <input type="text" class="chatbot-input" id="chatInput" placeholder="Escribe tu mensaje..." onkeypress="handleKeyPress(event)">
            <button class="chatbot-send" onclick="sendMessage()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>