<?php
session_start();

/* Solo usuarios generales */
if (!isset($_SESSION['mail']) || $_SESSION['mail'] === 'admin@gmail.com') {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

$nombre = isset($_SESSION['nom_usr']) ? $_SESSION['nom_usr'] : '';

require_once __DIR__ . '/../../lib/gestor_pictogramas.php';

$pictogramas_activos = mostrar_pictogramas_activos();
if (!is_array($pictogramas_activos)) {
    $pictogramas_activos = [];
}
// Limitar a 8 pictogramas para la muestra
$pictogramas_muestra = array_slice($pictogramas_activos, 0, 8);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mindkind - Inicio usuario</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Estilos para los botones de navegación (en lugar de pestañas) */
        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 40px 0;
        }
        
        .nav-btn {
            padding: 15px 50px;
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
            transition: all 0.3s ease;
            color: #3b82f6;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: inline-block;
        }
        
        .nav-btn:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        
        /* Tus estilos existentes (se mantienen igual) */
        .app-download-section {
            background: #dceaf5af;
            padding: 60px 20px;
            text-align: center;
            margin-top: 40px;
            border-radius: 20px;
        }
        
        .download-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1f2937;
        }
        
        .download-subtitle {
            font-size: 18px;
            margin-bottom: 40px;
            color: #4b5563;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        .download-buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .download-btn {
            display: inline-block;
            background-color: #dceaf5af;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            padding: 15px;
        }
        
        .download-btn:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: #8cb2eeff;
        }
        
        .download-btn:active {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .btn-img {
            height: 60px;
            width: auto;
            display: block;
            border-radius: 10px;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12); }
            50% { box-shadow: 0 6px 25px rgba(59, 130, 246, 0.25); }
            100% { box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12); }
        }
        
        .download-btn {
            animation: pulse 1s infinite;
        }
        
        .download-btn:hover {
            animation: none;
        }
        
        /* Estilos para el chatbot */
        .chatbot-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
        
        .chatbot-button {
            width: 65px;
            height: 65px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
            border: 2px solid white;
        }
        
        .chatbot-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
        }
        
        .chat-icon {
            width: 32px;
            height: 32px;
            color: white;
        }
        
        .chat-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 12px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
        
        .chatbot-panel {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chatbot-panel.active {
            display: flex;
        }
        
        .chatbot-header {
            background: #3b82f6;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
        }
        
        .chatbot-header-info h3 {
            margin: 0;
            font-size: 16px;
        }
        
        .chatbot-header-info p {
            margin: 5px 0 0;
            font-size: 12px;
            opacity: 0.9;
        }
        
        .chatbot-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
        
        .chatbot-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f5f5f5;
        }
        
        .message {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .bot-message {
            justify-content: flex-start;
        }
        
        .user-message {
            justify-content: flex-end;
        }
        
        .message-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .user-message .message-avatar {
            background: #10b981;
        }
        
        .message-content {
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .user-message .message-content {
            background: #3b82f6;
            color: white;
        }
        
        .message-time {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
            display: block;
        }
        
        .chatbot-options {
            padding: 10px;
            background: white;
            border-top: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .option-btn {
            padding: 8px 12px;
            background: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
        }
        
        .option-btn:hover {
            background: #3b82f6;
            color: white;
        }
        
        .chatbot-input-area {
            padding: 10px;
            background: white;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        
        .chatbot-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        
        .chatbot-input:focus {
            border-color: #3b82f6;
        }
        
        .chatbot-send {
            padding: 8px 15px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .chatbot-send:hover {
            background: #2563eb;
        }
        
        .typing-indicator {
            display: flex;
            gap: 5px;
            padding: 10px;
        }
        
        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #999;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }
        
        /* Estilos para la muestra de pictogramas */
        .muestra-pictos {
            margin: 40px 0;
            padding: 20px;
        }
        
        .muestra-titulo {
            text-align: center;
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 30px;
        }
        
        .pictos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .picto-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .picto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .picto-img {
            height: 180px;
            overflow: hidden;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        
        .picto-img img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .picto-body {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .picto-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .picto-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        
        .picto-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Botón ver más */
        .ver-mas-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .ver-mas-btn {
            display: inline-block;
            padding: 12px 40px;
            background: white;
            border: 2px solid #3b82f6;
            color: #3b82f6;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .ver-mas-btn:hover {
            background: #3b82f6;
            color: white;
        }
        
        @media (max-width: 768px) {
            .nav-buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .nav-btn {
                width: 250px;
                text-align: center;
                padding: 12px 30px;
                font-size: 18px;
            }
            
            .download-buttons {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
            
            .download-btn {
                width: 250px;
            }
            
            .btn-img {
                height: 50px;
                margin: 0 auto;
            }
            
            .chatbot-panel {
                width: 300px;
                right: 0;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER ÚNICO -->
    <header class="general-header">
        <div class="gh-left">
            <a href="../login.php" class="header-btn header-btn-login">Inicio de sesión</a>
            <a href="../registro.php" class="header-btn header-btn-register">Registro</a>
        </div>

        <div class="gh-center">
            <img src="../assets/img/mindkind.jpeg" alt="Mindkind" class="gh-logo" style="border-radius: 50%;">
        </div>

        <div class="gh-right">
            <a href="#quienes" class="gh-link">Quiénes somos</a>
            <a href="#servicios" class="gh-link">Servicios</a>
            <a href="#contacto" class="gh-link">Contacto</a>
        </div>
    </header>

    <main class="general-main">

        <!-- QUIÉNES SOMOS -->
        <section id="quienes" class="section-hero">
            <div class="hero-left">
                <h1 class="hero-title">MindKind</h1>
                <h2 class="hero-subtitle">"Donde cada mente importa"</h2>
                <p class="hero-text">
                    Somos una empresa enfocada en el desarrollo de soluciones tecnológicas accesibles e inclusivas,
                    diseñadas para mejorar la calidad de vida de personas con necesidades comunicativas específicas.
                </p>
                <p class="hero-text">
                    Nos especializamos en la creación de aplicaciones móviles intuitivas, visuales y emocionalmente
                    respetuosas, que facilitan la comunicación y promueven la interacción social.
                </p>
            </div>

            <div class="hero-right">
                <img src="../assets/img/pictograma.jpg" alt="Mindkind" class="hero-video">
                <p class="hero-text">
                    Creemos en el poder de la tecnología como puente para la inclusión,
                    y trabajamos con empatía, innovación y respeto para lograrlo.
                </p>
            </div>
        </section>

        <!-- SERVICIOS -->
        <section id="servicios" class="section-services">
            <h2 class="services-title">Servicios</h2>
            <p class="services-desc">
                Nuestra aplicación ofrece el uso de pictogramas para facilitar la comunicación de nuestros usuarios
                con Trastorno del Espectro Autista, permitiendo expresar necesidades, emociones y pensamientos
                de forma sencilla y visual.
            </p>
        </section>

        <!-- BOTONES DE NAVEGACIÓN A CATÁLOGO Y COMPRAS (EN VEZ DE PESTAÑAS) -->
        <div class="nav-buttons">
            <a href="catalogo.php" class="nav-btn"> Catálogo</a>
            <a href="compras.php" class="nav-btn"> Compra de Contenido</a>
        </div>

        <!-- MUESTRA DE PICTOGRAMAS (LOS QUE YA ESTABAN) -->
        <section class="muestra-pictos">
            <h2 class="muestra-titulo">Muestra de Contenido</h2>

            <?php if (empty($pictogramas_muestra)) { ?>
                <p style="text-align: center; color: #6b7280;">No hay pictogramas disponibles por el momento.</p>
            <?php } else { ?>
                <div class="pictos-grid">
                    <?php foreach ($pictogramas_muestra as $p) { ?>
                        <div class="picto-card">
                            <div class="picto-img">
                                <img src="../assets/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                                     alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                            </div>
                            <div class="picto-body">
                                <h3 class="picto-name"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                                <p class="picto-desc"><?php echo htmlspecialchars($p['descripcion']); ?></p>
                                <span class="picto-badge">GRATUITO</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <!-- Botón para ver más en el catálogo -->
            <div class="ver-mas-container">
                <a href="catalogo.php" class="ver-mas-btn">Ver catálogo completo →</a>
            </div>
        </section>

        <!-- SECCIÓN DE DESCARGA DE APLICACIÓN -->
        <section class="app-download-section">
            <h2 class="download-title">Descarga nuestra App</h2>
            <p class="download-subtitle">Lleva MindKind contigo a todas partes</p>
            
            <div class="download-buttons">
                <!-- Enlace a App Store -->
                <a href="https://apps.apple.com/mx/app/mindkind/id123456789" target="_blank" rel="noopener noreferrer" class="download-btn">
                    <img src="../assets/img/app-store.png.jpg" alt="Descargar en App Store" class="btn-img">
                </a>
                
                <!-- Enlace a Google Play -->
                <a href="https://play.google.com/store/apps/details?id=com.mindkind.app" target="_blank" rel="noopener noreferrer" class="download-btn">
                    <img src="../assets/img/google-play.png.jpg" alt="Descargar en Google Play" class="btn-img">
                </a>
            </div>
        </section>

        <!-- CONTACTO -->
        <section id="contacto" class="section-contact">
            <h3 class="contact-title">Encuéntranos</h3>
            <p class="contact-text">
                Mindkind_MAD@gmail.com · 4426087664 · 4428239529 · 4417039029
            </p>
        </section>

    </main>

    <!-- AYUDA EN LÍNEA - CHATBOT -->
    <?php include 'ayuda_panel.php'; ?>

    <script>
        // ========================================
        // CHATBOT CON RESPUESTAS VARIADAS
        // ========================================
        let isChatbotOpen = false;
        let chatHistory = [];

        // Base de conocimientos con múltiples respuestas para cada tema
        const botResponses = {
            saludos: [
                "¡Hola!  ¿En qué puedo ayudarte hoy?",
                "¡Buenos días! ¿Cómo estás?",
                "¡Hola! Cuéntame, ¿qué necesitas saber?",
                "¡Hey! Estoy aquí para ayudarte.",
                "¡Bienvenido! ¿Qué te trae por aquí hoy?"
            ],
            
            app: [
                " **¿Cómo usar la aplicación?**\n\nEs muy fácil:\n1. Descarga la app (botones abajo)\n2. Regístrate con tu correo\n3. Explora los pictogramas\n4. Crea tus tableros\n\n¿Quieres que te explique alguna parte en específico?",
                
                " **Guía rápida de la app**\n\n• Los pictogramas están organizados por categorías\n• Puedes buscar por nombre\n• Crea tableros personalizados\n• Guarda tus favoritos\n\n¿Necesitas ayuda con algo más?",
                
                " **La app es muy intuitiva**\n\nSolo selecciona el pictograma que necesitas y se mostrará en pantalla. También puedes crear secuencias de varios pictogramas para formar frases.\n\n¿Te gustaría saber cómo hacer una secuencia?",
                
                " **Primeros pasos**\n\nDespués de registrarte, verás los pictogramas gratuitos. Si quieres más variedad, revisa la sección de Compras Premium.\n\n¿Ya exploraste el catálogo?"
            ],
            
            compras: [
                " **Compras premium**\n\nTenemos 3 paquetes disponibles:\n• Emociones Avanzadas - $4.99\n• Kit Escolar - $6.99\n• Comunicación Social - $5.99\n\n¿Quieres saber más de alguno?",
                
                " **¿Cómo comprar?**\n\n1. Ve a la pestaña 'Compras Premium'\n2. Selecciona el paquete que te interese\n3. Haz clic en 'Comprar'\n4. Elige tu método de pago\n\n¡Y listo! El contenido se activa automáticamente.",
                
                " **Medios de pago**\n\nAceptamos:\n• Tarjetas de crédito/débito\n• PayPal\n• Transferencia bancaria\n\nTodo es 100% seguro.",
                
                " **¿Qué incluye cada paquete?**\n\n• Emociones: 50 pictogramas + actividades\n• Escolar: 75 pictogramas + rutinas\n• Social: 60 pictogramas + situaciones\n\n¿Te gustaría verlos en detalle?"
            ],
            
            pictogramas: [
                " **Pictogramas disponibles**\n\nCategorías principales:\n•  Emociones (feliz, triste, enojado)\n•  Actividades (comer, dormir, jugar)\n•  Alimentos (manzana, leche, pan)\n•  Lugares (casa, escuela, parque)\n•  Social (hola, gracias, por favor)\n\n¿Qué categoría te interesa?",
                
                " **¿Buscas algo específico?**\n\nPuedes encontrar pictogramas para:\n• Rutinas diarias\n• Comunicación básica\n• Emociones y sentimientos\n• Necesidades médicas\n• Actividades escolares",
                
                " **Todos los pictogramas**\n\nTenemos más de 200 pictogramas gratuitos y 150 premium. En el catálogo puedes filtrar por categoría y verlos todos.",
                
                " **¿Cómo usar los pictogramas?**\n\nSolo haz clic en el que necesites y aparecerá en tu tablero. Puedes combinarlos para formar frases como 'Quiero comer manzana'."
            ],
            
            problemas: [
                " **Problemas técnicos**\n\nPrimero prueba esto:\n1. Cierra y abre la app\n2. Verifica tu conexión\n3. Actualiza a la última versión\n\n¿Sigues con problemas?",
                
                " **La app no carga**\n\nPuede ser por:\n• Conexión lenta\n• Muchos datos acumulados\n• Versión desactualizada\n\nIntenta borrar caché o reiniciar el dispositivo.",
                
                " **Error al comprar**\n\nSi tienes problemas al comprar:\n• Revisa tu saldo\n• Verifica tus datos\n• Contacta a soporte@mindkind.com",
                
                " **Soporte técnico**\n\nPara ayuda personalizada, escríbenos a:\nsoporte@mindkind.com\n\nTe responderemos en menos de 24 horas."
            ],
            
            contacto: [
                " **Información de contacto**\n\nEmail: soporte@mindkind.com\nTeléfono: 442-608-7664\nHorario: Lunes a Viernes 9am-6pm\n\n¡Estamos para ayudarte!",
                
                " **¿Necesitas hablar con alguien?**\n\nPuedes llamarnos al 442-608-7664 en horario laboral, o enviarnos un correo y te respondemos el mismo día.",
                
                " **Redes sociales**\n\nTambién nos encuentras en:\n• Facebook: @MindKindOficial\n• Instagram: @mindkind_app\n• Twitter: @mindkind"
            ],
            
            default: [
                "No estoy seguro de entender. ¿Podrías ser más específico? Puedes preguntarme sobre:\n• Cómo usar la app\n• Compras premium\n• Pictogramas\n• Problemas técnicos\n• Contacto",
                
                "Ups, no entendí bien. ¿Qué necesitas saber exactamente?",
                
                "Disculpa, no tengo esa información. ¿Te gustaría preguntar sobre otro tema?",
                
                "No conozco la respuesta a eso. ¿Qué tal si me preguntas sobre la app, compras o pictogramas?"
            ]
        };

        function toggleChatbot() {
            const panel = document.getElementById('chatbotPanel');
            const button = document.querySelector('.chatbot-button');
            
            isChatbotOpen = !isChatbotOpen;
            
            if (isChatbotOpen) {
                panel.classList.add('active');
                button.style.transform = 'scale(0.9)';
                document.querySelector('.chat-notification').style.display = 'none';
                
                // Mensaje de bienvenida aleatorio
                if (chatHistory.length === 0) {
                    setTimeout(() => {
                        const welcomeMsg = getRandomResponse('saludos');
                        addMessage(welcomeMsg, 'bot');
                    }, 500);
                }
            } else {
                panel.classList.remove('active');
                button.style.transform = 'scale(1)';
            }
        }

        function getRandomResponse(category) {
            const responses = botResponses[category] || botResponses.default;
            return responses[Math.floor(Math.random() * responses.length)];
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (message) {
                addMessage(message, 'user');
                chatHistory.push({role: 'user', content: message});
                input.value = '';
                showTypingIndicator();
                
                setTimeout(() => {
                    removeTypingIndicator();
                    const response = getBotResponse(message);
                    addMessage(response, 'bot');
                    chatHistory.push({role: 'bot', content: response});
                }, 1500);
            }
        }

        function sendQuickMessage(type) {
            let message = '';
            switch(type) {
                case 'app':
                    message = '¿Cómo usar la aplicación?';
                    break;
                case 'comprar':
                    message = '¿Cómo compro contenido premium?';
                    break;
                case 'pictogramas':
                    message = '¿Qué pictogramas tienen disponibles?';
                    break;
                case 'problemas':
                    message = 'Tengo un problema técnico';
                    break;
            }
            
            addMessage(message, 'user');
            chatHistory.push({role: 'user', content: message});
            showTypingIndicator();
            
            setTimeout(() => {
                removeTypingIndicator();
                const response = getBotResponse(message);
                addMessage(response, 'bot');
                chatHistory.push({role: 'bot', content: response});
            }, 1000);
        }

        function getBotResponse(message) {
            message = message.toLowerCase();
            
            // Detectar saludos
            if (message.match(/\b(hola|buenos días|buenas tardes|buenas noches|hey|que tal|saludos)\b/)) {
                return getRandomResponse('saludos');
            }
            
            // Detectar preguntas sobre la app
            if (message.match(/\b(app|aplicación|usar|funciona|como usar|manejar|descargar|instalar)\b/)) {
                return getRandomResponse('app');
            }
            
            // Detectar preguntas sobre compras
            if (message.match(/\b(comprar|premium|pago|precio|costo|adquirir|paquete|producto|pagar|tarjeta)\b/)) {
                return getRandomResponse('compras');
            }
            
            // Detectar preguntas sobre pictogramas
            if (message.match(/\b(pictograma|imagen|dibujo|categoría|visual|gratis|gratuito|emociones|actividades)\b/)) {
                return getRandomResponse('pictogramas');
            }
            
            // Detectar problemas técnicos
            if (message.match(/\b(problema|error|falla|bug|técnico|no funciona|ayuda|soporte|no carga|falló)\b/)) {
                return getRandomResponse('problemas');
            }
            
            // Detectar contacto
            if (message.match(/\b(contacto|correo|email|teléfono|whatsapp|llamar|mensaje|comunicar)\b/)) {
                return getRandomResponse('contacto');
            }
            
            // Si no detecta nada, respuesta aleatoria por defecto
            return getRandomResponse('default');
        }

        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}-message`;
            
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}`;
            
            // Formatear el texto para mostrar saltos de línea
            const formattedText = text.replace(/\n/g, '<br>');
            
            messageDiv.innerHTML = `
                <div class="message-avatar">${sender === 'bot' ? 'M' : 'U'}</div>
                <div class="message-content">
                    <p>${formattedText}</p>
                    <span class="message-time">${timeString}</span>
                </div>
            `;
            
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function showTypingIndicator() {
            const messagesDiv = document.getElementById('chatMessages');
            const indicator = document.createElement('div');
            indicator.className = 'message bot-message';
            indicator.id = 'typingIndicator';
            indicator.innerHTML = `
                <div class="message-avatar">M</div>
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            `;
            messagesDiv.appendChild(indicator);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) indicator.remove();
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        // Cerrar chatbot al hacer clic fuera
        document.addEventListener('click', function(event) {
            const chatbot = document.querySelector('.chatbot-container');
            const panel = document.getElementById('chatbotPanel');
            
            if (chatbot && !chatbot.contains(event.target) && panel.classList.contains('active')) {
                toggleChatbot();
            }
        });

        // Mostrar notificación después de 10 segundos
        setTimeout(() => {
            if (!isChatbotOpen) {
                const notification = document.querySelector('.chat-notification');
                if (notification) notification.style.display = 'flex';
            }
        }, 10000);

        // Mensaje de bienvenida automático después de abrir (opcional)
        window.addEventListener('load', function() {
            // Puedes activar esto si quieres que el chatbot salude al cargar la página
            // setTimeout(() => {
            //     if (!isChatbotOpen) {
            //         toggleChatbot();
            //     }
            // }, 3000);
        });
    </script>
</body>