class NotesAPI {
    constructor(baseURL = '') {
        this.baseURL = baseURL;
    }

    async #fetchAPI(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
        };

        const response = await fetch(url, { ...defaultOptions, ...options });
        if (response.status === 500) {
            throw new Error("Internal Server Error")
        }
        const data = await response.json();

        if (data.status === 'error') {
            throw new Error(data.message);
        }

        return data;
    }

    async register(username, password) {
        return this.#fetchAPI('/api/user/', {
            method: 'POST',
            body: JSON.stringify({ username, password }),
        });
    }

    async login(username, password) {
        return this.#fetchAPI('/api/user/login', {
            method: 'POST',
            body: JSON.stringify({ username, password }),
        });
    }

    async createNote(title, content) {
        return this.#fetchAPI('/api/note/create', {
            method: 'POST',
            body: JSON.stringify({ title, content }),
        });
    }

    async searchNotes(query) {
        return this.#fetchAPI('/api/note/search', {
            method: 'POST',
            body: JSON.stringify({ query }),
        });
    }

    async getAllNotes() {
        return this.#fetchAPI('/api/note/all');
    }
}

const api = new NotesAPI();
export default api;