<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Bạn';
$BASE_PATH = '/DoAn_ThucTapChuyenNganh';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tin nhắn - Chợ Điện Tử</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      overflow: hidden;
    }
    
    /* Top Navigation */
    .msg-topbar {
      height: 60px;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      display: flex;
      align-items: center;
      padding: 0 20px;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .msg-topbar .back-btn {
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 500;
      padding: 8px 16px;
      border-radius: 8px;
      transition: background 0.2s;
    }
    
    .msg-topbar .back-btn:hover {
      background: rgba(255,255,255,0.1);
      color: #fff;
    }
    
    .msg-topbar .title {
      flex: 1;
      text-align: center;
      color: #fff;
      font-size: 18px;
      font-weight: 600;
    }
    
    .msg-topbar .user-info {
      color: rgba(255,255,255,0.8);
      font-size: 14px;
    }
    
    /* Main Container */
    .msg-container {
      display: flex;
      height: calc(100vh - 60px);
      margin-top: 60px;
    }
    
    /* Sidebar */
    .msg-sidebar {
      width: 380px;
      background: #fff;
      border-right: 1px solid #e4e6eb;
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
    }
    
    .sidebar-header {
      padding: 16px 20px;
      border-bottom: 1px solid #e4e6eb;
      background: #fff;
    }
    
    .sidebar-header h4 {
      margin: 0;
      font-size: 24px;
      font-weight: 700;
      color: #050505;
    }
    
    .sidebar-search {
      padding: 12px 16px;
      border-bottom: 1px solid #e4e6eb;
    }
    
    .sidebar-search input {
      width: 100%;
      padding: 10px 16px;
      border: none;
      border-radius: 20px;
      background: #f0f2f5;
      font-size: 14px;
      outline: none;
    }
    
    .sidebar-search input:focus {
      background: #e4e6eb;
    }
    
    .conversations-list {
      flex: 1;
      overflow-y: auto;
    }
    
    .conv-item {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      cursor: pointer;
      transition: background 0.15s;
      border-left: 3px solid transparent;
    }
    
    .conv-item:hover {
      background: #f5f5f5;
    }
    
    .conv-item.active {
      background: #e7f3ff;
      border-left-color: #0084ff;
    }
    
    .conv-item.unread {
      background: #fff8e1;
    }
    
    .conv-item.unread .conv-name,
    .conv-item.unread .conv-preview {
      font-weight: 600;
    }
    
    .conv-avatar-placeholder {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 20px;
      font-weight: 600;
      margin-right: 12px;
      flex-shrink: 0;
    }
    
    .conv-content {
      flex: 1;
      min-width: 0;
    }
    
    .conv-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 4px;
    }
    
    .conv-name {
      font-size: 15px;
      font-weight: 500;
      color: #050505;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .conv-time {
      font-size: 12px;
      color: #65676b;
      flex-shrink: 0;
      margin-left: 8px;
    }
    
    .conv-bottom {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .conv-preview {
      font-size: 13px;
      color: #65676b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
    }
    
    .conv-product {
      font-size: 11px;
      color: #0084ff;
      background: #e7f3ff;
      padding: 2px 8px;
      border-radius: 10px;
      white-space: nowrap;
      max-width: 120px;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-top: 4px;
    }
    
    .conv-badge {
      background: #0084ff;
      color: #fff;
      font-size: 11px;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: 10px;
      flex-shrink: 0;
    }
    
    /* Chat Area */
    .msg-chat {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #fff;
    }
    
    .chat-header {
      padding: 12px 20px;
      border-bottom: 1px solid #e4e6eb;
      display: flex;
      align-items: center;
      background: #fff;
      min-height: 72px;
    }
    
    .chat-header-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 18px;
      font-weight: 600;
      margin-right: 12px;
    }
    
    .chat-header-info h5 {
      margin: 0 0 2px 0;
      font-size: 16px;
      font-weight: 600;
      color: #050505;
    }
    
    .chat-header-info small {
      color: #65676b;
      font-size: 13px;
    }
    
    .chat-header-info a {
      color: #0084ff;
      text-decoration: none;
    }
    
    .chat-header-info a:hover {
      text-decoration: underline;
    }
    
    .chat-header-actions {
      margin-left: auto;
      display: flex;
      gap: 8px;
    }
    
    .btn-icon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: none;
      background: #f0f2f5;
      color: #0084ff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s;
      text-decoration: none;
    }
    
    .btn-icon:hover {
      background: #e4e6eb;
      color: #0084ff;
    }
    
    /* Messages Area */
    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background: #f0f2f5;
    }
    
    .msg-date-divider {
      text-align: center;
      margin: 20px 0;
    }
    
    .msg-date-divider span {
      background: #e4e6eb;
      padding: 4px 12px;
      color: #65676b;
      font-size: 12px;
      border-radius: 12px;
    }
    
    .msg-row {
      display: flex;
      margin-bottom: 8px;
      align-items: flex-end;
    }
    
    .msg-row.mine {
      justify-content: flex-end;
    }
    
    .msg-row > div {
      max-width: 65%;
    }
    
    .msg-bubble {
      padding: 10px 14px;
      border-radius: 18px;
      font-size: 15px;
      line-height: 1.4;
      word-wrap: break-word;
      display: inline-block;
    }
    
    .msg-row:not(.mine) .msg-bubble {
      background: #fff;
      color: #050505;
      border-bottom-left-radius: 4px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .msg-row.mine .msg-bubble {
      background: linear-gradient(135deg, #0084ff 0%, #0066cc 100%);
      color: #fff;
      border-bottom-right-radius: 4px;
    }
    
    .msg-row:not(.mine) > div {
      text-align: left;
    }
    
    .msg-row.mine > div {
      text-align: right;
    }
    
    .msg-time {
      font-size: 11px;
      color: #65676b;
      margin-top: 4px;
      padding: 0 4px;
    }
    
    /* Chat Input */
    .chat-input-area {
      padding: 12px 20px;
      border-top: 1px solid #e4e6eb;
      background: #fff;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .chat-input-wrapper {
      flex: 1;
    }
    
    .chat-input {
      width: 100%;
      padding: 12px 20px;
      border: none;
      border-radius: 24px;
      background: #f0f2f5;
      font-size: 15px;
      outline: none;
    }
    
    .chat-input:focus {
      background: #e4e6eb;
    }
    
    .chat-send-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0084ff 0%, #0066cc 100%);
      color: #fff;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      font-size: 18px;
    }
    
    .chat-send-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0,132,255,0.4);
    }
    
    .chat-send-btn:disabled {
      background: #bcc0c4;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
    
    /* Empty States */
    .empty-state {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #65676b;
      padding: 40px;
    }
    
    .empty-state svg {
      margin-bottom: 20px;
      opacity: 0.5;
    }
    
    .empty-state h5 {
      margin: 0 0 8px 0;
      color: #050505;
      font-weight: 600;
    }
    
    .empty-state p {
      margin: 0;
      font-size: 14px;
    }
    
    /* Loading */
    .loading-spinner {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      color: #65676b;
    }
    
    .loading-spinner i {
      font-size: 24px;
      margin-bottom: 12px;
      color: #0084ff;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .msg-sidebar {
        width: 100%;
        position: absolute;
        left: 0;
        top: 60px;
        bottom: 0;
        z-index: 50;
        transition: transform 0.3s;
      }
      
      .msg-sidebar.hidden {
        transform: translateX(-100%);
      }
      
      .msg-chat {
        position: absolute;
        left: 0;
        right: 0;
        top: 60px;
        bottom: 0;
      }
      
      .mobile-back-btn {
        display: flex !important;
      }
      
      .msg-bubble {
        max-width: 85%;
      }
    }
    
    @media (min-width: 769px) {
      .mobile-back-btn {
        display: none !important;
      }
    }
    
    /* Scrollbar */
    .conversations-list::-webkit-scrollbar,
    .chat-messages::-webkit-scrollbar {
      width: 6px;
    }
    
    .conversations-list::-webkit-scrollbar-track,
    .chat-messages::-webkit-scrollbar-track {
      background: transparent;
    }
    
    .conversations-list::-webkit-scrollbar-thumb,
    .chat-messages::-webkit-scrollbar-thumb {
      background: #bcc0c4;
      border-radius: 3px;
    }
  </style>
</head>
<body>

<!-- Top Bar -->
<div class="msg-topbar">
  <a href="<?php echo $BASE_PATH; ?>/index.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Trang chủ</span>
  </a>
  <div class="title">
    <i class="fa-solid fa-comments me-2"></i>Tin nhắn
  </div>
  <div class="user-info">
    <i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($user_name); ?>
  </div>
</div>

<!-- Main Container -->
<div class="msg-container">
  
  <!-- Sidebar -->
  <div class="msg-sidebar" id="msgSidebar">
    <div class="sidebar-header">
      <h4>Chat</h4>
    </div>
    <div class="sidebar-search">
      <input type="text" id="searchConv" placeholder="Tìm kiếm cuộc hội thoại...">
    </div>
    <div class="conversations-list" id="conversationsList">
      <div class="loading-spinner">
        <i class="fa-solid fa-spinner fa-spin"></i>
        <span>Đang tải...</span>
      </div>
    </div>
  </div>
  
  <!-- Chat Area -->
  <div class="msg-chat" id="msgChat">
    <div class="empty-state" id="emptyState">
      <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
      <h5>Chọn một cuộc hội thoại</h5>
      <p>Chọn từ danh sách bên trái để bắt đầu nhắn tin</p>
    </div>
    
    <div id="chatContent" style="display:none; flex-direction:column; height:100%;">
      <div class="chat-header" id="chatHeader"></div>
      <div class="chat-messages" id="chatMessages"></div>
      <div class="chat-input-area">
        <button class="mobile-back-btn btn-icon" onclick="showSidebar()" style="display:none;">
          <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div class="chat-input-wrapper">
          <input type="text" class="chat-input" id="chatInput" placeholder="Nhập tin nhắn...">
        </div>
        <button class="chat-send-btn" id="sendBtn">
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </div>
    </div>
  </div>
  
</div>

<script>
const currentUserId = <?php echo $user_id; ?>;
const currentUserName = <?php echo json_encode($user_name); ?>;
const basePath = <?php echo json_encode($BASE_PATH); ?>;

let activeConversation = null;
let pollingInterval = null;
let lastMessageId = 0;
let allConversations = [];

function loadConversations() {
  fetch(basePath + '/api/get_conversations.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data.status !== 'success') return;
      allConversations = data.conversations;
      renderConversations(allConversations);
    })
    .catch(err => {
      document.getElementById('conversationsList').innerHTML = 
        '<div class="empty-state"><p>Không thể tải dữ liệu</p></div>';
    });
}

function renderConversations(conversations) {
  const list = document.getElementById('conversationsList');
  
  if (conversations.length === 0) {
    list.innerHTML = `
      <div class="empty-state">
        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <p>Chưa có tin nhắn nào</p>
      </div>`;
    return;
  }
  
  let html = '';
  conversations.forEach(conv => {
    const isActive = activeConversation && 
      activeConversation.product_id === conv.product_id && 
      activeConversation.other_user_id === conv.other_user_id;
    
    const initial = (conv.other_user_name || '?').charAt(0).toUpperCase();
    const preview = conv.last_message 
      ? (conv.is_last_mine ? 'Bạn: ' : '') + truncate(conv.last_message, 25)
      : 'Chưa có tin nhắn';
    
    html += `
      <div class="conv-item ${isActive ? 'active' : ''} ${conv.unread_count > 0 ? 'unread' : ''}"
           data-product-id="${conv.product_id}"
           data-other-user-id="${conv.other_user_id}"
           data-other-user-name="${escapeAttr(conv.other_user_name)}"
           data-product-title="${escapeAttr(conv.product_title)}"
           data-product-image="${escapeAttr(conv.product_image)}"
           onclick="selectConversation(this)">
        <div class="conv-avatar-placeholder">${initial}</div>
        <div class="conv-content">
          <div class="conv-top">
            <span class="conv-name">${escapeHtml(conv.other_user_name)}</span>
            <span class="conv-time">${timeAgo(conv.last_message_time)}</span>
          </div>
          <div class="conv-bottom">
            <span class="conv-preview">${escapeHtml(preview)}</span>
            ${conv.unread_count > 0 ? `<span class="conv-badge">${conv.unread_count}</span>` : ''}
          </div>
          <div class="conv-product">
            <i class="fa-solid fa-box fa-xs me-1"></i>${escapeHtml(truncate(conv.product_title, 20))}
          </div>
        </div>
      </div>`;
  });
  
  list.innerHTML = html;
}

function selectConversation(item) {
  document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
  item.classList.add('active');
  item.classList.remove('unread');
  
  const badge = item.querySelector('.conv-badge');
  if (badge) badge.remove();
  
  activeConversation = {
    product_id: parseInt(item.dataset.productId),
    other_user_id: parseInt(item.dataset.otherUserId),
    other_user_name: item.dataset.otherUserName,
    product_title: item.dataset.productTitle,
    product_image: item.dataset.productImage
  };
  
  lastMessageId = 0;
  
  document.getElementById('emptyState').style.display = 'none';
  document.getElementById('chatContent').style.display = 'flex';
  
  if (window.innerWidth <= 768) {
    document.getElementById('msgSidebar').classList.add('hidden');
  }
  
  const initial = (activeConversation.other_user_name || '?').charAt(0).toUpperCase();
  document.getElementById('chatHeader').innerHTML = `
    <button class="mobile-back-btn btn-icon" onclick="showSidebar()" style="margin-right:12px;">
      <i class="fa-solid fa-arrow-left"></i>
    </button>
    <div class="chat-header-avatar">${initial}</div>
    <div class="chat-header-info">
      <h5>${escapeHtml(activeConversation.other_user_name)}</h5>
      <small>
        <i class="fa-solid fa-box me-1"></i>
        <a href="${basePath}/product.php?id=${activeConversation.product_id}" target="_blank">
          ${escapeHtml(activeConversation.product_title)}
        </a>
      </small>
    </div>
    <div class="chat-header-actions">
      <a href="${basePath}/product.php?id=${activeConversation.product_id}" target="_blank" class="btn-icon" title="Xem sản phẩm">
        <i class="fa-solid fa-external-link"></i>
      </a>
    </div>
  `;
  
  loadMessages();
  
  if (pollingInterval) clearInterval(pollingInterval);
  pollingInterval = setInterval(pollNewMessages, 3000);
}

function showSidebar() {
  document.getElementById('msgSidebar').classList.remove('hidden');
}

function loadMessages() {
  if (!activeConversation) return;
  
  document.getElementById('chatMessages').innerHTML = `
    <div class="loading-spinner">
      <i class="fa-solid fa-spinner fa-spin"></i>
      <span>Đang tải tin nhắn...</span>
    </div>`;
  
  const url = `${basePath}/api/get_messages.php?product_id=${activeConversation.product_id}&other_user_id=${activeConversation.other_user_id}`;
  
  fetch(url, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data.status !== 'success') {
        document.getElementById('chatMessages').innerHTML = 
          '<div class="empty-state"><p>Không thể tải tin nhắn</p></div>';
        return;
      }
      renderMessages(data.messages);
    })
    .catch(err => console.error('Error:', err));
}

function renderMessages(messages) {
  const container = document.getElementById('chatMessages');
  
  if (messages.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <h5>Bắt đầu cuộc trò chuyện!</h5>
        <p>Gửi tin nhắn đầu tiên của bạn</p>
      </div>`;
    return;
  }
  
  let html = '';
  let lastDate = '';
  
  messages.forEach(msg => {
    if (msg.id > lastMessageId) lastMessageId = msg.id;
    
    const msgDate = new Date(msg.created_at).toLocaleDateString('vi-VN');
    if (msgDate !== lastDate) {
      html += `<div class="msg-date-divider"><span>${msgDate}</span></div>`;
      lastDate = msgDate;
    }
    
    const time = new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    
    html += `
      <div class="msg-row ${msg.is_mine ? 'mine' : ''}">
        <div>
          <div class="msg-bubble">${escapeHtml(msg.message)}</div>
          <div class="msg-time">${time}</div>
        </div>
      </div>`;
  });
  
  container.innerHTML = html;
  container.scrollTop = container.scrollHeight;
}

function pollNewMessages() {
  if (!activeConversation || lastMessageId === 0) return;
  
  const url = `${basePath}/api/get_messages.php?product_id=${activeConversation.product_id}&other_user_id=${activeConversation.other_user_id}&last_id=${lastMessageId}`;
  
  fetch(url, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data.status !== 'success' || data.messages.length === 0) return;
      
      const container = document.getElementById('chatMessages');
      const emptyState = container.querySelector('.empty-state');
      if (emptyState) emptyState.remove();
      
      data.messages.forEach(msg => {
        if (msg.id > lastMessageId) {
          lastMessageId = msg.id;
          const time = new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
          const msgDiv = document.createElement('div');
          msgDiv.className = `msg-row ${msg.is_mine ? 'mine' : ''}`;
          msgDiv.innerHTML = `
            <div>
              <div class="msg-bubble">${escapeHtml(msg.message)}</div>
              <div class="msg-time">${time}</div>
            </div>`;
          container.appendChild(msgDiv);
        }
      });
      
      container.scrollTop = container.scrollHeight;
    })
    .catch(err => console.error('Poll error:', err));
}

function sendMessage() {
  if (!activeConversation) return;
  
  const input = document.getElementById('chatInput');
  const message = input.value.trim();
  if (!message) return;
  
  const btn = document.getElementById('sendBtn');
  btn.disabled = true;
  
  const formData = new FormData();
  formData.append('product_id', activeConversation.product_id);
  formData.append('receiver_id', activeConversation.other_user_id);
  formData.append('message', message);
  
  fetch(basePath + '/api/send_message.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(r => r.json())
  .then(data => {
    btn.disabled = false;
    
    if (data.status === 'success') {
      input.value = '';
      
      const container = document.getElementById('chatMessages');
      const emptyState = container.querySelector('.empty-state');
      if (emptyState) emptyState.remove();
      
      const time = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
      const msgDiv = document.createElement('div');
      msgDiv.className = 'msg-row mine';
      msgDiv.innerHTML = `
        <div>
          <div class="msg-bubble">${escapeHtml(data.data.message)}</div>
          <div class="msg-time">${time}</div>
        </div>`;
      container.appendChild(msgDiv);
      container.scrollTop = container.scrollHeight;
      
      if (data.data.id > lastMessageId) lastMessageId = data.data.id;
      loadConversations();
    } else {
      alert(data.message || 'Không thể gửi tin nhắn');
    }
  })
  .catch(err => {
    btn.disabled = false;
    alert('Lỗi kết nối!');
  });
}

document.getElementById('sendBtn').addEventListener('click', sendMessage);
document.getElementById('chatInput').addEventListener('keypress', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

document.getElementById('searchConv').addEventListener('input', function() {
  const query = this.value.toLowerCase().trim();
  if (!query) {
    renderConversations(allConversations);
    return;
  }
  
  const filtered = allConversations.filter(conv => 
    conv.other_user_name.toLowerCase().includes(query) ||
    conv.product_title.toLowerCase().includes(query)
  );
  renderConversations(filtered);
});

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function escapeAttr(text) {
  if (!text) return '';
  return text.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function truncate(str, len) {
  if (!str) return '';
  return str.length > len ? str.substring(0, len) + '...' : str;
}

function timeAgo(dateStr) {
  if (!dateStr) return '';
  const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
  if (diff < 60) return 'Vừa xong';
  if (diff < 3600) return Math.floor(diff / 60) + ' phút';
  if (diff < 86400) return Math.floor(diff / 3600) + ' giờ';
  if (diff < 604800) return Math.floor(diff / 86400) + ' ngày';
  const d = new Date(dateStr);
  return d.getDate() + '/' + (d.getMonth() + 1);
}

document.addEventListener('DOMContentLoaded', function() {
  loadConversations();
  
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('product_id');
  const userId = urlParams.get('user_id');
  
  if (productId && userId) {
    setTimeout(() => {
      const item = document.querySelector(`.conv-item[data-product-id="${productId}"][data-other-user-id="${userId}"]`);
      if (item) selectConversation(item);
    }, 500);
  }
});

window.addEventListener('beforeunload', function() {
  if (pollingInterval) clearInterval(pollingInterval);
});
</script>

</body>
</html>
