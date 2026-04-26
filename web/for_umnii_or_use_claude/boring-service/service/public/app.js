// State management
let currentUser = null;
let notes = [];
let images = [];
let currentEditingNote = null;

// DOM Elements
const authScreen = document.getElementById('authScreen');
const appScreen = document.getElementById('appScreen');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const loginTab = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const authError = document.getElementById('authError');
const notesContainer = document.getElementById('notesContainer');
const imagesContainer = document.getElementById('imagesContainer');
const emptyState = document.getElementById('emptyState');
const emptyImagesState = document.getElementById('emptyImagesState');
const editModal = document.getElementById('editModal');
const imageModal = document.getElementById('imageModal');
const createNoteForm = document.getElementById('createNoteForm');
const editNoteForm = document.getElementById('editNoteForm');
const uploadImageForm = document.getElementById('uploadImageForm');
const uploadImageFromUrlForm = document.getElementById('uploadImageFromUrlForm');
const imageFileInput = document.getElementById('imageFile');
const imageUrlInput = document.getElementById('imageUrl');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const notesPage = document.getElementById('notesPage');
const imagesPage = document.getElementById('imagesPage');
const notesPageTab = document.getElementById('notesPageTab');
const imagesPageTab = document.getElementById('imagesPageTab');
const fileUploadTab = document.getElementById('fileUploadTab');
const urlUploadTab = document.getElementById('urlUploadTab');

// Utility functions
function showError(message, elementId = 'authError') {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
    setTimeout(() => {
        errorElement.classList.add('hidden');
    }, 5000);
}

function showSuccess(message) {
    // You can implement a toast notification here
    console.log('Success:', message);
}

// Page navigation
function showNotesPage() {
    notesPage.classList.remove('hidden');
    imagesPage.classList.add('hidden');
    notesPageTab.classList.add('border-purple-600', 'text-purple-600');
    notesPageTab.classList.remove('border-transparent', 'text-gray-500');
    imagesPageTab.classList.remove('border-purple-600', 'text-purple-600');
    imagesPageTab.classList.add('border-transparent', 'text-gray-500');
    lucide.createIcons();
}

function showImagesPage() {
    imagesPage.classList.remove('hidden');
    notesPage.classList.add('hidden');
    imagesPageTab.classList.add('border-purple-600', 'text-purple-600');
    imagesPageTab.classList.remove('border-transparent', 'text-gray-500');
    notesPageTab.classList.remove('border-purple-600', 'text-purple-600');
    notesPageTab.classList.add('border-transparent', 'text-gray-500');
    lucide.createIcons();
}

// Tab click handlers
notesPageTab.addEventListener('click', showNotesPage);
imagesPageTab.addEventListener('click', showImagesPage);

// Upload method tab switching
function showFileUpload() {
    uploadImageForm.classList.remove('hidden');
    uploadImageFromUrlForm.classList.add('hidden');
    fileUploadTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    fileUploadTab.classList.remove('text-gray-600');
    urlUploadTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    urlUploadTab.classList.add('text-gray-600');
}

function showUrlUpload() {
    uploadImageFromUrlForm.classList.remove('hidden');
    uploadImageForm.classList.add('hidden');
    urlUploadTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    urlUploadTab.classList.remove('text-gray-600');
    fileUploadTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    fileUploadTab.classList.add('text-gray-600');
}

fileUploadTab.addEventListener('click', showFileUpload);
urlUploadTab.addEventListener('click', showUrlUpload);

// Tab switching
loginTab.addEventListener('click', () => {
    loginTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    loginTab.classList.remove('text-gray-600');
    registerTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    registerTab.classList.add('text-gray-600');
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
    authError.classList.add('hidden');
});

registerTab.addEventListener('click', () => {
    registerTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    registerTab.classList.remove('text-gray-600');
    loginTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    loginTab.classList.add('text-gray-600');
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
    authError.classList.add('hidden');
});

// Auth functions
loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;
    
    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Login failed');
            return;
        }
        
        await loadUserInfo();
        showApp();
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Login error:', error);
    }
});

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('registerUsername').value;
    const password = document.getElementById('registerPassword').value;
    
    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Registration failed');
            return;
        }
        
        await loadUserInfo();
        showApp();
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Registration error:', error);
    }
});

document.getElementById('logoutBtn').addEventListener('click', async () => {
    try {
        await fetch('/logout', {
            method: 'POST',
        });
        
        currentUser = null;
        notes = [];
        images = [];
        authScreen.classList.remove('hidden');
        appScreen.classList.add('hidden');
        loginForm.reset();
        registerForm.reset();
    } catch (error) {
        console.error('Logout error:', error);
    }
});

// Load user info
async function loadUserInfo() {
    try {
        const response = await fetch('/userInfo');
        const data = await response.json();
        
        if (response.ok) {
            currentUser = data;
            document.getElementById('userInfo').textContent = `Welcome, ${data.username}!`;
        }
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

// Show app screen
function showApp() {
    authScreen.classList.add('hidden');
    appScreen.classList.remove('hidden');
    loadNotes();
    loadImages();
}

// Notes functions
async function loadNotes() {
    try {
        const response = await fetch('/notes');
        const data = await response.json();
        
        if (!response.ok) {
            if (response.status === 401) {
                // Not authenticated
                authScreen.classList.remove('hidden');
                appScreen.classList.add('hidden');
            }
            return;
        }
        
        notes = data;
        renderNotes();
    } catch (error) {
        console.error('Error loading notes:', error);
    }
}

function renderNotes() {
    notesContainer.innerHTML = '';
    
    if (notes.length === 0) {
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    
    notes.forEach(note => {
        const noteCard = createNoteCard(note);
        notesContainer.appendChild(noteCard);
    });
    
    // Reinitialize Lucide icons
    lucide.createIcons();
}

function createNoteCard(note) {
    const card = document.createElement('div');
    card.className = 'bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover fade-in';
    
    card.innerHTML = `
        <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">${escapeHtml(note.title)}</h3>
            <div class="flex space-x-1">
                <button onclick="editNote('${note.id}')" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <button onclick="deleteNote('${note.id}')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <p class="text-gray-600 text-sm line-clamp-3 whitespace-pre-wrap">${escapeHtml(note.content)}</p>
    `;
    
    return card;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMs = now - date;
    const diffInMinutes = Math.floor(diffInMs / 60000);
    const diffInHours = Math.floor(diffInMs / 3600000);
    const diffInDays = Math.floor(diffInMs / 86400000);
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInHours < 24) return `${diffInHours}h ago`;
    if (diffInDays < 7) return `${diffInDays}d ago`;
    
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// Create note
createNoteForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const title = document.getElementById('noteTitle').value;
    const content = document.getElementById('noteContent').value;
    
    try {
        const response = await fetch('/createNote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ title, content }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Failed to create note');
            return;
        }
        
        createNoteForm.reset();
        await loadNotes();
        showSuccess('Note created successfully!');
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Create note error:', error);
    }
});

// Edit note
window.editNote = function(noteId) {
    const note = notes.find(n => n.id.toString() === noteId.toString());
    if (!note) return;
    
    currentEditingNote = note;
    document.getElementById('editNoteId').value = note.id;
    document.getElementById('editNoteTitle').value = note.title;
    document.getElementById('editNoteContent').value = note.content;
    editModal.classList.remove('hidden');
};

document.getElementById('closeModal').addEventListener('click', () => {
    editModal.classList.add('hidden');
    currentEditingNote = null;
});

document.getElementById('cancelEdit').addEventListener('click', () => {
    editModal.classList.add('hidden');
    currentEditingNote = null;
});

editNoteForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('editNoteId').value;
    const title = document.getElementById('editNoteTitle').value;
    const content = document.getElementById('editNoteContent').value;
    
    try {
        const response = await fetch('/updateNote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id, title, content }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Failed to update note');
            return;
        }
        
        editModal.classList.add('hidden');
        currentEditingNote = null;
        await loadNotes();
        showSuccess('Note updated successfully!');
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Update note error:', error);
    }
});

// Delete note
window.deleteNote = async function(noteId) {
    if (!confirm('Are you sure you want to delete this note?')) {
        return;
    }
    
    try {
        const response = await fetch('/deleteNote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: noteId }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Failed to delete note');
            return;
        }
        
        await loadNotes();
        showSuccess('Note deleted successfully!');
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Delete note error:', error);
    }
};

// Close modal on backdrop click
editModal.addEventListener('click', (e) => {
    if (e.target === editModal) {
        editModal.classList.add('hidden');
        currentEditingNote = null;
    }
});

// Check if user is already logged in on page load
async function checkAuth() {
    try {
        const response = await fetch('/userInfo');
        if (response.ok) {
            await loadUserInfo();
            showApp();
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
}

// Images functions
async function loadImages() {
    try {
        const response = await fetch('/images');
        const data = await response.json();
        
        if (!response.ok) {
            if (response.status === 401) {
                // Not authenticated
                authScreen.classList.remove('hidden');
                appScreen.classList.add('hidden');
            }
            return;
        }
        
        images = data;
        renderImages();
    } catch (error) {
        console.error('Error loading images:', error);
    }
}

function renderImages() {
    imagesContainer.innerHTML = '';
    
    if (images.length === 0) {
        emptyImagesState.classList.remove('hidden');
        return;
    }
    
    emptyImagesState.classList.add('hidden');
    
    images.forEach(image => {
        const imageCard = createImageCard(image);
        imagesContainer.appendChild(imageCard);
    });
    
    // Reinitialize Lucide icons
    lucide.createIcons();
}

function createImageCard(image) {
    const card = document.createElement('div');
    card.className = 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover fade-in relative group';
    
    // Convert buffer to base64 if needed
    let imageData;
    if (image.image && image.image.type === 'Buffer' && image.image.data) {
        // Convert buffer array to base64
        const bytes = new Uint8Array(image.image.data);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        imageData = 'data:image/jpeg;base64,' + btoa(binary);
    } else if (typeof image.image === 'string') {
        // Already base64 or data URL
        if (image.image.startsWith('data:')) {
            imageData = image.image;
        } else {
            imageData = 'data:image/jpeg;base64,' + image.image;
        }
    }
    
    card.innerHTML = `
        <div class="relative cursor-pointer" onclick="viewImage('${image.id}')">
            <img src="${imageData}" alt="User uploaded image" class="w-full h-64 object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center">
                <i data-lucide="maximize-2" class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-all"></i>
            </div>
            <button onclick="deleteImage('${image.id}'); event.stopPropagation();" class="absolute top-2 right-2 p-2 bg-white bg-opacity-90 hover:bg-red-50 text-gray-400 hover:text-red-600 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    return card;
}

// Image file preview
imageFileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        imagePreview.classList.add('hidden');
    }
});

// Upload image
uploadImageForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const file = imageFileInput.files[0];
    if (!file) {
        showError('Please select an image file');
        return;
    }
    
    try {
        // Convert file to base64
        const reader = new FileReader();
        reader.onload = async (e) => {
            const base64Data = e.target.result;
            
            const response = await fetch('/createImage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ image: base64Data }),
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                showError(data.error || 'Failed to upload image');
                return;
            }
            
            uploadImageForm.reset();
            imagePreview.classList.add('hidden');
            await loadImages();
            showSuccess('Image uploaded successfully!');
        };
        reader.readAsDataURL(file);
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Upload image error:', error);
    }
});

// View image
window.viewImage = function(imageId) {
    const image = images.find(img => img.id.toString() === imageId.toString());
    if (!image) return;
    
    // Convert buffer to base64 if needed
    let imageData;
    if (image.image && image.image.type === 'Buffer' && image.image.data) {
        const bytes = new Uint8Array(image.image.data);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        imageData = 'data:image/jpeg;base64,' + btoa(binary);
    } else if (typeof image.image === 'string') {
        if (image.image.startsWith('data:')) {
            imageData = image.image;
        } else {
            imageData = 'data:image/jpeg;base64,' + image.image;
        }
    }
    
    document.getElementById('modalImage').src = imageData;
    imageModal.classList.remove('hidden');
};

document.getElementById('closeImageModal').addEventListener('click', () => {
    imageModal.classList.add('hidden');
});

// Close image modal on backdrop click
imageModal.addEventListener('click', (e) => {
    if (e.target === imageModal) {
        imageModal.classList.add('hidden');
    }
});

// Delete image
window.deleteImage = async function(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }
    
    try {
        const response = await fetch('/deleteImage', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: imageId }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Failed to delete image');
            return;
        }
        
        await loadImages();
        showSuccess('Image deleted successfully!');
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Delete image error:', error);
    }
};

// Upload image from URL
uploadImageFromUrlForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const url = imageUrlInput.value.trim();
    if (!url) {
        showError('Please enter an image URL');
        return;
    }
    
    try {
        const response = await fetch('/createImageFromUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showError(data.error || 'Failed to upload image from URL');
            return;
        }
        
        uploadImageFromUrlForm.reset();
        await loadImages();
        showSuccess('Image uploaded successfully from URL!');
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Upload image from URL error:', error);
    }
});

// Initialize
checkAuth();

