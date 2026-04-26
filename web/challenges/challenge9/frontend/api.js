const got = require("got").default

const checkPath = (path) => decodeURI(path).indexOf('..') === -1

class API {
  constructor(backendUrl, token) {
    this.url = backendUrl
    this.token = token
  }

  async register(username, password) {
    const form = new FormData()
    form.set("username", username)
    form.set("password", password)

    const res = await got.post(`${this.url}/user/register`, {
      body: form,
      headers: {
        'Token': this.token,
      }
    }).json()
    
    return res
  }

  async login(username, password) {
    const form = new FormData()
    form.set("username", username)
    form.set("password", password)

    const res = await got.post(`${this.url}/user/login`, {
      body: form,
      headers: {
        'Token': this.token,
      }
    }).json()
    
    return res
  }

  async myNotes(username) {
    const res = await got.get(`${this.url}/note/my`, {
      headers: {
        Token: this.token,
        Username: username
      }
    }).json()

    return res
  }

  async createNote(username, language, content, isPrivate) {
    const form = new FormData()
    form.set("language", language)
    form.set("content", content)
    form.set("private", isPrivate)

    const res = await got.post(`${this.url}/note/create`,
      {
        body: form,
        headers: {
          Token: this.token,
          Username: username
        }
      }).json()
    return res
  }

  async getNote(username, noteId) {
    const form = new FormData()
    form.set("note_id", noteId)

    const res = await got.post(`${this.url}/note/get`, {
      body: form,
      headers: {
        Token: this.token,
        Username: username
      }
    }).json()

    return res 
  }

  async deleteNote(username, noteId) {
    const form = new FormData()
    form.set("note_id", noteId)

    const res = await got.post(`${this.url}/note/delete`, {
      body: form,
      headers: {
        Token: this.token,
        Username: username
      }
    }).json()
    return res 
  }

  async getUserInfo(username) {
    if (!checkPath(username)) return { status: "erorr", message: "invalid username" }

    console.log(`${this.url}/user/${username}`)

    const res = await got.get(`${this.url}/user/${username}`,
      { headers: { Token: this.token } }).json()

    return res
  }

  async getUsernames() {
    const res = await got.get(`${this.url}/user/`,
      { headers: { Token: this.token } }).json()

    return res
  }
}

module.exports = API
