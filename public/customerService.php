<?php
session_start();
include '../components/connection.php';

$db = new Database();
$con = $db->getConnection();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="../css/animation.css">
    <link rel="stylesheet" href="../css/reviews.css">
</head>
<body>
    
    <?php include '../components/header.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center mb-0">
                            <i class="fas fa-robot me-2"></i>Hotel Assistant
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chat-container" id="chatContainer">
                            <!-- Welcome message -->
                            <div class="message system-message">
                                <div class="message-content">
                                    <div class="message-icon">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="message-text">
                                        Hello! I'm your hotel's AI assistant. I can help you with:
                                        <ul class="mt-2">
                                            <li>Room bookings and availability</li>
                                            <li>Hotel amenities and services</li>
                                            <li>Check-in/check-out information</li>
                                            <li>General inquiries and support</li>
                                        </ul>
                                        How may I assist you today?
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form id="customerServiceForm" class="mt-4">
                            <div class="input-group">
                                <input type="text" 
                                       id="userInput" 
                                       class="form-control" 
                                       placeholder="Type your message here..."
                                       required>
                                <button type="button" id="sendBtn" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="quick-actions mt-3">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-question">
                                        <i class="fas fa-calendar-check"></i> Check Availability
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-question">
                                        <i class="fas fa-concierge-bell"></i> Services
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-question">
                                        <i class="fas fa-credit-card"></i> Payment Methods
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-question">
                                        <i class="fas fa-clock"></i> Check-in Times
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chat-container {
            height: 400px;
            overflow-y: auto;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .message {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            max-width: 80%;
        }

        .message-content {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .message-icon {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .system-message {
            background: #e3f2fd;
            margin-right: auto;
            border-top-left-radius: 0.2rem;
        }

        .system-message .message-icon {
            background: #1976d2;
            color: white;
        }

        .user-message {
            background: #dcf8c6;
            margin-left: auto;
            border-top-right-radius: 0.2rem;
        }

        .user-message .message-icon {
            background: #4caf50;
            color: white;
        }

        .message-text {
            flex-grow: 1;
        }

        .message-text ul {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }

        .quick-actions {
            border-top: 1px solid #dee2e6;
            padding-top: 1rem;
        }

        .quick-question {
            transition: all 0.3s ease;
        }

        .quick-question:hover {
            transform: translateY(-2px);
        }

        .loading-dots:after {
            content: '...';
            animation: dots 1.5s steps(5, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80% { content: ''; }
        }
    </style>

    <!-- Footer Section -->
    <?php include '../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <!-- custom js scripts -->
    <script src="../js/imageSwiper.js"></script>
    <script src="../js/bannerSwipper.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/animation.js"></script>
    <script src="../js/Menus.js"></script>
    <script src="../js/fetchClientNotifications.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chatContainer');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const quickQuestions = document.querySelectorAll('.quick-question');
        
        // Store chat history
        let chatHistory = [{
            role: 'system',
            content: 'You are a helpful hotel customer service assistant.'
        }];
        
        // Request queue for rate limiting
        let requestQueue = [];
        let isProcessing = false;

        // Function to add a message to the chat
        function addMessage(message, isUser = false, isFallback = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user-message' : 'system-message'}`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            
            // Add icon
            const iconDiv = document.createElement('div');
            iconDiv.className = 'message-icon';
            iconDiv.innerHTML = `<i class="fas ${isUser ? 'fa-user' : 'fa-robot'}"></i>`;
            
            // Add fallback indicator if needed
            if (isFallback) {
                iconDiv.style.background = '#ff9800';
                iconDiv.setAttribute('title', 'Fallback response due to high demand');
            }
            
            contentDiv.appendChild(iconDiv);
            
            // Add message text
            const textDiv = document.createElement('div');
            textDiv.className = 'message-text';
            textDiv.innerHTML = message.replace(/\n/g, '<br>');
            contentDiv.appendChild(textDiv);
            
            messageDiv.appendChild(contentDiv);
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            return messageDiv;
        }

        // Function to show loading indicator
        function showLoading() {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message system-message loading-message';
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            
            const iconDiv = document.createElement('div');
            iconDiv.className = 'message-icon';
            iconDiv.innerHTML = '<i class="fas fa-robot"></i>';
            contentDiv.appendChild(iconDiv);
            
            const textDiv = document.createElement('div');
            textDiv.className = 'message-text';
            textDiv.innerHTML = 'Thinking<span class="loading-dots"></span>';
            contentDiv.appendChild(textDiv);
            
            loadingDiv.appendChild(contentDiv);
            chatContainer.appendChild(loadingDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            return loadingDiv;
        }

        // Function to show error message with retry option
        function showErrorWithRetry(originalMessage, error) {
            const errorDiv = addMessage(
                `I apologize, but I'm experiencing some technical difficulties. ${error} <br><br>` +
                `<button class="btn btn-sm btn-outline-primary retry-btn" data-message="${encodeURIComponent(originalMessage)}">` +
                `<i class="fas fa-redo"></i> Try Again</button>`, 
                false
            );
            
            // Add retry functionality
            const retryBtn = errorDiv.querySelector('.retry-btn');
            if (retryBtn) {
                retryBtn.addEventListener('click', function() {
                    const message = decodeURIComponent(this.dataset.message);
                    errorDiv.remove();
                    handleChat(message);
                });
            }
        }

        // Function to process request queue
        async function processQueue() {
            if (isProcessing || requestQueue.length === 0) return;
            
            isProcessing = true;
            const { userMessage, loadingDiv, resolve, reject } = requestQueue.shift();
            
            try {
                const res = await fetch("../api/chat_bot.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ 
                        message: userMessage,
                        history: chatHistory
                    })
                });

                const data = await res.json();
                
                // Remove loading indicator
                if (loadingDiv && loadingDiv.parentNode) {
                    loadingDiv.remove();
                }
                
                if (data.success) {
                    // Add AI response to chat history
                    chatHistory.push({
                        role: 'assistant',
                        content: data.message
                    });
                    
                    // Show AI response with fallback indicator if needed
                    addMessage(data.message, false, data.fallback || false);
                    
                    // Show note if it's a fallback response
                    if (data.fallback && data.note) {
                        setTimeout(() => {
                            addMessage(`<small><em>${data.note}</em></small>`, false);
                        }, 500);
                    }
                    
                    resolve(data);
                } else {
                    throw new Error(data.error || 'Failed to get response');
                }
                
            } catch (error) {
                if (loadingDiv && loadingDiv.parentNode) {
                    loadingDiv.remove();
                }
                
                let errorMessage = 'Please try again in a moment.';
                
                if (error.message.includes('429') || error.message.includes('rate limit')) {
                    errorMessage = 'I\'m currently experiencing high demand. Please wait a moment before trying again.';
                } else if (error.message.includes('network') || error.message.includes('fetch')) {
                    errorMessage = 'There seems to be a connection issue. Please check your internet connection.';
                }
                
                showErrorWithRetry(userMessage, errorMessage);
                reject(error);
            } finally {
                isProcessing = false;
                // Process next item in queue after a short delay
                setTimeout(processQueue, 1000);
            }
        }

        // Function to handle the chat interaction
        async function handleChat(userMessage) {
            // Disable send button temporarily
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-hourglass-half"></i> Sending...';
            
            // Show user message
            addMessage(userMessage, true);
            
            // Add to chat history
            chatHistory.push({
                role: 'user',
                content: userMessage
            });
            
            // Show loading
            const loadingDiv = showLoading();

            // Add to queue
            return new Promise((resolve, reject) => {
                requestQueue.push({
                    userMessage,
                    loadingDiv,
                    resolve,
                    reject
                });
                
                processQueue();
            }).finally(() => {
                // Re-enable send button
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
                
                // Clear input
                userInput.value = '';
            });
        }

        // Debounce function to prevent spam clicking
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Debounced chat handler
        const debouncedHandleChat = debounce(handleChat, 500);

        // Handle send button click
        sendBtn.addEventListener('click', () => {
            const message = userInput.value.trim();
            if (message && !sendBtn.disabled) {
                debouncedHandleChat(message);
            }
        });

        // Handle quick questions
        quickQuestions.forEach(button => {
            button.addEventListener('click', function() {
                if (sendBtn.disabled) return;
                
                const question = this.textContent.trim();
                debouncedHandleChat(question);
            });
        });

        // Handle Enter key
        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const message = userInput.value.trim();
                if (message && !sendBtn.disabled) {
                    debouncedHandleChat(message);
                }
            }
        });

        // Add some additional CSS for error handling
        const style = document.createElement('style');
        style.textContent = `
            .retry-btn {
                margin-top: 0.5rem;
                font-size: 0.85rem;
            }
            
            .message-icon[title] {
                position: relative;
                cursor: help;
            }
            
            .loading-message {
                opacity: 0.8;
            }
            
            .btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
        `;
        document.head.appendChild(style);
    });
    </script>
</body>
</html>