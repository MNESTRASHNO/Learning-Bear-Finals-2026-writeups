class Api {
  constructor(baseUrl = '/api') {
    this.baseUrl = baseUrl;
  }

  async request(endpoint, options = {}) {
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      ...options,
      credentials: 'include', 
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        ...options.headers,
      },
    });
    return response.json();
  }

  // Auth methods
  async login(username, password) {
    const params = new URLSearchParams();
    params.append('username', username);
    params.append('password', password);
    return this.request('/auth/login', {
      method: 'POST',
      body: params,
    });
  }

  async register(username, password) {
    const params = new URLSearchParams();
    params.append('username', username);
    params.append('password', password);
    return this.request('/auth/register', {
      method: 'POST',
      body: params,
    });
  }

  async logout() {
    return this.request('/auth/logout', { method: 'GET' });
  }

  // Notes methods
  async getMyNotes() {
    return this.request('/note/my', { method: 'GET' });
  }

  async createNote(language, content, isPrivate) {
    const params = new URLSearchParams();
    params.append('language', language);
    params.append('content', content);
    params.append('private', isPrivate);
    console.log(params);
    return this.request('/note/create', {
      method: 'POST',
      body: params,
    });
  }

  async getNote(noteId) {
    return this.request(`/note/get/${noteId}`, { method: 'GET' });
  }

  async deleteNote(noteId) {
    return this.request(`/note/delete/${noteId}`, { method: 'GET' });
  }

  // User methods
  async getProfile() {
    return this.request('/user/profile', { method: 'GET' });
  }

  async updateProfile(userData) {
    const params = new URLSearchParams();
    for (const [key, value] of Object.entries(userData)) {
      params.append(key, value);
    }
    return this.request('/user/update', {
      method: 'POST',
      body: params,
    });
  }
  async getUserPastes(username) {
    return this.request(`/user/${username}`, { method: 'GET' });
  }
}

const api = new Api("/api")
export default api;