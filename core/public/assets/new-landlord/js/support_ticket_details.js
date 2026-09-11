
function sendMessage() {
    const input = document.getElementById('messageInput')
    const text = input.value.trim()
    if (!text) return alert('Please write something before submitting.')


    const time = new Date()
    let now = time.toLocaleString([], { hour: '2-digit', minute: '2-digit' })


    const messages = document.getElementById('chatMessages')

    const msgDiv = document.createElement('div')
    msgDiv.className = ('flex flex-col items-end gap-1')

    msgDiv.innerHTML = `
            <div class="bg-teal-50 rounded-2xl rounded-br-sm px-4 py-3 max-w-auto text-right">
         <p class="text-sm text-gray-700">${escapeHtml(text)}</p>
       </div>
    <p class="text-xs text-gray-400 mt-0.5 mr-1">${now}</p>
    `
    messages.appendChild(msgDiv)
    messages.scrollTop = messages.scrollHeight
    input.value = ''

    setTimeout(() => {
        const replies = [
            "Thanks for reaching out! I'll look into this.",
            "Got it, let me check the details.",
            "Sure! Can you share more information?",
            "I understand. I'll get back to you shortly.",
            "That makes sense. Let me review and respond.",
    
        ]
        const replay = replies[Math.floor(Math.random() * replies.length)]
        const replayTime = new Date().toLocaleString([], { hour: '2-digit', minute: '2-digit' })

        const replayDiv = document.createElement('div')
        replayDiv.className = ('flex items-end gap-3')
        replayDiv.innerHTML = `

                    <img src="https://i.pravatar.cc/36?img=12"
                class="w-9 h-9 rounded-full object-cover flex-shrink-0 mb-5" alt="" />
            <div>
                <div class="bg-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 max-w-sm">
                    <p class="text-sm text-gray-700">${replay}</p>
                </div>
                <p class="text-xs text-gray-400 mt-1.5 ml-1">${replayTime}</p>
            </div>
        
        `
        messages.appendChild(replayDiv)
        messages.scrollTop = messages.scrollHeight
    },1200)
















    function escapeHtml(text) {
        return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

}