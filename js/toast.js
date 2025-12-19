/**
 * Toast Notification System
 * Hệ thống thông báo đẹp thay thế alert()
 */

// Tạo container cho toast nếu chưa có
function createToastContainer() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }
    return document.getElementById('toast-container');
}

// Hiển thị toast notification
function showToast(message, type = 'info', duration = 3000) {
    const container = createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    // Icon theo loại thông báo
    const icons = {
        success: '<i class="fas fa-check-circle"></i>',
        error: '<i class="fas fa-times-circle"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        info: '<i class="fas fa-info-circle"></i>'
    };
    
    // Màu sắc theo loại
    const colors = {
        success: { bg: '#d4edda', border: '#28a745', text: '#155724', icon: '#28a745' },
        error: { bg: '#f8d7da', border: '#dc3545', text: '#721c24', icon: '#dc3545' },
        warning: { bg: '#fff3cd', border: '#ffc107', text: '#856404', icon: '#ffc107' },
        info: { bg: '#d1ecf1', border: '#17a2b8', text: '#0c5460', icon: '#17a2b8' }
    };
    
    const color = colors[type] || colors.info;
    
    toast.style.cssText = `
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 20px;
        background: ${color.bg};
        border-left: 4px solid ${color.border};
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease-out;
        min-width: 300px;
        max-width: 400px;
    `;
    
    toast.innerHTML = `
        <span style="color: ${color.icon}; font-size: 20px; flex-shrink: 0; margin-top: 2px;">
            ${icons[type] || icons.info}
        </span>
        <div style="flex: 1;">
            <p style="margin: 0; color: ${color.text}; font-size: 14px; line-height: 1.5; word-wrap: break-word;">
                ${message}
            </p>
        </div>
        <button onclick="this.parentElement.remove()" style="
            background: none;
            border: none;
            color: ${color.text};
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            margin-left: 10px;
            opacity: 0.7;
            flex-shrink: 0;
        ">&times;</button>
    `;
    
    container.appendChild(toast);
    
    // Tự động ẩn sau duration
    if (duration > 0) {
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-in forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    return toast;
}

// Các hàm tiện ích
function toastSuccess(message, duration = 3000) {
    return showToast(message, 'success', duration);
}

function toastError(message, duration = 4000) {
    return showToast(message, 'error', duration);
}

function toastWarning(message, duration = 3500) {
    return showToast(message, 'warning', duration);
}

function toastInfo(message, duration = 3000) {
    return showToast(message, 'info', duration);
}

// Toast với redirect sau khi hiển thị
function toastAndRedirect(message, type, redirectUrl, delay = 1500) {
    showToast(message, type, delay + 500);
    setTimeout(() => {
        window.location.href = redirectUrl;
    }, delay);
}

// Hộp thoại xác nhận đẹp thay thế confirm()
function showConfirm(message, onConfirm, onCancel = null, options = {}) {
    const title = options.title || 'Xác nhận';
    const confirmText = options.confirmText || 'Xác nhận';
    const cancelText = options.cancelText || 'Hủy';
    const type = options.type || 'warning'; // warning, danger, info

    // Tạo overlay
    const overlay = document.createElement('div');
    overlay.id = 'confirm-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100000;
        animation: fadeIn 0.2s ease-out;
    `;

    // Màu sắc theo loại
    const colors = {
        warning: { icon: '#f6c23e', iconBg: '#fff3cd', btn: '#f6c23e' },
        danger: { icon: '#dc3545', iconBg: '#f8d7da', btn: '#dc3545' },
        info: { icon: '#17a2b8', iconBg: '#d1ecf1', btn: '#17a2b8' }
    };
    const color = colors[type] || colors.warning;

    // Icons
    const icons = {
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        danger: '<i class="fas fa-trash-alt"></i>',
        info: '<i class="fas fa-question-circle"></i>'
    };

    // Tạo modal
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        max-width: 400px;
        width: 90%;
        padding: 30px;
        text-align: center;
        animation: scaleIn 0.3s ease-out;
    `;

    modal.innerHTML = `
        <div style="
            width: 70px;
            height: 70px;
            background: ${color.iconBg};
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: ${color.icon};
        ">
            ${icons[type] || icons.warning}
        </div>
        <h4 style="margin: 0 0 15px; color: #1a1a2e; font-weight: 700; font-size: 20px;">${title}</h4>
        <p style="margin: 0 0 25px; color: #666; font-size: 15px; line-height: 1.6;">${message}</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="confirm-cancel-btn" style="
                padding: 12px 28px;
                border: 2px solid #ddd;
                background: #fff;
                color: #666;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
            ">${cancelText}</button>
            <button id="confirm-ok-btn" style="
                padding: 12px 28px;
                border: none;
                background: ${color.btn};
                color: #fff;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
            ">${confirmText}</button>
        </div>
    `;

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    // Xử lý sự kiện
    const cancelBtn = overlay.querySelector('#confirm-cancel-btn');
    const okBtn = overlay.querySelector('#confirm-ok-btn');

    cancelBtn.onmouseover = () => { cancelBtn.style.background = '#f5f5f5'; };
    cancelBtn.onmouseout = () => { cancelBtn.style.background = '#fff'; };
    okBtn.onmouseover = () => { okBtn.style.opacity = '0.9'; };
    okBtn.onmouseout = () => { okBtn.style.opacity = '1'; };

    const closeModal = () => {
        overlay.style.animation = 'fadeOut 0.2s ease-in forwards';
        setTimeout(() => overlay.remove(), 200);
    };

    cancelBtn.onclick = () => {
        closeModal();
        if (onCancel) onCancel();
    };

    okBtn.onclick = () => {
        closeModal();
        if (onConfirm) onConfirm();
    };

    // Đóng khi click overlay
    overlay.onclick = (e) => {
        if (e.target === overlay) {
            closeModal();
            if (onCancel) onCancel();
        }
    };

    // ESC để đóng
    const escHandler = (e) => {
        if (e.key === 'Escape') {
            closeModal();
            if (onCancel) onCancel();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
}

// Hàm tiện ích để xác nhận xóa
function confirmDelete(message, deleteUrl) {
    showConfirm(message, () => {
        window.location.href = deleteUrl;
    }, null, {
        title: 'Xác nhận xóa',
        confirmText: 'Xóa',
        cancelText: 'Hủy',
        type: 'danger'
    });
}

// Thêm CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .toast-notification {
        font-family: 'Roboto', 'Segoe UI', sans-serif;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    @media (max-width: 480px) {
        #toast-container {
            left: 10px;
            right: 10px;
            max-width: none;
        }
        
        .toast-notification {
            min-width: auto !important;
            max-width: none !important;
        }
    }
`;
document.head.appendChild(style);
